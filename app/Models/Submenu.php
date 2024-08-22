<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    use HasFactory;

    protected $table = 'submenus';

    protected $primaryKey = 'submenu_id';

    protected $fillable = [
        'menu_id',
        'submenu',
        'submenu_slug',
        'submenu_redirect',
        'submenu_sort',
    ];

    public $timestamps = true;

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }
}
