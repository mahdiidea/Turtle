<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed user_id
 * @property mixed uri
 * @property mixed name
 * @property mixed mime
 * @property mixed size
 * @property mixed created_at
 * @property mixed updated_at
 */
class Attach extends Model
{
    protected $table = 'attaches';
    public $timestamps = true;
    protected $fillable = ['user_id', 'name', 'uri', 'mime', 'size'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
