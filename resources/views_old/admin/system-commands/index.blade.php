<x-layout>
    @section('top_title', 'System Commands - Emergency Runner')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Emergency Command Runner')
            
            <!-- Header Stats -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Available Commands</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ collect($commands)->sum(fn($cat) => count($cat['commands'])) }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-primary my-auto float-end">
                                    <i class="fe fe-terminal"></i> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Recent Executions</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ count($recentExecutions) }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-success my-auto float-end">
                                    <i class="fe fe-activity"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">System Status</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">Online</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-warning my-auto float-end">
                                    <i class="fe fe-server"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Last Execution</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">
                                        @if(count($recentExecutions) > 0)
                                            {{ \Carbon\Carbon::parse($recentExecutions[0]['timestamp'])->diffForHumans() }}
                                        @else
                                            Never
                                        @endif
                                    </h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-info my-auto float-end">
                                    <i class="fe fe-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Safety Warning -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h6><i class="fe fe-alert-triangle"></i> Important Safety Notice</h6>
                    <p class="mb-2">
                        <strong>⚠️ Emergency Use Only:</strong> These commands are intended for emergency situations and system maintenance.
                        Please ensure you understand the implications before executing any command.
                    </p>
                    <ul class="mb-0">
                        <li><strong>Dangerous commands</strong> are marked with <span class="badge bg-danger">⚠️ Dangerous</span></li>
                        <li><strong>All executions are logged</strong> with your admin account information</li>
                        <li><strong>Some commands may take several minutes</strong> to complete</li>
                        <li><strong>Do not refresh the page</strong> while commands are running</li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Command Categories -->
        @foreach($commands as $categoryKey => $category)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="{{ $category['icon'] }} me-2"></i>
                                {{ $category['title'] }}
                            </h5>
                            <p class="text-muted mb-0">{{ count($category['commands']) }} commands available</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($category['commands'] as $command => $info)
                                    <div class="col-lg-6 col-xl-4 mb-3">
                                        <div class="card h-100 command-card {{ isset($info['maintenance']) && $info['maintenance'] ? 'maintenance-command' : 'border-left-primary' }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0 {{ isset($info['maintenance']) && $info['maintenance'] ? 'maintenance-title' : '' }}">
                                                        <i class="{{ $info['icon'] }} me-1"></i>
                                                        {{ $info['name'] }}
                                                    </h6>
                                                    @if(isset($info['maintenance']) && $info['maintenance'])
                                                        @if($info['action_type'] === 'enable')
                                                            <span class="badge bg-danger maintenance-badge">🔴 CRITICAL</span>
                                                        @else
                                                            <span class="badge bg-success maintenance-badge">🟢 RECOVERY</span>
                                                        @endif
                                                    @elseif($info['danger'])
                                                        <span class="badge bg-danger">⚠️ Dangerous</span>
                                                    @else
                                                        <span class="badge bg-success">✅ Safe</span>
                                                    @endif
                                                </div>
                                                <p class="card-text text-muted small mb-2 {{ isset($info['maintenance']) && $info['maintenance'] ? 'maintenance-description' : '' }}">{{ $info['description'] }}</p>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="fe fe-clock me-1"></i>
                                                        Est. time: {{ $info['estimated_time'] }}
                                                    </small>
                                                </div>
                                                <div class="mb-2">
                                                    <code class="bg-light text-dark p-1 rounded small">{{ $command }}</code>
                                                </div>
                                                <button type="button" 
                                                        class="btn btn-sm {{ isset($info['maintenance']) && $info['maintenance'] ? ($info['action_type'] === 'enable' ? 'btn-danger maintenance-btn-danger' : 'btn-success maintenance-btn-success') : ($info['danger'] ? 'btn-danger' : 'btn-primary') }} execute-command w-100"
                                                        data-command="{{ $command }}"
                                                        data-name="{{ $info['name'] }}"
                                                        data-danger="{{ $info['danger'] ? 'true' : 'false' }}"
                                                        data-maintenance="{{ isset($info['maintenance']) ? 'true' : 'false' }}"
                                                        data-action-type="{{ $info['action_type'] ?? 'normal' }}">
                                                    @if(isset($info['maintenance']) && $info['maintenance'])
                                                        @if($info['action_type'] === 'enable')
                                                            <i class="fe fe-power me-1"></i>🔴 ENABLE MAINTENANCE
                                                        @else
                                                            <i class="fe fe-check-circle me-1"></i>🟢 DISABLE MAINTENANCE
                                                        @endif
                                                    @else
                                                        <i class="fe fe-play me-1"></i>
                                                        Execute Command
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Recent Executions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                <i class="fe fe-activity me-2"></i>
                                Recent Command Executions
                            </h5>
                            <p class="text-muted mb-0">Last 10 command executions</p>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshExecutions()">
                            <i class="fe fe-refresh-cw"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="executionsTable">
                                <thead>
                                    <tr>
                                        <th>Command</th>
                                        <th>Status</th>
                                        <th>Execution Time</th>
                                        <th>Executed By</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentExecutions as $execution)
                                        <tr>
                                            <td>
                                                <code class="bg-light text-dark p-1 rounded">{{ $execution['command'] }}</code>
                                            </td>
                                            <td>
                                                @if($execution['success'])
                                                    <span class="badge bg-success">✅ Success</span>
                                                @else
                                                    <span class="badge bg-danger">❌ Failed ({{ $execution['exit_code'] }})</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $execution['execution_time'] }}s</span>
                                            </td>
                                            <td>
                                                <strong>{{ $execution['user_name'] }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ \Carbon\Carbon::parse($execution['timestamp'])->format('M d, Y H:i:s') }}</span>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($execution['timestamp'])->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fe fe-info me-1"></i>
                                                    No recent command executions found
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Command Execution Modal -->
        <div class="modal fade" id="executeModal" tabindex="-1" aria-labelledby="executeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="executeModalLabel">
                            <i class="fe fe-terminal me-2"></i>
                            Execute Command
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="fe fe-info me-1"></i> Command Information</h6>
                            <p class="mb-2"><strong>Command:</strong> <code id="modalCommand"></code></p>
                            <p class="mb-0"><strong>Description:</strong> <span id="modalDescription"></span></p>
                        </div>
                        
                        <div id="dangerWarning" class="alert alert-danger" style="display: none;">
                            <h6><i class="fe fe-alert-triangle me-1"></i> Danger Warning</h6>
                            <p class="mb-0">This is a potentially dangerous command that can affect your system. Please confirm you understand the implications.</p>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmExecution" required>
                            <label class="form-check-label" for="confirmExecution">
                                I understand this command will be executed on the live system and take full responsibility for the consequences.
                            </label>
                        </div>

                        <div id="executionOutput" style="display: none;">
                            <h6>Command Output:</h6>
                            <div class="bg-dark text-light p-3 rounded" style="font-family: monospace; white-space: pre-wrap; max-height: 300px; overflow-y: auto;" id="outputContent"></div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Exit Code: <span id="exitCode" class="fw-bold"></span> | 
                                    Execution Time: <span id="executionTime"></span>s
                                </small>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        Status: <span id="commandStatus"></span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div id="executionProgress" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                <span>Executing command... Please wait.</span>
                            </div>
                            <div class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="executeBtn" disabled>
                            <i class="fe fe-play me-1"></i>
                            Execute Command
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
        <script>
            $(document).ready(function() { 
                let currentCommand = '';
                let isDangerous = false;

                // Handle command execution button click
                $('.execute-command').on('click', function() {
                    currentCommand = $(this).data('command');
                    const commandName = $(this).data('name');
                    isDangerous = $(this).data('danger') === 'true';
                    const isMaintenance = $(this).data('maintenance') === 'true';
                    const actionType = $(this).data('action-type');

                    // Special handling for maintenance commands
                    if (isMaintenance) {
                        handleMaintenanceCommand(currentCommand, commandName, actionType);
                        return;
                    }

                    // Find command info
                    const commandInfo = findCommandInfo(currentCommand);

                    // Populate modal
                    $('#modalCommand').text(currentCommand);
                    $('#modalDescription').text(commandInfo ? commandInfo.description : 'No description available');

                    // Show/hide danger warning
                    if (isDangerous) {
                        $('#dangerWarning').show();
                    } else {
                        $('#dangerWarning').hide();
                    }

                    // Reset modal state
                    $('#confirmExecution').prop('checked', false);
                    $('#executeBtn').prop('disabled', true);
                    $('#executionOutput').hide();
                    $('#executionProgress').hide();

                    // Show modal
                    $('#executeModal').modal('show');
                });

                // Handle confirmation checkbox
                $('#confirmExecution').on('change', function() {
                    $('#executeBtn').prop('disabled', !$(this).is(':checked'));
                });

                // Handle execute button click
                $('#executeBtn').on('click', function() {
                    if (!$('#confirmExecution').is(':checked')) {
                        alert('Please confirm execution first.');
                        return;
                    }

                    executeCommand(currentCommand);
                });

                // Execute command function
                function executeCommand(command) {
                    $('#executeBtn').prop('disabled', true);
                    $('#executionProgress').show();
                    $('#executionOutput').hide();

                    $.ajax({
                        url: '{{ route("admin.system-commands.execute") }}',
                        method: 'POST',
                        data: {
                            command: command,
                            confirm: 1,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            displayExecutionResult(response);
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON || {
                                success: false,
                                message: 'Request failed',
                                output: 'Error: ' + xhr.statusText,
                                exit_code: xhr.status,
                                execution_time: 0
                            };
                            displayExecutionResult(response);
                        },
                        complete: function() {
                            $('#executionProgress').hide();
                            $('#executeBtn').prop('disabled', false);
                        }
                    });
                }

                // Display execution result
                function displayExecutionResult(response) {
                    console.log('Response received:', response); // Debug log
                    
                    $('#outputContent').text(response.output || 'No output');
                    
                    // Handle exit code display
                    let exitCode = response.exit_code;
                    if (exitCode === undefined || exitCode === null) {
                        exitCode = response.success ? 0 : 1; // Fallback based on success
                    }
                    $('#exitCode').text(exitCode);
                    
                    $('#executionTime').text(response.execution_time || '0');
                    $('#executionOutput').show();

                    // Show success/error message with better status indication
                    if (response.success || exitCode === 0) {
                        // Show success notification with SweetAlert2
                        Swal.fire({
                            title: 'Command Executed!',
                            text: response.message || 'Command executed successfully',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        // Update styling for success
                        $('#exitCode').removeClass('text-danger').addClass('text-success');
                        $('#commandStatus').html('<span class="badge bg-success">✅ Success</span>');
                    } else {
                        // Show error notification with SweetAlert2
                        Swal.fire({
                            title: 'Command Failed!',
                            text: response.message || 'Command execution failed',
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true
                        });
                        
                        // Update styling for failure
                        $('#exitCode').removeClass('text-success').addClass('text-danger');
                        $('#commandStatus').html('<span class="badge bg-danger">❌ Failed</span>');
                    }

                    // Refresh executions table
                    setTimeout(refreshExecutions, 1000);
                }

                // Find command info from available commands
                function findCommandInfo(command) {
                    const commands = @json($commands);
                    for (const category of Object.values(commands)) {
                        if (category.commands && category.commands[command]) {
                            return category.commands[command];
                        }
                    }
                    return null;
                }

                // Refresh executions table
                window.refreshExecutions = function() {
                    location.reload(); // Simple refresh for now
                };

                // Handle maintenance mode commands with special confirmation
                function handleMaintenanceCommand(command, commandName, actionType) {
                    const isEnable = actionType === 'enable';
                    const title = isEnable ? '🔴 Enable Maintenance Mode?' : '🟢 Disable Maintenance Mode?';
                    const text = isEnable 
                        ? 'This will put your site OFFLINE. Users will see a maintenance page.' 
                        : 'This will bring your site back ONLINE. Users will be able to access it normally.';
                    const confirmButtonText = isEnable ? '🔴 Yes, Put Site OFFLINE' : '🟢 Yes, Bring Site ONLINE';
                    const confirmButtonColor = isEnable ? '#dc3545' : '#28a745';
                    
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: isEnable ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonColor: confirmButtonColor,
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            popup: 'maintenance-modal',
                            title: 'maintenance-modal-title',
                            confirmButton: 'maintenance-confirm-btn',
                            cancelButton: 'maintenance-cancel-btn'
                        },
                        showLoaderOnConfirm: true,
                        allowOutsideClick: false,
                        preConfirm: () => {
                            return executeMaintenanceCommand(command);
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Success handled in executeMaintenanceCommand
                        }
                    });
                }

                // Execute maintenance command with enhanced feedback
                function executeMaintenanceCommand(command) {
                    return $.ajax({
                        url: '{{ route("admin.system-commands.execute") }}',
                        method: 'POST',
                        data: {
                            command: command,
                            confirm: 1,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    }).then(function(response) {
                        const isEnableCommand = command.includes('down');
                        const successTitle = isEnableCommand ? '🔴 Maintenance Mode Enabled!' : '🟢 Site Back Online!';
                        const successText = isEnableCommand 
                            ? 'Your site is now in maintenance mode. Only admins can access it.' 
                            : 'Your site is now accessible to all users.';
                        
                        Swal.fire({
                            title: successTitle,
                            text: successText,
                            icon: 'success',
                            confirmButtonColor: isEnableCommand ? '#dc3545' : '#28a745',
                            confirmButtonText: 'OK'
                        });
                        
                        // Refresh executions table
                        setTimeout(refreshExecutions, 1000);
                        return response;
                    }).catch(function(xhr) {
                        const errorResponse = xhr.responseJSON || {
                            success: false,
                            message: 'Request failed: ' + xhr.statusText
                        };
                        
                        Swal.fire({
                            title: '❌ Command Failed!',
                            text: errorResponse.message || 'An error occurred while executing the command.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'OK'
                        });
                        throw xhr;
                    });
                }

                // Initialize tooltips
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        </script>

        <style>
            .command-card {
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            }

            .command-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }

            .border-left-primary {
                border-left: 3px solid var(--bs-primary) !important;
            }

            /* Maintenance Mode Special Styling */
            .maintenance-command {
                border-left: 5px solid #dc3545 !important;
                background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
                box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1);
            }

            .maintenance-command:hover {
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(220, 53, 69, 0.2);
                background: linear-gradient(135deg, #fff0f0 0%, #ffffff 100%);
            }

            .maintenance-title {
                color: #dc3545 !important;
                font-weight: 600;
                text-shadow: 0 1px 2px rgba(220, 53, 69, 0.1);
            }

            .maintenance-description {
                color: #6c757d !important;
                font-weight: 500;
            }

            .maintenance-badge {
                font-weight: 600;
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
                border-radius: 0.375rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .maintenance-btn-danger {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                border: none;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 3px 6px rgba(220, 53, 69, 0.3);
                transition: all 0.3s ease;
            }

            .maintenance-btn-danger:hover {
                background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
                transform: translateY(-1px);
                box-shadow: 0 5px 10px rgba(220, 53, 69, 0.4);
            }

            .maintenance-btn-success {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                border: none;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
                transition: all 0.3s ease;
            }

            .maintenance-btn-success:hover {
                background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
                transform: translateY(-1px);
                box-shadow: 0 5px 10px rgba(40, 167, 69, 0.4);
            }

            /* Pulsing animation for maintenance commands */
            .maintenance-command .maintenance-badge {
                animation: maintenancePulse 2s infinite;
            }

            @keyframes maintenancePulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }

            /* Emergency section header styling */
            .card-header:has(.card-title:contains("Emergency")) {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                color: white;
                border-bottom: 3px solid #bd2130;
            }

            .card-header:has(.card-title:contains("Emergency")) .card-title {
                color: white !important;
                font-weight: 600;
                text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            }

            .card-header:has(.card-title:contains("Emergency")) .text-muted {
                color: rgba(255,255,255,0.8) !important;
            }

            /* Maintenance Mode Modal Styling */
            .maintenance-modal {
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
            }

            .maintenance-modal-title {
                color: #dc3545;
                font-weight: 600;
                text-shadow: 0 1px 2px rgba(220, 53, 69, 0.1);
            }

            .maintenance-confirm-btn {
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-radius: 8px;
                padding: 8px 16px;
            }

            .maintenance-cancel-btn {
                font-weight: 500;
                border-radius: 8px;
                padding: 8px 16px;
            }

            /* Enhanced status indicators */
            .status-indicator {
                display: inline-block;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                margin-right: 8px;
                animation: statusPulse 2s infinite;
            }

            .status-online {
                background-color: #28a745;
                box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
            }

            .status-maintenance {
                background-color: #dc3545;
                box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
            }

            @keyframes statusPulse {
                0% { opacity: 1; }
                50% { opacity: 0.6; }
                100% { opacity: 1; }
            }

            #outputContent {
                font-size: 12px;
                line-height: 1.4;
            }

            .progress {
                height: 4px;
            }

            .spinner-border-sm {
                width: 1rem;
                height: 1rem;
            }
        </style>
    @endpush
</x-layout>
