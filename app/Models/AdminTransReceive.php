<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminTransReceive extends Model
{
    use HasFactory;
    
    protected $table = 'admin_trans_receives';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // Remove this line - let Laravel use the default 'id' primary key
    // protected $primaryKey = 'admin_id';

    protected $fillable = [
        'admin_id',
        'user_transfer',
        'amount',
        'status',
        'user_receive',
        'note'
    ];
    protected $casts = [
        'status' => 'boolean',
        'amount' => 'decimal:2',
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
