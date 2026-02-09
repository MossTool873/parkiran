@extends('components.app')

@section('title', 'Manajemen Membership Tier')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Tier Membership </h1>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Membership Tier</th>
                    <th class="px-4 py-3">Diskon (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tiers as $tier)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $tier->membership_tier }}</td>
                        <td class="px-4 py-3">{{ $tier->diskon }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            Data membership tier kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
