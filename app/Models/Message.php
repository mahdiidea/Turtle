<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed user_id
 * @property mixed thread_id
 * @property mixed body
 * @property mixed created_at
 * @property mixed updated_at
 */
class Message extends Model
{
    use SoftDeletes;

    protected $table = "messages";
    public $timestamps = true;
    protected $fillable = ['thread_id', 'user_id', 'body'];
    protected $appends = ['user', 'attaches'];

    protected function getAttachesAttribute()
    {
        return $this->attaches()->get();
    }

    protected function getUserAttribute()
    {
        return $this->user()->first();
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function attaches()
    {
        return $this->belongsToMany(Attach::class, 'messages_attaches');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
