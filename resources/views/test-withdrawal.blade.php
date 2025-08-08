<!DOCTYPE html>
<html>
<head>
    <title>Simple Withdrawal Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Simple Withdrawal Test Form</h2>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-warning">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('user.withdraw.wallet.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" value="10" step="0.01">
            </div>
            <div class="mb-3">
                <label class="form-label">Method</label>
                <select name="method_id" class="form-control">
                    <option value="">Select Method</option>
                    @foreach($withdrawMethods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Account Details</label>
                <textarea name="account_details" class="form-control">Test account details</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Submit Test</button>
        </form>
    </div>
</body>
</html>
