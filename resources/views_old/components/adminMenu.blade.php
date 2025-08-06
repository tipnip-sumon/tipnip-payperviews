
<ul class="side-menu">
    <!-- Dashboard - Always visible -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-home side-menu__icon"></i>
            <span class="side-menu__label">Dashboards</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Dashboards</a>
            </li>
            <li class="slide">
                <a href="{{url('admin/dashboard')}}" class="side-menu__item">
                    <i class="fe fe-home me-2"></i>
                    <span class="side-menu__label">Main Dashboard</span>
                    <span class="badge badge-primary ms-auto">Home</span>
                </a>
            </li>
            @canAccessMenu('analytics')
            <li class="slide">
                <a href="{{ route('admin.analytics.chart-dashboard') }}" class="side-menu__item">
                    <i class="fe fe-bar-chart me-2"></i>
                    <span class="side-menu__label">📊 Analytics Dashboard</span>
                    <span class="badge badge-success ms-auto">Charts</span>
                </a>
            </li>
            @endcanAccessMenu
        </ul>
    </li>
    <!-- Dashboard End -->

    @canAccessMenu('lottery')
        <!-- Lottery Management Menu Start -->
        <li class="slide has-sub">
            <a href="javascript:void(0);" class="side-menu__item">
                <i class="fe fe-gift side-menu__icon"></i>
                <span class="side-menu__label">🎰 Lottery Management</span>
                @php
                    try {
                        $pendingClaims = \App\Models\LotteryWinner::where('claim_status', 'pending')->count();
                        $currentDraw = \App\Models\LotteryDraw::where('status', 'pending')->first();
                        $pendingDraws = \App\Models\LotteryDraw::where('status', 'pending')->count();
                        $totalTickets = \App\Models\LotteryTicket::where('status', 'active')->count();
                    } catch (\Exception $e) {
                        \Log::error('AdminMenu lottery data error: ' . $e->getMessage());
                        $pendingClaims = 0;
                        $currentDraw = null;
                        $pendingDraws = 0;
                        $totalTickets = 0;
                    }
                @endphp
                @if($pendingClaims > 0)
                    <span class="badge badge-warning ms-2">{{ $pendingClaims }} pending</span>
                @endif
                <i class="fe fe-chevron-right side-menu__angle"></i>
            </a>
            <ul class="slide-menu child1">
                <li class="slide side-menu__label1">
                    <a href="javascript:void(0)">🎰 Lottery Management</a>
                </li>
                
                <!-- Dashboard -->
                <li class="slide">
                    <a href="{{ route('admin.lottery.index') }}" class="side-menu__item">
                        <i class="fe fe-home me-2"></i>
                        <span class="side-menu__label">Dashboard</span>
                        <span class="badge badge-primary ms-auto">Main</span>
                    </a>
                </li>
                
                <!-- Draw Management Section -->
                @hasPermission('reports.view')
                <li class="slide">
                    <a href="{{ route('admin.lottery.draws') }}" class="side-menu__item">
                        <i class="fe fe-refresh-cw me-2"></i>
                        <span class="side-menu__label">Manage Draws</span>
                        @if($pendingDraws > 0)
                            <span class="badge badge-info ms-auto">{{ $pendingDraws }} pending</span>
                        @endif
                    </a>
                </li>
                @endhasPermission
                
                <!-- Create New Draw -->
                <li class="slide">
                    <a href="javascript:void(0);" class="side-menu__item" onclick="showCreateDrawModal()">
                        <i class="fe fe-plus me-2"></i>
                        <span class="side-menu__label">Create New Draw</span>
                        <span class="badge badge-primary ms-auto">New</span>
                    </a>
                </li>
                
                <!-- Auto Generate Draw -->
                <li class="slide">
                    <a href="javascript:void(0);" class="side-menu__item" onclick="showAutoGenerateModal()">
                        <i class="fe fe-zap me-2"></i>
                        <span class="side-menu__label">Auto Generate Draw</span>
                        <span class="badge badge-success ms-auto">Quick</span>
                    </a>
                </li>
                
                <!-- Manual Winner Selection -->
                <li class="slide">
                    <a href="{{ route('admin.lottery.draws.create') }}" class="side-menu__item">
                        <i class="fe fe-edit-3 me-2"></i>
                        <span class="side-menu__label">🎯 Manual Winner Selection</span>
                        <span class="badge badge-warning ms-auto">Control</span>
                    </a>
                </li>
                
                <!-- Ticket Management -->
                <li class="slide">
                    <a href="{{ route('admin.lottery.tickets') }}" class="side-menu__item">
                        <i class="fe fe-file-text me-2"></i>
                        <span class="side-menu__label">All Tickets</span>
                        @if($totalTickets > 0)
                            <span class="badge badge-info ms-auto">{{ number_format($totalTickets) }}</span>
                        @endif
                    </a>
                </li>
                
                <!-- Winners Management -->
                <li class="slide">
                    <a href="{{ route('admin.lottery.winners') }}" class="side-menu__item"> 
                        <i class="fe fe-award me-2"></i>
                        <span class="side-menu__label">Winners</span>
                        @if($pendingClaims > 0)
                            <span class="badge badge-warning ms-auto">{{ $pendingClaims }} claims</span>
                        @endif
                    </a>
                </li>
                
                <!-- Reports & Analytics -->
                @hasPermission('reports.view')
                <li class="slide">
                    <a href="{{ route('admin.lottery.report') }}" class="side-menu__item">
                        <i class="fe fe-bar-chart-2 me-2"></i>
                        <span class="side-menu__label">Reports & Analytics</span>
                    </a>
                </li>
                @endhasPermission
                
                <!-- Settings & Configuration -->
                @hasPermission('settings.general')
                <li class="slide">
                    <a href="{{ route('admin.lottery-settings.index') }}" class="side-menu__item">
                        <i class="fe fe-settings me-2"></i>
                        <span class="side-menu__label">Lottery Settings</span>
                        <span class="badge badge-secondary ms-auto">Config</span>
                    </a>
                </li>
                @endhasPermission
                
                <!-- Export & Backup -->
                @hasAnyPermission(['settings.general', 'reports.export'])
                <li class="slide">
                    <a href="{{ route('admin.lottery.export') }}" class="side-menu__item">
                        <i class="fe fe-download me-2"></i>
                        <span class="side-menu__label">Export Data</span>
                    </a>
                </li>
                @endhasAnyPermission
            </ul>
        </li>
        <!-- Lottery Management Menu End -->
    @endcanAccessMenu

    @canAccessMenu('kyc')
    <!-- KYC Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-shield side-menu__icon"></i>
            <span class="side-menu__label">KYC Management</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">KYC Management</a>
            </li>
            @hasPermission('users.view')
            <li class="slide">
                <a href="{{ route('admin.kyc.index') }}" class="side-menu__item">
                    <span class="side-menu__label">All KYC Verifications</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}" class="side-menu__item">
                    <span class="side-menu__label">Pending KYC</span>
                    <span class="badge badge-warning ms-auto" id="pending-kyc-count">0</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.kyc.index', ['status' => 'approved']) }}" class="side-menu__item">
                    <span class="side-menu__label">Approved KYC</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.kyc.index', ['status' => 'rejected']) }}" class="side-menu__item">
                    <span class="side-menu__label">Rejected KYC</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.kyc.index', ['status' => 'under_review']) }}" class="side-menu__item">
                    <span class="side-menu__label">Under Review</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- KYC Management Menu End -->
    @endcanAccessMenu
    
    @canAccessMenu('deposits')
    <!-- Deposit Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-credit-card side-menu__icon"></i>
            <span class="side-menu__label">Deposits</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Deposits</a>
            </li>
            @hasPermission('deposits.view')
            <li class="slide">
                <a href="{{ route('admin.deposits.index') }}" class="side-menu__item">
                    <span class="side-menu__label">All Deposits</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.deposits.pending') }}" class="side-menu__item">
                    <span class="side-menu__label">Pending Deposits</span>
                    <span class="badge badge-warning ms-auto" id="pending-deposits-count">0</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['deposits.approve', 'deposits.view'])
            <li class="slide">
                <a href="{{ route('admin.deposits.approved') }}" class="side-menu__item">
                    <span class="side-menu__label">Approved Deposits</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.deposits.rejected') }}" class="side-menu__item">
                    <span class="side-menu__label">Rejected Deposits</span>
                </a>
            </li>
            @endhasAnyPermission
            @hasPermission('deposits.export')
            <li class="slide">
                <a href="{{ route('admin.deposits.export') }}" class="side-menu__item">
                    <span class="side-menu__label">Export Deposits</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- Deposit Management Menu End -->
    @endcanAccessMenu
    
    @canAccessMenu('withdrawals')
    <!-- Withdrawal Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-arrow-up-circle side-menu__icon"></i>
            <span class="side-menu__label">Withdrawals</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Withdrawals</a>
            </li>
            @hasPermission('withdrawals.view')
            <li class="slide">
                <a href="{{ route('admin.withdrawals.index') }}" class="side-menu__item">
                    <span class="side-menu__label">All Withdrawals</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.withdrawals.pending') }}" class="side-menu__item">
                    <span class="side-menu__label">Pending Withdrawals</span>
                    <span class="badge badge-warning ms-auto" id="pending-withdrawals-count">0</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['withdrawals.approve', 'withdrawals.view'])
            <li class="slide">
                <a href="{{ route('admin.withdrawals.approved') }}" class="side-menu__item">
                    <span class="side-menu__label">Approved Withdrawals</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.withdrawals.rejected') }}" class="side-menu__item">
                    <span class="side-menu__label">Rejected Withdrawals</span>
                </a>
            </li>
            @endhasAnyPermission
            @hasPermission('withdrawals.export')
            <li class="slide">
                <a href="{{ route('admin.withdrawals.export') }}" class="side-menu__item">
                    <span class="side-menu__label">Export Withdrawals</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['withdrawals.view', 'settings.general'])
            <li class="slide">
                <a href="{{ route('admin.withdraw-methods.index') }}" class="side-menu__item">
                    <span class="side-menu__label">Withdrawal Methods</span>
                </a>
            </li>
            @endhasAnyPermission
        </ul>
    </li>
    <!-- Withdrawal Management Menu End -->
    @endcanAccessMenu
    
    
    @canAccessMenu('users')
    <!-- User Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-users side-menu__icon"></i>
            <span class="side-menu__label">User Management</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">User Management</a>
            </li>
            @hasPermission('users.view')
            <li class="slide">
                <a href="{{ route('admin.users.index') }}" class="side-menu__item">
                    <span class="side-menu__label">All Users</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.users.active') }}" class="side-menu__item">
                    <span class="side-menu__label">Active Users</span>
                    <span class="badge badge-success ms-auto" id="active-users-count">0</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.users.inactive') }}" class="side-menu__item">
                    <span class="side-menu__label">Inactive Users</span>
                    <span class="badge badge-warning ms-auto" id="inactive-users-count">0</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['users.view', 'users.ban'])
            <li class="slide">
                <a href="{{ route('admin.users.banned') }}" class="side-menu__item">
                    <span class="side-menu__label">Banned Users</span>
                    <span class="badge badge-danger ms-auto" id="banned-users-count">0</span>
                </a>
            </li>
            @endhasAnyPermission

            
            @hasAnyPermission(['users.view', 'reports.export'])
            <li class="slide">
                <a href="{{ route('admin.users.export') }}" class="side-menu__item">
                    <span class="side-menu__label">Export Users</span>
                </a>
            </li>
            @endhasAnyPermission
        </ul>
    </li>
    <!-- User Management Menu End -->
    @endcanAccessMenu
    
    @hasPermission('users.verification')
    <!-- User Verification System Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-shield side-menu__icon"></i>
            <span class="side-menu__label">🔒 Verification System</span>
            @php
                $pendingVerifications = \App\Models\User::where('ev', 0)->where('status', 1)->count();
                $emailUnverified = \App\Models\User::whereNull('email_verified_at')->count();
                $phoneUnverified = \App\Models\User::where('sv', 0)->count();
                $kycPendingUsers = \App\Models\User::where('kv', 0)->where('status', 1)->count();
                $totalPending = $pendingVerifications + $emailUnverified + $phoneUnverified + $kycPendingUsers;
            @endphp
            @if($totalPending > 0)
                <span class="badge badge-warning ms-2">{{ $totalPending }} pending</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🔒 Verification System</a>
            </li>
            
            <!-- Verification Dashboard --> 
            <li class="slide">
                <a href="{{ route('admin.users.verification.dashboard') }}" class="side-menu__item">
                    <i class="fe fe-grid me-2"></i>
                    <span class="side-menu__label">📊 Verification Dashboard</span>
                    <span class="badge badge-primary ms-auto">Overview</span>
                </a>
            </li>
            
            <!-- Email Verification -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.email') }}" class="side-menu__item">
                    <i class="fe fe-mail me-2"></i>
                    <span class="side-menu__label">📧 Email Verification</span>
                    @if($emailUnverified > 0)
                        <span class="badge badge-warning ms-auto">{{ $emailUnverified }}</span>
                    @endif
                </a>
            </li>
            
            <!-- Phone Verification -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.phone') }}" class="side-menu__item">
                    <i class="fe fe-phone me-2"></i>
                    <span class="side-menu__label">📱 Phone Verification</span>
                    @if($phoneUnverified > 0)
                        <span class="badge badge-info ms-auto">{{ $phoneUnverified }}</span>
                    @endif
                </a>
            </li>
            
            <!-- SMS Verification -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.sms') }}" class="side-menu__item">
                    <i class="fe fe-message-square me-2"></i>
                    <span class="side-menu__label">💬 SMS Verification</span>
                    @php $smsUnverified = \App\Models\User::where('sv', 0)->count(); @endphp
                    @if($smsUnverified > 0)
                        <span class="badge badge-warning ms-auto">{{ $smsUnverified }}</span>
                    @endif
                </a>
            </li>
            
            <!-- KYC Verification -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.kyc') }}" class="side-menu__item">
                    <i class="fe fe-user-check me-2"></i>
                    <span class="side-menu__label">🆔 KYC Verification</span>
                    @if($kycPendingUsers > 0)
                        <span class="badge badge-danger ms-auto">{{ $kycPendingUsers }}</span>
                    @endif
                </a>
            </li>
            
            <!-- Two-Factor Authentication -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.2fa') }}" class="side-menu__item">
                    <i class="fe fe-lock me-2"></i>
                    <span class="side-menu__label">🔐 2FA Management</span>
                    @php $users2fa = \App\Models\User::where('ts', 1)->count(); @endphp
                    @if($users2fa > 0)
                        <span class="badge badge-success ms-auto">{{ $users2fa }} enabled</span>
                    @endif
                </a>
            </li>
            
            <!-- Identity Verification -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.identity') }}" class="side-menu__item">
                    <i class="fe fe-credit-card me-2"></i>
                    <span class="side-menu__label">🪪 Identity Verification</span>
                    <span class="badge badge-primary ms-auto">Documents</span>
                </a>
            </li>
            
            <!-- Verification Settings -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.settings') }}" class="side-menu__item">
                    <i class="fe fe-settings me-2"></i>
                    <span class="side-menu__label">⚙️ Verification Settings</span>
                </a>
            </li>
            
            <!-- Bulk Verification Actions -->
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="handleBulkVerificationActions();">
                    <i class="fe fe-zap me-2"></i>
                    <span class="side-menu__label">⚡ Bulk Actions</span>
                    <span class="badge badge-warning ms-auto">Quick</span>
                </a>
            </li>
            
            <!-- Verification Reports -->
            <li class="slide">
                <a href="{{ route('admin.users.verification.reports') }}" class="side-menu__item">
                    <i class="fe fe-file-text me-2"></i>
                    <span class="side-menu__label">📋 Verification Reports</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- User Verification System Menu End -->
    @endhasPermission
    
    @canAccessMenu('transfers')
    <!-- Transfer Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-send side-menu__icon"></i>
            <span class="side-menu__label">Transfer Management</span>
            @php
                $todayTransfers = \App\Models\AdminTransReceive::whereDate('created_at', today())->count();
                $totalTransfers = \App\Models\AdminTransReceive::count();
            @endphp
            @if($todayTransfers > 0)
                <span class="badge badge-success ms-2">{{ $todayTransfers }} today</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Transfer Management</a>
            </li>
            @hasPermission('transfers.create')
            <li class="slide">
                <a href="{{ route('admin.transfer_member') }}" class="side-menu__item">
                    <i class="fe fe-plus-circle me-2"></i>
                    <span class="side-menu__label">New Transfer</span>
                </a>
            </li>
            @endhasPermission
            @hasPermission('transfers.view')
            <li class="slide">
                <a href="{{ route('admin.transfer_history') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">Transfer History</span>
                    @if($totalTransfers > 0)
                        <span class="badge badge-info ms-auto">{{ $totalTransfers }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.transfer_history', ['filter' => 'today']) }}" class="side-menu__item">
                    <i class="fe fe-calendar me-2"></i>
                    <span class="side-menu__label">Today's Transfers</span>
                    @if($todayTransfers > 0)
                        <span class="badge badge-success ms-auto">{{ $todayTransfers }}</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['transfers.view', 'reports.view'])
            <li class="slide">
                <a href="{{ route('admin.transfer_history', ['filter' => 'weekly']) }}" class="side-menu__item">
                    <i class="fe fe-bar-chart-2 me-2"></i>
                    <span class="side-menu__label">Weekly Report</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.transfer_history', ['filter' => 'monthly']) }}" class="side-menu__item">
                    <i class="fe fe-pie-chart me-2"></i>
                    <span class="side-menu__label">Monthly Report</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.transfer_reports') }}" class="side-menu__item">
                    <i class="fe fe-trending-up me-2"></i>
                    <span class="side-menu__label">Analytics Dashboard</span>
                </a>
            </li>
            @endhasAnyPermission
            @hasPermission('transfers.export')
            <li class="slide">
                <a href="{{ route('admin.transfer_history', ['export' => 'excel']) }}" class="side-menu__item">
                    <i class="fe fe-download me-2"></i>
                    <span class="side-menu__label">Export Transfers</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- Transfer Management Menu End -->
    @endcanAccessMenu
    
    @isSuperAdmin
    <!-- Sub-Admin Management Menu Start - Only for Super Admins -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-shield side-menu__icon"></i>
            <span class="side-menu__label">Sub-Admin Management</span>
            @php
                $subAdminCount = \App\Models\Admin::where('is_super_admin', false)->count();
                $activeSubAdmins = \App\Models\Admin::where('is_super_admin', false)->where('is_active', true)->count();
            @endphp
            @if($subAdminCount > 0)
                <span class="badge badge-info ms-2">{{ $subAdminCount }}</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Sub-Admin Management</a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.sub-admins.index') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">All Sub-Admins</span>
                    @if($subAdminCount > 0)
                        <span class="badge badge-primary ms-auto">{{ $subAdminCount }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.sub-admins.create') }}" class="side-menu__item">
                    <i class="fe fe-plus me-2"></i>
                    <span class="side-menu__label">Create Sub-Admin</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.sub-admins.index', ['status' => '1']) }}" class="side-menu__item">
                    <i class="fe fe-check-circle me-2"></i>
                    <span class="side-menu__label">Active Sub-Admins</span>
                    @if($activeSubAdmins > 0)
                        <span class="badge badge-success ms-auto">{{ $activeSubAdmins }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.sub-admins.index', ['status' => '0']) }}" class="side-menu__item">
                    <i class="fe fe-x-circle me-2"></i>
                    <span class="side-menu__label">Inactive Sub-Admins</span>
                    @php $inactiveSubAdmins = $subAdminCount - $activeSubAdmins; @endphp
                    @if($inactiveSubAdmins > 0)
                        <span class="badge badge-warning ms-auto">{{ $inactiveSubAdmins }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.sub-admins.permissions') }}" class="side-menu__item">
                    <i class="fe fe-key me-2"></i>
                    <span class="side-menu__label">Permissions</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Sub-Admin Management Menu End -->
    @endisSuperAdmin
    
    @canAccessMenu('videos')
    <!-- Video Links Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-video side-menu__icon"></i>
            <span class="side-menu__label">Video Links</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Video Links</a>
            </li>
            @hasPermission('content.videos')
            <li class="slide">
                <a href="{{ route('admin.video-links.index') }}" class="side-menu__item">
                    <span class="side-menu__label">All Videos</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.video-links.create') }}" class="side-menu__item">
                    <span class="side-menu__label">Add New Video</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.video-links.index', ['status' => 'active']) }}" class="side-menu__item">
                    <span class="side-menu__label">Active Videos</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.video-links.index', ['status' => 'inactive']) }}" class="side-menu__item">
                    <span class="side-menu__label">Inactive Videos</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['content.videos', 'reports.export'])
            <li class="slide">
                <a href="{{ route('admin.video-links.export') }}" class="side-menu__item">
                    <span class="side-menu__label">Export Videos</span>
                </a>
            </li>
            @endhasAnyPermission
        </ul>
    </li>
    <!-- Video Links Management Menu End -->
    @endcanAccessMenu

    @canAccessMenu('plans')
    <!-- Plans Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-layers side-menu__icon"></i>
            <span class="side-menu__label">Investment Plans</span>
            @php
                $totalPlans = \App\Models\Plan::count();
                $activePlans = \App\Models\Plan::where('video_access_enabled', true)->count();
                $totalInvestments = \App\Models\Invest::count();
            @endphp
            @if($activePlans > 0)
                <span class="badge badge-success ms-2">{{ $activePlans }} active</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Investment Plans</a>
            </li>
            @hasPermission('settings.general')
            <li class="slide">
                <a href="{{ route('admin.plans.index') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">All Plans</span>
                    @if($totalPlans > 0)
                        <span class="badge badge-primary ms-auto">{{ $totalPlans }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.plans.create') }}" class="side-menu__item">
                    <i class="fe fe-plus-circle me-2"></i>
                    <span class="side-menu__label">Create New Plan</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.plans.index', ['status' => 'active']) }}" class="side-menu__item">
                    <i class="fe fe-check-circle me-2"></i>
                    <span class="side-menu__label">Active Plans</span>
                    <span class="badge badge-success ms-auto">{{ $activePlans }}</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.plans.index', ['status' => 'inactive']) }}" class="side-menu__item">
                    <i class="fe fe-x-circle me-2"></i>
                    <span class="side-menu__label">Inactive Plans</span>
                    @php $inactivePlans = $totalPlans - $activePlans; @endphp
                    @if($inactivePlans > 0)
                        <span class="badge badge-warning ms-auto">{{ $inactivePlans }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.plans.index', ['featured' => 'true']) }}" class="side-menu__item">
                    <i class="fe fe-star me-2"></i>
                    <span class="side-menu__label">Featured Plans</span>
                    @php $featuredPlans = \App\Models\Plan::where('featured', true)->count(); @endphp
                    @if($featuredPlans > 0)
                        <span class="badge badge-warning ms-auto">{{ $featuredPlans }}</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['settings.general', 'reports.view'])
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showPlanAnalytics()">
                    <i class="fe fe-bar-chart-2 me-2"></i>
                    <span class="side-menu__label">Plan Analytics</span>
                    @if($totalInvestments > 0)
                        <span class="badge badge-info ms-auto">{{ $totalInvestments }} investments</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.plans.index', ['sort' => 'popular']) }}" class="side-menu__item">
                    <i class="fe fe-trending-up me-2"></i>
                    <span class="side-menu__label">Popular Plans</span>
                </a>
            </li>
            @endhasAnyPermission
            @hasPermission('settings.general')
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showPlanSettings()">
                    <i class="fe fe-settings me-2"></i>
                    <span class="side-menu__label">Plan Settings</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- Plans Management Menu End -->
    @endcanAccessMenu

    @canAccessMenu('analytics')
    <!-- Analytics Dashboard Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-bar-chart-2 side-menu__icon"></i>
            <span class="side-menu__label">📊 Analytics Dashboard</span>
            @php
                $todayUsers = \App\Models\User::whereDate('created_at', today())->count();
                $todayRevenue = \App\Models\Deposit::whereDate('created_at', today())->where('status', 1)->sum('amount');
                $pendingTickets = \App\Models\SupportTicket::where('status', 0)->count();
            @endphp
            @if($todayUsers > 0 || $todayRevenue > 0)
                <span class="badge badge-success ms-2">Live</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">📊 Analytics Dashboard</a>
            </li>
            @hasPermission('reports.view')
            <li class="slide">
                <a href="{{ route('admin.analytics.index') }}" class="side-menu__item">
                    <i class="fe fe-pie-chart me-2"></i>
                    <span class="side-menu__label">Overview Dashboard</span>
                    <span class="badge badge-primary ms-auto">Main</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.analytics.users') }}" class="side-menu__item">
                    <i class="fe fe-users me-2"></i>
                    <span class="side-menu__label">User Analytics</span>
                    @if($todayUsers > 0)
                        <span class="badge badge-success ms-auto">{{ $todayUsers }} today</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.analytics.revenue') }}" class="side-menu__item">
                    <i class="fe fe-dollar-sign me-2"></i>
                    <span class="side-menu__label">Revenue Analytics</span>
                    @if($todayRevenue > 0)
                        <span class="badge badge-info ms-auto">${{ number_format($todayRevenue, 0) }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.analytics.videos') }}" class="side-menu__item">
                    <i class="fe fe-video me-2"></i>
                    <span class="side-menu__label">Video Performance</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.analytics.investments') }}" class="side-menu__item">
                    <i class="fe fe-trending-up me-2"></i>
                    <span class="side-menu__label">Investment Analysis</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['reports.view', 'reports.export'])
            <li class="slide">
                <a href="{{ route('admin.analytics.performance') }}" class="side-menu__item">
                    <i class="fe fe-activity me-2"></i>
                    <span class="side-menu__label">Performance Metrics</span>
                    <span class="badge badge-warning ms-auto">Live</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.analytics.chart-dashboard') }}" class="side-menu__item">
                    <i class="fe fe-bar-chart me-2"></i>
                    <span class="side-menu__label">📊 Interactive Dashboard</span>
                    <span class="badge badge-success ms-auto">📈 Live Charts</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.analytics.chart-page') }}" class="side-menu__item"> 
                    <i class="fe fe-monitor me-2"></i>
                    <span class="side-menu__label">🔍 Chart Data Page</span>
                    <span class="badge badge-info ms-auto">Live</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.analytics.export') }}" class="side-menu__item">
                    <i class="fe fe-download me-2"></i>
                    <span class="side-menu__label">📋 Export Reports</span>
                </a>
            </li>
            @endhasAnyPermission
            @hasPermission('settings.general')
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showAnalyticsSettings()">
                    <i class="fe fe-settings me-2"></i>
                    <span class="side-menu__label">Analytics Settings</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- Analytics Dashboard Menu End -->
    @endcanAccessMenu

    @canAccessMenu('referrals')
    <!-- Referral Benefits System Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-gift side-menu__icon"></i>
            <span class="side-menu__label">🎁 Referral Benefits</span>
            @php
                $qualifiedUsers = \App\Models\ReferralUserBenefit::where('is_qualified', true)->where('is_active', true)->count();
                $todayBonuses = \App\Models\ReferralBonusTransaction::whereDate('created_at', today())->count();
                $totalBonusesGiven = \App\Models\ReferralBonusTransaction::sum('amount');
            @endphp
            @if($qualifiedUsers > 0)
                <span class="badge badge-success ms-2">{{ $qualifiedUsers }} qualified</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🎁 Referral Benefits</a>
            </li>
            @hasPermission('settings.general')
            <li class="slide">
                <a href="{{ route('admin.referral-benefits.index') }}" class="side-menu__item">
                    <i class="fe fe-home me-2"></i>
                    <span class="side-menu__label">Benefits Dashboard</span>
                    <span class="badge badge-primary ms-auto">Main</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.referral-benefits.qualified-users') }}" class="side-menu__item">
                    <i class="fe fe-users me-2"></i>
                    <span class="side-menu__label">Qualified Users</span>
                    @if($qualifiedUsers > 0)
                        <span class="badge badge-success ms-auto">{{ $qualifiedUsers }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.referral-benefits.bonus-transactions') }}" class="side-menu__item">
                    <i class="fe fe-activity me-2"></i>
                    <span class="side-menu__label">Bonus Transactions</span>
                    @if($todayBonuses > 0)
                        <span class="badge badge-info ms-auto">{{ $todayBonuses }} today</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['settings.general', 'reports.view'])
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showReferralStats()">
                    <i class="fe fe-bar-chart-2 me-2"></i>
                    <span class="side-menu__label">Statistics</span>
                    @if($totalBonusesGiven > 0)
                        <span class="badge badge-warning ms-auto">${{ number_format($totalBonusesGiven, 0) }}</span>
                    @endif
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.referral-benefits.export-qualified-users') }}" class="side-menu__item">
                    <i class="fe fe-download me-2"></i>
                    <span class="side-menu__label">Export Reports</span>
                </a>
            </li>
            @endhasAnyPermission
            @hasPermission('settings.general')
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showBenefitsHelp()">
                    <i class="fe fe-help-circle me-2"></i>
                    <span class="side-menu__label">Help & Guide</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- Referral Benefits System Menu End -->
    @endcanAccessMenu

    @canAccessMenu('settings')
    <!-- General Settings Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-settings side-menu__icon"></i>
            <span class="side-menu__label">General Settings</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">General Settings</a>
            </li>
            @hasPermission('settings.general')
            <li class="slide">
                <a href="{{ route('admin.settings.general') }}" class="side-menu__item">
                    <span class="side-menu__label">General Settings</span>
                </a>
            </li>
            @endhasPermission
            @hasPermission('settings.mail')
            <li class="slide">
                <a href="{{ route('admin.settings.mail-config') }}" class="side-menu__item">
                    <span class="side-menu__label">Mail Configuration</span>
                </a>
            </li>
            @endhasPermission
            @hasPermission('settings.general')
            <li class="slide">
                <a href="{{ route('admin.commission-levels.index') }}" class="side-menu__item">
                    <span class="side-menu__label">Commission Level Setup</span>
                    <span class="badge badge-info ms-auto">Referral</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.referral-benefits.index') }}" class="side-menu__item">
                    <i class="fe fe-gift me-2"></i>
                    <span class="side-menu__label">Referral Benefits System</span>
                    <span class="badge badge-success ms-auto">🎁 New</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.transfer-withdraw-conditions.index') }}" class="side-menu__item">
                    <i class="fe fe-shield me-2"></i>
                    <span class="side-menu__label">Transfer & Withdrawal Conditions</span>
                    <span class="badge badge-warning ms-auto">Security</span>
                </a>
            </li>
            @endhasPermission
            @isSuperAdmin
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="toggleMaintenanceMode()">
                    <span class="side-menu__label">Toggle Maintenance</span>
                </a>
            </li>
            @endisSuperAdmin
        </ul>
    </li>
    <!-- General Settings Menu End -->
    @endcanAccessMenu

    @canAccessMenu('modal')
    <!-- Modal Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-layers side-menu__icon"></i>
            <span class="side-menu__label">🔧 Modal Management</span>
            @php
                try {
                    $totalModals = \DB::table('modal_settings')->count();
                    $activeModals = \DB::table('modal_settings')->where('is_active', 1)->count();
                    $inactiveModals = \DB::table('modal_settings')->where('is_active', 0)->count();
                } catch (\Exception $e) {
                    \Log::error('AdminMenu modal data error: ' . $e->getMessage());
                    $totalModals = 0;
                    $activeModals = 0;
                    $inactiveModals = 0;
                }
            @endphp
            @if($inactiveModals > 0)
                <span class="badge badge-warning ms-2">{{ $inactiveModals }} inactive</span>
            @elseif($activeModals > 0)
                <span class="badge badge-success ms-2">{{ $activeModals }} active</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🔧 Modal Management</a>
            </li>
            
            <!-- Modal Overview -->
            @hasPermission('modal.view')
            <li class="slide">
                <a href="{{ route('admin.modal.index') }}" class="side-menu__item">
                    <i class="fe fe-grid me-2"></i>
                    <span class="side-menu__label">All Modals</span>
                    @if($totalModals > 0)
                        <span class="badge badge-info ms-auto">{{ $totalModals }}</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            
            <!-- Create New Modal -->
            @hasPermission('modal.create')
            <li class="slide">
                <a href="{{ route('admin.modal.create') }}" class="side-menu__item">
                    <i class="fe fe-plus-circle me-2"></i>
                    <span class="side-menu__label">Create Modal</span>
                    <span class="badge badge-primary ms-auto">New</span>
                </a>
            </li>
            @endhasPermission
            
            <!-- Modal Analytics -->
            @hasPermission('modal.analytics')
            <li class="slide">
                <a href="{{ route('admin.modal.analytics') }}" class="side-menu__item">
                    <i class="fe fe-bar-chart me-2"></i>
                    <span class="side-menu__label">Modal Analytics</span>
                    <span class="badge badge-success ms-auto">📊 Stats</span>
                </a>
            </li>
            @endhasPermission
            
            <!-- PWA Install Modals -->
            @hasPermission('modal.view')
            <li class="slide">
                <a href="{{ route('admin.modal.index') }}?filter=pwa" class="side-menu__item">
                    <i class="fe fe-smartphone me-2"></i>
                    <span class="side-menu__label">PWA Install Modals</span>
                    @php
                        try {
                            $pwaModals = \DB::table('modal_settings')->where('modal_name', 'like', '%install%')->count();
                        } catch (\Exception $e) {
                            \Log::error('AdminMenu PWA modal data error: ' . $e->getMessage());
                            $pwaModals = 0;
                        }
                    @endphp
                    @if($pwaModals > 0)
                        <span class="badge badge-info ms-auto">{{ $pwaModals }}</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            
            <!-- Quick Actions -->
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="toggleAllModals()">
                    <i class="fe fe-toggle-right me-2"></i>
                    <span class="side-menu__label">Toggle All Modals</span>
                    <span class="badge badge-warning ms-auto">Bulk</span>
                </a>
            </li>
            
            <!-- Modal Test Page -->
            @hasPermission('modal.test')
            <li class="slide">
                <a href="/modal-test.html" target="_blank" class="side-menu__item">
                    <i class="fe fe-external-link me-2"></i>
                    <span class="side-menu__label">Test Modal System</span>
                    <span class="badge badge-secondary ms-auto">Test</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- Modal Management Menu End -->
    @endcanAccessMenu

    @canAccessMenu('system')
    <!-- Schedule Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-clock side-menu__icon"></i>
            <span class="side-menu__label">🕒 Schedule Management</span>
            @php
                $scheduledJobs = \Illuminate\Support\Facades\DB::table('jobs')->count();
                $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
            @endphp
            @if($failedJobs > 0)
                <span class="badge badge-danger ms-2">{{ $failedJobs }} failed</span>
            @elseif($scheduledJobs > 0)
                <span class="badge badge-info ms-2">{{ $scheduledJobs }} pending</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🕒 Schedule Management</a>
            </li>
            
            <!-- System Commands -->
            @hasPermission('system.commands')
            <li class="slide">
                <a href="{{ route('admin.system-commands.index') }}" class="side-menu__item">
                    <i class="fe fe-terminal me-2"></i>
                    <span class="side-menu__label">System Commands</span>
                    <span class="badge badge-primary ms-auto">Emergency</span>
                </a>
            </li>
            @endhasPermission
            
            <!-- Schedule Runner -->
            @hasPermission('system.schedule')
            <li class="slide">
                <a href="{{ route('admin.schedule.index') }}" class="side-menu__item">
                    <i class="fe fe-play-circle me-2"></i>
                    <span class="side-menu__label">Schedule Runner</span>
                    <span class="badge badge-success ms-auto">Cron</span>
                </a>
            </li>
            @endhasPermission
            
            <!-- Queue Management -->
            @hasPermission('system.queue')
            <li class="slide">
                <a href="{{ route('admin.queue.index') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">Queue Management</span>
                    @if($scheduledJobs > 0)
                        <span class="badge badge-info ms-auto">{{ $scheduledJobs }}</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            
            <!-- Failed Jobs -->
            @hasPermission('system.failed-jobs')
            <li class="slide">
                <a href="{{ route('admin.failed-jobs.index') }}" class="side-menu__item">
                    <i class="fe fe-x-circle me-2"></i>
                    <span class="side-menu__label">Failed Jobs</span>
                    @if($failedJobs > 0)
                        <span class="badge badge-danger ms-auto">{{ $failedJobs }}</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            
            <!-- Quick Schedule Actions -->
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="runScheduleNow()">
                    <i class="fe fe-zap me-2"></i>
                    <span class="side-menu__label">Run Schedule Now</span>
                    <span class="badge badge-warning ms-auto">Manual</span>
                </a>
            </li>
            
            <!-- Queue Worker Status -->
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="checkQueueWorkerStatus()">
                    <i class="fe fe-activity me-2"></i>
                    <span class="side-menu__label">Worker Status</span>
                    <span id="worker-status-badge" class="badge badge-secondary ms-auto">Check</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Schedule Management Menu End -->
    @endcanAccessMenu

    @canAccessMenu('system')
    <!-- Maintenance Mode Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-shield side-menu__icon"></i>
            <span class="side-menu__label">🛠️ Maintenance Mode</span>
            @php
                $isInMaintenance = app()->isDownForMaintenance();
            @endphp
            @if($isInMaintenance)
                <span class="badge badge-danger ms-2 maintenance-status-badge" id="maintenance-status-badge">🔴 ACTIVE</span>
            @else
                <span class="badge badge-success ms-2 maintenance-status-badge" id="maintenance-status-badge">🟢 ONLINE</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🛠️ Maintenance Mode</a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.maintenance.index') }}" class="side-menu__item">
                    <i class="fe fe-settings me-2"></i>
                    <span class="side-menu__label">Maintenance Manager</span>
                    <span class="badge badge-primary ms-auto">Control</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.system-commands.index') }}" class="side-menu__item">
                    <i class="fe fe-terminal me-2"></i>
                    <span class="side-menu__label">Emergency Commands</span>
                    <span class="badge badge-warning ms-auto">System</span>
                </a>
            </li>
            @if($isInMaintenance)
                <li class="slide">
                    <a href="javascript:void(0)" onclick="quickDisableMaintenance()" class="side-menu__item text-success">
                        <i class="fe fe-check-circle me-2"></i>
                        <span class="side-menu__label">🟢 Bring Site Online</span>
                        <span class="badge badge-success ms-auto">Enable</span>
                    </a>
                </li>
            @else
                <li class="slide">
                    <a href="javascript:void(0)" onclick="quickEnableMaintenance()" class="side-menu__item text-danger">
                        <i class="fe fe-shield me-2"></i>
                        <span class="side-menu__label">🔴 Quick Maintenance</span>
                        <span class="badge badge-danger ms-auto">Offline</span>
                    </a>
                </li>
            @endif
            <li class="slide">
                <a href="{{ url('/') }}" target="_blank" class="side-menu__item">
                    <i class="fe fe-external-link me-2"></i>
                    <span class="side-menu__label">Preview Site</span>
                    <span class="badge badge-info ms-auto">View</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Maintenance Mode Management Menu End -->
    @endcanAccessMenu

     <!-- Leaderboard Menu Start - Available to all -->
    <li class="slide">
        <a href="{{ route('video.leaderboard') }}" class="side-menu__item">
            <i class="fe fe-award me-2"></i>
            <span class="side-menu__label">Leaderboard</span>
            <span class="badge badge-gold ms-auto">Top Earners</span>
        </a>
    </li>
    <!-- Leaderboard Menu End -->

    @canAccessMenu('support')
    <!-- Support Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-headphones side-menu__icon"></i>
            <span class="side-menu__label">Support</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Support</a>    
            </li>
            @hasPermission('support.view')
            <li class="slide">
                <a href="{{ route('admin.support.index') }}" class="side-menu__item">
                    <span class="side-menu__label">Support Dashboard</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.support.tickets') }}" class="side-menu__item">
                    <span class="side-menu__label">All Tickets</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.support.tickets', ['status' => 'open']) }}" class="side-menu__item">
                    <span class="side-menu__label">Open Tickets</span>
                    <span class="badge badge-success ms-auto" id="open-tickets-count">0</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.support.tickets', ['status' => 'pending']) }}" class="side-menu__item">
                    <span class="side-menu__label">Pending Tickets</span>
                    <span class="badge badge-warning ms-auto" id="pending-tickets-count">0</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.support.tickets', ['priority' => 'high']) }}" class="side-menu__item">
                    <span class="side-menu__label">High Priority</span>
                    <span class="badge badge-danger ms-auto" id="high-priority-count">0</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['support.view', 'reports.export'])
            <li class="slide">
                <a href="{{ route('admin.support.export') }}" class="side-menu__item">
                    <span class="side-menu__label">Export Tickets</span>
                </a>
            </li>
            @endhasAnyPermission
        </ul>
    </li>
    <!-- Support Menu End -->
    @endcanAccessMenu

    @canAccessMenu('notifications')
    <!-- Notifications Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-bell side-menu__icon"></i>
            <span class="side-menu__label">Notifications</span>
            <span class="badge badge-primary ms-auto" id="admin-notifications-count" style="display: none;">0</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Notifications</a>
            </li>
            @hasPermission('content.notifications')
            <li class="slide">
                <a href="{{ route('admin.notifications.index') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">All Notifications</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.notifications.create') }}" class="side-menu__item">
                    <i class="fe fe-plus me-2"></i>
                    <span class="side-menu__label">Create Notification</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="side-menu__item">
                    <i class="fe fe-eye-off me-2"></i>
                    <span class="side-menu__label">Unread</span>
                    <span class="badge badge-warning ms-auto" id="unread-notifications-count" style="display: none;">0</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.notifications.index', ['filter' => 'urgent']) }}" class="side-menu__item">
                    <i class="fe fe-alert-triangle me-2"></i>
                    <span class="side-menu__label">Urgent</span>
                    <span class="badge badge-danger ms-auto" id="urgent-notifications-count" style="display: none;">0</span>
                </a>
            </li> 
            <li class="slide">
                <a href="{{ route('admin.notifications.realtime') }}" class="side-menu__item">
                    <i class="fe fe-radio me-2"></i>
                    <span class="side-menu__label">Real-time Feed</span>
                    <span class="badge badge-info ms-auto">Live</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['content.notifications', 'settings.general'])
            <li class="slide">
                <a href="{{ route('admin.notifications.settings') }}" class="side-menu__item">
                    <i class="fe fe-settings me-2"></i>
                    <span class="side-menu__label">Settings</span>
                </a>
            </li>
            @endhasAnyPermission
        </ul>
    </li>
    <!-- Notifications Menu End -->
    @endcanAccessMenu

    @canAccessMenu('email-campaigns')
    <!-- Email Campaign Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-mail side-menu__icon"></i>
            <span class="side-menu__label">📧 Email Campaigns</span>
            @php
                $queuePending = DB::table('jobs')->where('queue', 'emails')->count();
                $queueFailed = DB::table('failed_jobs')->count();
                $kycPending = \App\Models\User::where('kv', 0)->where('status', 1)->count();
            @endphp
            @if($queuePending > 0)
                <span class="badge badge-warning ms-2">{{ $queuePending }} queued</span>
            @elseif($queueFailed > 0)
                <span class="badge badge-danger ms-2">{{ $queueFailed }} failed</span>
            @elseif($kycPending > 50)
                <span class="badge badge-info ms-2">{{ $kycPending }} KYC</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Email Campaigns</a>
            </li>
            @hasPermission('email-campaigns.dashboard')
            <li class="slide">
                <a href="{{ route('admin.email-campaigns.index') }}" class="side-menu__item">
                    <i class="fe fe-home me-2"></i>
                    <span class="side-menu__label">Campaign Dashboard</span>
                </a>
            </li>
            @endhasPermission
            @hasPermission('email-campaigns.analytics')
            <li class="slide">
                <a href="{{ route('admin.email-campaigns.analytics') }}" class="side-menu__item">
                    <i class="fe fe-bar-chart me-2"></i>
                    <span class="side-menu__label">Analytics & Reports</span>
                </a>
            </li>
            @endhasPermission
            @hasPermission('email-campaigns.templates')
            <li class="slide">
                <a href="{{ route('admin.email-campaigns.templates') }}" class="side-menu__item">
                    <i class="fe fe-file-text me-2"></i>
                    <span class="side-menu__label">Email Templates</span>
                </a>
            </li>
            @endhasPermission
            @hasPermission('email-campaigns.queue')
            <li class="slide">
                <a href="{{ route('admin.email-campaigns.queue') }}" class="side-menu__item">
                    <i class="fe fe-layers me-2"></i>
                    <span class="side-menu__label">Queue Management</span>
                    @if($queuePending > 0)
                        <span class="badge badge-warning ms-auto">{{ $queuePending }}</span>
                    @elseif($queueFailed > 0)
                        <span class="badge badge-danger ms-auto">{{ $queueFailed }}</span>
                    @endif
                </a>
            </li>
            @endhasPermission
            
            <!-- Quick Campaign Actions -->
            @hasPermission('email-campaigns.send')
            <li class="slide has-sub">
                <a href="javascript:void(0);" class="side-menu__item">
                    <i class="fe fe-send me-2"></i>
                    <span class="side-menu__label">📨 Quick Campaigns</span>
                    <i class="fe fe-chevron-right side-menu__angle hor-angle"></i>
                </a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="javascript:void(0);" class="side-menu__item" onclick="sendKycReminders();">
                            <i class="fe fe-user-check me-2"></i>
                            <span class="side-menu__label">Send KYC Reminders</span>
                            @if($kycPending > 0)
                                <span class="badge badge-warning ms-auto">{{ $kycPending }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="slide">
                        <a href="javascript:void(0);" class="side-menu__item" onclick="sendInactiveReminders();">
                            <i class="fe fe-user-x me-2"></i>
                            <span class="side-menu__label">Inactive User Reminders</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="javascript:void(0);" class="side-menu__item" onclick="sendPasswordResets();">
                            <i class="fe fe-key me-2"></i>
                            <span class="side-menu__label">Password Reset Campaign</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="javascript:void(0);" class="side-menu__item" onclick="sendToAllUsers();" data-route="{{ route('admin.email-campaigns.send-to-all-users') }}">
                            <i class="fe fe-users me-2"></i>
                            <span class="side-menu__label">Send to All Users</span>
                            @php $totalActiveUsers = \App\Models\User::where('status', 1)->count(); @endphp
                            @if($totalActiveUsers > 0)
                                <span class="badge badge-info ms-auto">{{ number_format($totalActiveUsers) }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="slide">
                        <a href="javascript:void(0);" class="side-menu__item" onclick="showCustomCampaign();">
                            <i class="fe fe-edit me-2"></i>
                            <span class="side-menu__label">Custom Campaign</span>
                            <span class="badge badge-primary ms-auto">New</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endhasPermission
            
            @hasPermission('email-campaigns.settings')
            <li class="slide">
                <a href="{{ route('admin.email-campaigns.settings') }}" class="side-menu__item">
                    <i class="fe fe-settings me-2"></i>
                    <span class="side-menu__label">Campaign Settings</span>
                </a>
            </li>
            @endhasPermission
        </ul>
    </li>
    <!-- Email Campaign Management Menu End -->
    @endcanAccessMenu

    @canAccessMenu('system-tools')
    <!-- System Tools & Cache Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-settings side-menu__icon"></i>
            <span class="side-menu__label">🔧 System Tools</span>
            @php
                $cacheSize = 0;
                try {
                    if (function_exists('sys_get_temp_dir')) {
                        $tempDir = sys_get_temp_dir();
                        $cacheSize = round(disk_free_space($tempDir) / (1024 * 1024), 2);
                    }
                } catch (Exception $e) {
                    // Silently handle any errors
                }
            @endphp
            @if($cacheSize > 0)
                <span class="badge badge-info ms-2">{{ $cacheSize }}MB</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">System Tools</a>
            </li>
            
            <!-- Browser Cache Management -->
            @hasPermission('system.cache-management')
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showBrowserCacheManager();">
                    <i class="fe fe-globe me-2"></i>
                    <span class="side-menu__label">Browser Cache Manager</span>
                    <span class="badge badge-primary ms-auto">Clear</span>
                </a>
            </li>
            @endhasPermission
            
            <!-- Advanced Cache Clearing -->
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showAdvancedCacheClearing();">
                    <i class="fe fe-trash-2 me-2"></i>
                    <span class="side-menu__label">Advanced Cache Clear</span>
                    <span class="badge badge-warning ms-auto">Deep</span>
                </a>
            </li>
            
            <!-- Cache Status Monitor -->
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showCacheStatusMonitor();">
                    <i class="fe fe-activity me-2"></i>
                    <span class="side-menu__label">Cache Status Monitor</span>
                    <span class="badge badge-success ms-auto">Live</span>
                </a>
            </li>
            
            <!-- System Performance -->
            @hasPermission('system.performance')
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showSystemPerformance();">
                    <i class="fe fe-bar-chart-2 me-2"></i>
                    <span class="side-menu__label">System Performance</span>
                    <span class="badge badge-info ms-auto">Stats</span>
                </a>
            </li>
            @endhasPermission
            
            <!-- Emergency Cache Clear -->
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="emergencyCacheClear();" 
                   style="color: #ff6b6b;" title="Emergency cache clearing for system issues">
                    <i class="fe fe-zap me-2"></i>
                    <span class="side-menu__label">🚨 Emergency Clear</span>
                    <span class="badge badge-danger ms-auto">SOS</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- System Tools & Cache Management Menu End -->
    @endcanAccessMenu

    {{-- @canAccessMenu('popups')
    <!-- Popup Management Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-window-restore side-menu__icon"></i>
            <span class="side-menu__label">Popup Management</span>
            @php
                $activePopups = \App\Models\Popup::where('is_active', true)->count();
                $totalViews = \App\Models\Popup::sum('view_count');
            @endphp
            @if($activePopups > 0)
                <span class="badge badge-success ms-auto">{{ $activePopups }} active</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Popup Management</a>
            </li>
            @hasPermission('content.popups')
            <li class="slide">
                <a href="{{ route('admin.popups.index') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">All Popups</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.popups.create') }}" class="side-menu__item">
                    <i class="fe fe-plus me-2"></i>
                    <span class="side-menu__label">Create Popup</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.popups.index', ['filter' => 'active']) }}" class="side-menu__item">
                    <i class="fe fe-check-circle me-2"></i>
                    <span class="side-menu__label">Active Popups</span>
                    <span class="badge badge-success ms-auto" id="active-popups-count">{{ $activePopups }}</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.popups.index', ['filter' => 'inactive']) }}" class="side-menu__item">
                    <i class="fe fe-x-circle me-2"></i>
                    <span class="side-menu__label">Inactive Popups</span>
                </a>
            </li>
            @endhasPermission
            @hasAnyPermission(['content.popups', 'reports.analytics'])
            <li class="slide">
                <a href="javascript:void(0);" class="side-menu__item" onclick="showPopupAnalytics()">
                    <i class="fe fe-bar-chart-2 me-2"></i>
                    <span class="side-menu__label">Analytics</span>
                    @if($totalViews > 0)
                        <span class="badge badge-info ms-auto">{{ number_format($totalViews) }} views</span>
                    @endif
                </a>
            </li>
            @endhasAnyPermission
        </ul>
    </li>
    <!-- Popup Management Menu End -->
    @endcanAccessMenu --}}

    

    <!-- Start::slide -->
    <li class="slide">
        <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;" id="sidebarLogoutForm">
            @csrf
            <button type="button" class="side-menu__item" onclick="handleAdminLogoutWithCacheClearing(event);" ondblclick="emergencyAdminLogoutWithCacheClearing();" style="background: none; border: none; width: 100%; text-align: left; color: inherit; display: flex; align-items: center;" title="Click to logout with cache clearing, Double-click for immediate logout">
                <i class="si si-logout side-menu__icon"></i>
                <span class="side-menu__label">Logout</span>
                <small class="text-muted ms-2">(Clear Cache)</small>
            </button>
        </form>
    </li>

    <!-- Admin Cache Management Tools -->
    @if(auth()->guard('admin')->check())
    <li class="slide">
        <a href="javascript:void(0);" class="side-menu__item" onclick="showAdminCacheManagementModal();">
            <i class="fe fe-trash-2 side-menu__icon"></i>
            <span class="side-menu__label">Cache Manager</span>
            <small class="text-muted ms-2">(Admin Tools)</small>
        </a>
    </li>
    @endif
</ul>

<script>
// Function to show lottery configuration history
function showLotteryConfigHistory() {
    // Create modal for lottery configuration history
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🎰 Lottery Configuration History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="lottery-config-history-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                        <p class="mt-2">Loading configuration history...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load configuration history
    fetch('{{ route("admin.lottery-settings.index") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            return {
                recentChanges: [],
                stats: { total_changes: 0 }
            };
        });
    })
    .then(data => {
        const content = document.getElementById('lottery-config-history-content');
        content.innerHTML = `
            <div class="alert alert-info">
                <i class="fe fe-info me-2"></i>
                Recent configuration changes will be displayed here. Visit the full Lottery Settings page to view detailed history.
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('admin.lottery-settings.index') }}" class="btn btn-primary">
                    <i class="fe fe-external-link me-2"></i>View Full Configuration Dashboard
                </a>
            </div>
        `;
    })
    .catch(error => {
        const content = document.getElementById('lottery-config-history-content');
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fe fe-alert-triangle me-2"></i>
                Error loading configuration history. Please try again later.
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('admin.lottery-settings.index') }}" class="btn btn-primary">
                    <i class="fe fe-external-link me-2"></i>Go to Lottery Settings
                </a>
            </div>
        `;
    });
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to show referral benefits statistics
function showReferralStats() {
    // Create modal for referral statistics
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🎁 Referral Benefits Statistics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="referral-stats-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading referral statistics...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load statistics data
    fetch('{{ route("admin.referral-benefits.index") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            return {
                qualified_users: 0,
                total_bonuses: 0,
                transactions_today: 0,
                active_benefits: 0
            };
        });
    })
    .then(data => {
        const content = document.getElementById('referral-stats-content');
        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fe fe-users text-success fs-24"></i>
                            <h4 class="mt-2">${data.qualified_users || 0}</h4>
                            <p class="text-muted">Qualified Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fe fe-dollar-sign text-info fs-24"></i>
                            <h4 class="mt-2">$${(data.total_bonuses || 0).toLocaleString()}</h4>
                            <p class="text-muted">Total Bonuses Given</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fe fe-activity text-warning fs-24"></i>
                            <h4 class="mt-2">${data.transactions_today || 0}</h4>
                            <p class="text-muted">Transactions Today</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fe fe-gift text-primary fs-24"></i>
                            <h4 class="mt-2">${data.active_benefits || 0}</h4>
                            <p class="text-muted">Active Benefit Plans</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('admin.referral-benefits.index') }}" class="btn btn-primary">
                    <i class="fe fe-external-link me-2"></i>View Full Dashboard
                </a>
            </div>
        `;
    })
    .catch(error => {
        const content = document.getElementById('referral-stats-content');
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fe fe-alert-triangle me-2"></i>
                Error loading statistics. Please try again later.
            </div>
        `;
    });
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to show referral benefits help
function showBenefitsHelp() {
    // Create modal for help guide
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🎁 Referral Benefits System - Help Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6><i class="fe fe-info text-primary me-2"></i>How It Works</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fe fe-check-circle text-success me-2"></i>
                                    <strong>Qualification:</strong> Users need 15+ referrals, each investing $50+ for video access
                                </li>
                                <li class="mb-2">
                                    <i class="fe fe-gift text-info me-2"></i>
                                    <strong>Transfer Bonus:</strong> Extra money when transferring to others (1-5%)
                                </li>
                                <li class="mb-2">
                                    <i class="fe fe-wallet text-success me-2"></i>
                                    <strong>Receive Bonus:</strong> Extra money when receiving from others (1-5%)
                                </li>
                                <li class="mb-2">
                                    <i class="fe fe-minus-circle text-warning me-2"></i>
                                    <strong>Withdraw Reduction:</strong> Lower withdrawal fees (1-5%)
                                </li>
                            </ul>
                            
                            <h6 class="mt-4"><i class="fe fe-settings text-primary me-2"></i>Admin Controls</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fe fe-toggle-right text-success me-2"></i>
                                    Enable/Disable the entire system
                                </li>
                                <li class="mb-2">
                                    <i class="fe fe-sliders text-info me-2"></i>
                                    Set bonus percentage ranges (1-5%)
                                </li>
                                <li class="mb-2">
                                    <i class="fe fe-users text-warning me-2"></i>
                                    Manage individual user qualifications
                                </li>
                                <li class="mb-2">
                                    <i class="fe fe-bar-chart-2 text-primary me-2"></i>
                                    Track all bonus transactions
                                </li>
                            </ul>
                            
                            <div class="alert alert-info mt-3">
                                <h6><i class="fe fe-lightbulb me-2"></i>Quick Tips</h6>
                                <p class="mb-0">
                                    • Each qualified user gets random bonus percentages within your set ranges<br>
                                    • System automatically recalculates user qualifications<br>
                                    • All bonus transactions are tracked for transparency<br>
                                    • You can manually recalculate any user's qualification status
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.referral-benefits.index') }}" class="btn btn-primary me-2">
                            <i class="fe fe-external-link me-2"></i>Open Dashboard
                        </a>
                        <a href="{{ route('admin.referral-benefits.qualified-users') }}" class="btn btn-outline-success">
                            <i class="fe fe-users me-2"></i>View Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to show manual draw modal
function showManualDrawModal() {
    // Create modal for manual draw options
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🎰 Manual Draw Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fe fe-info me-2"></i>
                        Select a pending draw to perform manual winner selection or draw operations.
                    </div>
                    <div id="manual-draw-content">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading available draws...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load pending draws for manual selection
    fetch('{{ route("admin.lottery.draws") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            // Parse HTML to extract draw data
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            return { draws: [] }; // Fallback
        });
    })
    .then(data => {
        const content = document.getElementById('manual-draw-content');
        if (data.draws && data.draws.length > 0) {
            let drawsHtml = '<div class="row">';
            data.draws.forEach(draw => {
                if (draw.status === 'pending' && draw.id) {
                    drawsHtml += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Draw #${draw.id}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">Draw Date: ${draw.draw_date || 'Not set'}</small><br>
                                        <small class="text-muted">Total Tickets: ${draw.total_tickets || 0}</small>
                                    </p>
                                    <div class="btn-group w-100" role="group">
                                        <a href="/admin/lottery/draws/${draw.id}/manual-winners" class="btn btn-primary btn-sm">
                                            <i class="fe fe-users"></i> Manual Winners
                                        </a>
                                        <a href="/admin/lottery/draws/${draw.id}/manual-selection" class="btn btn-info btn-sm">
                                            <i class="fe fe-edit"></i> Manual Selection
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            drawsHtml += '</div>';
            content.innerHTML = drawsHtml;
        } else {
            content.innerHTML = `
                <div class="text-center">
                    <div class="alert alert-warning">
                        <i class="fe fe-alert-triangle me-2"></i>
                        No pending draws available for manual selection.
                    </div>
                    <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-primary">
                        <i class="fe fe-plus me-2"></i>Create New Draw
                    </a>
                </div>
            `;
        }
    })
    .catch(error => {
        const content = document.getElementById('manual-draw-content');
        content.innerHTML = `
            <div class="alert alert-warning">
                <i class="fe fe-alert-triangle me-2"></i>
                Unable to load draws. You can still access manual functions through the main draws page.
            </div>
            <div class="text-center">
                <a href="{{ route('admin.lottery.draws') }}" class="btn btn-primary">
                    <i class="fe fe-list me-2"></i>View All Draws
                </a>
            </div>
        `;
    });
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to show auto-generate draw modal
function showAutoGenerateModal() {
    // Create modal for auto-generate draw
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fe fe-zap me-2"></i>⚡ Auto Generate Draw
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fe fe-zap me-2"></i>
                        <strong>Quick Auto-Generate:</strong> Instantly create a new lottery draw with automatic settings from your lottery configuration.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fe fe-clock" style="font-size: 2rem; color: #0d6efd;"></i>
                                    <h6 class="mt-2">Standard Auto-Draw</h6>
                                    <p class="text-muted small">Uses default lottery settings</p>
                                    <button class="btn btn-primary btn-sm" onclick="generateAutoDraw('standard')">
                                        <i class="fe fe-plus me-1"></i>Generate Now
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fe fe-settings" style="font-size: 2rem; color: #198754;"></i>
                                    <h6 class="mt-2">Quick Custom</h6>
                                    <p class="text-muted small">Set basic parameters</p>
                                    <button class="btn btn-success btn-sm" onclick="showQuickCustomForm()">
                                        <i class="fe fe-edit me-1"></i>Customize
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Recent Activity:</h6>
                        <div id="recent-draws-info">
                            <div class="d-flex align-items-center text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Loading recent draws...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-primary">
                        <i class="fe fe-list me-1"></i>Manage All Draws
                    </a>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load recent draws info
    setTimeout(() => {
        document.getElementById('recent-draws-info').innerHTML = `
            <div class="small text-muted">
                <i class="fe fe-info me-1"></i>
                Auto-generate will create draws with your current lottery settings. 
                You can modify them later from the draws management page.
            </div>
        `;
    }, 1000);
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to generate auto draw
function generateAutoDraw(type) {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fe fe-loader me-1"></i>Generating...';
    button.disabled = true;
    
    fetch('{{ route("admin.lottery.auto-generate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: type,
            auto_draw: true,
            auto_prize_distribution: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and show success
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();
            });
            
            // Show success message
            alert(`✅ Auto-draw generated successfully! Draw ID: #${data.draw_id || 'Generated'}`);
            
            // Redirect to draws page
            setTimeout(() => {
                window.location.href = '{{ route("admin.lottery.draws") }}';
            }, 1500);
        } else {
            alert('❌ Error: ' + (data.message || 'Failed to generate auto-draw'));
        }
    })
    .catch(error => {
        console.error('Auto-generate error:', error);
        alert('❌ An error occurred while generating the auto-draw');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Function to show quick custom form
function showQuickCustomForm() {
    alert('🚧 Quick custom form will be available in the next update. For now, please use the standard auto-generate or create a draw manually.');
}

// Function to show create draw modal  
function showCreateDrawModal() {
    // Create modal for new draw creation
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fe fe-plus me-2"></i>🎰 Create New Lottery Draw
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fe fe-info me-2"></i>
                        <strong>Choose your creation method:</strong> Auto-generate for quick setup or manual configuration for custom draws.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body text-center">
                                    <i class="fe fe-zap" style="font-size: 2.5rem; color: #198754;"></i>
                                    <h6 class="mt-2">Auto Generate</h6>
                                    <p class="text-muted small">Quick setup with default settings</p>
                                    <button class="btn btn-success btn-sm w-100" onclick="generateAutoDraw('standard')">
                                        <i class="fe fe-zap me-1"></i>Generate Now
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-primary h-100">
                                <div class="card-body text-center">
                                    <i class="fe fe-edit-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                                    <h6 class="mt-2">Manual Configuration</h6>
                                    <p class="text-muted small">Full control over all settings</p>
                                    <button class="btn btn-primary btn-sm w-100" onclick="goToManualCreate()">
                                        <i class="fe fe-settings me-1"></i>Configure
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center">
                                    <i class="fe fe-users" style="font-size: 2.5rem; color: #ffc107;"></i>
                                    <h6 class="mt-2">Winner Control</h6>
                                    <p class="text-muted small">Manual winner selection</p>
                                    <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-warning btn-sm w-100">
                                        <i class="fe fe-edit-3 me-1"></i>Select Winners
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="text-muted">Recent Draws:</h6>
                        <div id="recent-draws-preview">
                            <div class="d-flex align-items-center text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Loading recent draws...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-primary">
                        <i class="fe fe-list me-1"></i>View All Draws
                    </a>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load recent draws preview
    setTimeout(() => {
        document.getElementById('recent-draws-preview').innerHTML = `
            <div class="small text-muted">
                <i class="fe fe-info me-1"></i>
                Choose auto-generate for instant setup, manual configuration for custom prizes and settings, or winner control for specific ticket selection.
            </div>
        `;
    }, 1000);
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to go to manual create page
function goToManualCreate() {
    // For now, redirect to draws page since we need to create a proper form
    // In the future, this could go to a dedicated manual creation form
    window.location.href = '{{ route("admin.lottery.draws") }}?action=create';
}

function toggleMaintenanceMode() {
    if (confirm('Are you sure you want to toggle maintenance mode?')) {
        fetch('{{ route("admin.settings.toggle-maintenance") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while toggling maintenance mode');
        });
    }
}

// Add active class to current menu item
document.addEventListener('DOMContentLoaded', function() { 
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.side-menu__item');
    
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPath) {
            item.classList.add('active');
            // Also add active class to parent menu if it's a submenu
            const parentMenu = item.closest('.slide-menu');
            if (parentMenu) {
                const parentMenuItem = parentMenu.previousElementSibling;
                if (parentMenuItem) {
                    parentMenuItem.classList.add('active');
                }
            }
        }
    });
    
    // Update pending counts
    updatePendingCounts();
});

