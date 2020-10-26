<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel_id',
        'user_id',
        'content'
    ];

    /**
     * Get the user that owns the message.
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
