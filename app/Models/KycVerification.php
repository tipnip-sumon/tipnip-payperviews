<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KycVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth', // Optional field for date of birth
        'document_type',
        'document_number',
        'document_front',
        'document_back',
        'selfie_image',
        'nationality',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone_number',
        'status',
        'admin_remarks',
        'admin_notes',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'approved_at',
        'rejected_at',
        'under_review_at'
        
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'under_review_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Mutators
    public function setSubmittedAtAttribute($value)
    {
        $this->attributes['submitted_at'] = now();
    }

    // Validation rules
    public static function getValidationRules($step = null)
    {
        $rules = [
            'step1' => [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'date_of_birth' => 'required|date|before:-18 years',
                'nationality' => 'required|string|max:100',
                'phone_number' => 'required|string|max:20',
            ],
            'step2' => [
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
            ],
            'step3' => [
                'document_type' => 'required|in:passport,national_id,driving_license',
                'document_number' => 'required|string|max:50',
                'document_front' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
                'document_back' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
                'selfie_image' => 'required|file|mimes:jpeg,jpg,png|max:5120',
                'terms' => 'required|accepted',
            ]
        ];

        return $step ? $rules[$step] : array_merge($rules['step1'], $rules['step2'], $rules['step3']);
    }

    // Check if document number is unique
    public static function isDocumentNumberUnique($documentNumber, $excludeId = null)
    {
        $query = static::where('document_number', $documentNumber);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }
}
