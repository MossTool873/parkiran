@extends('components.app')

@section('title', 'Ganti Password')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen">

    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Ganti Password</h1>

    {{-- Error --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Success --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-lg mx-auto">
        <form method="POST" action="{{ url('/ganti-password') }}" class="space-y-6"
              onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
            @csrf

            {{-- Username --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" value="{{ auth()->user()->username }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-700 cursor-not-allowed"
                       readonly>
            </div>

            {{-- Password Lama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                <div class="relative">
                    <input type="password" name="password_lama" id="password_lama"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-12 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                           required>

                    <button type="button"
                            onclick="togglePassword('password_lama', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Password Baru --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <div class="relative">
                    <input type="password" name="password" id="password_baru"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-12 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                           required>

                    <button type="button"
                            onclick="togglePassword('password_baru', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_konfirmasi"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-12 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                           required>

                    <button type="button"
                            onclick="togglePassword('password_konfirmasi', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4 pt-4 justify-end">
                <a href="{{ url()->previous() }}"
                   class="px-4 py-2 rounded-lg bg-gray-300 text-gray-800 hover:bg-gray-400 transition">
                    Batal
                </a>

                <button type="submit"
                        class="px-6 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>

</div>

{{-- SCRIPT --}}
<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon  = button.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection
