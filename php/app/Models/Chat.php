<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $first_user_id
 * @property int $second_user_id
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Chat extends Model
{
    protected $table = "chat";

    public function first_user()
    {
        return $this->hasOne(User::class, 'first_user_id');
    }

    public function second_user()
    {
        return $this->hasOne(User::class, 'second_user_id');
    }
}
