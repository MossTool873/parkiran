@extends('components.app')

@section('title', 'Tambah Membership Tier')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah Membership Tier</h1>

    <div class="bg-white rounded-lg shadow p-6 max-w-xl">
        <form action="{{ route('membership-tier.store') }}" method="POST" class="space-y-4" onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
            @csrf

            <div>
                <label class="block mb-1 font-medium">Nama Membership</label>
                <input type="text"
                       name="membership_tier"
                       value="{{ old('membership_tier') }}"
                       class="w-full border rounded px-3 py-2"
                       required>
                @error('membership_tier')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label class="block mb-1 font-medium">Diskon (%)</label>
                <input type="number"
                       name="diskon"
                       value="{{ old('diskon') }}"
                       class="w-full border rounded px-3 py-2"
                       min="0" max="100" required>
                @error('diskon')
                    <small class="text-red-600">{{ $message }}</small>
                @enderror
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Simpan
                </button>
                <a href="{{ route('membership-tier.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Kembali
                </a>
            </div>
        </form>
    </div>
@endsection
