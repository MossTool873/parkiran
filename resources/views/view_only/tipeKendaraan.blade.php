@extends('components.app')

@section('title', 'Tipe Kendaraan')

@section('content')

    <h1 class="text-2xl font-bold mb-4">Tipe Kendaraan</h1>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Tipe</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tipeKendaraans as $tipe)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $tipe->tipe_kendaraan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            Data tipe kendaraan kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
