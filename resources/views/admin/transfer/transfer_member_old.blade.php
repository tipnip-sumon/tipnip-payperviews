<x-layout>
    @section('top_title','Transfer Funds to User')
    @section('title','Transfer Funds to User')
    
    @push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush

    @push('styles')
    <style>
        .transfer-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 1rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-attachment: fixed;
        }
        
        .transfer-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
        }
        
        .transfer-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            transition: all 0.4s ease;
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            position: relative;
        }
        
        .transfer-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 50%, #11998e 100%);
            z-index: 1;
        }
        
        .transfer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.2);
        }
        
        .transfer-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }
        
        .transfer-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .transfer-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
        }
        
        .transfer-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            position: relative;
            z-index: 1;
        }
        
        .transfer-icon i {
            font-size: 1.8rem;
            color: white;
        }
        
        .transfer-body {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
            color: #2d3748;
            font-weight: 500;
            resize: vertical;
        }
        
        .form-control[type="number"] {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        textarea.form-control {
            min-height: 80px;
            line-height: 1.5;
            font-family: inherit;
        }
        
        .form-control::placeholder {
            color: #a0aec0;
            opacity: 1;
        }
        
        .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            background: white;
            outline: none;
            color: #2d3748;
        }
        
        .form-control:hover {
            border-color: #cbd5e0;
            background: white;
        }
        
        .input-group {
            position: relative;
            display: flex;
        }
        
        .input-group-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 8px 0 0 8px;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 50px;
        }
        
        .input-group-text:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
            flex: 1;
            background: white;
        }
        
        .input-group .form-control:focus {
            background: white;
            border-color: #4facfe;
            color: #2d3748;
        }
        
        .user-search-container {
            position: relative;
        }
        
        .user-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 12px 12px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }
        
        .user-suggestions::-webkit-scrollbar {
            width: 6px;
        }
        
        .user-suggestions::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .user-suggestions::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        .user-suggestion {
            padding: 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .user-suggestion::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            transition: width 0.3s ease;
        }
        
        .user-suggestion:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transform: translateX(5px);
        }
        
        .user-suggestion:hover::before {
            width: 4px;
        }
        
        .user-suggestion:last-child {
            border-bottom: none;
            border-radius: 0 0 12px 12px;
        }
        
        .user-suggestion.loading {
            justify-content: center;
            color: #718096;
        }
        
        .user-suggestion.no-results {
            justify-content: center;
            color: #a0aec0;
            font-style: italic;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 1rem;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
        }
        
        .user-avatar::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.3;
        }
        
        .user-info {
            flex: 1;
            min-width: 0;
        }
        
        .user-name {
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 0.25rem 0;
            font-size: 1rem;
        }
        
        .user-email {
            font-size: 0.85rem;
            color: #718096;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-status {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-badge {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .user-balance {
            font-size: 0.8rem;
            color: #4a5568;
            font-weight: 600;
        }
        
        .btn-transfer {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-transfer:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
            color: white;
        }
        
        .btn-transfer:active {
            transform: translateY(0);
        }
        
        .btn-transfer.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .btn-transfer .spinner {
            display: none;
        }
        
        .btn-transfer.loading .spinner {
            display: inline-block;
            margin-right: 0.5rem;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
            font-size: 0.9rem;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
            color: #721c24;
        }
        
        .alert ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.25rem;
        }
        
        .balance-info {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-radius: 8px;
            padding: 1.25rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .balance-amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
        
        .balance-label {
            color: #718096;
            margin: 0.25rem 0 0 0;
            font-size: 0.9rem;
        }
        
        .amount-suggestions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        
        .amount-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            min-width: 60px;
            text-align: center;
        }
        
        .amount-btn:hover {
            background: #4facfe;
            color: white;
            border-color: #4facfe;
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
            animation: shake 0.5s ease-in-out;
            background: #fef2f2 !important;
            color: #991b1b !important;
        }
        
        .is-invalid::placeholder {
            color: #f87171 !important;
        }
        
        .is-valid {
            border-color: #28a745 !important;
            background: #f0fdf4 !important;
            color: #166534 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.7-.04 1.4-1.4-1.41-1.41-.71.71.7.7-1.4 1.4z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem 1rem;
        }
        
        .is-valid::placeholder {
            color: #86efac !important;
        }
        
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.8rem;
            color: #dc3545;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .step-feedback {
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 4px;
            background: #d4f8d4;
            border: 1px solid #28a745;
            animation: slideIn 0.3s ease-in-out;
        }
        
        .transfer-card.shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Enhanced Animations */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(79, 172, 254, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0); }
        }
        
        @keyframes slideInFromLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        /* Loading Animation */
        .loading-dots {
            display: inline-block;
        }
        
        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
        
        /* Success Animation */
        .success-checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #4bb71b;
            stroke-miterlimit: 10;
            margin: 10% auto;
            box-shadow: inset 0px 0px 0px #4bb71b;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        
        .success-checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #4bb71b;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        
        .success-checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        
        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }
        
        @keyframes scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }
        
        @keyframes fill {
            100% { box-shadow: inset 0px 0px 0px 30px #4bb71b; }
        }
        
        /* Smooth transitions */
        .form-control, .btn, .alert {
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.15);
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        /* Progress indicator */
        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 2px;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
            width: 0%;
            transition: width 0.5s ease;
            border-radius: 2px;
        }
        
        /* Form Steps Animation */
        .form-step {
            opacity: 0.6;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }
        
        .form-step.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .form-step.completed {
            opacity: 1;
            transform: translateY(0);
        }
        
        .form-step.completed .form-control {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #166534;
        }
        
        .form-step.completed .form-control::placeholder {
            color: #86efac;
        }
        
        /* Loading States */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .loading-spinner .spinner-border {
            color: #4facfe;
            width: 3rem;
            height: 3rem;
        }
        
        .loading-text {
            margin-top: 1rem;
            color: #666;
            font-weight: 500;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .transfer-container {
                padding: 0.5rem;
                min-height: 100vh;
                display: block;
            }
            
            .transfer-card {
                margin: 1rem 0;
            }
            
            .transfer-body {
                padding: 1rem;
            }
            
            .transfer-header {
                padding: 1rem;
            }
            
            .transfer-header h2 {
                font-size: 1.25rem;
            }
            
            .balance-amount {
                font-size: 1.5rem;
            }
            
            .amount-suggestions {
                gap: 0.25rem;
            }
            
            .amount-btn {
                font-size: 0.75rem;
                padding: 0.4rem 0.5rem;
                min-width: 50px;
            }
            
            .form-control {
                font-size: 16px; /* Prevents zoom on iOS */
            }
            
            .user-suggestions {
                max-height: 150px;
            }
            
            .btn-transfer {
                font-size: 0.9rem;
                padding: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .transfer-container {
                padding: 0.25rem;
            }
            
            .transfer-body {
                padding: 0.75rem;
            }
            
            .transfer-header {
                padding: 0.75rem;
            }
            
            .transfer-header h2 {
                font-size: 1.1rem;
            }
            
            .balance-amount {
                font-size: 1.25rem;
            }
            
            .amount-suggestions {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.25rem;
            }
            
            .amount-btn {
                font-size: 0.7rem;
                padding: 0.35rem 0.4rem;
            }
            
            .form-control {
                padding: 0.6rem;
            }
            
            .btn-transfer {
                font-size: 0.85rem;
                padding: 0.875rem;
            }
        }
        
        @media (max-width: 360px) {
            .transfer-container {
                padding: 0.125rem;
            }
            
            .transfer-header h2 {
                font-size: 1rem;
            }
            
            .balance-amount {
                font-size: 1.1rem;
            }
            
            .amount-suggestions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
    @endpush

    @section('content')
    <div class="transfer-container">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                    <div class="transfer-card">
                        <!-- Transfer Header -->
                        <div class="transfer-header">
                            <div class="transfer-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <h2>Transfer Funds</h2>
                            <p>Send money to users instantly and securely</p>
                        </div>

                        <!-- Transfer Body -->
                        <div class="transfer-body">
                            <!-- Progress Bar -->
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill"></div>
                            </div>
                            
                            <!-- Admin Balance Info -->
                            <div class="balance-info" data-balance="{{ auth()->guard('admin')->user()->balance ?? 10000 }}">
                                <h3 class="balance-amount">${{ number_format(auth()->guard('admin')->user()->balance ?? 10000, 2) }}</h3>
                                <p class="balance-label">Available Balance</p>
                            </div>

                            <!-- Alert Messages -->
                            @if(session('success'))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Please fix the following errors:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Transfer Form -->
                            <form action="{{ route('admin.transfer_member.store') }}" method="POST" id="transferForm">
                                @csrf
                                
                                <!-- User Selection -->
                                <div class="form-group form-step active" data-step="1">
                                    <label class="form-label" for="user_receive">
                                        <i class="fas fa-user me-2"></i>Select User
                                    </label>
                                    <div class="user-search-container">
                                        <input type="text" 
                                               name="user_receive" 
                                               id="user_receive" 
                                               class="form-control" 
                                               placeholder="Type username or email to search..." 
                                               autocomplete="off"
                                               value="{{ old('user_receive') }}"
                                               required>
                                        <div class="user-suggestions" id="userSuggestions"></div>
                                    </div>
                                </div>

                                <!-- Amount Input -->
                                <div class="form-group form-step" data-step="2">
                                    <label class="form-label" for="amount">
                                        <i class="fas fa-dollar-sign me-2"></i>Transfer Amount
                                    </label>
                                    <input type="number" 
                                           name="amount" 
                                           id="amount" 
                                           class="form-control" 
                                           placeholder="Enter amount (e.g., 100.00)" 
                                           step="0.01"
                                           min="1"
                                           value="{{ old('amount') }}"
                                           style="font-size: 1.2rem; font-weight: 700; text-align: center; color: #1a202c;"
                                           required>
                                    <div class="amount-suggestions">
                                        <button type="button" class="amount-btn" data-amount="10">$10</button>
                                        <button type="button" class="amount-btn" data-amount="25">$25</button>
                                        <button type="button" class="amount-btn" data-amount="50">$50</button>
                                        <button type="button" class="amount-btn" data-amount="100">$100</button>
                                        <button type="button" class="amount-btn" data-amount="500">$500</button>
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="form-group form-step" data-step="3">
                                    <label class="form-label" for="note">
                                        <i class="fas fa-sticky-note me-2"></i>Transfer Note (Optional)
                                    </label>
                                    <textarea name="note" 
                                              id="note" 
                                              class="form-control" 
                                              rows="3"
                                              placeholder="Add a note for this transfer...">{{ old('note') }}</textarea>
                                </div>

                                <!-- Hidden Fields -->
                                <input type="hidden" name="admin_id" value="{{ auth()->guard('admin')->id() }}">
                                <input type="hidden" name="username" value="{{ auth()->guard('admin')->user()->username ?? 'admin' }}">

                                <!-- Transaction Password -->
                                <div class="form-group form-step" data-step="4">
                                    <label class="form-label" for="signin-password">
                                        <i class="fas fa-lock me-2"></i>Transaction Password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text" onclick="togglePassword('signin-password', this)">
                                            <i class="fe fe-eye-off align-middle"></i>
                                        </span>
                                        <input type="password" 
                                               id="signin-password" 
                                               name="password" 
                                               class="form-control" 
                                               placeholder="Enter your transaction password"
                                               required>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-transfer" id="submitBtn">
                                        <span class="spinner-border spinner-border-sm spinner" role="status"></span>
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Transfer Funds
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="loading-text">Processing transfer<span class="loading-dots"></span></div>
            </div>
        </div>
        
        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-5">
                        <div class="success-checkmark">
                            <svg class="success-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="success-checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="success-checkmark__check" fill="none" d="M16 26l6 6 12-12"/>
                            </svg>
                        </div>
                        <h3 class="text-success mb-3">Transfer Successful!</h3>
                        <p class="text-muted mb-4" id="successMessage">Your transfer has been processed successfully.</p>
                        <div class="d-flex gap-3 justify-content-center">
                            <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                                <i class="fas fa-redo me-2"></i>New Transfer
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get balance from data attribute
            const adminBalance = parseFloat($('.balance-info').data('balance')) || 10000;
            
            // Initialize form steps
            const formSteps = $('.form-step');
            let currentStep = 1;
            
            // Initialize progress bar
            updateProgressBar();
            
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // User search functionality with AJAX
            let searchTimeout;
            $('#user_receive').on('input', function() {
                const query = $(this).val().trim();
                const suggestions = $('#userSuggestions');
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    suggestions.hide();
                    return;
                }
                
                searchTimeout = setTimeout(function() {
                    // Show loading state
                    suggestions.html('<div class="user-suggestion loading"><i class="fas fa-spinner fa-spin me-2"></i>Searching users...</div>').show();
                    
                    // Debug the URL being called
                    const searchUrl = '/admin/users/search';
                    console.log('Making AJAX request to:', searchUrl);
                    console.log('Query:', query);
                    
                    // AJAX call to search users
                    $.ajax({
                        url: searchUrl,
                        method: 'GET',
                        data: { 
                            query: query,
                            limit: 10 
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success && response.users.length > 0) {
                                let html = '';
                                response.users.forEach(user => {
                                    const initials = user.firstname && user.lastname 
                                        ? (user.firstname[0] + user.lastname[0]).toUpperCase()
                                        : user.username[0].toUpperCase();
                                    
                                    const fullName = user.firstname && user.lastname 
                                        ? `${user.firstname} ${user.lastname}`
                                        : user.username;
                                    
                                    const balance = user.balance ? parseFloat(user.balance).toFixed(2) : '0.00';
                                    const status = user.status == 1 ? 'Active' : 'Inactive';
                                    const statusClass = user.status == 1 ? 'user-badge' : 'user-badge bg-warning';
                                    
                                    html += `
                                        <div class="user-suggestion" 
                                             data-username="${user.username}" 
                                             data-email="${user.email}" 
                                             data-name="${fullName}"
                                             data-id="${user.id}"
                                             data-balance="${balance}">
                                            <div class="user-avatar">${initials}</div>
                                            <div class="user-info">
                                                <div class="user-name">${fullName}</div>
                                                <div class="user-email">@${user.username} • ${user.email}</div>
                                            </div>
                                            <div class="user-status">
                                                <div class="user-balance">$${balance}</div>
                                                <div class="${statusClass}">${status}</div>
                                            </div>
                                        </div>
                                    `;
                                });
                                suggestions.html(html).show();
                            } else {
                                suggestions.html('<div class="user-suggestion no-results"><i class="fas fa-search me-2"></i>No users found matching "' + query + '"</div>').show();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Search error:', error);
                            console.error('XHR status:', xhr.status);
                            console.error('Response text:', xhr.responseText);
                            console.error('Status:', status);
                            
                            let errorMessage = 'Error searching users. Please try again.';
                            
                            if (xhr.status === 404) {
                                errorMessage = 'Search endpoint not found (404). Please check the route configuration.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Server error (500). Please check the server logs.';
                            } else if (xhr.status === 403) {
                                errorMessage = 'Access denied (403). Please check permissions.';
                            } else if (xhr.status === 401) {
                                errorMessage = 'Unauthorized (401). Please login again.';
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            suggestions.html('<div class="user-suggestion no-results"><i class="fas fa-exclamation-triangle me-2"></i>' + errorMessage + '</div>').show();
                        }
                    });
                }, 300);
            });

            // User suggestion selection
            $(document).on('click', '.user-suggestion', function() {
                if ($(this).hasClass('loading') || $(this).hasClass('no-results')) {
                    return;
                }
                
                const username = $(this).data('username');
                const email = $(this).data('email');
                const name = $(this).data('name');
                const userId = $(this).data('id');
                const balance = $(this).data('balance');
                
                if (username) {
                    $('#user_receive').val(username);
                    $('#userSuggestions').hide();
                    
                    // Add hidden input for user ID
                    $('input[name="user_id"]').remove();
                    $('#transferForm').append(`<input type="hidden" name="user_id" value="${userId}">`);
                    
                    // Mark step as completed and activate next
                    activateStep(2);
                    $('#amount').focus();
                    
                    // Show success feedback with user info
                    showStepFeedback(1, `User selected: ${name} (Balance: $${balance})`);
                    
                    // Add visual confirmation
                    $('#user_receive').addClass('is-valid');
                    setTimeout(() => {
                        $('#user_receive').removeClass('is-valid');
                    }, 2000);
                }
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.user-search-container').length) {
                    $('#userSuggestions').hide();
                }
            });

            // Amount suggestions
            $('.amount-btn').on('click', function() {
                const amount = $(this).data('amount');
                $('#amount').val(amount);
                
                // Validate amount
                validateAmount(amount);
                
                // Activate next step
                activateStep(3);
                $('#note').focus();
                
                // Show success feedback
                showStepFeedback(2, 'Amount selected: $' + amount);
            });

            // Form step progression
            $('#user_receive').on('blur', function() {
                if ($(this).val().trim()) {
                    activateStep(2);
                }
            });

            $('#amount').on('blur', function() {
                const amount = $(this).val();
                if (amount && validateAmount(amount)) {
                    activateStep(3);
                }
            });

            $('#note').on('blur', function() {
                activateStep(4);
            });

            // Amount validation with real-time formatting
            $('#amount').on('input', function() {
                let value = $(this).val();
                
                // Remove any non-numeric characters except decimal point
                value = value.replace(/[^0-9.]/g, '');
                
                // Ensure only one decimal point
                const parts = value.split('.');
                if (parts.length > 2) {
                    value = parts[0] + '.' + parts.slice(1).join('');
                }
                
                // Limit to 2 decimal places
                if (parts[1] && parts[1].length > 2) {
                    value = parts[0] + '.' + parts[1].substring(0, 2);
                }
                
                $(this).val(value);
                
                const amount = parseFloat(value);
                if (value && !isNaN(amount)) {
                    validateAmount(amount);
                    
                    // Update button text with amount
                    const submitBtn = $('#submitBtn');
                    if (amount > 0) {
                        submitBtn.html(`
                            <span class="spinner-border spinner-border-sm spinner" role="status"></span>
                            <i class="fas fa-paper-plane me-2"></i>
                            Transfer $${amount.toFixed(2)}
                        `);
                    } else {
                        submitBtn.html(`
                            <span class="spinner-border spinner-border-sm spinner" role="status"></span>
                            <i class="fas fa-paper-plane me-2"></i>
                            Transfer Funds
                        `);
                    }
                }
            });

            // Form submission with AJAX for better user experience
            $('#transferForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                if (!validateForm()) {
                    return false;
                }
                
                // Show loading
                showLoading();
                
                // Get form data
                const formData = new FormData(this);
                
                // Submit via AJAX for better user feedback
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        hideLoading();
                        
                        if (response.success) {
                            // Show success modal
                            showSuccessModal(response.message || 'Transfer completed successfully!');
                            
                            // Reset form
                            $('#transferForm')[0].reset();
                            $('.form-control').removeClass('is-valid is-invalid');
                            $('.invalid-feedback, .step-feedback').remove();
                            currentStep = 1;
                            updateProgressBar();
                            
                            // Reset form steps
                            $('.form-step').removeClass('active completed');
                            $('[data-step="1"]').addClass('active');
                            
                        } else {
                            showNotification(response.message || 'Transfer failed. Please try again.', 'error');
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                const input = $(`[name="${field}"]`);
                                input.addClass('is-invalid');
                                input.after(`<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>${errors[field][0]}</div>`);
                            });
                            showNotification('Please fix the validation errors', 'error');
                        } else {
                            showNotification('An error occurred. Please try again.', 'error');
                        }
                    }
                });
            });

            // Helper functions
            function activateStep(stepNumber) {
                if (stepNumber > currentStep) {
                    // Mark previous steps as completed
                    for (let i = 1; i < stepNumber; i++) {
                        $(`[data-step="${i}"]`).addClass('completed');
                    }
                    
                    // Activate current step
                    $(`[data-step="${stepNumber}"]`).addClass('active');
                    currentStep = stepNumber;
                    
                    // Update progress bar
                    updateProgressBar();
                }
            }
            
            function updateProgressBar() {
                const progress = (currentStep / 4) * 100;
                $('#progressFill').css('width', progress + '%');
            }

            function showStepFeedback(stepNumber, message) {
                const step = $(`[data-step="${stepNumber}"]`);
                const feedback = step.find('.step-feedback');
                
                if (feedback.length === 0) {
                    step.append(`<div class="step-feedback text-success"><small><i class="fas fa-check me-1"></i>${message}</small></div>`);
                } else {
                    feedback.html(`<small><i class="fas fa-check me-1"></i>${message}</small>`);
                }
                
                // Add bounce animation
                step.find('.step-feedback').css('animation', 'bounceIn 0.5s ease-out');
                
                setTimeout(() => {
                    step.find('.step-feedback').fadeOut();
                }, 4000);
            }

            function validateAmount(amount) {
                const balance = parseFloat(adminBalance);
                const amountInput = $('#amount');
                
                // Remove existing feedback
                amountInput.removeClass('is-invalid is-valid');
                amountInput.next('.invalid-feedback').remove();
                
                if (isNaN(amount) || amount <= 0) {
                    amountInput.addClass('is-invalid');
                    amountInput.after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Please enter a valid amount</div>');
                    return false;
                }
                
                if (amount < 1) {
                    amountInput.addClass('is-invalid');
                    amountInput.after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Minimum transfer amount is $1.00</div>');
                    return false;
                }
                
                if (amount > balance) {
                    amountInput.addClass('is-invalid');
                    amountInput.after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Amount exceeds available balance ($' + balance.toFixed(2) + ')</div>');
                    return false;
                }
                
                // Add success state
                amountInput.addClass('is-valid');
                return true;
            }

            function validateForm() {
                let isValid = true;
                let firstError = null;
                
                // Clear previous errors
                $('.form-control').removeClass('is-invalid is-valid');
                $('.invalid-feedback').remove();
                
                // Validate user selection
                const userInput = $('#user_receive');
                if (!userInput.val().trim()) {
                    userInput.addClass('is-invalid');
                    userInput.after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Please select a user from the suggestions</div>');
                    isValid = false;
                    if (!firstError) firstError = userInput;
                } else {
                    userInput.addClass('is-valid');
                }
                
                // Validate amount
                const amount = parseFloat($('#amount').val());
                if (!validateAmount(amount)) {
                    isValid = false;
                    if (!firstError) firstError = $('#amount');
                }
                
                // Validate password
                const passwordInput = $('#signin-password');
                if (!passwordInput.val().trim()) {
                    passwordInput.addClass('is-invalid');
                    passwordInput.after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Please enter your transaction password</div>');
                    isValid = false;
                    if (!firstError) firstError = passwordInput;
                } else if (passwordInput.val().length < 6) {
                    passwordInput.addClass('is-invalid');
                    passwordInput.after('<div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>Password must be at least 6 characters</div>');
                    isValid = false;
                    if (!firstError) firstError = passwordInput;
                } else {
                    passwordInput.addClass('is-valid');
                }
                
                if (!isValid) {
                    // Shake the form and focus on first error
                    $('.transfer-card').addClass('shake');
                    setTimeout(() => {
                        $('.transfer-card').removeClass('shake');
                    }, 500);
                    
                    // Focus on first error field
                    if (firstError) {
                        setTimeout(() => {
                            firstError.focus();
                        }, 600);
                    }
                    
                    // Show error notification
                    showNotification('Please fix the errors above', 'error');
                }
                
                return isValid;
            }

            function showLoading() {
                const submitBtn = $('#submitBtn');
                const loadingOverlay = $('#loadingOverlay');
                
                submitBtn.addClass('loading');
                submitBtn.prop('disabled', true);
                loadingOverlay.css('display', 'flex');
                
                // Auto-hide loading after 15 seconds as failsafe
                setTimeout(() => {
                    hideLoading();
                }, 15000);
            }

            function hideLoading() {
                const submitBtn = $('#submitBtn');
                const loadingOverlay = $('#loadingOverlay');
                
                submitBtn.removeClass('loading');
                submitBtn.prop('disabled', false);
                loadingOverlay.hide();
            }
            
            function showNotification(message, type = 'info') {
                const alertClass = type === 'error' ? 'alert-danger' : type === 'success' ? 'alert-success' : 'alert-info';
                const icon = type === 'error' ? 'fas fa-exclamation-triangle' : type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';
                
                const notification = $(`
                    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                         style="top: 20px; right: 20px; z-index: 10000; min-width: 300px; animation: slideInFromLeft 0.5s ease-out;">
                        <i class="${icon} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                $('body').append(notification);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    notification.fadeOut(() => notification.remove());
                }, 5000);
            }
            
            function showSuccessModal(message) {
                $('#successMessage').text(message);
                $('#successModal').modal('show');
            }
        });

        // Password toggle function
        function togglePassword(id, element) {
            const input = document.getElementById(id);
            const icon = element.querySelector('i');
            
            if (input.type === "password") {
                input.type = "text";
                icon.className = 'fe fe-eye align-middle';
            } else {
                input.type = "password";
                icon.className = 'fe fe-eye-off align-middle';
            }
        }
    </script>
    @endpush
</x-layout>