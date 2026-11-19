@extends('layouts.admin')

@section('content')
<div x-data="trashManager()" class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Trash Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage, inspect, and restore deleted items across the system</p>
        </div>
        
        <!-- Stats Badge -->
        <div class="bg-red-50 text-red-600 px-4 py-2 rounded-xl flex items-center gap-3 border border-red-100 shadow-sm">
            <div class="bg-red-100 p-2 rounded-lg text-red-600">
                <i class="fas fa-trash-alt"></i>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-wider opacity-80">Total Trashed</div>
                <div class="text-xl font-black leading-none mt-0.5">{{ $totalTrashed }}</div>
            </div>
        </div>
    </div>

    <!-- Restore Success Alert Notice -->
    <div x-show="restoreNotice.show" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
         class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm flex items-start justify-between gap-4"
         style="display: none;">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-emerald-500 text-white rounded-xl shrink-0 mt-0.5 shadow-sm">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-emerald-900 text-base">Item Restored Successfully</h4>
                <p class="text-sm text-emerald-700 mt-0.5" x-text="restoreNotice.message"></p>
            </div>
        </div>
        <button @click="restoreNotice.show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg transition-colors">
            <i class="fas fa-times text-base"></i>
        </button>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row justify-between gap-4">
            
            <!-- Section Tabs -->
            <div class="flex-1 overflow-x-auto pb-2 lg:pb-0 hide-scrollbar">
                <div class="flex gap-2 min-w-max">
                    <button @click="changeSection('all')" 
                            :class="section === 'all' ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                        All Items
                        @if($totalTrashed > 0)
                            <span :class="section === 'all' ? 'bg-slate-600 text-white' : 'bg-slate-200 text-slate-700'" class="px-2 py-0.5 rounded-full text-xs">{{ $totalTrashed }}</span>
                        @endif
                    </button>
                    
                    @foreach($sections as $key => $label)
                        @if(($sectionCounts[$key] ?? 0) > 0)
                            <button @click="changeSection('{{ $key }}')" 
                                    :class="section === '{{ $key }}' ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                                {{ $label }}
                                <span :class="section === '{{ $key }}' ? 'bg-slate-600 text-white' : 'bg-slate-200 text-slate-700'" class="px-2 py-0.5 rounded-full text-xs">{{ $sectionCounts[$key] }}</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Search -->
            <div class="relative w-full lg:w-72 shrink-0">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input x-model="searchQuery" 
                       @input.debounce.500ms="fetchData()"
                       type="text" 
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                       placeholder="Search deleted items...">
            </div>
            
            <!-- Empty Trash Button -->
            <button x-show="section !== 'all' && items.length > 0" 
                    @click="confirmEmptyTrash()"
                    class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white border border-red-100 hover:border-red-500 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2 whitespace-nowrap shrink-0">
                <i class="fas fa-dumpster-fire"></i> Empty Trash
            </button>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div x-show="selectedItems.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="bg-blue-600 text-white rounded-2xl shadow-lg p-4 mb-6 flex flex-wrap items-center justify-between gap-4 z-10 relative"
         style="display: none;">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg">
                <i class="fas fa-check-square text-lg"></i>
            </div>
            <div>
                <span class="font-bold text-lg" x-text="selectedItems.length"></span>
                <span class="text-blue-100 ml-1">items selected</span>
            </div>
        </div>
        <div class="flex gap-3">
            <button @click="bulkRestore()" class="px-4 py-2 bg-white text-blue-600 hover:bg-blue-50 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-undo-alt"></i> Restore Selected
            </button>
            <button @click="confirmBulkDelete()" class="px-4 py-2 bg-red-500 text-white hover:bg-red-400 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center gap-2 border border-red-400">
                <i class="fas fa-trash-alt"></i> Delete Permanently
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative min-h-[400px]">
        
        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <i class="fas fa-circle-notch fa-spin text-3xl text-blue-500"></i>
                <p class="text-sm font-semibold text-slate-600">Loading trashed items...</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 w-12 text-center">
                            <input type="checkbox" 
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                   :checked="items.length > 0 && selectedItems.length === items.length"
                                   @change="toggleSelectAll()">
                        </th>
                        <th class="p-4 font-semibold">Item Details</th>
                        <th class="p-4 font-semibold">Section</th>
                        <th class="p-4 font-semibold">Deleted At</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <template x-for="item in items" :key="item.section + '-' + item.id">
                        <tr class="hover:bg-slate-50/50 transition-colors group" :class="{'bg-blue-50/30': isSelected(item)}">
                            <td class="p-4 text-center">
                                <input type="checkbox" 
                                       class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                       :value="item.id"
                                       :checked="isSelected(item)"
                                       @change="toggleSelect(item)">
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <template x-if="item.image_url">
                                        <img :src="item.image_url" class="w-12 h-12 rounded-xl object-cover border border-slate-200 bg-slate-50 shrink-0 cursor-pointer shadow-sm hover:scale-105 transition-transform" @click="openViewModal(item)" alt="Thumbnail">
                                    </template>
                                    <template x-if="!item.image_url">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center shrink-0">
                                            <i class="fas fa-box text-lg"></i>
                                        </div>
                                    </template>
                                    <div>
                                        <div class="font-bold text-slate-800 hover:text-blue-600 transition-colors cursor-pointer" @click="openViewModal(item)" x-text="item.title"></div>
                                        <div class="text-xs text-slate-500 mt-0.5" x-text="item.subtitle"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold" x-text="item.section_label"></span>
                            </td>
                            <td class="p-4">
                                <div class="text-slate-700" x-text="item.deleted_at"></div>
                                <div class="text-xs text-slate-400 mt-0.5" x-text="item.deleted_at_diff"></div>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button @click="openViewModal(item)" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5" title="View Details">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button @click="restoreItem(item)" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5" title="Restore Item">
                                    <i class="fas fa-undo"></i> Restore
                                </button>
                                <button @click="confirmDelete(item)" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors inline-flex" title="Delete Permanently">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    
                    <!-- Empty State -->
                    <tr x-show="!loading && items.length === 0">
                        <td colspan="5" class="p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4 text-slate-400 text-2xl">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700">No trashed items found</h3>
                            <p class="text-slate-500 text-sm mt-1">
                                <span x-show="searchQuery">No results matching your search.</span>
                                <span x-show="!searchQuery">The trash is currently empty for this section.</span>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Item Details View Modal -->
    <div x-show="viewModal.show" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition.opacity
         style="display: none;">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col transform transition-all overflow-hidden"
             @click.away="viewModal.show = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-100 text-blue-600 rounded-xl">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-slate-800" x-text="viewModal.data?.title || viewModal.item?.title || 'Item Details'"></h3>
                            <span class="px-2.5 py-0.5 bg-slate-200 text-slate-700 rounded-full text-xs font-semibold" x-text="viewModal.data?.section_label || viewModal.item?.section_label"></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Deleted At: <span class="font-medium text-slate-700" x-text="viewModal.data?.deleted_at || viewModal.item?.deleted_at"></span></p>
                    </div>
                </div>
                <button @click="viewModal.show = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto space-y-6">
                <div x-show="viewModal.loading" class="flex flex-col items-center justify-center py-12 gap-3 text-slate-500">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-blue-500"></i>
                    <p class="text-sm font-semibold">Loading item details...</p>
                </div>
                
                <template x-if="!viewModal.loading && viewModal.data">
                    <div class="space-y-6">
                        <!-- Photos Gallery -->
                        <template x-if="viewModal.data.photos && viewModal.data.photos.length > 0">
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                                    <i class="fas fa-images"></i> Item Photos (<span x-text="viewModal.data.photos.length"></span>)
                                </h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <template x-for="(photoUrl, pIdx) in viewModal.data.photos" :key="pIdx">
                                        <a :href="photoUrl" target="_blank" class="block aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50 hover:opacity-95 transition-opacity group relative">
                                            <img :src="photoUrl" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="Photo">
                                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                <i class="fas fa-external-link-alt text-sm"></i>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Attributes List -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                                <i class="fas fa-list-alt"></i> Properties & Information
                            </h4>
                            <div class="bg-slate-50 rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                <template x-for="(val, key) in viewModal.data.attributes" :key="key">
                                    <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-start justify-between gap-2 text-sm">
                                        <span class="font-semibold text-slate-600 sm:w-1/3 shrink-0" x-text="key"></span>
                                        <div class="text-slate-800 font-medium sm:w-2/3 break-words whitespace-pre-wrap" x-text="val"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center gap-3">
                <button @click="viewModal.show = false" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-200 font-semibold text-sm transition-colors">
                    Close
                </button>
                <button @click="restoreItemFromModal()" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class="fas fa-undo"></i> Restore Item
                </button>
            </div>
        </div>
    </div>

    <!-- Confirmation Delete Modal -->
    <div x-show="modal.show" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition.opacity
         style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 transform transition-all"
             @click.away="modal.show = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
            
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto mb-4 text-xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h3 class="text-xl font-bold text-center text-slate-800 mb-2" x-text="modal.title"></h3>
            <p class="text-center text-slate-500 text-sm mb-6" x-text="modal.message"></p>
            
            <div class="flex gap-3 justify-center">
                <button @click="modal.show = false" class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold transition-colors">
                    Cancel
                </button>
                <button @click="executeModalAction()" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold shadow-sm shadow-red-500/30 transition-colors flex items-center gap-2">
                    <i class="fas fa-trash-alt"></i> <span x-text="modal.confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@push('scripts')
<script>
    function trashManager() {
        return {
            section: '{{ $currentSection }}',
            searchQuery: '',
            items: [],
            selectedItems: [],
            loading: false,
            
            restoreNotice: {
                show: false,
                message: ''
            },
            
            viewModal: {
                show: false,
                loading: false,
                item: null,
                data: null
            },
            
            modal: {
                show: false,
                title: '',
                message: '',
                confirmText: 'Delete',
                action: null,
                data: null
            },
            
            init() {
                this.fetchData();
            },
            
            changeSection(newSection) {
                this.section = newSection;
                this.selectedItems = [];
                this.fetchData();
            },
            
            async fetchData() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        section: this.section,
                        search: this.searchQuery
                    });
                    
                    const response = await fetch(`/admin/trash/data?${params.toString()}`);
                    const result = await response.json();
                    
                    if (result.success) {
                        this.items = result.data;
                        this.selectedItems = this.selectedItems.filter(selected => 
                            this.items.some(item => item.id === selected.id && item.section === selected.section)
                        );
                    }
                } catch (error) {
                    console.error('Error fetching trash data:', error);
                    toastr.error('Failed to load trash data');
                } finally {
                    this.loading = false;
                }
            },

            async openViewModal(item) {
                this.viewModal.item = item;
                this.viewModal.data = null;
                this.viewModal.loading = true;
                this.viewModal.show = true;

                try {
                    const response = await fetch(`/admin/trash/${item.section}/${item.id}/details`);
                    const result = await response.json();
                    if (result.success) {
                        this.viewModal.data = result.data;
                    } else {
                        toastr.error(result.message || 'Failed to fetch item details');
                    }
                } catch (error) {
                    console.error('Error loading details:', error);
                    toastr.error('Failed to load item details');
                } finally {
                    this.viewModal.loading = false;
                }
            },

            restoreItemFromModal() {
                if (this.viewModal.item) {
                    this.restoreItem(this.viewModal.item);
                    this.viewModal.show = false;
                }
            },
            
            // Selection logic
            toggleSelect(item) {
                const index = this.selectedItems.findIndex(i => i.id === item.id && i.section === item.section);
                if (index > -1) {
                    this.selectedItems.splice(index, 1);
                } else {
                    this.selectedItems.push({ id: item.id, section: item.section });
                }
            },
            
            toggleSelectAll() {
                if (this.selectedItems.length === this.items.length) {
                    this.selectedItems = [];
                } else {
                    this.selectedItems = this.items.map(item => ({ id: item.id, section: item.section }));
                }
            },
            
            isSelected(item) {
                return this.selectedItems.some(i => i.id === item.id && i.section === item.section);
            },
            
            // Actions
            async restoreItem(item) {
                try {
                    const response = await fetch(`/admin/trash/${item.section}/${item.id}/restore`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        const titleStr = item.title || 'Item';
                        const message = result.message || `Item "${titleStr}" has been successfully restored!`;
                        
                        this.restoreNotice = {
                            show: true,
                            message: message
                        };

                        toastr.success(message);
                        
                        this.items = this.items.filter(i => !(i.id === item.id && i.section === item.section));
                        this.selectedItems = this.selectedItems.filter(i => !(i.id === item.id && i.section === item.section));
                        
                        setTimeout(() => {
                            this.fetchData();
                        }, 1000);
                    } else {
                        toastr.error(result.message || 'Failed to restore item');
                    }
                } catch (error) {
                    console.error(error);
                    toastr.error('An error occurred while restoring item');
                }
            },
            
            confirmDelete(item) {
                this.modal = {
                    show: true,
                    title: 'Permanent Delete',
                    message: `Are you sure you want to permanently delete "${item.title}"? This action cannot be undone and associated files will be removed.`,
                    confirmText: 'Delete Forever',
                    action: 'single',
                    data: item
                };
            },
            
            async forceDeleteItem(item) {
                try {
                    const response = await fetch(`/admin/trash/${item.section}/${item.id}/force-delete`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        toastr.success(result.message);
                        this.items = this.items.filter(i => !(i.id === item.id && i.section === item.section));
                        this.selectedItems = this.selectedItems.filter(i => !(i.id === item.id && i.section === item.section));
                        this.fetchData();
                    } else {
                        toastr.error(result.message || 'Failed to delete item');
                    }
                } catch (error) {
                    console.error(error);
                    toastr.error('An error occurred');
                }
            },
            
            // Bulk Actions
            async bulkRestore() {
                if (this.selectedItems.length === 0) return;
                
                try {
                    const response = await fetch(`/admin/trash/bulk-restore`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ items: this.selectedItems })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        const message = result.message || `${this.selectedItems.length} item(s) restored successfully!`;
                        this.restoreNotice = {
                            show: true,
                            message: message
                        };
                        toastr.success(message);
                        this.selectedItems = [];
                        this.fetchData();
                    } else {
                        toastr.error(result.message || 'Failed to restore items');
                    }
                } catch (error) {
                    console.error(error);
                    toastr.error('An error occurred');
                }
            },
            
            confirmBulkDelete() {
                this.modal = {
                    show: true,
                    title: 'Delete Selected Items',
                    message: `Are you sure you want to permanently delete ${this.selectedItems.length} items? This action cannot be undone.`,
                    confirmText: `Delete ${this.selectedItems.length} Items`,
                    action: 'bulk',
                    data: null
                };
            },
            
            async bulkForceDelete() {
                try {
                    const response = await fetch(`/admin/trash/bulk-force-delete`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ items: this.selectedItems })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        toastr.success(result.message);
                        this.selectedItems = [];
                        this.fetchData();
                    } else {
                        toastr.error(result.message || 'Failed to delete items');
                    }
                } catch (error) {
                    console.error(error);
                    toastr.error('An error occurred');
                }
            },
            
            confirmEmptyTrash() {
                this.modal = {
                    show: true,
                    title: 'Empty Trash',
                    message: `Are you sure you want to permanently delete ALL items in the ${this.section} section? This action cannot be undone.`,
                    confirmText: 'Empty Trash',
                    action: 'empty',
                    data: null
                };
            },
            
            async emptyTrash() {
                try {
                    const response = await fetch(`/admin/trash/${this.section}/empty`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        toastr.success(result.message);
                        this.selectedItems = [];
                        this.fetchData();
                    } else {
                        toastr.error(result.message || 'Failed to empty trash');
                    }
                } catch (error) {
                    console.error(error);
                    toastr.error('An error occurred');
                }
            },
            
            executeModalAction() {
                if (this.modal.action === 'single') {
                    this.forceDeleteItem(this.modal.data);
                } else if (this.modal.action === 'bulk') {
                    this.bulkForceDelete();
                } else if (this.modal.action === 'empty') {
                    this.emptyTrash();
                }
                this.modal.show = false;
            }
        };
    }
</script>
@endpush
@endsection
