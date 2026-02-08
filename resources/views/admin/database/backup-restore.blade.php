@extends('components.app')

@section('title', 'Backup & Restore Database')

@section('content')

<div class="w-full">

    {{-- ================= HEADER ================= --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold mb-1">
            Backup & Restore Database
        </h1>
        <p class="text-sm text-gray-600">
            Gunakan fitur ini untuk menyimpan atau mengembalikan database sistem
        </p>
    </div>

    {{-- ================= ALERT ================= --}}
    @if (session('success'))
        <div class="mb-6 bg-green-100 text-green-700 px-5 py-4 rounded border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-100 text-red-700 px-5 py-4 rounded border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- ================= GRID ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ================= BACKUP CARD ================= --}}
        <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">
                    Backup Database
                </h2>
            </div>

            <div class="px-6 py-6">
                <p class="text-sm text-gray-600 mb-6">
                    Klik tombol di bawah untuk mengunduh file backup database
                    dalam format <span class="font-semibold">.sql</span>.
                </p>

                <form action="{{ route('database.backup') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        onclick="this.disabled=true; this.innerText='Memproses...'; this.form.submit();"
                        class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                        Backup Sekarang
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= RESTORE CARD ================= --}}
        <div class="bg-white rounded-lg shadow border border-red-200">
            <div class="px-6 py-4 border-b bg-red-50">
                <h2 class="text-lg font-semibold text-red-600">
                    Restore Database
                </h2>
            </div>

            <div class="px-6 py-6">
                <p class="text-sm text-gray-600 mb-4">
                    Upload file <span class="font-semibold">.sql</span> untuk mengembalikan database.
                </p>

                <p class="text-sm text-red-600 font-medium mb-6">
                    ⚠ Seluruh data saat ini akan tertimpa dan tidak dapat dikembalikan.
                </p>

                <form action="{{ route('database.restore') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      onsubmit="return confirm('Yakin ingin restore database? Semua data saat ini akan diganti!')">
                    @csrf

                    <input
                        type="file"
                        name="backup_file"
                        accept=".sql"
                        required
                        class="block w-full border rounded-lg px-3 py-2 text-sm mb-4">

                    @error('backup_file')
                        <p class="text-sm text-red-600 mb-4">
                            {{ $message }}
                        </p>
                    @enderror

                    <button
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                        Restore Database
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

@endsection
