<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chat_id
 * @property int $sender_id
 * @property string $body
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Message extends Model
{
    protected $table = "message";

    protected $fillable = [
        "chat_id",
        "sender_id",
        "body",
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, "chat_id");
    }

    public function sender()
    {
        return $this->belongsTo(User::class, "sender_id");
    }
}

