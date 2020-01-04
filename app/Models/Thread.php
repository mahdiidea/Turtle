<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Thread
 * @package App
 *
 * @property mixed id
 * @property mixed creator
 * @property mixed caption
 * @property mixed created_at
 * @property mixed updated_at
 * @property array last_message
 */
class Thread extends Model
{
    protected $table = "threads";
    public $timestamps = true;
    protected $fillable = ["creator", "caption"];
    protected $appends = ['last_message', 'name'];

    protected function getNameAttribute()
    {
        return empty($this->caption)
            ? $this["username"]
            : $this->caption;
    }

    protected function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function markAsRead($user_id)
    {
        $this->participants()
            ->where('user_id', $user_id)
            ->update([
                'last_seen' => now()->format('Y-m-d H:i:s')
            ]);
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function hasParticipant($user_id)
    {
        return $this->participants()->where('user_id', $user_id)->count() > 0;
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "participants");
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
