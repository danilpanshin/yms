<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * @param string $email
 * @param string $password
 * @param string $name
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPermissions, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function is_admin(){
        return Auth::user()->hasRole('admin');
    }

    public function is_manager(){
        return Auth::user()->hasRole('manager');
    }

    public function is_stock_admin(){
        return Auth::user()->hasRole('stock_admin');
    }

    public function is_driver(){
        return Auth::user()->hasRole('driver');
    }

    public function is_supplier(){
        return Auth::user()->hasRole('supplier');
    }

    public function can_set_claim_supplier(): bool
    {
        return $this->is_admin() || $this->is_manager() || $this->is_stock_admin();
    }

    public function can_choose_external_supply_type(): bool
    {
        return $this->is_admin() || $this->is_stock_admin();
    }

}
