<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;

    protected $table = 'roles';

    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
    ];

    protected $hidden = [];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
