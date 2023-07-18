<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function permissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //methods
    public static function getRole($name)
    {
        return DB::table('roles')->where('name', $name)->pluck('id')->first();
    }
}
