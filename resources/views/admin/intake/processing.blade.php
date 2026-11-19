@extends('layouts.admin')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    <div class="w-full p-6 bg-[#f8fafc] min-h-screen">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-[#1e293b] tracking-tight">Processing Intake</h1>
                <p class="text-slate-400 text-xs font-bold flex items-center mt-1">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse mr-2"></span> SYSTEM ACTIVE
                </p>
            </div>
            <a href="{{ route('admin.recycling.intake') }}"
                class="bg-[#2563eb] hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold text-sm flex items-center shadow-lg shadow-blue-500/20 transition-all">
                <i class="fas fa-plus mr-2 text-xs"></i> Add Pallet
            </a>
        </div>

        {{-- Stats Grid - Exactly matching the image/JS logic --}}
        <div id="statsContainer" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-10">
            {{-- Stats injected via AJAX here --}}
            @for ($i = 0; $i < 5; $i++)
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-slate-200 animate-pulse h-32"></div>
            @endfor
        </div>

        {{-- Filters & Search Bar --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-8 flex flex-wrap items-center gap-4">
            <div class="relative flex-1 min-w-[300px]">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="searchInput" placeholder="Search pallets..."
                    class="w-full pl-12 pr-5 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/5 outline-none transition-all">
            </div>

            <select id="statusFilter"
                class="bg-white border border-slate-200 rounded-xl px-5 py-3 text-sm font-bold text-slate-600 outline-none cursor-pointer">

                <option value="All Statuses">All Statuses</option>
                <option value="Draft">Draft</option>
                <option value="Received">Received</option>
                <option value="Processing">Processing</option>
                <option value="Completed">Completed</option>
            </select>

            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm">
                <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                <input type="date" id="dateFilter" title="Filter by Intake Date"
                    class="bg-transparent text-sm font-bold text-slate-600 outline-none cursor-pointer">
                <button type="button" id="clearDateFilterBtn" class="text-slate-400 hover:text-red-500 hidden ml-1" title="Clear Date Filter">
                    <i class="fas fa-times-circle text-xs"></i>
                </button>
            </div>

            <button id="exportCsvBtn"
                class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-600 px-6 py-3 rounded-xl font-black text-[11px] uppercase tracking-widest transition-all">
                <i class="fas fa-download"></i> Export CSV
            </button>

            <button onclick="window.print()"
                class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-600 px-6 py-3 rounded-xl font-black text-[11px] uppercase tracking-widest transition-all">
                <i class="fas fa-print"></i> Print All
            </button>

            <div id="bulkActions" class="hidden w-full flex flex-wrap items-center gap-3 pt-4 mt-2 border-t border-slate-100">
                <span id="bulkSelectedCount" class="text-[11px] font-black uppercase tracking-widest text-slate-500">0 selected</span>
                <select id="bulkStatusSelect"
                    class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none cursor-pointer">
                    <option value="">Change status...</option>
                    <option value="Draft">Draft</option>
                    <option value="Received">Received</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                </select>
                <button id="bulkStatusBtn"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-black text-[11px] uppercase tracking-widest transition-all">
                    <i class="fas fa-sync-alt"></i> Change Status
                </button>
                <button id="bulkDeleteBtn"
                    class="flex items-center gap-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-5 py-2.5 rounded-xl font-black text-[11px] uppercase tracking-widest transition-all">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
                <button id="bulkEmailBtn"
                    class="flex items-center gap-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white px-5 py-2.5 rounded-xl font-black text-[11px] uppercase tracking-widest transition-all">
                    <i class="fas fa-envelope"></i> Send Email
                </button>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="hidden md:block overflow-x-auto cursor-grab select-none global-drag-scroll">
                <table class="w-full text-left border-collapse min-w-max">
                    <thead>
                        <tr
                            class="text-[10px] uppercase font-black text-slate-400 tracking-[0.2em] bg-slate-50/50 border-b border-slate-50">
                            <th class="px-6 py-6 w-10 text-center">
                                <input type="checkbox" id="selectAllPallets" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-8 py-6">Date</th>
                            <th class="px-8 py-6">Company</th>
                            <th class="px-8 py-6">Client</th>
                            <th class="px-8 py-6">Pallet #</th>
                            <th class="px-8 py-6 text-center">Status</th>
                            <th class="px-8 py-6 text-center">Quantity<br><span class="text-[8px] tracking-widest text-slate-300">Total / Remain / Transferred / Reuse / Scrap</span></th>
                            <th class="px-8 py-6 text-center">Weight<br><span class="text-[8px] tracking-widest text-slate-300">Total / Remain / Transferred / Reuse / Scrap</span></th>
                            <th class="px-8 py-6">Location</th>
                            <th class="px-8 py-6">Description</th>
                            <th class="px-8 py-6 text-center">Photos</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="palletsTableBody" class="text-sm font-bold text-slate-700 divide-y divide-slate-50">
                        <tr>
                            <td colspan="12" class="py-16 text-center text-slate-400 font-bold">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="w-6 h-6 border-3 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                                    <span class="text-sm font-bold text-slate-500">Loading pallets...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Mobile View Container --}}
            <div id="mobileView" class="md:hidden divide-y divide-slate-100"></div>

            <div class="p-6 border-t bg-slate-50/30">
                <div id="paginationContainer"></div>
            </div>
        </div>
    </div>

    <datalist id="employeesList">
        @if(isset($employees) && count($employees) > 0)
            @foreach($employees as $emp)
                <option value="{{ $emp->name }}"></option>
            @endforeach
        @endif
    </datalist>

    {{-- MODALS --}}
    @include('admin.partials.view-modal')
    @include('admin.partials.edit-modal')
    @include('admin.partials.image-gallery-modal')
@endsection

@push('scripts')
    <script>
        const API_ROUTES = {
            getStats: "{{ route('admin.processing-intake.stats') }}",
            getPallets: "{{ route('admin.processing-intake.get-pallets') }}",

            // We use the url() helper to get the correct base path automatically
            getPalletDetails: "{{ url('admin/processing-intake') }}/ID/details",
            updateStatus: "{{ url('admin/processing-intake') }}/ID/update-status",
            updatePallet: "{{ url('admin/processing-intake') }}/ID/update",
            deletePallet: "{{ url('admin/processing-intake') }}/ID/delete",
            bulkStatus: "{{ route('admin.processing-intake.bulk-status') }}",
            bulkDelete: "{{ route('admin.processing-intake.bulk-delete') }}",
            bulkSendEmail: "{{ route('admin.processing-intake.bulk-send-email') }}",

            exportCsv: "{{ route('admin.processing-intake.export-csv') }}",
            getClients: "{{ route('admin.processing-intake.get-clients') }}",
            getMarketingRefs: "{{ route('admin.processing-intake.get-marketing-refs') }}"
        };
        const filesUrl = "{{ url('files/') }}";
        const palletImageUrl = "{{ asset('') }}"; // Images stored in public/pallets/{barcode}
        const storageUrl = "{{ asset('') }}"; // Fallback for compatibility with modals
    </script>
    <script src="{{ asset('js/admin/intake/processingintake.js') }}?v={{ filemtime(public_path('js/admin/intake/processingintake.js')) }}"></script>
@endpush
