<?php

namespace App\Modules\Mailbox\Controllers;

use Exception;
use App\Mail\sendMail;
use App\Models\Mailbox;
use App\Models\Maillist;
use Illuminate\Http\Request;
use SimpleHtmlToText\Parser;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Mailbox\Requests\MailboxRequest;
use App\Modules\Mailbox\Resources\MailboxResource;
use \Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MailboxController extends Controller
{

    private $user;

    function __construct()
    {
        $this->user = auth()->user();
    }
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        [$column, $order] = explode(',', $request->input('sortBy', 'id,asc'));
        $pageSize = (int) $request->input('pageSize', 10);
        $resource = Mailbox::query()
            ->where(Mailbox::COLUMN_USER_ID, "=",  $this->user->id)
            ->when($request->filled('search'), function (Builder $q) use ($request) {
                $q->andWhere(Mailbox::COLUMN_FROM, 'like', '%' . $request->search . '%');
            })
            ->orderBy($column, $order)->paginate($pageSize);
        return MailboxResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     * @param MailboxRequest $request
     * @return JsonResponse
     */
    public function store(MailboxRequest $request)
    {
        $email=null;
        $data = $request->validated();
        $mailbox = new Mailbox($data);
        $mailbox->text = (new Parser())->parseString($mailbox->html);
        $mailbox->user_id = $this->user->id;
        $mailbox->attachment = ($mailbox->attachment == "0") ? null : $mailbox->attachment;
        $contact = new Maillist();
        $email = $contact
            ->where('email', $mailbox->to)
            ->where('user_id', $this->user->id)
            ->value('email');
        if(!$email){
            $contact->user_id = $this->user->id;
            $contact->name =  $mailbox->to;
            $contact->email = $mailbox->to;
            $contact->save();
        }
        $mailbox->save();
        
        // $emailId = $mailbox->id;
        // $emailBox = Mailbox::findOrFail($mailbox->id);
        
        $message = (new sendMail($mailbox))
            ->onConnection('database')
            ->onQueue('default');
        $queueResponse = Mail::to($mailbox->to)
            ->queue($message);
        $status = (isset($queueResponse)) ? "sent" : "failed";
        $job_id = (isset($queueResponse)) ? $queueResponse : null;


        DB::table('mailboxes')
            ->where('id', $mailbox->id)
            ->update(['status' => $status, 'job_id' => $job_id]);



        return response()->json([
            'type' => self::RESPONSE_TYPE_SUCCESS,
            'message' => 'Successfully queued',
            'queuejobid' => $queueResponse,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Mailbox $mailbox
     * @return MailboxResource
     */
    public function show(Mailbox $mailbox)
    {
        return new MailboxResource($mailbox);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MailboxRequest $request
     * @param Mailbox $mailbox
     * @return JsonResponse
     */
    public function update(MailboxRequest $request, Mailbox $mailbox)
    {
        $data = $request->validated();
        $mailbox->fill($data)->save();
        return response()->json([
            'type' => self::RESPONSE_TYPE_SUCCESS,
            'message' => 'Successfully updated'
        ]);
    }

    /**
     * @param Mailbox $mailbox
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Mailbox $mailbox)
    {
        $mailbox->delete();
        return response()->json([
            'type' => self::RESPONSE_TYPE_SUCCESS,
            'message' => 'Successfully deleted'
        ]);
    }

}
