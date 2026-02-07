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
                    <input type="password" name="password_lama"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        required>
                </div>

                {{-- Password Baru --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        required>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        required>
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
@endsection
