@extends('layouts.admin')

@section('content')
<div x-data="{ 
    openModal: false, 
    viewMode: false,
    editMode: false, 
    search: '',
    actionUrl: '',
    selectedClients: [],
    formData: { id: '', name: '', email: '', phone: '', company: '', address: '', notes: '' },
    viewData: { client: {}, pallets: [] },

    openCreate() {
        this.viewMode = false;
        this.editMode = false;
        this.openModal = true;
        this.formData = { id: '', name: '', email: '', phone: '', company: '', address: '', notes: '' };
        this.actionUrl = '{{ route('admin.clients.store') }}'; 
    },

    openEdit(client) {
        this.viewMode = false;
        this.editMode = true;
        this.openModal = true;
        this.formData = { ...client };
        this.actionUrl = `/admin/clients/${client.id}`;
    },

    async openView(clientId) {
        this.viewMode = true;
        this.editMode = false;
        this.openModal = true;
        const response = await fetch(`/admin/clients/${clientId}`);
        const data = await response.json();
        this.viewData.client = data.client || data;
        this.viewData.pallets = data.pallets || [];
    },

    shouldShow(name, email, company) {
        if (!this.search) return true;
        const s = this.search.toLowerCase();
        return (name && name.toLowerCase().includes(s)) || 
               (email && email.toLowerCase().includes(s)) || 
               (company && company.toLowerCase().includes(s));
    },

    toggleSelectAll(checked) {
        if (checked) {
            const checkboxes = Array.from(document.querySelectorAll('.client-checkbox'));
            this.selectedClients = checkboxes.map(cb => parseInt(cb.value));
        } else {
            this.selectedClients = [];
        }
    },

    confirmBulkDelete() {
        if (this.selectedClients.length === 0) return;
        if (!confirm(`Are you sure you want to move ${this.selectedClients.length} selected client(s) to trash?`)) return;
        
        const form = document.getElementById('bulkDeleteForm');
        form.querySelectorAll('input[name=\'ids[]\']').forEach(el => el.remove());
        this.selectedClients.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        form.submit();
    }
}" x-cloak>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700 flex items-center justify-between shadow-sm">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <a href="{{ route('admin.trash.index', ['section' => 'clients']) }}" class="underline hover:text-emerald-900 text-xs font-semibold flex items-center gap-1">
                <i class="fas fa-trash-alt text-[10px]"></i> View Trash
            </a>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700 shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-slate-800">Clients</h1>
            <a href="{{ route('admin.trash.index', ['section' => 'clients']) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 border border-slate-200">
                <i class="fas fa-trash-alt text-slate-400"></i> Client Trash
            </a>
        </div>
        <div class="flex items-center gap-3">
            <button 
                type="button" 
                x-show="selectedClients.length > 0" 
                @click="confirmBulkDelete()" 
                class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2 rounded-lg font-bold text-sm border border-red-200 transition-all flex items-center gap-2 shadow-sm"
            >
                <i class="fas fa-trash-alt"></i> Delete Selected (<span x-text="selectedClients.length"></span>)
            </button>
            <button @click="openCreate()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold shadow-sm transition-all">
                + Add Client
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-6 relative">
        <input x-model="search" type="text" placeholder="Search name, email, or company..." 
               class="w-full bg-white border border-gray-200 rounded-xl py-3 px-4 outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm">
    </div>

    {{-- Hidden Bulk Delete Form --}}
    <form id="bulkDeleteForm" action="{{ route('admin.clients.bulk-delete') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-gray-100">
                <tr class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">
                    <th class="px-4 py-4 w-10 text-center">
                        <input 
                            type="checkbox" 
                            @change="toggleSelectAll($event.target.checked)" 
                            :checked="selectedClients.length > 0 && selectedClients.length === {{ $clients->count() }}"
                            class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            title="Select all on this page"
                        >
                    </th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Company</th>
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($clients as $client)
                <tr x-show="shouldShow(@js($client->name), @js($client->email), @js($client->company))" class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 py-4 text-center">
                        <input 
                            type="checkbox" 
                            value="{{ $client->id }}" 
                            x-model.number="selectedClients" 
                            class="client-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        >
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900">{{ $client->name }}</div>
                        <div class="text-[10px] text-slate-400">{{ Str::limit($client->address, 30) }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $client->company ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs text-slate-500">{{ $client->email }}<br>{{ $client->phone }}</td>
                    <td class="px-6 py-4 text-right flex justify-end gap-3">
                        <button @click="openView({{ $client->id }})" title="View" class="text-slate-400 hover:text-blue-500"><i class="far fa-eye"></i></button>
                        <button @click="openEdit({{ $client->toJson() }})" title="Edit" class="text-slate-400 hover:text-indigo-600"><i class="far fa-edit"></i></button>
                        <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Move client to trash?')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Delete to Trash" class="text-slate-400 hover:text-red-500"><i class="far fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
                        No clients found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($clients->hasPages())
        <div class="bg-slate-50 border-t border-slate-100 px-6 py-4">
            {{ $clients->links() }}
        </div>
        @endif
    </div>

    {{-- Universal Modal (Add, Edit, View) --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition>
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden" @click.away="openModal = false">
            
            <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
                <h2 class="text-xl font-bold text-slate-800" x-text="viewMode ? 'Client Details' : (editMode ? 'Edit Client' : 'Add New Client')"></h2>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>

            <template x-if="viewMode">
                <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Full Name</label>
                            <p class="font-bold text-slate-800" x-text="viewData.client.name"></p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Company</label>
                            <p class="font-bold text-slate-800" x-text="viewData.client.company || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Email</label>
                            <p class="text-sm text-slate-600" x-text="viewData.client.email || 'N/A'"></p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Phone</label>
                            <p class="text-sm text-slate-600" x-text="viewData.client.phone || 'N/A'"></p>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Address</label>
                        <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg mt-1" x-text="viewData.client.address || 'No address'"></p>
                    </div>
                    
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Recent Pallets</label>
                        <div class="space-y-2">
                            <template x-for="pallet in viewData.pallets" :key="pallet.id">
                                <div class="flex justify-between text-xs border-b pb-2">
                                    <span x-text="pallet.id + ' - ' + pallet.description"></span>
                                    <span class="font-bold text-blue-600" x-text="'$' + pallet.price"></span>
                                </div>
                            </template>
                            <template x-if="viewData.pallets.length === 0">
                                <p class="text-xs text-slate-400 italic">No pallets found for this client.</p>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="!viewMode">
                <form :action="actionUrl" method="POST" class="p-8 space-y-4">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Name</label>
                        <input type="text" name="name" x-model="formData.name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="email" name="email" x-model="formData.email" placeholder="Email" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2">
                        <input type="text" name="phone" x-model="formData.phone" placeholder="Phone" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2">
                    </div>
                    <input type="text" name="company" x-model="formData.company" placeholder="Company" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2">
                    <textarea name="address" x-model="formData.address" placeholder="Address" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2"></textarea>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="openModal = false" class="text-slate-400 font-bold text-xs uppercase">Cancel</button>
                        <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-xl font-bold text-xs uppercase" x-text="editMode ? 'Update' : 'Save'"></button>
                    </div>
                </form>
            </template>

            <template x-if="viewMode">
                <div class="p-6 bg-slate-50 text-right">
                    <button @click="openModal = false" class="bg-slate-800 text-white px-6 py-2 rounded-lg text-xs font-bold uppercase tracking-widest">Close</button>
                </div>
            </template>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection