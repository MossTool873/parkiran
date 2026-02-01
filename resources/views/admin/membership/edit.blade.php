@extends('components.app')

@section('title', 'Edit Membership')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit Membership</h1>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 w-full">
        <form action="{{ route('membership.update', $membership->id) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Nama Membership</label>
                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama', $membership->nama) }}"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tier Membership</label>
                <select
                    name="membership_tier_id"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
                    <option value="">-- Pilih Tier --</option>
                    @foreach ($tiers as $tier)
                        <option value="{{ $tier->id }}"
                            {{ old('membership_tier_id', $membership->membership_tier_id) == $tier->id ? 'selected' : '' }}>
                            {{ $tier->membership_tier }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Pembaruan Terakhir</label>
                <input
                    type="date"
                    name="pembaruan_terakhir"
                    value="{{ old('pembaruan_terakhir', $membership->pembaruan_terakhir) }}"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Kadaluarsa</label>
                <input
                    type="date"
                    name="kadaluarsa"
                    value="{{ old('kadaluarsa', $membership->kadaluarsa) }}"
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                >
            </div>

            <div class="flex gap-2 pt-4">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
                >
                    Update
                </button>

                <a
                    href="{{ route('membership.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
