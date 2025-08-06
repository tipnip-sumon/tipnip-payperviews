<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'image',
        'phone',
        'address',
        'role',
        'permissions',
        'is_active',
        'is_super_admin',
        'balance',
        'total_deposited',
        'total_withdrawn',
        'total_transferred',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'login_attempts',
        'locked_until',
        'notes',
        'email_verified_at',
    ];

    public $timestamps = true;

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];
    
    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'permissions' => 'json',
        'is_active' => 'boolean',
        'is_super_admin' => 'boolean',
        'balance' => 'decimal:2',
        'total_deposited' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'total_transferred' => 'decimal:2',
        'login_attempts' => 'integer',
    ];
    public function transReceives()
    {
        return $this->hasMany(AdminTransReceive::class, 'admin_id');
    }
    
    /**
     * Check if admin has specific permission
     */
    public function hasPermission($module, $action = 'view')
    {
        if ($this->is_super_admin) {
            return true;
        }
        
        $permissions = $this->permissions ?? [];
        return isset($permissions[$module]) && in_array($action, $permissions[$module]);
    }
    
    /**
     * Check if admin can perform action on module
     */
    public function canDo($permission)
    {
        if ($this->is_super_admin) {
            return true;
        }
        
        [$module, $action] = explode('.', $permission);
        return $this->hasPermission($module, $action);
    }
    
    /**
     * Get admin's avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->image && $this->image !== 'default.png') {
            return asset('assets/images/admins/' . $this->image);
        }
        return asset('assets/images/admins/default.png');
    }
    
    /**
     * Update admin's balance
     */
    public function updateBalance($amount, $type = 'add')
    {
        if ($type === 'add') {
            $this->balance += $amount;
        } else {
            $this->balance -= $amount;
        }
        
        return $this->save();
    }
    
    /**
     * Record login attempt
     */
    public function recordLogin($ip, $userAgent)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'last_login_user_agent' => $userAgent,
            'login_attempts' => 0, // Reset failed attempts on successful login
        ]);
    }
     
    /**
     * Lock account for security
     */
    public function lockAccount($minutes = 30)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }
    
    /**
     * Check if account is locked
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until instanceof \Carbon\Carbon && $this->locked_until->isFuture();
    }
    
    /**
     * Increment failed login attempts
     */
    public function incrementLoginAttempts()
    {
        $this->increment('login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->login_attempts >= 5) {
            $this->lockAccount();
        }
    }
    
    /**
     * Update last login information
     */
    public function updateLastLogin()
    {
        try {
            $this->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_login_user_agent' => request()->userAgent(),
                'login_attempts' => 0, // Reset on successful login
                'locked_until' => null, // Clear any lock
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update admin last login time', [
                'error' => $e->getMessage(),
                'admin_id' => $this->id
            ]);
        }
    }
}
