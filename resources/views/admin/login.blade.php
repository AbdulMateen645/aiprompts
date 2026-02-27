<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AIPromptHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white font-bold mx-auto mb-4 text-2xl shadow-lg">AI</div>
            <h1 class="text-2xl font-bold text-gray-800">AIPromptHub CMS</h1>
            <p class="text-gray-500 mt-2 text-sm">Admin Login</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 border focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6 text-right">
                <a href="{{ route('admin.password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 font-bold hover:bg-blue-700 transition-colors">
                Login
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-500">
            <p>Admin credentials:</p>
            <p class="font-mono">a.mateen2025@gmail.com / 12345678</p>
        </div>
    </div>
</body>
</html>
