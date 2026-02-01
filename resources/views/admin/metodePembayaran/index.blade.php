@extends('components.app')

@section('title', 'Metode Pembayaran')

@section('content')
<div class="max-w-6xl mx-auto mt-6">

    {{-- SUCCESS --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Metode Pembayaran</h1>
        <a href="{{ url('/admin/metodePembayaran/create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah
        </a>
    </div>

    <div class="bg-white rounded shadow">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Nama Metode</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($metodePembayarans as $index => $metode)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $metode->nama_metode }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ url('/admin/metodePembayaran/'.$metode->id.'/edit') }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ url('/admin/metodePembayaran/'.$metode->id) }}"
                                  method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Hapus metode ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline ml-2">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">
                            Data belum ada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
