<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maillist extends Model
{
    use HasFactory;

    const COLUMN_ID             = 'id';
    const COLUMN_USER_ID        = 'user_id';
    const COLUMN_NAME           = 'name';
    const COLUMN_EMAIL          = 'email';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::COLUMN_NAME,
        self::COLUMN_EMAIL,
    ];

    protected $guarded = [self::COLUMN_ID];
}
