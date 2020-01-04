<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    public $timestamps = true;
    protected $fillable = ['caption', 'content', 'picture'];

    public function users()
    {
        return $this->belongsToMany(User::class, "users_categories_role");
    }
}
