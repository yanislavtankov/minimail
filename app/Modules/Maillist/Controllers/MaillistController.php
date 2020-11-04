<?php

namespace App\Modules\Maillist\Controllers;

use App\Models\Maillist;
use App\Modules\Maillist\Requests\MaillistRequest;
use App\Modules\Maillist\Resources\MaillistResource;
use App\Http\Controllers\Controller;
use App\Models\Mailbox;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MaillistController extends Controller
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
        $resource = Maillist::query()
            ->where(Maillist::COLUMN_USER_ID, "=",  $this->user->id)
            ->when($request->filled('search'), function (Builder $q) use ($request) {
                $q->where(Maillist::COLUMN_NAME, 'like', '%' . $request->search . '%');
            })
            ->orderBy($column, $order)->paginate($pageSize);

        return MaillistResource::collection($resource);
    }

    /**
     * Store a newly created resource in storage.
     * @param MaillistRequest $request
     * @return JsonResponse
     */
    public function store(MaillistRequest $request)
    {
        $data = $request->validated();
        $maillist = new Maillist($data);
        $id = $maillist
            ->where('email', $maillist->email)
            ->where('user_id', $this->user->id)
            ->value('id');
        if(!$id){
            $maillist->user_id = $this->user->id;
            $maillist->save();
        } else {
            $maillist
                ->where('id', $id)
                ->update(['name' => $maillist->name]);
        }
        return response()->json([
            'type' => self::RESPONSE_TYPE_SUCCESS,
            'message' => 'Successfully created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Maillist $maillist
     * @return MaillistResource
     */
    public function show(Maillist $maillist)
    {
        $response = [];
        $contact =  new MaillistResource($maillist);
        $activities = new Mailbox();
        $activitiesList = $activities
            ->where('user_id', '=', $this->user->id)
            ->where('to', '=', $maillist->email)
            ->orderByDesc("id")
            ->get();
        if($activitiesList){
            foreach($activitiesList as $value){
                $response[] = [
                    'status' => $value->status,
                    'subject' => $value->subject,
                    'timestamp' => date_format($value->created_at, "Y-m-d / h:m:s")
                ];
            }
        }
        return response()->json([
            'type' => self::RESPONSE_TYPE_SUCCESS,
            'contact' => $contact,
            'activities' => $response
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MaillistRequest $request
     * @param Maillist $maillist
     * @return JsonResponse
     */
    public function update(MaillistRequest $request, Maillist $maillist)
    {
        $data = $request->validated();
        $maillist->fill($data)->save();
        return response()->json([
            'type' => self::RESPONSE_TYPE_SUCCESS,
            'message' => 'Successfully updated'
        ]);
    }

    /**
     * @param Maillist $maillist
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Maillist $maillist)
    {
        $maillist->delete();
        return response()->json([
            'type' => self::RESPONSE_TYPE_SUCCESS,
            'message' => 'Successfully deleted'
        ]);
    }

}
