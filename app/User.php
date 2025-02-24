<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
    ];

    protected $guarded = [
        'password',
    ];
}
