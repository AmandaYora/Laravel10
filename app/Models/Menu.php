<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $primaryKey = 'menu_id';

    protected $fillable = [
        'menu',
        'menu_slug',
        'menu_icon',
        'menu_redirect',
        'menu_sort',
        'menu_type_id',
    ];

    public $timestamps = true;

    public function menuType()
    {
        return $this->belongsTo(MenuType::class, 'menu_type_id', 'menu_type_id');
    }

    public function submenus()
    {
        return $this->hasMany(Submenu::class, 'menu_id', 'menu_id');
    }

    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'menu_id', 'menu_id');
    }
}
