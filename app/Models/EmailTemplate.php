<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'content',
        'variables',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    // Template types
    const TYPES = [
        'kyc_reminder' => 'KYC Pending Reminder',
        'inactive_user' => 'Inactive User Reminder',
        'password_reset' => 'Password Reset Reminder',
        'congratulations' => 'Investment Congratulations'
    ];

    /**
     * Get the admin who created this template
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the admin who last updated this template
     */
    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /**
     * Replace variables in template content
     */
    public function render($data = [])
    {
        $content = $this->content;
        
        // Default variables
        $defaultVars = [
            '{{user_name}}' => $data['user_name'] ?? 'User',
            '{{company_name}}' => config('app.name', 'Your Company'),
            '{{current_year}}' => date('Y'),
            '{{login_url}}' => route('login'),
            '{{support_email}}' => config('mail.support_email', 'support@yoursite.com')
        ];

        // Merge with custom data
        $variables = array_merge($defaultVars, $data);

        // Replace variables
        foreach ($variables as $variable => $value) {
            $content = str_replace($variable, $value, $content);
        }

        return $content;
    }

    /**
     * Get template by slug
     */
    public static function getBySlug($slug)
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * Get available variables for this template
     */
    public function getAvailableVariables()
    {
        return $this->variables ?? [
            '{{user_name}}',
            '{{company_name}}',
            '{{current_year}}',
            '{{login_url}}',
            '{{support_email}}'
        ];
    }
}