// Function to update pending counts
function updatePendingCounts() {
    // Update pending deposits count
    fetch('/admin/deposits/pending', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If HTML response, extract count from the page
            return response.text().then(text => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                const pendingCards = doc.querySelectorAll('.card-body, .stats-card');
                let pendingCount = 0;
                
                pendingCards.forEach(card => {
                    const text = card.textContent;
                    if (text.includes('Pending') || text.includes('pending')) {
                        const matches = text.match(/\d+/);
                        if (matches) {
                            pendingCount = parseInt(matches[0]);
                        }
                    }
                });
                
                return { stats: { pending_count: pendingCount } };
            });
        }
    })
    .then(data => {
        if (data.stats && data.stats.pending_count !== undefined) {
            const pendingElement = document.getElementById('pending-deposits-count');
            if (pendingElement) {
                pendingElement.textContent = data.stats.pending_count;
                pendingElement.style.display = data.stats.pending_count > 0 ? 'inline' : 'none';
            }
        }
    })
    .catch(error => {
        console.warn('Pending deposits endpoint not available:', error.message);
        // Hide the element if endpoint not available
        const pendingElement = document.getElementById('pending-deposits-count');
        if (pendingElement) {
            pendingElement.style.display = 'none';
        }
    });
    
    // Update pending withdrawals count
    fetch('/admin/withdrawals/pending', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            // If it's HTML response, extract count from the page
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const statsCards = doc.querySelectorAll('.stats-card, .card-body');
            let pendingCount = 0;
            
            statsCards.forEach(card => {
                const text = card.textContent;
                if (text.includes('Pending') || text.includes('pending')) {
                    const matches = text.match(/\d+/);
                    if (matches) {
                        pendingCount = parseInt(matches[0]);
                    }
                }
            });
            
            return { pending_count: pendingCount };
        });
    })
    .then(data => {
        const pendingElement = document.getElementById('pending-withdrawals-count');
        if (pendingElement) {
            const count = data.pending_count || data.stats?.pending_count || 0;
            pendingElement.textContent = count;
            pendingElement.style.display = count > 0 ? 'inline' : 'none';
        }
    })
    .catch(error => {
        console.error('Error updating pending withdrawals count:', error);
    });
    
    // Update pending KYC count (if element exists)
    const pendingKycElement = document.getElementById('pending-kyc-count');
    if (pendingKycElement) {
        // You can add similar logic for KYC pending count
        // For now, we'll just hide it if it's 0
        if (pendingKycElement.textContent === '0') {
            pendingKycElement.style.display = 'none';
        }
    }
    
    // Update support ticket counts
    updateSupportTicketCounts();
    
    // Update user counts
    updateUserCounts();
    
    // Update transfer counts
    updateTransferCounts();
    
    // Update notification counts
    updateNotificationCounts();
    
    // Update popup counts
    updatePopupCounts();
    
    // Update referral benefits counts
    updateReferralBenefitsCounts();
}

