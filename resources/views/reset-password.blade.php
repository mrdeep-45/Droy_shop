<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Hexagon CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-900">Reset Password</h2>
            <p class="text-sm text-slate-500 mt-2">Enter your new password below.</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-50 text-red-600 text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.reset.process') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-xl outline-none text-slate-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors">
                Reset Password
            </button>
        </form>
    </div>
</body>
</html>
