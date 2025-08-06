<?php

namespace App\helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissionHelper
{
    /**
     * Check if the current admin has permission
     */
    public static function hasPermission($permission)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return false;
        }
        
        // Super admins have all permissions
        if ($admin->is_super_admin) {
            return true;
        }
        
        // Check if admin is active
        if (!$admin->is_active) {
            return false;
        }
        
        // Get role-based permissions
        $rolePermissions = self::getRolePermissions();
        $adminRole = $admin->role ?? '';
        
        // Check role-based permissions first
        if (isset($rolePermissions[$adminRole]) && strpos($permission, '.') !== false) {
            list($category, $action) = explode('.', $permission, 2);
            if (isset($rolePermissions[$adminRole][$category]) && 
                in_array($action, $rolePermissions[$adminRole][$category])) {
                return true;
            }
        }
        
        // Check custom permissions stored in database
        $permissions = $admin->permissions;
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true);
        }
        $permissions = $permissions ?? [];
        
        // Check if permission exists in the nested structure
        if (strpos($permission, '.') !== false) {
            list($category, $action) = explode('.', $permission, 2);
            return isset($permissions[$category]) && in_array($action, $permissions[$category]);
        }
        
        // Fallback: check in flat array format for backward compatibility
        return in_array($permission, $permissions);
    }
    
    /**
     * Check if the current admin has any of the given permissions
     */
    public static function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if (self::hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if the current admin has all of the given permissions
     */
    public static function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if the current admin can access a specific menu section
     */
    public static function canAccessMenu($menuKey)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return false;
        }
        
        // Super admins have access to everything
        if ($admin->is_super_admin) {
            return true;
        }
        
        $menuPermissions = self::getMenuPermissions();
        
        // If no specific permissions defined for this menu, deny access for sub-admins
        if (!isset($menuPermissions[$menuKey])) {
            return false;
        }
        
        // Check if admin has any of the required permissions
        return self::hasAnyPermission($menuPermissions[$menuKey]);
    }
    
    /**
     * Get role-based permissions mapping
     */
    public static function getRolePermissions()
    {
        return [
            'manager' => [
                'users' => ['view', 'edit', 'ban'],
                'deposits' => ['view', 'approve', 'reject'],
                'withdrawals' => ['view', 'approve', 'reject'],
                'transfers' => ['view', 'create', 'export'],
                'support' => ['view', 'reply', 'close', 'assign'],
                'content' => ['videos', 'popups', 'notifications'],
                'plans' => ['view', 'create', 'edit', 'delete'],
                'reports' => ['view', 'export', 'analytics'],
            ],
            'moderator' => [
                'users' => ['view', 'edit'],
                'deposits' => ['view'],
                'withdrawals' => ['view'],
                'support' => ['view', 'reply', 'close'],
                'content' => ['videos', 'popups'],
                'reports' => ['view'],
            ],
            'support' => [
                'users' => ['view'],
                'deposits' => ['view'],
                'withdrawals' => ['view'],
                'support' => ['view', 'reply', 'close'],
            ],
            'accountant' => [
                'users' => ['view'],
                'deposits' => ['view', 'approve', 'reject', 'export'],
                'withdrawals' => ['view', 'approve', 'reject', 'export'],
                'transfers' => ['view', 'create', 'export'],
                'reports' => ['view', 'export', 'analytics'],
            ],
            'editor' => [
                'users' => ['view'],
                'content' => ['videos', 'popups', 'notifications'],
            ],
            'sub_admin' => [
                'users' => ['view'],
                'deposits' => ['view'],
                'withdrawals' => ['view'],
                'support' => ['view', 'reply'],
                'reports' => ['view'],
            ],
        ];
    }
    
    /**
     * Get menu sections and their required permissions
     */
    public static function getMenuPermissions()
    {
        return [
            // Core admin functionalities
            'analytics' => ['reports.view', 'reports.analytics'],
            'lottery' => ['content.videos', 'reports.view'],
            'kyc' => ['users.view', 'users.edit'],
            'deposits' => ['deposits.view'],
            'withdrawals' => ['withdrawals.view'],
            'users' => ['users.view'],
            'user_management' => ['users.view'],
            'sub_admins' => ['settings.general'], // Only super admins
            'admin_management' => ['settings.general'], // Only super admins
            'videos' => ['content.videos'],
            'plans' => ['plans.view', 'settings.general'],
            'settings' => ['settings.general'],
            'system_settings' => ['settings.general'],
            'system' => ['settings.general'], // For Schedule Management & Maintenance
            'support' => ['support.view'],
            'notifications' => ['content.notifications'],
            'email-campaigns' => ['email-campaigns.dashboard', 'email-campaigns.analytics', 'email-campaigns.templates', 'email-campaigns.queue', 'email-campaigns.settings'],
            'popups' => ['content.popups'],
            'transfers' => ['transfers.view'],
            'balance_transfers' => ['transfers.view', 'transfers.create'],
            'referrals' => ['users.view', 'settings.general'], // Referral Benefits System
            'reports' => ['reports.view'],
            'financial_reports' => ['reports.view', 'deposits.view', 'withdrawals.view'],
            'user_reports' => ['users.view', 'reports.view'],
            'video_analytics' => ['content.videos', 'reports.view'],
            'referral_system' => ['users.view', 'reports.view'],
            'payment_gateways' => ['settings.general', 'deposits.view'],
            'email_settings' => ['settings.mail'],
            'general_settings' => ['settings.general'],
            'security_settings' => ['settings.security'],
            'maintenance' => ['settings.general'],
            'backup' => ['settings.general'],
            'logs' => ['settings.general'],
            'cache' => ['settings.general'],
        ];
    }
    
    /**
     * Get the current admin's role
     */
    public static function getCurrentAdminRole()
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return null;
        }
        
        if ($admin->is_super_admin) {
            return 'super_admin';
        }
        
        return $admin->role;
    }
    
    /**
     * Check if current user is super admin
     */
    public static function isSuperAdmin()
    {
        $admin = Auth::guard('admin')->user();
        return $admin && $admin->is_super_admin;
    }
    
    /**
     * Get available permissions list
     */
    public static function getAllPermissions()
    {
        return [
            'users' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
                'users.ban' => 'Ban/Unban Users',
            ],
            'deposits' => [
                'deposits.view' => 'View Deposits',
                'deposits.approve' => 'Approve Deposits',
                'deposits.reject' => 'Reject Deposits',
                'deposits.export' => 'Export Deposits',
            ],
            'withdrawals' => [
                'withdrawals.view' => 'View Withdrawals',
                'withdrawals.approve' => 'Approve Withdrawals',
                'withdrawals.reject' => 'Reject Withdrawals',
                'withdrawals.export' => 'Export Withdrawals',
            ],
            'support' => [
                'support.view' => 'View Support Tickets',
                'support.reply' => 'Reply to Tickets',
                'support.close' => 'Close Tickets',
                'support.assign' => 'Assign Tickets',
            ],
            'content' => [
                'content.videos' => 'Manage Videos',
                'content.popups' => 'Manage Popups',
                'content.notifications' => 'Manage Notifications',
            ],
            'email-campaigns' => [
                'email-campaigns.dashboard' => 'Email Campaign Dashboard',
                'email-campaigns.analytics' => 'Campaign Analytics & Reports',
                'email-campaigns.templates' => 'Manage Email Templates',
                'email-campaigns.queue' => 'Manage Email Queue',
                'email-campaigns.settings' => 'Campaign Settings',
                'email-campaigns.send' => 'Send Email Campaigns',
            ],
            'settings' => [
                'settings.general' => 'General Settings',
                'settings.security' => 'Security Settings',
                'settings.mail' => 'Mail Configuration',
            ],
            'plans' => [
                'plans.view' => 'View Plans',
                'plans.create' => 'Create Plans',
                'plans.edit' => 'Edit Plans',
                'plans.delete' => 'Delete Plans',
            ],
            'reports' => [
                'reports.view' => 'View Reports',
                'reports.export' => 'Export Reports',
                'reports.analytics' => 'View Analytics',
            ],
            'notifications' => [
                'content.notifications' => 'Manage Notifications',
            ],
            'popups' => [
                'content.popups' => 'Manage Popups',
            ],
            'transfers' => [
                'transfers.view' => 'View Transfer History',
                'transfers.create' => 'Create New Transfers',
                'transfers.export' => 'Export Transfer Data',
            ],
            'transfer' => [
                'users.view' => 'View Users',
                'users.edit' => 'Edit Users', // For transferring members
            ]
        ];
    }
}