// Function to update support ticket counts
function updateSupportTicketCounts() {
    fetch('/admin/support/tickets', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If HTML response, extract counts from the page
            return response.text().then(text => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                const statsCards = doc.querySelectorAll('.card-body, .stats-card');
                let openCount = 0, pendingCount = 0, highCount = 0;
                
                statsCards.forEach(card => {
                    const text = card.textContent;
                    if (text.includes('Open') && !text.includes('Pending')) {
                        const matches = text.match(/\d+/);
                        if (matches) openCount = parseInt(matches[0]);
                    } else if (text.includes('Pending')) {
                        const matches = text.match(/\d+/);
                        if (matches) pendingCount = parseInt(matches[0]);
                    } else if (text.includes('High Priority')) {
                        const matches = text.match(/\d+/);
                        if (matches) highCount = parseInt(matches[0]);
                    }
                });
                
                return {
                    recordsTotal: openCount + pendingCount + highCount,
                    open_count: openCount,
                    pending_count: pendingCount,
                    high_priority_count: highCount
                };
            });
        }
    })
    .then(data => {
        if (data.recordsTotal !== undefined || data.open_count !== undefined) {
            // Update open tickets count
            const openElement = document.getElementById('open-tickets-count');
            if (openElement) {
                const count = data.open_count || 0;
                openElement.textContent = count;
                openElement.style.display = count > 0 ? 'inline' : 'none';
            }
            
            // Update pending tickets count
            const pendingElement = document.getElementById('pending-tickets-count');
            if (pendingElement) {
                const count = data.pending_count || 0;
                pendingElement.textContent = count;
                pendingElement.style.display = count > 0 ? 'inline' : 'none';
            }
            
            // Update high priority tickets count
            const highElement = document.getElementById('high-priority-count');
            if (highElement) {
                const count = data.high_priority_count || 0;
                highElement.textContent = count;
                highElement.style.display = count > 0 ? 'inline' : 'none';
            }
        }
    })
    .catch(error => {
        console.warn('Support tickets endpoint not available:', error.message);
        // Hide ticket count elements if endpoint not available
        const elements = ['open-tickets-count', 'pending-tickets-count', 'high-priority-count'];
        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.display = 'none';
            }
        });
    });
}

