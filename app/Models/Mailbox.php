<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailbox extends Model
{
    use HasFactory;

    const COLUMN_ID             = 'id';
    const COLUMN_USER_ID        = 'user_id';
    const COLUMN_FROM           = 'from';
    const COLUMN_TO             = 'to';
    const COLUMN_SUBJECT        = 'subject';
    const COLUMN_TEXT           = 'text';
    const COLUMN_HTML           = 'html';
    const COLUMN_ATTACHMENT     = 'attachment';
    const COLUMN_STATUS         = 'status';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::COLUMN_FROM,
        self::COLUMN_TO,
        self::COLUMN_SUBJECT,
        self::COLUMN_TEXT,
        self::COLUMN_HTML,
        self::COLUMN_ATTACHMENT,
        self::COLUMN_STATUS,
    ];   



    protected $guarded = [self::COLUMN_ID];
}
