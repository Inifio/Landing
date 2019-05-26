<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    // Table Name
    protected $table = 'users';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;
}
