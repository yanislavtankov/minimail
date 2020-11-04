<?php

namespace App\Modules\Maillist\Requests;

use App\Models\Maillist;
use Illuminate\Foundation\Http\FormRequest;

class MaillistRequest extends FormRequest
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
            Maillist::COLUMN_NAME => 'required|string',
            Maillist::COLUMN_EMAIL => 'required|string',
        ];
    }
}
