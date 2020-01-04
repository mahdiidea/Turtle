<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed user_id
 * @property mixed body
 * @property mixed created_at
 * @property mixed updated_at
 */
class Post extends Model
{
    protected $table = "posts";
    public $timestamps = true;
    protected $fillable = ['user_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewers()
    {
        return $this->belongsToMany(Post::class, "content_views");
    }

    public function attaches()
    {
        return $this->belongsToMany(Attach::class, 'posts_attaches');
    }
}
