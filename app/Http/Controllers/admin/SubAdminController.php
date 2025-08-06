<?php

namespace App\Http\Controllers\admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubAdminController extends Controller
{
    /**
     * Display a listing of sub-admins
     */
    public function index(Request $request)
    {
        $query = Admin::where('is_super_admin', false);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $subAdmins = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => Admin::where('is_super_admin', false)->count(),
            'active' => Admin::where('is_super_admin', false)->where('is_active', true)->count(),
            'inactive' => Admin::where('is_super_admin', false)->where('is_active', false)->count(),
        ];

        return view('admin.sub-admins.index', compact('subAdmins', 'stats'));
    }

    /**
     * Show the form for creating a new sub-admin
     */
    public function create()
    {
        $roles = $this->getAvailableRoles();
        $permissions = $this->getAvailablePermissions();
        
        return view('admin.sub-admins.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created sub-admin
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'username' => 'required|string|min:3|max:50|unique:admins,username',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:' . implode(',', array_keys($this->getAvailableRoles())),
            'permissions' => 'array',
            'permissions.*' => 'string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) 
                ->withInput();
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('admin-avatars', 'public');
        }

        // Create sub-admin
        $subAdmin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'permissions' => json_encode($request->permissions ?? []),
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $imagePath,
            'notes' => $request->notes,
            'is_active' => true,
            'is_super_admin' => false,
            'balance' => 0,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.sub-admins.index')
            ->with('success', 'Sub-admin created successfully!');
    }

    /**
     * Display the specified sub-admin
     */
    public function show($id)
    {
        $subAdmin = Admin::where('is_super_admin', false)->findOrFail($id);
        
        // Get activity stats
        $stats = [
            'last_login' => $subAdmin->last_login_at,
            'login_attempts' => $subAdmin->login_attempts,
            'account_age' => $subAdmin->created_at->diffForHumans(),
        ];

        return view('admin.sub-admins.show', compact('subAdmin', 'stats'));
    }

    /**
     * Show the form for editing the specified sub-admin
     */
    public function edit($id)
    {
        $subAdmin = Admin::where('is_super_admin', false)->findOrFail($id);
        $roles = $this->getAvailableRoles();
        $permissions = $this->getAvailablePermissions();
        
        return view('admin.sub-admins.edit', compact('subAdmin', 'roles', 'permissions'));
    }

    /**
     * Update the specified sub-admin
     */
    public function update(Request $request, $id)
    {
        $subAdmin = Admin::where('is_super_admin', false)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
            'username' => 'required|string|min:3|max:50|unique:admins,username,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|in:' . implode(',', array_keys($this->getAvailableRoles())),
            'permissions' => 'array',
            'permissions.*' => 'string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($subAdmin->image) {
                Storage::disk('public')->delete($subAdmin->image);
            }
            $imagePath = $request->file('image')->store('admin-avatars', 'public');
            $subAdmin->image = $imagePath;
        }

        

        // Update sub-admin
        $res = $subAdmin->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $request->role,
            'permissions' => json_encode($request->permissions ?? []),
            'phone' => $request->phone,
            'address' => $request->address,
            'notes' => $request->notes,
        ]);
        if (!$res) {
            return redirect()->back()
                ->with('error', 'Failed to update sub-admin. Please try again.')
                ->withInput();
        }
        // Update password if provided
        if ($request->filled('password')) {
            $subAdmin->password = Hash::make($request->password);
            $subAdmin->save();
        }

        return redirect()->route('admin.sub-admins.index')
            ->with('success', 'Sub-admin updated successfully!');
    }

    /**
     * Remove the specified sub-admin
     */
    public function destroy($id)
    {
        $subAdmin = Admin::where('is_super_admin', false)->findOrFail($id);
        
        // Delete image if exists
        if ($subAdmin->image) {
            Storage::disk('public')->delete($subAdmin->image);
        }
        
        $subAdmin->delete();

        return redirect()->route('admin.sub-admins.index')
            ->with('success', 'Sub-admin deleted successfully!');
    }

    /**
     * Toggle sub-admin status
     */
    public function toggleStatus($id)
    {
        $subAdmin = Admin::where('is_super_admin', false)->findOrFail($id);
        $subAdmin->is_active = !$subAdmin->is_active;
        $subAdmin->save();

        $status = $subAdmin->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Sub-admin {$status} successfully!");
    }

    /**
     * Reset sub-admin password
     */
    public function resetPassword($id)
    {
        $subAdmin = Admin::where('is_super_admin', false)->findOrFail($id);
        
        // Generate new password
        $newPassword = Str::random(10);
        $subAdmin->password = Hash::make($newPassword);
        $subAdmin->save();

        return redirect()->back()
            ->with('success', "Password reset successfully! New password: {$newPassword}")
            ->with('new_password', $newPassword);
    }

    /**
     * Get available roles for sub-admins
     */
    private function getAvailableRoles()
    {
        return [
            'manager' => 'Manager',
            'moderator' => 'Moderator',
            'support' => 'Support Staff',
            'accountant' => 'Accountant',
            'editor' => 'Content Editor',
        ];
    }

    /**
     * Get available permissions
     */
    private function getAvailablePermissions()
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
            'transfers' => [
                'transfers.view' => 'View Transfer History',
                'transfers.create' => 'Create New Transfers',
                'transfers.export' => 'Export Transfer Data',
            ],
        ];
    }

    /**
     * Show permissions management page
     */
    public function permissions()
    {
        $permissions = $this->getAvailablePermissions();
        $subAdmins = Admin::where('is_super_admin', false)->get();
        
        return view('admin.sub-admins.permissions', compact('permissions', 'subAdmins'));
    }
}
