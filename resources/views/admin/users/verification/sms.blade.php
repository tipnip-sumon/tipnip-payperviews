@extends('components.layout')

@section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <div class="card rounded-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.users.verification.dashboard') }}" class="btn btn-primary btn-sm me-3">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
                </div>
            </div>
            <div class="card-body">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-6 col-lg-6 col-sm-6 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold h4 mb-1">{{ $stats['verified'] }}</div>
                                    <div class="small">SMS Verified Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-6 mb-3">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold h4 mb-1">{{ $stats['unverified'] }}</div>
                                    <div class="small">SMS Unverified Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Actions -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('admin.users.verification.sms') }}">
                            <div class="input-group">
                                <select name="status" class="form-select">
                                    <option value="">All Users</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Verified</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Unverified</option>
                                </select>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" onclick="bulkVerify('verify')">
                                <i class="fas fa-check"></i> Bulk Verify
                            </button>
                            <button type="button" class="btn btn-warning" onclick="bulkVerify('unverify')">
                                <i class="fas fa-times"></i> Bulk Unverify
                            </button>
                            <button type="button" class="btn btn-info" onclick="bulkSendSms()">
                                <i class="fas fa-sms"></i> Send Verification SMS
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card rounded-3">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                                </th>
                                                <th>User</th>
                                                <th>Mobile Number</th>
                                                <th>Status</th>
                                                <th>Verified At</th>
                                                <th>Joined</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($users as $user)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $user->firstname }} {{ $user->lastname }}</span><br>
                                                    <span class="small text-primary">{{ $user->username }}</span>
                                                </td>
                                                <td>
                                                    @if($user->mobile)
                                                        {{ $user->mobile }}
                                                    @else
                                                        <span class="text-muted">No mobile number</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->sv)
                                                        <span class="badge bg-success">Verified</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Unverified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->sms_verified_at)
                                                        {{ \Carbon\Carbon::parse($user->sms_verified_at)->format('M d, Y H:i') }}
                                                    @else
                                                        <span class="text-muted">Not verified</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if($user->sv)
                                                                <a class="dropdown-item" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'unverify')">
                                                                    <i class="fas fa-times text-warning"></i> Unverify SMS
                                                                </a>
                                                            @else
                                                                <a class="dropdown-item" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'verify')">
                                                                    <i class="fas fa-check text-success"></i> Verify SMS
                                                                </a>
                                                                @if($user->mobile)
                                                                <a class="dropdown-item" href="javascript:void(0)" onclick="sendVerificationSms({{ $user->id }})">
                                                                    <i class="fas fa-sms text-info"></i> Send Verification SMS
                                                                </a>
                                                                @endif
                                                            @endif
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                                <i class="fas fa-eye text-primary"></i> View Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No users found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($users->hasPages())
                            <div class="card-footer py-4">
                                {{ paginateLinks($users) }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    'use strict';

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    // Individual verification actions
    function changeVerification(userId, action) {
        let url = action === 'verify' 
            ? "{{ route('admin.users.verification.sms.verify', ':id') }}"
            : "{{ route('admin.users.verification.sms.unverify', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                notify('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Send verification SMS
    function sendVerificationSms(userId) {
        let url = "{{ route('admin.users.verification.send.sms', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                }
            },
            error: function(xhr) {
                notify('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Bulk operations
    function bulkVerify(action) {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            notify('error', 'Please select at least one user');
            return;
        }

        let url = action === 'verify' 
            ? "{{ route('admin.users.verification.bulk.verify') }}"
            : "{{ route('admin.users.verification.bulk.unverify') }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_ids: checkedUsers,
                verification_type: 'sms'
            },
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                notify('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Bulk send verification SMS
    function bulkSendSms() {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            notify('error', 'Please select at least one user');
            return;
        }

        // Send SMS one by one
        let sent = 0;
        checkedUsers.forEach(function(userId) {
            sendVerificationSms(userId);
            sent++;
        });

        notify('success', `Verification SMS sent to ${sent} users`);
    }
</script>
@endpush
