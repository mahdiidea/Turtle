<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed email
 * @property mixed password
 * @property mixed remember_token
 * @property mixed created_at
 * @property mixed updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, "users_categories_role");
    }

    public function threads()
    {
        return $this->belongsToMany(Thread::class, "participants");
    }

    public function threadResource($thread = null)
    {
        $user_id = $this->id;

        $query = Thread::query()
            ->select('threads.*', DB::raw('count(u.id) as unread'),
                'c.user_id', 'users.name as username', 'c.last_seen', 'm.body',
                DB::raw('(m.user_id = ' . $user_id . ') you'),
                DB::raw('(c.last_seen >= m.created_at) seen'))
            ->join('participants as p', 'threads.id', '=', 'p.thread_id')
            ->leftJoin('participants as c', function ($join) use ($user_id) {
                $join->on('threads.id', '=', 'c.thread_id')
                    ->where('c.id', '=', function ($query) use ($user_id) {
                        $query->select('pr.id')
                            ->from('participants as pr')
                            ->whereRaw('pr.thread_id = threads.id')
                            ->whereRaw('pr.user_id != ' . $user_id)
                            ->orderByDesc('pr.last_seen')
                            ->limit(1);
                    });
            })
            ->leftJoin('users', 'users.id', '=', 'c.user_id')
            ->leftJoin('messages as u', function ($join) {
                $join->on('threads.id', '=', 'u.thread_id')
                    ->on('u.created_at', '>', 'p.last_seen');
            })
            ->leftJoin('messages as m', function ($join) {
                $join->on('threads.id', '=', 'm.thread_id')
                    ->where('m.id', '=', function ($query) {
                        $query->select('msg.id')
                            ->from('messages as msg')
                            ->whereRaw('msg.thread_id = threads.id')
                            ->orderByDesc('msg.created_at')
                            ->limit(1);
                    });
            })
            ->where('p.user_id', $user_id)
            ->groupBy('threads.id')
            ->groupBy('m.id')
            ->groupBy('c.id')
            ->orderByDesc('threads.updated_at');

        if (isset($thread))
            return $query->where('threads.id', $thread)->first();

        return $query->get();
    }

    /**
     * @param $user_id
     * @return Thread|null
     */
    public function hasThread($user_id)
    {
        $auth_id = $this->id;
        /** @var Thread|null $thread */
        $thread = Thread::query()
            ->select('threads.*',
                DB::raw('count(p.id) as p_count'),
                DB::raw('count(s.id) as s_count'))
            ->leftJoin("participants as p", function ($join) use ($user_id) {
                $join->on("threads.id", "=", "p.thread_id")
                    ->where("p.user_id", "=", $user_id);
            })->leftJoin("participants as s", function ($join) use ($auth_id) {
                $join->on("threads.id", "=", "s.thread_id")
                    ->where("s.user_id", "=", $auth_id);
            })
            ->groupBy('threads.id')
            ->having('p_count', '>', 0)
            ->having('s_count', '>', 0)
            ->first();
        return $thread;
    }
}
