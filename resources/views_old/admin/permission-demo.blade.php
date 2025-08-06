@extends('components.layout')

@section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fe fe-shield me-2"></i>
                    Admin Permission System Overview
                </h4>
            </div>
            <div class="card-body">
                
                <!-- Current Admin Info -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fe fe-user me-2"></i>Current Admin Information:</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Name:</strong> {{ Auth::guard('admin')->user()->name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Role:</strong> 
                                    @if(Auth::guard('admin')->user()->is_super_admin)
                                        <span class="badge badge-danger">Super Admin</span>
                                    @else
                                        <span class="badge badge-primary">{{ ucfirst(Auth::guard('admin')->user()->role) }}</span>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-{{ Auth::guard('admin')->user()->is_active ? 'success' : 'danger' }}">
                                        {{ Auth::guard('admin')->user()->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Permissions:</strong> 
                                    @php
                                        $userPermissions = Auth::guard('admin')->user()->permissions ? json_decode(Auth::guard('admin')->user()->permissions, true) : [];
                                        $permissionCount = is_array($userPermissions) ? count($userPermissions) : 0;
                                    @endphp
                                    <span class="badge badge-info">{{ $permissionCount }} assigned</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permission Testing Section -->
                <div class="row">
                    <div class="col-lg-8">
                        <h5 class="mb-3">Menu Access Testing</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Menu Section</th>
                                        <th>Required Permissions</th>
                                        <th>Access Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><i class="fe fe-gift me-2"></i>Lottery Management</td>
                                        <td><code>content.videos</code>, <code>reports.view</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('lottery'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>Lottery system management</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-shield me-2"></i>KYC Management</td>
                                        <td><code>users.view</code>, <code>users.edit</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('kyc'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>User verification system</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-credit-card me-2"></i>Deposits</td>
                                        <td><code>deposits.view</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('deposits'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>Deposit management</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-arrow-up-circle me-2"></i>Withdrawals</td>
                                        <td><code>withdrawals.view</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('withdrawals'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>Withdrawal management</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-users me-2"></i>User Management</td>
                                        <td><code>users.view</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('users'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>User administration</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-shield me-2"></i>Sub-Admin Management</td>
                                        <td><code>Super Admin Only</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::isSuperAdmin())
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>Only super admins can manage sub-admins</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-video me-2"></i>Video Management</td>
                                        <td><code>content.videos</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('videos'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>Video content management</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-headphones me-2"></i>Support</td>
                                        <td><code>support.view</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('support'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>Customer support system</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-bell me-2"></i>Notifications</td>
                                        <td><code>content.notifications</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('notifications'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>System notifications</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-window-restore me-2"></i>Popup Management</td>
                                        <td><code>content.popups</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('popups'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>Popup management system</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fe fe-settings me-2"></i>Settings</td>
                                        <td><code>settings.general</code>, <code>settings.security</code>, <code>settings.mail</code></td>
                                        <td>
                                            @if(\App\helpers\PermissionHelper::canAccessMenu('settings'))
                                                <span class="badge badge-success">✓ Accessible</span>
                                            @else
                                                <span class="badge badge-danger">✗ Denied</span>
                                            @endif
                                        </td>
                                        <td>System configuration</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <h5 class="mb-3">Your Permissions</h5>
                        
                        @if(\App\helpers\PermissionHelper::isSuperAdmin())
                            <div class="alert alert-success">
                                <i class="fe fe-crown me-2"></i>
                                <strong>Super Admin Access</strong><br>
                                You have access to all features and sections.
                            </div>
                        @else
                            @if(count($userPermissions) > 0)
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Assigned Permissions:</h6>
                                        @php $permissions = \App\helpers\PermissionHelper::getAllPermissions(); @endphp
                                        @foreach($userPermissions as $permission)
                                            @php
                                                $permissionLabel = '';
                                                foreach($permissions as $cat => $catPerms) {
                                                    if(isset($catPerms[$permission])) {
                                                        $permissionLabel = $catPerms[$permission];
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if($permissionLabel)
                                                <span class="badge bg-primary mb-1 me-1">{{ $permissionLabel }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fe fe-alert-triangle me-2"></i>
                                    <strong>No Permissions Assigned</strong><br>
                                    Contact a super admin to assign permissions.
                                </div>
                            @endif
                        @endif
                        
                        <!-- Role-based Information -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Role Information</h6>
                            </div>
                            <div class="card-body">
                                @if(\App\helpers\PermissionHelper::isSuperAdmin())
                                    <p><strong>Super Admin</strong> - Full system access</p>
                                @else
                                    @php $role = Auth::guard('admin')->user()->role; @endphp
                                    <p><strong>{{ ucfirst($role) }}</strong></p>
                                    @switch($role)
                                        @case('manager')
                                            <small class="text-muted">Can manage users, deposits, withdrawals, support, content, and view reports.</small>
                                            @break
                                        @case('moderator') 
                                            <small class="text-muted">Can moderate users, view deposits/withdrawals, handle support, and manage content.</small>
                                            @break
                                        @case('support')
                                            <small class="text-muted">Can view users, deposits, withdrawals, and handle support tickets.</small>
                                            @break
                                        @case('accountant')
                                            <small class="text-muted">Can manage deposits, withdrawals, and view financial reports.</small>
                                            @break
                                        @case('editor')
                                            <small class="text-muted">Can manage content like videos, popups, and notifications.</small>
                                            @break
                                        @default
                                            <small class="text-muted">Custom role with specific permissions.</small>
                                    @endswitch
                                @endif
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                @isSuperAdmin
                                <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-sm btn-primary mb-2 w-100">
                                    <i class="fe fe-shield me-2"></i>Manage Sub-Admins
                                </a>
                                <a href="{{ route('admin.sub-admins.permissions') }}" class="btn btn-sm btn-info mb-2 w-100">
                                    <i class="fe fe-key me-2"></i>Permission Overview
                                </a>
                                @endisSuperAdmin
                                
                                @hasPermission('users.view')
                                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary mb-2 w-100">
                                    <i class="fe fe-users me-2"></i>Manage Users
                                </a>
                                @endhasPermission
                                
                                @hasPermission('support.view')
                                <a href="{{ route('admin.support.tickets') }}" class="btn btn-sm btn-outline-success mb-2 w-100">
                                    <i class="fe fe-headphones me-2"></i>Support Tickets
                                </a>
                                @endhasPermission
                                
                                @hasPermission('deposits.view')
                                <a href="{{ route('admin.deposits.pending') }}" class="btn btn-sm btn-outline-warning mb-2 w-100">
                                    <i class="fe fe-credit-card me-2"></i>Pending Deposits
                                </a>
                                @endhasPermission
                                
                                @hasPermission('withdrawals.view')
                                <a href="{{ route('admin.withdrawals.pending') }}" class="btn btn-sm btn-outline-danger mb-2 w-100">
                                    <i class="fe fe-arrow-up-circle me-2"></i>Pending Withdrawals
                                </a>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Role Comparison Table -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3">Admin Role Comparison</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Feature</th>
                                        <th>Super Admin</th>
                                        <th>Manager</th>
                                        <th>Moderator</th>
                                        <th>Support</th>
                                        <th>Accountant</th>
                                        <th>Editor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>User Management</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-warning">View/Edit/Ban</span></td>
                                        <td><span class="badge badge-info">View/Edit</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                    </tr>
                                    <tr>
                                        <td>Deposit Management</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-warning">Approve/Reject</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-warning">Full Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                    </tr>
                                    <tr>
                                        <td>Withdrawal Management</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-warning">Approve/Reject</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-warning">Full Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                    </tr>
                                    <tr>
                                        <td>Support Management</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-warning">Full Access</span></td>
                                        <td><span class="badge badge-warning">Reply/Close</span></td>
                                        <td><span class="badge badge-warning">Full Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                    </tr>
                                    <tr>
                                        <td>Content Management</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-warning">Full Access</span></td>
                                        <td><span class="badge badge-warning">Videos/Popups</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-warning">Full Access</span></td>
                                    </tr>
                                    <tr>
                                        <td>Settings & Configuration</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                    </tr>
                                    <tr>
                                        <td>Sub-Admin Management</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                        <td><span class="badge badge-secondary">No Access</span></td>
                                    </tr>
                                    <tr>
                                        <td>Reports & Analytics</td>
                                        <td><span class="badge badge-success">Full</span></td>
                                        <td><span class="badge badge-warning">Full Access</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                        <td><span class="badge badge-warning">Financial Reports</span></td>
                                        <td><span class="badge badge-info">View Only</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
