@extends('layouts.admin')

@section('content')
    <div class="w-full min-h-screen bg-white" x-data="locationManager()">

        {{-- Top Navigation & Header --}}
        <div class="border-b border-slate-200">
            <div class="px-8 py-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    {{-- Title & Tabs --}}
                    <div class="flex flex-col gap-6">
                        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Time Tracking</h1>

                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit">
                            <button
                                class="px-6 py-2 rounded-lg font-bold text-sm bg-blue-600 text-white shadow-sm">Locations</button>
                            <a href="{{ route('admin.time-tracking.assign') }}"
                                class="px-6 py-2 rounded-lg font-bold text-sm {{ request()->routeIs('admin.time-tracking.assign') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }} transition-colors">
                                Assign to employee
                            </a>
                            <a href="#"
                                class="px-6 py-2 rounded-lg font-bold text-sm text-slate-500 hover:text-slate-700 transition-colors">Check-ins</a>
                        </div>
                    </div>

                    {{-- Global Action --}}
                    <button @click="openCreateModal()"
                        class="flex items-center gap-2 bg-[#2563eb] hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold transition-all shadow-sm">
                        <i class="fas fa-plus text-sm"></i>
                        <span>Add location</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div
                class="mx-8 mt-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 font-bold flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Locations List --}}
        <div class="px-8 py-6">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">Locations</h2>
            </div>

            <div class="divide-y divide-slate-100 border-t border-slate-100">
                @forelse($locations as $loc)
                    <div
                        class="group flex items-center justify-between py-5 hover:bg-slate-50/50 transition-colors px-2 rounded-lg">
                        <div class="flex flex-col gap-1">
                            <h4 class="text-[15px] font-bold text-slate-900">{{ $loc->name }}</h4>
                            <p class="text-sm text-slate-500">{{ $loc->address ?? 'No address set' }}</p>
                            <button @click="viewOnMap('{{ $loc->latitude }}', '{{ $loc->longitude }}')"
                                class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1 mt-1">
                                <i class="fas fa-map-marker-alt text-[10px]"></i> View on map
                            </button>
                        </div>

                        <div class="flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openEditModal({{ json_encode($loc) }})"
                                class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas fa-pen text-sm"></i>
                            </button>
                            <form action="{{ route('admin.time-tracking.destroy', $loc->id) }}" method="POST"
                                class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete location?')"
                                    class="text-slate-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center">
                        <p class="text-slate-400 font-medium">No locations found. Click "Add location" to begin.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Modal Component (Shared for Create/Edit) --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" role="dialog">
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

                <div x-show="modalOpen" x-transition.scale.95
                    class="relative w-full max-w-xl transform overflow-hidden rounded-[2.5rem] bg-white p-8 text-left shadow-2xl transition-all">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-black text-slate-800"
                            x-text="editMode ? 'Update Zone' : 'New Tracking Zone'"></h2>
                        <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600"><i
                                class="fas fa-times-circle text-2xl"></i></button>
                    </div>

                    <form :action="formAction" method="POST" class="space-y-5">
                        @csrf
                        <template x-if="editMode">@method('PUT')</template>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Internal Name</label>
                            <input type="text" name="name" x-model="formData.name" required
                                placeholder="Main Office HQ"
                                class="w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 font-medium">
                        </div>

                        <div class="bg-blue-50 p-6 rounded-[2rem] border border-blue-100">
                            <label class="block text-sm font-bold text-blue-700 mb-3">Address Lookup</label>
                            <div class="flex gap-2 mb-4">
                                <input type="text" x-model="searchQuery" placeholder="Search address..."
                                    class="flex-1 bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-blue-500">
                                <button type="button" @click="lookup()" :disabled="loading"
                                    class="bg-blue-600 text-white px-5 rounded-xl font-bold text-sm disabled:opacity-50">
                                    <i x-show="!loading" class="fas fa-search"></i>
                                    <i x-show="loading" class="fas fa-spinner fa-spin"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black uppercase text-blue-400">Latitude</label>
                                    <input type="number" step="any" name="latitude" x-model="formData.latitude"
                                        class="w-full bg-white/50 border-none rounded-lg p-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase text-blue-400">Longitude</label>
                                    <input type="number" step="any" name="longitude" x-model="formData.longitude"
                                        class="w-full bg-white/50 border-none rounded-lg p-2 text-sm">
                                </div>
                            </div>
                            <input type="hidden" name="address" x-model="formData.address">
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-sm font-bold text-slate-700">Radius</label>
                                <span class="text-blue-600 font-black" x-text="formData.radius + 'm'"></span>
                            </div>
                            <input type="range" name="radius" min="50" max="1000" step="50"
                                x-model="formData.radius"
                                class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        </div>

                        <div class="flex gap-3 pt-6">
                            <button type="button" @click="modalOpen = false"
                                class="flex-1 py-4 font-bold text-slate-400 hover:text-slate-600 transition-colors">Discard</button>
                            <button type="submit"
                                class="flex-[2] py-4 bg-blue-600 text-white font-black rounded-2xl shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        function locationManager() {
            return {
                modalOpen: false,
                mapModalOpen: false, // New state
                mapUrl: '', // New state
                editMode: false,
                loading: false,
                searchQuery: '',
                formAction: '{{ route('admin.time-tracking.store') }}',
                formData: {
                    name: '',
                    address: '',
                    latitude: '',
                    longitude: '',
                    radius: 200
                },

                openCreateModal() {
                    this.editMode = false;
                    this.modalOpen = true;
                    this.formAction = '{{ route('admin.time-tracking.store') }}';
                    this.formData = {
                        name: '',
                        address: '',
                        latitude: '',
                        longitude: '',
                        radius: 200
                    };
                },

                openEditModal(data) {
                    this.editMode = true;
                    this.modalOpen = true;
                    this.formAction = `{{ url('admin/time-tracking/update') }}/${data.id}`;
                    this.formData = {
                        ...data
                    };
                    this.searchQuery = data.address;
                },

                // NEW FUNCTION: Generate Map URL and open modal
                viewOnMap(lat, lng) {
                    // Direct Google Maps ka URL jisme marker (pin) bhi dikhayi dega
                    const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

                    // Naye tab mein open karne ke liye
                    window.open(url, '_blank');
                },

                async lookup() {
                    if (!this.searchQuery) return;
                    this.loading = true;
                    try {
                        const res = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}`
                        );
                        const data = await res.json();
                        if (data.length > 0) {
                            this.formData.latitude = parseFloat(data[0].lat).toFixed(8);
                            this.formData.longitude = parseFloat(data[0].lon).toFixed(8);
                            this.formData.address = data[0].display_name;
                        }
                    } catch (e) {
                        alert('Service unavailable');
                    }
                    this.loading = false;
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
