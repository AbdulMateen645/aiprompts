<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AIPromptHub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <img src="/logo.png" alt="AIPromptHub" class="w-20 h-20 mx-auto mb-4 rounded-full">
            <h1 class="text-3xl font-bold text-gray-800">Forgot Password</h1>
            <p class="text-gray-600 mt-2">Enter your email to receive a reset link</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.password.email') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="admin@example.com">
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('admin.login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                &larr; Back to Login
            </a>
        </div>
    </div>
</body>
</html>