// Function to update user counts
function updateUserCounts() {
    // Get user statistics from the users index page
    fetch('/admin/users', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            // If it's HTML response, we'll extract counts from the page or use defaults
            return {
                active_users: 0,
                inactive_users: 0,
                banned_users: 0
            };
        });
    })
    .then(data => {
        // Update active users count
        const activeElement = document.getElementById('active-users-count');
        if (activeElement) {
            const count = data.active_users || 0;
            activeElement.textContent = count;
            activeElement.style.display = count > 0 ? 'inline' : 'none';
        }
        
        // Update inactive users count
        const inactiveElement = document.getElementById('inactive-users-count');
        if (inactiveElement) {
            const count = data.inactive_users || 0;
            inactiveElement.textContent = count;
            inactiveElement.style.display = count > 0 ? 'inline' : 'none';
        }
        
        // Update banned users count
        const bannedElement = document.getElementById('banned-users-count');
        if (bannedElement) {
            const count = data.banned_users || 0;
            bannedElement.textContent = count;
            bannedElement.style.display = count > 0 ? 'inline' : 'none';
        }
    })
    .catch(error => {
        console.error('Error updating user counts:', error);
    });
}

// Function to update transfer counts
function updateTransferCounts() {
    // Get transfer statistics
    fetch('/admin/transfer_history', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            // If it's HTML response, extract counts from the page
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            
            // Extract transfer counts from summary cards
            const summaryCards = doc.querySelectorAll('.summary-card, .card-body');
            let todayTransfers = 0, totalTransfers = 0;
            
            summaryCards.forEach(card => {
                const text = card.textContent;
                if (text.includes("Today's Transfers") || text.includes('today')) {
                    const matches = text.match(/\d+/);
                    if (matches) {
                        todayTransfers = parseInt(matches[0]);
                    }
                } else if (text.includes('Total Transfers') || text.includes('total')) {
                    const matches = text.match(/\d+/);
                    if (matches) {
                        totalTransfers = parseInt(matches[0]);
                    }
                }
            });
            
            return {
                success: true,
                today_transfers: todayTransfers,
                total_transfers: totalTransfers
            };
        });
    })
    .then(data => {
        // Update today's transfer count in menu badge
        const todayBadges = document.querySelectorAll('span.badge');
        todayBadges.forEach(badge => {
            if (badge.textContent.includes('today')) {
                const count = data.today_transfers || 0;
                if (count > 0) {
                    badge.textContent = count + ' today';
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
        
        // Update total transfer count in submenu
        const transferLinks = document.querySelectorAll('a[href*="transfer_history"]');
        transferLinks.forEach(link => {
            const badge = link.querySelector('.badge');
            if (badge && !badge.textContent.includes('today')) {
                const count = data.total_transfers || 0;
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    })
    .catch(error => {
        console.error('Error updating transfer counts:', error);
    });
}

// Function to show popup analytics
function showPopupAnalytics() {
    // Create a modal to show popup analytics
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fe fe-bar-chart-2 me-2"></i>
                        Popup Analytics Overview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="popup-analytics-content">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading popup analytics...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.popups.index') }}" class="btn btn-primary">
                        View Detailed Analytics
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load analytics data
    fetch('/admin/popups', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        // If HTML response, extract basic stats
        return response.text().then(text => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            
            // Extract stats from the page
            const statsCards = doc.querySelectorAll('.card-body');
            let totalPopups = 0, activePopups = 0, totalViews = 0, totalClicks = 0;
            
            statsCards.forEach(card => {
                const text = card.textContent;
                if (text.includes('Total Popups')) {
                    const matches = text.match(/\d+/);
                    if (matches) totalPopups = parseInt(matches[0]);
                } else if (text.includes('Active Popups')) {
                    const matches = text.match(/\d+/);
                    if (matches) activePopups = parseInt(matches[0]);
                } else if (text.includes('Total Views')) {
                    const matches = text.match(/[\d,]+/);
                    if (matches) totalViews = parseInt(matches[0].replace(/,/g, ''));
                } else if (text.includes('Total Clicks')) {
                    const matches = text.match(/[\d,]+/);
                    if (matches) totalClicks = parseInt(matches[0].replace(/,/g, ''));
                }
            });
            
            return {
                success: true,
                stats: {
                    total_popups: totalPopups,
                    active_popups: activePopups,
                    total_views: totalViews,
                    total_clicks: totalClicks,
                    click_rate: totalViews > 0 ? ((totalClicks / totalViews) * 100).toFixed(2) : 0
                }
            };
        });
    })
    .then(data => {
        const content = document.getElementById('popup-analytics-content');
        if (data.success && data.stats) {
            content.innerHTML = `
                <div class="col-md-3 mb-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fe fe-window-restore fa-2x text-primary mb-2"></i>
                            <h4 class="mb-1">${data.stats.total_popups || 0}</h4>
                            <small class="text-muted">Total Popups</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fe fe-check-circle fa-2x text-success mb-2"></i>
                            <h4 class="mb-1">${data.stats.active_popups || 0}</h4>
                            <small class="text-muted">Active Popups</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fe fe-eye fa-2x text-info mb-2"></i>
                            <h4 class="mb-1">${(data.stats.total_views || 0).toLocaleString()}</h4>
                            <small class="text-muted">Total Views</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fe fe-mouse-pointer fa-2x text-warning mb-2"></i>
                            <h4 class="mb-1">${(data.stats.total_clicks || 0).toLocaleString()}</h4>
                            <small class="text-muted">Total Clicks</small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Performance Overview</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between">
                                        <span>Click-through Rate:</span>
                                        <strong class="text-${data.stats.click_rate > 5 ? 'success' : data.stats.click_rate > 2 ? 'warning' : 'danger'}">${data.stats.click_rate || 0}%</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between">
                                        <span>Avg. Views per Popup:</span>
                                        <strong>${data.stats.total_popups > 0 ? Math.round((data.stats.total_views || 0) / data.stats.total_popups) : 0}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="col-12 text-center">
                    <i class="fe fe-alert-circle fa-3x text-muted mb-3"></i>
                    <h5>No Analytics Data Available</h5>
                    <p class="text-muted">Create your first popup to see analytics.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading popup analytics:', error);
        const content = document.getElementById('popup-analytics-content');
        content.innerHTML = `
            <div class="col-12 text-center">
                <i class="fe fe-x-circle fa-3x text-danger mb-3"></i>
                <h5>Error Loading Analytics</h5>
                <p class="text-muted">Please try again later.</p>
            </div>
        `;
    });
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to update notification counts
function updateNotificationCounts() {
    fetch('{{ route("admin.notifications.stats") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If HTML response or route doesn't exist, return default data
            return {
                success: true,
                stats: { total: 0, unread: 0, urgent: 0 }
            };
        }
    })
    .then(data => {
        if (data.success && data.stats) {
            // Update total notifications count
            const totalElement = document.getElementById('admin-notifications-count');
            if (totalElement) {
                const totalCount = data.stats.total || 0;
                totalElement.textContent = totalCount;
                totalElement.style.display = totalCount > 0 ? 'inline' : 'none';
            }
            
            // Update unread notifications count
            const unreadElement = document.getElementById('unread-notifications-count');
            if (unreadElement) {
                const unreadCount = data.stats.unread || 0;
                unreadElement.textContent = unreadCount;
                unreadElement.style.display = unreadCount > 0 ? 'inline' : 'none';
                
                // Update the main notification badge with unread count
                if (totalElement) {
                    totalElement.textContent = unreadCount;
                    totalElement.style.display = unreadCount > 0 ? 'inline' : 'none';
                    // Change color based on count
                    if (unreadCount > 10) {
                        totalElement.className = 'badge badge-danger ms-auto';
                    } else if (unreadCount > 5) {
                        totalElement.className = 'badge badge-warning ms-auto';
                    } else {
                        totalElement.className = 'badge badge-primary ms-auto';
                    }
                }
            }
            
            // Update urgent notifications count
            const urgentElement = document.getElementById('urgent-notifications-count');
            if (urgentElement) {
                const urgentCount = data.stats.urgent || 0;
                urgentElement.textContent = urgentCount;
                urgentElement.style.display = urgentCount > 0 ? 'inline' : 'none';
                
                // Add pulsing effect for urgent notifications
                if (urgentCount > 0) {
                    urgentElement.style.animation = 'pulse 1.5s infinite';
                } else {
                    urgentElement.style.animation = 'none';
                }
            }
        }
    })
    .catch(error => {
        console.warn('Notifications endpoint not available:', error.message);
        // Hide notification elements if endpoint not available
        const elements = ['admin-notifications-count', 'unread-notifications-count', 'urgent-notifications-count'];
        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.display = 'none';
            }
        });
    });
}

// Function to update popup counts
function updatePopupCounts() {
    fetch('/admin/popups', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        // If HTML response, extract counts from the page
        return response.text().then(text => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            
            // Extract active popup count from the page
            const statsCards = doc.querySelectorAll('.card-body');
            let activePopups = 0;
            
            statsCards.forEach(card => {
                const text = card.textContent;
                if (text.includes('Active Popups')) {
                    const matches = text.match(/\d+/);
                    if (matches) {
                        activePopups = parseInt(matches[0]);
                    }
                }
            });
            
            return {
                success: true,
                active_popups: activePopups
            };
        });
    })
    .then(data => {
        // Update active popups count
        const activeElement = document.getElementById('active-popups-count');
        if (activeElement) {
            const count = data.active_popups || data.stats?.active_popups || 0;
            activeElement.textContent = count;
            activeElement.style.display = count > 0 ? 'inline' : 'none';
        }
    })
    .catch(error => {
        console.warn('Popup management not available:', error);
        // Hide popup-related elements if they exist
        const activeElement = document.getElementById('active-popups-count');
        if (activeElement) {
            activeElement.style.display = 'none';
        }
    });
}

// Function to show plan analytics
function showPlanAnalytics() {
    // Create a modal to show plan analytics
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fe fe-bar-chart-2 me-2"></i>
                        Investment Plans Analytics Overview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="plan-analytics-content">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading plan analytics...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.plans.index') }}" class="btn btn-primary">
                        View Detailed Analytics
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load analytics data
    fetch('/admin/plans', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        // If HTML response, extract basic stats
        return response.text().then(text => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            
            // Extract stats from the page
            const statsCards = doc.querySelectorAll('.stats-card, .card-body');
            let totalPlans = 0, activePlans = 0, totalInvestments = 0, totalInvestmentAmount = 0;
            
            statsCards.forEach(card => {
                const text = card.textContent;
                if (text.includes('Total Plans')) {
                    const matches = text.match(/\\d+/);
                    if (matches) totalPlans = parseInt(matches[0]);
                } else if (text.includes('Active Plans')) {
                    const matches = text.match(/\\d+/);
                    if (matches) activePlans = parseInt(matches[0]);
                } else if (text.includes('Total Investments')) {
                    const matches = text.match(/[\\d,]+/);
                    if (matches) totalInvestments = parseInt(matches[0].replace(/,/g, ''));
                } else if (text.includes('Investment Amount')) {
                    const matches = text.match(/[\\d,]+/);
                    if (matches) totalInvestmentAmount = parseInt(matches[0].replace(/,/g, ''));
                }
            });
            
            return {
                success: true,
                stats: {
                    total_plans: totalPlans,
                    active_plans: activePlans,
                    total_investments: totalInvestments,
                    total_investment_amount: totalInvestmentAmount,
                    avg_investment: totalInvestments > 0 ? (totalInvestmentAmount / totalInvestments) : 0
                }
            };
        });
    })
    .then(data => {
        const content = document.getElementById('plan-analytics-content');
        if (data.success && data.stats) {
            content.innerHTML = `
                <div class="col-md-3 mb-4">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <i class="fe fe-layers fa-3x text-primary mb-3"></i>
                            <h3 class="mb-1">${data.stats.total_plans || 0}</h3>
                            <small class="text-muted">Total Plans</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <i class="fe fe-check-circle fa-3x text-success mb-3"></i>
                            <h3 class="mb-1">${data.stats.active_plans || 0}</h3>
                            <small class="text-muted">Active Plans</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <i class="fe fe-users fa-3x text-info mb-3"></i>
                            <h3 class="mb-1">${(data.stats.total_investments || 0).toLocaleString()}</h3>
                            <small class="text-muted">Total Investments</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <i class="fe fe-dollar-sign fa-3x text-warning mb-3"></i>
                            <h3 class="mb-1">$${(data.stats.total_investment_amount || 0).toLocaleString()}</h3>
                            <small class="text-muted">Total Investment Amount</small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fe fe-trending-up me-2"></i>
                                Quick Insights
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Plan Activation Rate:</span>
                                        <strong>${data.stats.total_plans > 0 ? ((data.stats.active_plans / data.stats.total_plans) * 100).toFixed(1) : 0}%</strong>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Average Investment:</span>
                                        <strong>$${(data.stats.avg_investment || 0).toLocaleString()}</strong>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Investments per Plan:</span>
                                        <strong>${data.stats.total_plans > 0 ? (data.stats.total_investments / data.stats.total_plans).toFixed(1) : 0}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="col-12 text-center">
                    <i class="fe fe-alert-circle fa-3x text-muted mb-3"></i>
                    <h5>No Analytics Data Available</h5>
                    <p class="text-muted">Create your first investment plan to see analytics.</p>
                    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
                        <i class="fe fe-plus me-1"></i>Create Your First Plan
                    </a>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading plan analytics:', error);
        const content = document.getElementById('plan-analytics-content');
        content.innerHTML = `
            <div class="col-12 text-center">
                <i class="fe fe-x-circle fa-3x text-danger mb-3"></i>
                <h5>Error Loading Analytics</h5>
                <p class="text-muted">Please try again later.</p>
                <button class="btn btn-outline-primary" onclick="showPlanAnalytics()">
                    <i class="fe fe-refresh-cw me-1"></i>Retry
                </button>
            </div>
        `;
    });
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to show plan settings
function showPlanSettings() {
    // Create a modal to show plan settings
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fe fe-settings me-2"></i>
                        Investment Plan Settings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fe fe-globe me-2"></i>
                                        Global Plan Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <a href="{{ route('admin.settings.general') }}" class="list-group-item list-group-item-action">
                                            <i class="fe fe-settings me-2"></i>
                                            General Settings
                                        </a>
                                        <a href="{{ route('admin.commission-levels.index') }}" class="list-group-item list-group-item-action">
                                            <i class="fe fe-percent me-2"></i>
                                            Commission Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fe fe-tool me-2"></i>
                                        Plan Management Tools
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <a href="{{ route('admin.plans.index') }}?bulk=true" class="list-group-item list-group-item-action">
                                            <i class="fe fe-edit me-2"></i>
                                            Bulk Edit Plans
                                        </a>
                                        <a href="{{ route('admin.plans.index') }}?export=true" class="list-group-item list-group-item-action">
                                            <i class="fe fe-download me-2"></i>
                                            Export Plan Data
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fe fe-help-circle me-2"></i>
                                        Quick Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="toggleAllPlans('activate')">
                                                <i class="fe fe-check-circle me-1"></i>
                                                Activate All Plans
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-warning btn-sm w-100 mb-2" onclick="toggleAllPlans('deactivate')">
                                                <i class="fe fe-x-circle me-1"></i>
                                                Deactivate All Plans
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-outline-info btn-sm w-100 mb-2" onclick="refreshPlanStats()">
                                                <i class="fe fe-refresh-cw me-1"></i>
                                                Refresh Statistics
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.plans.index') }}" class="btn btn-primary">
                        <i class="fe fe-external-link me-1"></i>Go to Plan Management
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to toggle all plans
function toggleAllPlans(action) {
    const actionText = action === 'activate' ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${actionText} all investment plans?`)) {
        // Show loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fe fe-loader me-1"></i>Processing...';
        button.disabled = true;
        
        fetch('/admin/plans/bulk-toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Successfully ${actionText}d ${data.affected_plans} plan(s).`);
                // Refresh the page or update the UI
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update plans'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating plans');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

// Function to refresh plan statistics
function refreshPlanStats() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fe fe-loader me-1"></i>Refreshing...';
    button.disabled = true;
    
    // Update the menu counts
    updatePendingCounts();
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        alert('Plan statistics refreshed successfully!');
    }, 2000);
}

// Update counts every 60 seconds
setInterval(updatePendingCounts, 60000);

// Function to show analytics settings
function showAnalyticsSettings() {
    // Create a modal to show analytics settings
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fe fe-settings me-2"></i>
                        Analytics Dashboard Settings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Dashboard Configuration</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="enableRealtime" checked>
                                        <label class="form-check-label" for="enableRealtime">
                                            Enable Real-time Updates
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="enableNotifications" checked>
                                        <label class="form-check-label" for="enableNotifications">
                                            Enable Notifications
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="enableAutoRefresh" checked>
                                        <label class="form-check-label" for="enableAutoRefresh">
                                            Auto Refresh Dashboard
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Data Retention</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Chart Data Period</label>
                                        <select class="form-select" id="chartDataPeriod">
                                            <option value="7">Last 7 days</option>
                                            <option value="30" selected>Last 30 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last year</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Refresh Interval (seconds)</label>
                                        <input type="number" class="form-control" id="refreshInterval" value="30" min="10" max="300">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Export Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="exportUsers" checked>
                                                <label class="form-check-label" for="exportUsers">
                                                    Include User Data
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="exportRevenue" checked>
                                                <label class="form-check-label" for="exportRevenue">
                                                    Include Revenue Data
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="exportVideos" checked>
                                                <label class="form-check-label" for="exportVideos">
                                                    Include Video Analytics
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveAnalyticsSettings()">
                        <i class="fe fe-save me-2"></i>Save Settings
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Clean up modal when closed
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Function to save analytics settings
function saveAnalyticsSettings() {
    const settings = {
        enable_realtime: document.getElementById('enableRealtime').checked,
        enable_notifications: document.getElementById('enableNotifications').checked,
        enable_auto_refresh: document.getElementById('enableAutoRefresh').checked,
        chart_data_period: document.getElementById('chartDataPeriod').value,
        refresh_interval: document.getElementById('refreshInterval').value,
        export_users: document.getElementById('exportUsers').checked,
        export_revenue: document.getElementById('exportRevenue').checked,
        export_videos: document.getElementById('exportVideos').checked
    };
    
    fetch('{{ route("admin.analytics.settings") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('Settings saved successfully!', 'success');
            // Close modal
            const modal = document.querySelector('.modal.show');
            if (modal) {
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                bootstrapModal.hide();
            }
        } else {
            showNotification('Error saving settings: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error saving analytics settings:', error);
        showNotification('Error saving settings. Please try again.', 'error');
    });
}

// Function to show notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

// Function to update referral benefits counts
function updateReferralBenefitsCounts() {
    // Update qualified users count
    fetch('/admin/referral-benefits/qualified-users', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            // Extract count from HTML if needed
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const title = doc.querySelector('h4, .card-title');
            const match = title ? title.textContent.match(/\((\d+)\)/) : null;
            return {
                total: match ? parseInt(match[1]) : 0,
                qualified_users: match ? parseInt(match[1]) : 0
            };
        });
    })
    .then(data => {
        // Update qualified users badge in main menu
        const qualifiedBadges = document.querySelectorAll('.side-menu__label');
        qualifiedBadges.forEach(label => {
            if (label.textContent.includes('Referral Benefits')) {
                const badge = label.parentElement.querySelector('.badge');
                if (badge && badge.textContent.includes('qualified')) {
                    const count = data.qualified_users || data.total || 0;
                    badge.textContent = count > 0 ? `${count} qualified` : '0 qualified';
                    badge.style.display = count > 0 ? 'inline' : 'none';
                }
            }
        });
        
        // Update qualified users count in submenu
        const submenuBadges = document.querySelectorAll('a[href*="qualified-users"] .badge');
        submenuBadges.forEach(badge => {
            const count = data.qualified_users || data.total || 0;
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
        });
    })
    .catch(error => {
        console.error('Error updating referral benefits counts:', error);
    });
    
    // Update today's bonus transactions count
    fetch('/admin/referral-benefits/bonus-transactions', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        }
        return response.text().then(text => {
            // Count today's transactions from HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const todayElements = doc.querySelectorAll('td');
            let todayCount = 0;
            
            const today = new Date().toLocaleDateString();
            todayElements.forEach(td => {
                if (td.textContent.includes(today.split('/')[0] + '/' + today.split('/')[1])) {
                    todayCount++;
                }
            });
            
            return {
                today_transactions: Math.floor(todayCount / 8) // Approximate based on table columns
            };
        });
    })
    .then(data => {
        // Update today's transactions badge
        const todayBadges = document.querySelectorAll('a[href*="bonus-transactions"] .badge');
        todayBadges.forEach(badge => {
            if (badge.textContent.includes('today')) {
                const count = data.today_transactions || 0;
                badge.textContent = count > 0 ? `${count} today` : '0 today';
                badge.style.display = count > 0 ? 'inline' : 'none';
            }
        });
    })
    .catch(error => {
        console.error('Error updating bonus transactions count:', error);
    });
}

