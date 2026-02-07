@extends('components.app')

@section('title', 'Backup & Restore Database')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold mb-1">
            Backup & Restore Database
        </h1>
        <p class="text-sm text-gray-600">
            Gunakan fitur ini untuk menyimpan atau mengembalikan database sistem
        </p>
    </div>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- BACKUP CARD --}}
    <div class="bg-white rounded-lg shadow border mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">
                Backup Database
            </h2>
        </div>

        <div class="px-6 py-5">
            <p class="text-sm text-gray-600 mb-4">
                Klik tombol di bawah untuk mengunduh file backup database
                <span class="font-medium">(.sql)</span>
            </p>

            <form action="{{ route('database.backup') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    onclick="this.disabled=true; this.innerText='Memproses...'; this.form.submit();"
                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm">
                    Backup Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- RESTORE CARD --}}
    <div class="bg-white rounded-lg shadow border">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-red-600">
                Restore Database
            </h2>
        </div>

        <div class="px-6 py-5">
            <p class="text-sm text-gray-600 mb-4">
                Upload file <span class="font-medium">.sql</span> untuk mengembalikan database.
                <span class="text-red-600 font-medium">
                    Seluruh data saat ini akan tertimpa!
                </span>
            </p>

            <form action="{{ route('database.restore') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  onsubmit="return confirm('Yakin ingin restore database? Semua data saat ini akan diganti!')">
                @csrf

                <input
                    type="file"
                    name="file"
                    accept=".sql"
                    required
                    class="block w-full border rounded-lg px-3 py-2 text-sm mb-4">

                @error('file')
                    <p class="text-sm text-red-600 mb-3">
                        {{ $message }}
                    </p>
                @enderror

                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm">
                    Restore Database
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
