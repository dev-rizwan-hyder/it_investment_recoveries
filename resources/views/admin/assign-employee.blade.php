@extends('layouts.admin')

@section('content')
    {{-- Success Alert Message --}}
    @if (session('success'))
        <div
            class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 font-bold flex items-center gap-3 shadow-sm animate-pulse">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    <div class="w-full min-h-screen bg-white">
        <div class="border-b border-slate-200">
            <div class="px-8 py-6">
                <div class="flex flex-col gap-6">
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Time Tracking</h1>
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit">
                        <a href="{{ route('admin.time-tracking') }}"
                            class="px-6 py-2 rounded-lg font-bold text-sm text-slate-500">Locations</a>
                        <button class="px-6 py-2 rounded-lg font-bold text-sm bg-blue-600 text-white shadow-sm">Assign to
                            employee</button>
                        <a href="#" class="px-6 py-2 rounded-lg font-bold text-sm text-slate-500">Check-ins</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-8 py-10">
            <div class="max-w-2xl bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
                <form action="{{ route('admin.time-tracking.save-assignment') }}" method="POST">
                    @csrf
                    <h2 class="text-2xl font-black text-slate-800 mb-6">Assign locations to employee</h2>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Employee email</label>
                        <select name="employee_id" required
                            class="w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 font-medium appearance-none">
                            <option value="">Select an employee...</option>

                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{-- Agar aapke table mein 'first_name' hai toh wo likhein --}}
                                    {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->email ?? 'No Email' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 mb-4">Locations (toggle to assign)</label>
                        <div
                            class="border border-slate-100 rounded-3xl overflow-hidden divide-y divide-slate-50 max-h-[400px] overflow-y-auto">
                            @foreach ($locations as $loc)
                                <label
                                    class="flex items-start gap-4 p-5 hover:bg-slate-50 cursor-pointer transition-colors group">
                                    <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}"
                                        class="w-6 h-6 rounded-lg border-2 border-slate-200 text-blue-600">
                                    <div class="flex flex-col">
                                        <span class="text-[15px] font-bold text-slate-900">{{ $loc->name }}</span>
                                        <span class="text-sm text-slate-500">{{ $loc->address }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit"
                        onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Saving...'; this.classList.add('opacity-50')"
                        class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl shadow-lg hover:bg-blue-700 transition-all">
                        Save assignments
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