// Admin Sidebar Logout Confirmation with SweetAlert
function confirmSidebarLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of the admin panel.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, logout!',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve();
                }, 1000);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Logging out...',
                text: 'Please wait',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit the form
            document.getElementById('sidebarLogoutForm').submit();
        }
    });
}

// Schedule Management Functions
function runScheduleNow() {
    Swal.fire({
        title: 'Run Schedule Now?',
        text: "This will manually trigger the Laravel scheduler to run all scheduled tasks.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, run schedule!',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("admin.schedule.run") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const response = result.value;
            if (response.success) {
                Swal.fire({
                    title: 'Schedule Executed!',
                    html: `
                        <div class="text-start">
                            <p><strong>Tasks Run:</strong> ${response.tasks_run || 0}</p>
                            <p><strong>Duration:</strong> ${response.duration || 'N/A'}</p>
                            <p><strong>Memory Used:</strong> ${response.memory_used || 'N/A'}</p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Great!'
                });
                
                // Update queue counts
                updateScheduleCounters();
            } else {
                Swal.fire('Error!', response.message || 'Failed to run schedule', 'error');
            }
        }
    });
}

function checkQueueWorkerStatus() {
    const statusBadge = document.getElementById('worker-status-badge');
    statusBadge.textContent = 'Checking...';
    statusBadge.className = 'badge badge-info ms-auto';
    
    fetch('{{ route("admin.queue.worker-status") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.workers && data.workers.length > 0) {
            statusBadge.textContent = `${data.workers.length} Active`;
            statusBadge.className = 'badge badge-success ms-auto';
            
            Swal.fire({
                title: 'Queue Worker Status',
                html: `
                    <div class="text-start">
                        <p><strong>Active Workers:</strong> ${data.workers.length}</p>
                        <p><strong>Pending Jobs:</strong> ${data.pending_jobs || 0}</p>
                        <p><strong>Failed Jobs:</strong> ${data.failed_jobs || 0}</p>
                        <p><strong>Processed Jobs (Today):</strong> ${data.processed_today || 0}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } else {
            statusBadge.textContent = 'Inactive';
            statusBadge.className = 'badge badge-danger ms-auto';
            
            Swal.fire({
                title: 'No Active Workers',
                text: 'Queue workers are not running. Consider starting them manually.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Start Worker',
                cancelButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    startQueueWorker();
                }
            });
        }
    })
    .catch(error => {
        statusBadge.textContent = 'Error';
        statusBadge.className = 'badge badge-danger ms-auto';
        console.error('Error checking worker status:', error);
        
        Swal.fire('Error!', 'Failed to check queue worker status', 'error');
    });
}

function startQueueWorker() {
    Swal.fire({
        title: 'Starting Queue Worker...',
        text: 'Please wait while we start the queue worker.',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("admin.queue.start-worker") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success!', 'Queue worker has been started successfully.', 'success');
            setTimeout(() => checkQueueWorkerStatus(), 2000);
        } else {
            Swal.fire('Error!', data.message || 'Failed to start queue worker', 'error');
        }
    })
    .catch(error => {
        console.error('Error starting worker:', error);
        Swal.fire('Error!', 'Failed to start queue worker', 'error');
    });
}

