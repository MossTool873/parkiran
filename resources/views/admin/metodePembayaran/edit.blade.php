@extends('components.app')

@section('title', 'Edit Metode Pembayaran')

@section('content')
<div class="max-w-xl mx-auto mt-6">

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-4">Edit Metode Pembayaran</h1>

    <div class="bg-white rounded shadow p-6">
        <form action="{{ url('/admin/metodePembayaran/'.$metode->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1 font-medium">
                    Nama Metode
                </label>
                <input type="text"
                       name="nama_metode"
                       class="w-full border rounded px-3 py-2"
                       value="{{ old('nama_metode', $metode->nama_metode) }}"
                       required>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ url('/admin/metodePembayaran') }}"
                   class="px-4 py-2 border rounded">
                    Batal
                </a>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Update
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
