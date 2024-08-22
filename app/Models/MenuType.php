<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuType extends Model
{
    use HasFactory;

    protected $table = 'menu_types';

    protected $primaryKey = 'menu_type_id';

    protected $fillable = [
        'menu_type',
    ];

    public $timestamps = true;

    public function menus()
    {
        return $this->hasMany(Menu::class, 'menu_type_id', 'menu_type_id');
    }
}
