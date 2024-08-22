<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use App\Services\Attribute as AttributeService;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'code', 'name', 'phone', 'email', 'password', 'token', 'nonce', 'is_verify'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function getMappedAttributes()
    {
        $attributeService = new AttributeService(new \App\Models\Attribute());
        return $attributeService->getMappedAttributesByUserId($this->user_id);
    }

    public function roles()
    {
        return $this->hasMany(RoleAccess::class, 'user_id', 'user_id');
    }

    public function getMappedRoles()
    {
        return $this->roles()->with('role')->get()->map(function ($roleAccess) {
            return $roleAccess->role;
        })->toArray();
    }

}
