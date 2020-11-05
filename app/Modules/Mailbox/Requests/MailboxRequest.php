<?php

namespace App\Modules\Mailbox\Requests;

use App\Models\Mailbox;
use Illuminate\Foundation\Http\FormRequest;

class MailboxRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            Mailbox::COLUMN_FROM => 'required|string',
            Mailbox::COLUMN_TO => 'required|string',
            Mailbox::COLUMN_SUBJECT => 'required|string',
            Mailbox::COLUMN_TEXT => 'string',
            Mailbox::COLUMN_HTML => 'required|string',
            Mailbox::COLUMN_ATTACHMENT => 'string',
            Mailbox::COLUMN_STATUS => 'string',
            Mailbox::COLUMN_JOB_ID => 'integer',
        ];
    }
}
