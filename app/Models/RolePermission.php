<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permission';

    protected $primaryKey = 'role_permission_id';

    protected $fillable = [
        'role_id',
        'menu_id',
        'can_read',
        'can_create',
        'can_update',
        'can_delete',
    ];

    public $timestamps = true;

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }
}
