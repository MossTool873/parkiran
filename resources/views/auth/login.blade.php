<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 sm:p-10">
        <!-- Judul ParkiranApp -->
<h1 class="text-center mb-8 flex justify-center items-baseline gap-2">
    <span class="text-4xl font-bold text-gray-700">Parkiran</span>
    <span class="text-2xl font-medium lowercase text-gray-700">app</span>
</h1>


        {{-- Error global --}}
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3 text-sm">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    value="{{ old('username') }}"
                    autofocus
                    class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            {{-- Tombol Login --}}
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white
                       font-semibold py-2.5 rounded-lg transition duration-200 text-lg"
            >
                Login
            </button>
        </form>
    </div>

</body>
</html>
