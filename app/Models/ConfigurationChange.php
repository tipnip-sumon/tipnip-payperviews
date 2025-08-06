<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfigurationChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_name',
        'old_value',
        'new_value',
        'setting_type',
        'change_type',
        'changed_by',
        'changed_by_name',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the admin who made the change
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }

    /**
     * Log a configuration change
     */
    public static function logChange(
        string $settingName,
        $oldValue,
        $newValue,
        string $settingType = 'lottery',
        string $changeType = 'update',
        string $description = null,
        array $metadata = []
    ) {
        $currentUser = auth('admin')->user();
        
        return static::create([
            'setting_name' => $settingName,
            'old_value' => static::formatValue($oldValue),
            'new_value' => static::formatValue($newValue),
            'setting_type' => $settingType,
            'change_type' => $changeType,
            'changed_by' => $currentUser?->id,
            'changed_by_name' => $currentUser?->name ?? 'System',
            'description' => $description,
            'metadata' => $metadata
        ]);
    }

    /**
     * Format values for display
     */
    protected static function formatValue($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        
        return (string) $value;
    }

    /**
     * Get formatted old value for display
     */
    public function getFormattedOldValueAttribute()
    {
        return $this->formatDisplayValue($this->old_value, $this->setting_name);
    }

    /**
     * Get formatted new value for display
     */
    public function getFormattedNewValueAttribute()
    {
        return $this->formatDisplayValue($this->new_value, $this->setting_name);
    }

    /**
     * Format display value based on setting type
     */
    protected function formatDisplayValue($value, $settingName)
    {
        if (is_null($value)) {
            return '<span class="text-muted">null</span>';
        }

        // Handle boolean values
        if (in_array(strtolower($value), ['true', 'false'])) {
            $isTrue = strtolower($value) === 'true';
            $badgeClass = $isTrue ? 'bg-success' : 'bg-danger';
            $text = $isTrue ? 'Enabled' : 'Disabled';
            return "<span class=\"badge {$badgeClass}\">{$text}</span>";
        }

        // Handle price values
        if (str_contains(strtolower($settingName), 'price') || str_contains(strtolower($settingName), 'amount')) {
            if (is_numeric($value)) {
                return '$' . number_format((float) $value, 2);
            }
        }

        // Handle percentage values
        if (str_contains(strtolower($settingName), 'percentage') || str_contains(strtolower($settingName), 'commission')) {
            if (is_numeric($value)) {
                return number_format((float) $value, 2) . '%';
            }
        }

        // Handle JSON arrays
        if (is_string($value) && (str_starts_with($value, '[') || str_starts_with($value, '{'))) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($decoded)) {
                    return '<span class="text-info">[' . count($decoded) . ' items]</span>';
                }
            }
        }

        return $value;
    }

    /**
     * Get recent changes
     */
    public static function getRecent($limit = 10, $settingType = null)
    {
        $query = static::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($settingType) {
            $query->where('setting_type', $settingType);
        }

        return $query->get();
    }

    /**
     * Get changes count by period
     */
    public static function getChangesCount($period = 'week')
    {
        $query = static::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
        }

        return $query->count();
    }

    /**
     * Get change type badge class
     */
    public function getChangeTypeBadgeClass()
    {
        return match ($this->change_type) {
            'create' => 'bg-success',
            'update' => 'bg-info',
            'delete' => 'bg-danger',
            'enable' => 'bg-success',
            'disable' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Get change type display text
     */
    public function getChangeTypeText()
    {
        return match ($this->change_type) {
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'enable' => 'Enabled',
            'disable' => 'Disabled',
            default => ucfirst($this->change_type)
        };
    }
}
