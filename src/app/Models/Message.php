<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_from',
        'message_to',
        'content'
    ];

    /**
     * Get the user that owns the message.
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'message_from');
    }
}