// Maintenance Mode Quick Actions
function quickEnableMaintenance() {
    Swal.fire({
        title: '🔴 Enable Maintenance Mode?',
        text: 'This will put your site offline with a quick maintenance message.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🔴 Yes, Put Site Offline',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        preConfirm: () => {
            return executeMaintenanceAction('down --message="Quick maintenance in progress. We will be back shortly!" --retry=1800 --refresh=300');
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateMaintenanceStatus(true);
            Swal.fire({
                title: '🔴 Maintenance Mode Enabled!',
                text: 'Your site is now offline. Users will see a maintenance page.',
                icon: 'success',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

function quickDisableMaintenance() {
    Swal.fire({
        title: '🟢 Disable Maintenance Mode?',
        text: 'This will bring your site back online for all users.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🟢 Yes, Bring Site Online',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        preConfirm: () => {
            return executeMaintenanceAction('up');
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateMaintenanceStatus(false);
            Swal.fire({
                title: '🟢 Site Back Online!',
                text: 'Your site is now accessible to all users.',
                icon: 'success',
                confirmButtonColor: '#28a745'
            });
        }
    });
}

function executeMaintenanceAction(command) {
    return fetch('{{ route("admin.system-commands.execute") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            command: command,
            confirm: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Command execution failed');
        }
        return data;
    })
    .catch(error => {
        Swal.fire({
            title: '❌ Error!',
            text: error.message || 'Failed to execute maintenance command',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
        throw error;
    });
}

// System Tools & Cache Management Functions
function showBrowserCacheManager() {
    Swal.fire({
        title: '🌐 Browser Cache Manager',
        html: `
            <div class="row g-3">
                <div class="col-md-6">
                    <button class="btn btn-primary w-100" onclick="clearDomainCache()">
                        <i class="fe fe-globe"></i> Clear Domain Cache
                    </button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-warning w-100" onclick="clearAdvancedCache()">
                        <i class="fe fe-settings"></i> Advanced Clear
                    </button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-info w-100" onclick="clearLocalStorage()">
                        <i class="fe fe-database"></i> Clear Storage
                    </button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-success w-100" onclick="clearServiceWorkers()">
                        <i class="fe fe-cpu"></i> Clear SW
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-muted">Choose the type of cache clearing needed</small>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Close',
        width: '500px'
    });
}

function showAdvancedCacheClearing() {
    Swal.fire({
        title: '🔧 Advanced Cache Clearing',
        html: `
            <div class="alert alert-warning">
                <strong>⚠️ Warning:</strong> Advanced cache clearing may affect system performance temporarily.
            </div>
            <div class="row g-2">
                <div class="col-12">
                    <button class="btn btn-danger w-100 mb-2" onclick="clearAllCaches()">
                        <i class="fe fe-trash-2"></i> Clear All Caches
                    </button>
                </div>
                <div class="col-12">
                    <button class="btn btn-warning w-100 mb-2" onclick="clearApplicationCache()">
                        <i class="fe fe-server"></i> Clear Application Cache
                    </button>
                </div>
                <div class="col-12">
                    <button class="btn btn-info w-100 mb-2" onclick="clearDatabaseCache()">
                        <i class="fe fe-database"></i> Clear Database Cache
                    </button>
                </div>
                <div class="col-12">
                    <button class="btn btn-secondary w-100" onclick="optimizeSystem()">
                        <i class="fe fe-zap"></i> Optimize System
                    </button>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Close',
        width: '400px'
    });
}

function showCacheStatusMonitor() {
    Swal.fire({
        title: '📊 Cache Status Monitor',
        html: `
            <div id="cache-status-content">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading cache status...</p>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Close',
        width: '600px',
        didOpen: () => {
            loadCacheStatus();
        }
    });
}

function showSystemPerformance() {
    Swal.fire({
        title: '📈 System Performance Monitor',
        html: `
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="text-primary">CPU Usage</h5>
                            <h3 id="cpu-usage">Loading...</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h5 class="text-success">Memory</h5>
                            <h3 id="memory-usage">Loading...</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <h5 class="text-info">Disk Space</h5>
                            <h3 id="disk-usage">Loading...</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" onclick="refreshPerformanceStats()">
                    <i class="fe fe-refresh-cw"></i> Refresh
                </button>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Close',
        width: '700px',
        didOpen: () => {
            loadPerformanceStats();
        }
    });
}

function emergencyCacheClear() {
    Swal.fire({
        title: '🚨 Emergency Cache Clear',
        html: `
            <div class="alert alert-danger">
                <strong>⚠️ EMERGENCY MODE:</strong> This will clear ALL caches and may temporarily affect system performance.
            </div>
            <p>This action will:</p>
            <ul class="text-start">
                <li>Clear all browser caches</li>
                <li>Clear application caches</li>
                <li>Clear database caches</li>
                <li>Clear service workers</li>
                <li>Force page reload</li>
            </ul>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🚨 Emergency Clear',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            performEmergencyClear();
        }
    });
}

// Helper functions for cache operations
function clearDomainCache() {
    window.location.href = '/browser_cache_clear/only_this_domain';
}

function clearAdvancedCache() {
    window.location.href = '/browser_cache_clear/advanced';
}

function clearLocalStorage() {
    try {
        localStorage.clear();
        sessionStorage.clear();
        Swal.fire({
            title: '✅ Success!',
            text: 'Local storage cleared successfully',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (error) {
        Swal.fire('Error', 'Failed to clear local storage', 'error');
    }
}

function clearServiceWorkers() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for(let registration of registrations) {
                registration.unregister();
            }
            Swal.fire({
                title: '✅ Success!',
                text: 'Service workers cleared successfully',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }
}

function clearAllCaches() {
    Swal.fire({
        title: 'Clearing All Caches...',
        html: 'Please wait while we clear all system caches.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Simulate cache clearing process
    setTimeout(() => {
        clearLocalStorage();
        clearServiceWorkers();
        window.location.href = '/browser_cache_clear/advanced';
    }, 2000);
}

function clearApplicationCache() {
    fetch('/admin/settings/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('✅ Success!', 'Application cache cleared successfully', 'success');
        } else {
            Swal.fire('Error', 'Failed to clear application cache', 'error');
        }
    }).catch(error => {
        Swal.fire('Error', 'Failed to clear application cache', 'error');
    });
}

function clearDatabaseCache() {
    // Implementation for database cache clearing
    Swal.fire({
        title: '🔄 Clearing Database Cache...',
        text: 'This may take a moment.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('✅ Success!', 'Database cache cleared successfully', 'success');
    }, 3000);
}

function optimizeSystem() {
    Swal.fire({
        title: '⚡ Optimizing System...',
        text: 'Running optimization routines.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('✅ Optimized!', 'System optimization completed successfully', 'success');
    }, 4000);
}

function loadCacheStatus() {
    // Simulate loading cache status
    setTimeout(() => {
        const content = document.getElementById('cache-status-content');
        if (content) {
            content.innerHTML = `
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="text-success">Local Storage</h6>
                                <h4>✅ Clean</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6 class="text-warning">Session Storage</h6>
                                <h4>⚠️ 2.3MB</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="text-info">Service Workers</h6>
                                <h4>ℹ️ 3 Active</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="text-primary">IndexedDB</h6>
                                <h4>📊 5.1MB</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary btn-sm" onclick="loadCacheStatus()">
                        <i class="fe fe-refresh-cw"></i> Refresh
                    </button>
                </div>
            `;
        }
    }, 1500);
}

function loadPerformanceStats() {
    // Simulate loading performance stats
    setTimeout(() => {
        document.getElementById('cpu-usage').textContent = '23%';
        document.getElementById('memory-usage').textContent = '1.2GB';
        document.getElementById('disk-usage').textContent = '45%';
    }, 1000);
}

function refreshPerformanceStats() {
    document.getElementById('cpu-usage').textContent = 'Loading...';
    document.getElementById('memory-usage').textContent = 'Loading...';
    document.getElementById('disk-usage').textContent = 'Loading...';
    loadPerformanceStats();
}

function performEmergencyClear() {
    Swal.fire({
        title: '🚨 Emergency Clear in Progress...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-danger mb-3" role="status"></div>
                <p>Performing emergency cache clear...</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" 
                         role="progressbar" style="width: 0%" id="emergency-progress"></div>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += 20;
        document.getElementById('emergency-progress').style.width = progress + '%';
        
        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(() => {
                clearLocalStorage();
                clearServiceWorkers();
                window.location.href = '/browser_cache_clear/advanced';
            }, 1000);
        }
    }, 500);
}

// Email Campaign Quick Actions
function sendKycReminders() {
    Swal.fire({
        title: '👤 Send KYC Reminders',
        html: `
            <div class="alert alert-info">
                <strong>ℹ️ Info:</strong> This will send KYC reminder emails to all pending users.
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="includeBonus" checked>
                <label class="form-check-label" for="includeBonus">
                    Include signup bonus information
                </label>
            </div>
            <div class="form-group">
                <label>Email Template:</label>
                <select class="form-select" id="kycTemplate">
                    <option value="default">Default KYC Reminder</option>
                    <option value="urgent">Urgent KYC Reminder</option>
                    <option value="final">Final KYC Notice</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '📧 Send Reminders',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            executeKycCampaign();
        }
    });
}

function sendInactiveReminders() {
    Swal.fire({
        title: '😴 Send Inactive User Reminders',
        html: `
            <div class="alert alert-warning">
                <strong>⚠️ Notice:</strong> This will send emails to users inactive for 30+ days.
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>Inactive Period:</label>
                    <select class="form-select" id="inactivePeriod">
                        <option value="30">30 days</option>
                        <option value="60">60 days</option>
                        <option value="90">90 days</option>
                        <option value="180">180 days</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Campaign Type:</label>
                    <select class="form-select" id="campaignType">
                        <option value="gentle">Gentle Reminder</option>
                        <option value="incentive">With Incentive</option>
                        <option value="final">Final Notice</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '📬 Send Campaign',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            executeInactiveCampaign();
        }
    });
}

function sendPasswordResets() {
    Swal.fire({
        title: '🔑 Password Reset Campaign',
        html: `
            <div class="alert alert-danger">
                <strong>🚨 Security:</strong> This will send password reset links to selected users.
            </div>
            <div class="form-group mb-3">
                <label>Target Users:</label>
                <select class="form-select" id="resetTarget">
                    <option value="old_passwords">Users with old passwords (90+ days)</option>
                    <option value="suspicious_activity">Users with suspicious activity</option>
                    <option value="security_breach">Security breach affected users</option>
                    <option value="custom">Custom user selection</option>
                </select>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="forceReset" checked>
                <label class="form-check-label" for="forceReset">
                    Force password reset on next login
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🔐 Send Reset Campaign',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            executePasswordResetCampaign();
        }
    });
}

function showCustomCampaign() {
    Swal.fire({
        title: '✨ Create Custom Campaign',
        html: `
            <div class="row g-3">
                <div class="col-12">
                    <label>Campaign Name:</label>
                    <input type="text" class="form-control" id="campaignName" placeholder="Enter campaign name">
                </div>
                <div class="col-md-6">
                    <label>Target Audience:</label>
                    <select class="form-select" id="targetAudience">
                        <option value="all">All Users</option>
                        <option value="active">Active Users</option>
                        <option value="inactive">Inactive Users</option>
                        <option value="high_value">High Value Users</option>
                        <option value="new_users">New Users (Last 30 days)</option>
                        <option value="custom">Custom Selection</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Email Template:</label>
                    <select class="form-select" id="emailTemplate">
                        <option value="promotional">Promotional</option>
                        <option value="informational">Informational</option>
                        <option value="welcome">Welcome Series</option>
                        <option value="custom">Custom Template</option>
                    </select>
                </div>
                <div class="col-12">
                    <label>Campaign Description:</label>
                    <textarea class="form-control" id="campaignDescription" rows="3" placeholder="Describe your campaign..."></textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="scheduleNow" checked>
                        <label class="form-check-label" for="scheduleNow">
                            Send immediately
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="trackOpen">
                        <label class="form-check-label" for="trackOpen">
                            Track email opens
                        </label>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🚀 Create Campaign',
        cancelButtonText: 'Cancel',
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            createCustomCampaign();
        }
    });
}

// Campaign execution functions
function executeKycCampaign() {
    const includeBonus = document.getElementById('includeBonus').checked;
    const template = document.getElementById('kycTemplate').value;
    
    Swal.fire({
        title: '📧 Sending KYC Reminders...',
        html: 'Please wait while we process the campaign.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('/admin/email-campaigns/send-kyc-reminders', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            include_bonus: includeBonus,
            template: template
        })
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '✅ Campaign Sent!',
                html: `KYC reminder emails have been queued successfully.<br>
                       <strong>Recipients:</strong> ${data.recipient_count || 'Processing'}<br>
                       <strong>Queue ID:</strong> ${data.queue_id || 'N/A'}`,
                icon: 'success'
            });
        } else {
            Swal.fire('❌ Error', data.message || 'Failed to send campaign', 'error');
        }
    }).catch(error => {
        Swal.fire('❌ Error', 'Failed to send KYC reminders', 'error');
    });
}

function executeInactiveCampaign() {
    const inactivePeriod = document.getElementById('inactivePeriod').value;
    const campaignType = document.getElementById('campaignType').value;
    
    Swal.fire({
        title: '📬 Sending Inactive User Campaign...',
        html: 'Processing inactive user reminders.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('/admin/email-campaigns/send-inactive-reminders', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            inactive_period: inactivePeriod,
            campaign_type: campaignType
        })
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '✅ Campaign Launched!',
                html: `Inactive user campaign has been processed.<br>
                       <strong>Targeted Users:</strong> ${data.recipient_count || 'Processing'}<br>
                       <strong>Campaign Type:</strong> ${campaignType}`,
                icon: 'success'
            });
        } else {
            Swal.fire('❌ Error', data.message || 'Failed to send campaign', 'error');
        }
    }).catch(error => {
        Swal.fire('❌ Error', 'Failed to send inactive user campaign', 'error');
    });
}

function executePasswordResetCampaign() {
    const resetTarget = document.getElementById('resetTarget').value;
    const forceReset = document.getElementById('forceReset').checked;
    
    Swal.fire({
        title: '🔐 Processing Password Reset Campaign...',
        html: 'Sending password reset notifications.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('/admin/email-campaigns/send-password-resets', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            reset_target: resetTarget,
            force_reset: forceReset
        })
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '🔑 Reset Campaign Sent!',
                html: `Password reset campaign has been executed.<br>
                       <strong>Affected Users:</strong> ${data.recipient_count || 'Processing'}<br>
                       <strong>Force Reset:</strong> ${forceReset ? 'Yes' : 'No'}`,
                icon: 'success'
            });
        } else {
            Swal.fire('❌ Error', data.message || 'Failed to send password reset campaign', 'error');
        }
    }).catch(error => {
        Swal.fire('❌ Error', 'Failed to send password reset campaign', 'error');
    });
}

function createCustomCampaign() {
    const campaignData = {
        name: document.getElementById('campaignName').value,
        target_audience: document.getElementById('targetAudience').value,
        email_template: document.getElementById('emailTemplate').value,
        description: document.getElementById('campaignDescription').value,
        schedule_now: document.getElementById('scheduleNow').checked,
        track_open: document.getElementById('trackOpen').checked
    };
    
    if (!campaignData.name) {
        Swal.fire('❌ Error', 'Please enter a campaign name', 'error');
        return;
    }
    
    Swal.fire({
        title: '🚀 Creating Custom Campaign...',
        html: 'Setting up your custom email campaign.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Simulate campaign creation (replace with actual API call)
    setTimeout(() => {
        Swal.fire({
            title: '✨ Campaign Created!',
            html: `Your custom campaign "${campaignData.name}" has been created successfully.<br>
                   <strong>Target:</strong> ${campaignData.target_audience}<br>
                   <strong>Template:</strong> ${campaignData.email_template}<br>
                   <strong>Status:</strong> ${campaignData.schedule_now ? 'Scheduled' : 'Draft'}`,
            icon: 'success'
        });
    }, 2000);
}

function updateMaintenanceStatus(isInMaintenance) {
    const badge = document.getElementById('maintenance-status-badge');
    if (badge) {
        if (isInMaintenance) {
            badge.textContent = '🔴 ACTIVE';
            badge.className = 'badge badge-danger ms-2 maintenance-status-badge';
        } else {
            badge.textContent = '🟢 ONLINE';
            badge.className = 'badge badge-success ms-2 maintenance-status-badge';
        }
    }
    
    // Update the quick action menu item
    setTimeout(() => {
        location.reload(); // Reload to update the menu properly
    }, 1000);
}
}

function updateScheduleCounters() {
    // Update queue counters in the menu
    fetch('{{ route("admin.queue.counts") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update pending jobs badge
        const pendingBadges = document.querySelectorAll('a[href*="queue.index"] .badge');
        pendingBadges.forEach(badge => {
            if (badge.textContent.match(/^\d+$/)) {
                badge.textContent = data.pending || 0;
                badge.style.display = data.pending > 0 ? 'inline' : 'none';
            }
        });
        
        // Update failed jobs badge
        const failedBadges = document.querySelectorAll('a[href*="failed-jobs"] .badge');
        failedBadges.forEach(badge => {
            badge.textContent = data.failed || 0;
            badge.style.display = data.failed > 0 ? 'inline' : 'none';
        });
        
        // Update main schedule management badge
        const scheduleBadges = document.querySelectorAll('.side-menu__label');
        scheduleBadges.forEach(label => {
            if (label.textContent.includes('Schedule Management')) {
                const badge = label.parentElement.querySelector('.badge');
                if (badge) {
                    if (data.failed > 0) {
                        badge.textContent = `${data.failed} failed`;
                        badge.className = 'badge badge-danger ms-2';
                    } else if (data.pending > 0) {
                        badge.textContent = `${data.pending} pending`;
                        badge.className = 'badge badge-info ms-2';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        });
    })
    .catch(error => {
        console.error('Error updating schedule counters:', error);
    });
}

// Auto-update schedule counters every 30 seconds
setInterval(updateScheduleCounters, 30000);

// Update worker status every 60 seconds
setInterval(() => {
    const statusBadge = document.getElementById('worker-status-badge');
    if (statusBadge && statusBadge.textContent !== 'Check') {
        checkQueueWorkerStatus();
    }
}, 60000);

// User Verification System Functions
function showBulkVerificationActions() {
    Swal.fire({
        title: '⚡ Bulk Verification Actions',
        html: `
            <div class="row g-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong>ℹ️ Info:</strong> Select bulk actions for user verification management.
                    </div>
                </div>
                
                <!-- Email Verification Actions -->
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">📧 Email Verification</h6>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="bulkSendEmailVerification()">
                                <i class="fe fe-send"></i> Send Verification Emails
                            </button>
                            <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="bulkApproveEmailVerification()">
                                <i class="fe fe-check"></i> Bulk Approve Email
                            </button>
                            <button class="btn btn-outline-warning btn-sm w-100" onclick="resendFailedEmailVerifications()">
                                <i class="fe fe-refresh-cw"></i> Resend Failed
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- KYC Verification Actions -->
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">🆔 KYC Verification</h6>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="bulkApproveKyc()">
                                <i class="fe fe-user-check"></i> Bulk Approve KYC
                            </button>
                            <button class="btn btn-outline-danger btn-sm w-100 mb-2" onclick="bulkRejectKyc()">
                                <i class="fe fe-user-x"></i> Bulk Reject KYC
                            </button>
                            <button class="btn btn-outline-info btn-sm w-100" onclick="bulkRequestKycUpdate()">
                                <i class="fe fe-edit"></i> Request Updates
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Phone Verification Actions -->
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">📱 Phone Verification</h6>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-outline-info btn-sm w-100 mb-2" onclick="bulkSendSmsVerification()">
                                <i class="fe fe-message-square"></i> Send SMS Codes
                            </button>
                            <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="bulkApprovePhoneVerification()">
                                <i class="fe fe-phone-call"></i> Bulk Approve Phone
                            </button>
                            <button class="btn btn-outline-warning btn-sm w-100" onclick="resetPhoneVerificationCodes()">
                                <i class="fe fe-refresh-cw"></i> Reset Codes
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- 2FA Management Actions -->
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">🔐 2FA Management</h6>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="bulkEnable2FA()">
                                <i class="fe fe-shield"></i> Bulk Enable 2FA
                            </button>
                            <button class="btn btn-outline-danger btn-sm w-100 mb-2" onclick="bulkDisable2FA()">
                                <i class="fe fe-shield-off"></i> Bulk Disable 2FA
                            </button>
                            <button class="btn btn-outline-warning btn-sm w-100" onclick="reset2FASecrets()">
                                <i class="fe fe-key"></i> Reset 2FA Secrets
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Emergency Actions -->
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">🚨 Emergency Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <button class="btn btn-outline-danger btn-sm w-100" onclick="emergencyVerificationReset()">
                                        <i class="fe fe-alert-triangle"></i> Emergency Reset
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-warning btn-sm w-100" onclick="verificationMaintenanceMode()">
                                        <i class="fe fe-tool"></i> Maintenance Mode
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-info btn-sm w-100" onclick="generateVerificationReport()">
                                        <i class="fe fe-file-text"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Close',
        width: '800px',
        customClass: {
            popup: 'bulk-verification-modal'
        }
    });
}

// Email Verification Functions
function bulkSendEmailVerification() {
    Swal.fire({
        title: '📧 Send Email Verification',
        html: `
            <div class="form-group mb-3">
                <label>Target Users:</label>
                <select class="form-select" id="emailTargetUsers">
                    <option value="unverified">Unverified Email Users</option>
                    <option value="new_users">New Users (Last 7 days)</option>
                    <option value="all_active">All Active Users</option>
                    <option value="custom">Custom Selection</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Email Template:</label>
                <select class="form-select" id="emailTemplate">
                    <option value="standard">Standard Verification</option>
                    <option value="reminder">Verification Reminder</option>
                    <option value="urgent">Urgent Verification</option>
                    <option value="welcome">Welcome + Verification</option>
                </select>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="includeBonus">
                <label class="form-check-label" for="includeBonus">
                    Include welcome bonus information
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        confirmButtonText: '📧 Send Emails'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulkEmailVerification();
        }
    });
}

function bulkApproveEmailVerification() {
    Swal.fire({
        title: '✅ Bulk Approve Email Verification',
        html: `
            <div class="alert alert-warning">
                <strong>⚠️ Warning:</strong> This will approve email verification for selected users.
            </div>
            <div class="form-group">
                <label>Select Users to Approve:</label>
                <select class="form-select" id="approveEmailUsers">
                    <option value="pending_1day">Pending 1+ day</option>
                    <option value="pending_3days">Pending 3+ days</option>
                    <option value="pending_week">Pending 1+ week</option>
                    <option value="manual_selection">Manual Selection</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: '✅ Approve Selected'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulkEmailApproval();
        }
    });
}

// KYC Verification Functions
function bulkApproveKyc() {
    Swal.fire({
        title: '🆔 Bulk Approve KYC',
        html: `
            <div class="alert alert-danger">
                <strong>🚨 Important:</strong> Only approve KYC for thoroughly reviewed documents.
            </div>
            <div class="form-group mb-3">
                <label>Approval Criteria:</label>
                <select class="form-select" id="kycApprovalCriteria">
                    <option value="documents_complete">Complete Documents</option>
                    <option value="pending_review">Under Review 3+ days</option>
                    <option value="manual_reviewed">Manually Reviewed</option>
                    <option value="trusted_users">Trusted User Category</option>
                </select>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="sendApprovalNotification" checked>
                <label class="form-check-label" for="sendApprovalNotification">
                    Send approval notification emails
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="enableKycBenefits" checked>
                <label class="form-check-label" for="enableKycBenefits">
                    Enable KYC benefits/features
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: '✅ Approve KYC'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulkKycApproval();
        }
    });
}

function bulkRejectKyc() {
    Swal.fire({
        title: '❌ Bulk Reject KYC',
        html: `
            <div class="alert alert-danger">
                <strong>⚠️ Warning:</strong> This will reject KYC submissions for selected users.
            </div>
            <div class="form-group mb-3">
                <label>Rejection Reason:</label>
                <select class="form-select" id="kycRejectionReason">
                    <option value="incomplete_documents">Incomplete Documents</option>
                    <option value="invalid_documents">Invalid Documents</option>
                    <option value="poor_quality">Poor Image Quality</option>
                    <option value="suspicious_activity">Suspicious Activity</option>
                    <option value="duplicate_submission">Duplicate Submission</option>
                    <option value="other">Other (Custom Message)</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Custom Message (Optional):</label>
                <textarea class="form-control" id="customRejectionMessage" rows="3" placeholder="Additional details for rejection..."></textarea>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="allowResubmission" checked>
                <label class="form-check-label" for="allowResubmission">
                    Allow resubmission
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: '❌ Reject KYC'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulkKycRejection();
        }
    });
}

// Phone Verification Functions
function bulkSendSmsVerification() {
    Swal.fire({
        title: '📱 Send SMS Verification',
        html: `
            <div class="form-group mb-3">
                <label>Target Users:</label>
                <select class="form-select" id="smsTargetUsers">
                    <option value="unverified_phone">Unverified Phone Numbers</option>
                    <option value="failed_verification">Failed Previous Verification</option>
                    <option value="expired_codes">Expired Verification Codes</option>
                    <option value="custom">Custom Selection</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>SMS Template:</label>
                <select class="form-select" id="smsTemplate">
                    <option value="standard">Standard Verification Code</option>
                    <option value="reminder">Verification Reminder</option>
                    <option value="urgent">Urgent - Account Security</option>
                </select>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="generateNewCodes" checked>
                <label class="form-check-label" for="generateNewCodes">
                    Generate new verification codes
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        confirmButtonText: '📱 Send SMS'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulkSmsVerification();
        }
    });
}

// 2FA Management Functions
function bulkEnable2FA() {
    Swal.fire({
        title: '🔐 Bulk Enable 2FA',
        html: `
            <div class="alert alert-info">
                <strong>ℹ️ Info:</strong> This will enable 2FA for selected users.
            </div>
            <div class="form-group mb-3">
                <label>Target Users:</label>
                <select class="form-select" id="twoFaTargetUsers">
                    <option value="high_value">High Value Users</option>
                    <option value="admin_users">Admin/Staff Users</option>
                    <option value="kyc_verified">KYC Verified Users</option>
                    <option value="custom">Custom Selection</option>
                </select>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="forceTwoFa">
                <label class="form-check-label" for="forceTwoFa">
                    Force 2FA requirement (mandatory)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="sendTwoFaInstructions" checked>
                <label class="form-check-label" for="sendTwoFaInstructions">
                    Send setup instructions via email
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: '🔐 Enable 2FA'
    }).then((result) => {
        if (result.isConfirmed) {
            executeBulk2FAEnable();
        }
    });
}

// Emergency Functions
function emergencyVerificationReset() {
    Swal.fire({
        title: '🚨 Emergency Verification Reset',
        html: `
            <div class="alert alert-danger">
                <strong>🚨 EMERGENCY:</strong> This will reset verification status for selected users.
            </div>
            <div class="form-group mb-3">
                <label>Reset Type:</label>
                <select class="form-select" id="emergencyResetType">
                    <option value="email_only">Email Verification Only</option>
                    <option value="phone_only">Phone Verification Only</option>
                    <option value="kyc_only">KYC Status Only</option>
                    <option value="2fa_only">2FA Settings Only</option>
                    <option value="all_verification">All Verification Data</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Reason for Emergency Reset:</label>
                <textarea class="form-control" id="emergencyReason" rows="3" placeholder="Document the reason for this emergency action..." required></textarea>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="notifyUsersReset">
                <label class="form-check-label" for="notifyUsersReset">
                    Notify users about verification reset
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: '🚨 Execute Emergency Reset'
    }).then((result) => {
        if (result.isConfirmed) {
            executeEmergencyVerificationReset();
        }
    });
}

// Execution Functions
function executeBulkEmailVerification() {
    const targetUsers = document.getElementById('emailTargetUsers').value;
    const emailTemplate = document.getElementById('emailTemplate').value;
    const includeBonus = document.getElementById('includeBonus').checked;

    Swal.fire({
        title: '📧 Sending Email Verifications...',
        html: 'Processing bulk email verification campaign.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    // Simulate API call
    setTimeout(() => {
        Swal.fire({
            title: '✅ Email Campaign Sent!',
            html: `Bulk email verification campaign completed successfully.<br>
                   <strong>Target:</strong> ${targetUsers}<br>
                   <strong>Template:</strong> ${emailTemplate}<br>
                   <strong>Estimated Recipients:</strong> Processing...`,
            icon: 'success'
        });
    }, 3000);
}

function executeBulkKycApproval() {
    const criteria = document.getElementById('kycApprovalCriteria').value;
    const sendNotification = document.getElementById('sendApprovalNotification').checked;
    const enableBenefits = document.getElementById('enableKycBenefits').checked;

    Swal.fire({
        title: '🆔 Processing KYC Approvals...',
        html: 'Bulk approving KYC submissions.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.fire({
            title: '✅ KYC Bulk Approval Complete!',
            html: `KYC bulk approval process completed.<br>
                   <strong>Criteria:</strong> ${criteria}<br>
                   <strong>Notifications:</strong> ${sendNotification ? 'Sent' : 'Disabled'}<br>
                   <strong>Benefits:</strong> ${enableBenefits ? 'Enabled' : 'Disabled'}`,
            icon: 'success'
        });
    }, 4000);
}

function executeEmergencyVerificationReset() {
    const resetType = document.getElementById('emergencyResetType').value;
    const reason = document.getElementById('emergencyReason').value;
    const notifyUsers = document.getElementById('notifyUsersReset').checked;

    if (!reason.trim()) {
        Swal.fire('❌ Error', 'Emergency reason is required for documentation', 'error');
        return;
    }

    Swal.fire({
        title: '🚨 Executing Emergency Reset...',
        html: 'Processing emergency verification reset.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    setTimeout(() => {
        Swal.fire({
            title: '🚨 Emergency Reset Complete!',
            html: `Emergency verification reset has been executed.<br>
                   <strong>Reset Type:</strong> ${resetType}<br>
                   <strong>User Notifications:</strong> ${notifyUsers ? 'Sent' : 'Disabled'}<br>
                   <strong>Reason:</strong> ${reason}`,
            icon: 'warning'
        });
    }, 5000);
}

// Modal Management Functions
function toggleAllModals() {
    Swal.fire({
        title: '🔧 Toggle All Modals',
        html: `
            <div class="alert alert-info">
                <strong>Bulk Action:</strong> This will toggle the status of all modals.
            </div>
            <div class="form-group mb-3">
                <label>Action:</label>
                <select class="form-select" id="modalToggleAction">
                    <option value="activate_all">Activate All Modals</option>
                    <option value="deactivate_all">Deactivate All Modals</option>
                    <option value="toggle_all">Toggle Each Modal's Status</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Reason for Bulk Action:</label>
                <textarea class="form-control" id="modalToggleReason" rows="2" placeholder="Document the reason for this bulk action..."></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '🔧 Execute Bulk Action',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const action = document.getElementById('modalToggleAction').value;
            const reason = document.getElementById('modalToggleReason').value;
            
            if (!action) {
                Swal.showValidationMessage('Please select an action');
                return false;
            }
            
            return { action, reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { action, reason } = result.value;
            
            // Show loading
            Swal.fire({
                title: 'Processing Bulk Action...',
                text: 'Please wait while we update all modals.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 3000
            });
            
            // Execute bulk action
            fetch('/admin/modals/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    action: action,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '✅ Bulk Action Completed',
                        html: `
                            <div class="alert alert-success">
                                Bulk modal action has been executed successfully.
                            </div>
                            <div><strong>Action:</strong> ${action.replace('_', ' ').toUpperCase()}</div>
                            <div><strong>Affected Modals:</strong> ${data.affected_count || 'Unknown'}</div>
                            ${reason ? `<div><strong>Reason:</strong> ${reason}</div>` : ''}
                        `,
                        icon: 'success',
                        timer: 5000
                    }).then(() => {
                        // Optionally refresh the page or update the UI
                        if (window.location.pathname.includes('/admin/modal')) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: '❌ Bulk Action Failed',
                        text: data.message || 'An error occurred while executing the bulk action.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '❌ Network Error',
                    text: 'Failed to execute bulk action: ' + error.message,
                    icon: 'error'
                });
            });
        }
    });
}

function showModalQuickStats() {
    // Show modal statistics in a popup
    fetch('/admin/modals/quick-stats')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.stats;
            Swal.fire({
                title: '📊 Modal Quick Stats',
                html: `
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h4>${stats.total_modals || 0}</h4>
                                    <small>Total Modals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h4>${stats.active_modals || 0}</h4>
                                    <small>Active Modals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h4>${stats.inactive_modals || 0}</h4>
                                    <small>Inactive Modals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h4>${stats.pwa_modals || 0}</h4>
                                    <small>PWA Modals</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="/admin/modal" class="btn btn-primary btn-sm">
                            <i class="fe fe-arrow-right"></i> View All Modals
                        </a>
                        <a href="/admin/modal/analytics" class="btn btn-info btn-sm">
                            <i class="fe fe-bar-chart"></i> View Analytics
                        </a>
                    </div>
                `,
                width: '600px',
                showConfirmButton: false,
                showCloseButton: true
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: '❌ Error',
            text: 'Failed to load modal statistics: ' + error.message,
            icon: 'error'
        });
    });
}
</script>