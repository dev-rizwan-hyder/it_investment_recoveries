@extends('layouts.admin')

@section('title', 'Departments')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] p-6 lg:p-10 antialiased font-sans">
    
    <!-- Header Block -->
    <div class="max-w-8xl mx-auto flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6 no-print">
        <div>
            <nav class="flex mb-2 space-x-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                <span>Admin</span>
                <span class="text-slate-300">/</span>
                <span class="text-indigo-600">Organization</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Departments Directory</h1>
            <p class="text-slate-500 mt-1 text-sm font-medium">Manage organization departments, view staff allocations, and structure units.</p>
        </div>
        
        <button type="button" onclick="openAddModal()" 
           class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-200 active:scale-95">
            <i class="fas fa-plus mr-2 text-[10px]"></i>
            Add Department
        </button>
    </div>

    <!-- Quick Stats Cards Grid -->
    <div class="max-w-8xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 no-print">
        <!-- Card 1: Total Departments -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Departments</span>
                <span class="text-3xl font-black text-slate-900 mt-1 block">{{ $departments->total() }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg border border-indigo-100">
                <i class="fa-solid fa-sitemap"></i>
            </div>
        </div>

        <!-- Card 2: Total Employees Assigned -->
        @php
            $totalEmployees = \App\Models\Employee::count();
            $assignedEmployees = \App\Models\Employee::whereNotNull('employee_department_id')->count();
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Assigned Employees</span>
                <span class="text-3xl font-black text-emerald-600 mt-1 block">{{ $assignedEmployees }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg border border-emerald-100">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Card 3: Unassigned Employees -->
        @php
            $unassignedEmployees = $totalEmployees - $assignedEmployees;
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Unassigned Employees</span>
                <span class="text-3xl font-black text-amber-600 mt-1 block">{{ $unassignedEmployees }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-lg border border-amber-100">
                <i class="fa-solid fa-user-minus"></i>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <div class="max-w-8xl mx-auto mb-6">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-exclamation-triangle text-rose-600 text-lg"></i>
                    <span class="font-bold">Validation Errors:</span>
                </div>
                <ul class="list-disc ml-8 space-y-1 text-sm font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Filters Toolbar -->
    <div class="max-w-8xl mx-auto mb-6 no-print">
        <form action="{{ route('admin.departments.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="relative flex-grow max-w-xs">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search departments..."
                    class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 shadow-sm font-medium transition-all">
            </div>

            <!-- Search Buttons -->
            <button type="submit"
                class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold uppercase tracking-wider rounded-xl px-5 py-3 transition shadow-sm">
                Search
            </button>

            <!-- Reset Filters -->
            @if(!empty($search))
                <a href="{{ route('admin.departments.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center border border-slate-200 transition-all shadow-sm">
                    <i class="fas fa-times mr-2 opacity-70"></i> Clear Filter
                </a>
            @endif
        </form>
    </div>

    <!-- Main List Table -->
    <div class="max-w-8xl mx-auto bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] uppercase font-black text-slate-400 tracking-[0.12em]">
                        <th class="px-8 py-5 w-24 text-center">ID</th>
                        <th class="px-8 py-5 min-w-[200px]">Department</th>
                        <th class="px-8 py-5 text-center w-48">Allocated Staff</th>
                        <th class="px-8 py-5 text-center w-56">Created At</th>
                        <th class="px-8 py-5 text-right w-44">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($departments as $dept)
                    <tr class="hover:bg-slate-50/40 transition-all duration-200 group">
                        <!-- ID -->
                        <td class="px-8 py-5 text-center whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 bg-slate-100 text-[10px] font-bold text-slate-650 rounded-lg border border-slate-200/50 shadow-sm">
                                #{{ $dept->id }}
                            </span>
                        </td>

                        <!-- Department Name -->
                        <td class="px-8 py-5">
                            <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-650 transition-colors">
                                {{ $dept->department }}
                            </div>
                        </td>

                        <!-- Allocated Staff Count -->
                        <td class="px-8 py-5 text-center whitespace-nowrap">
                            @php
                                $staffCount = $dept->employees->count();
                            @endphp
                            @if($staffCount > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-xs font-bold shadow-sm">
                                    <i class="fa-solid fa-users text-[10px]"></i>
                                    {{ $staffCount }} {{ Str::plural('Staff', $staffCount) }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 text-slate-400 border border-slate-200 rounded-full text-xs font-medium">
                                    <i class="fa-solid fa-users-slash text-[10px]"></i>
                                    Empty
                                </span>
                            @endif
                        </td>

                        <!-- Created At -->
                        <td class="px-8 py-5 text-center whitespace-nowrap">
                            <span class="text-xs font-medium text-slate-500">
                                {{ $dept->created_at ? $dept->created_at->format('M d, Y H:i') : '---' }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-8 py-5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" 
                                        onclick="openEditModal({{ $dept->id }}, '{{ addslashes($dept->department) }}')" 
                                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all border border-transparent hover:border-amber-100 shadow-sm hover:shadow-amber-50/50" 
                                        title="Edit Department">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </button>
                                
                                <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to delete this department?')" 
                                            class="w-8 h-8 flex items-center justify-center text-slate-350 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all border border-transparent hover:border-rose-100 shadow-sm hover:shadow-rose-50/50" 
                                            title="Delete Department">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-folder-open text-slate-200 text-5xl mb-4"></i>
                                <h3 class="text-slate-800 font-extrabold">No Departments Found</h3>
                                <p class="text-slate-400 text-xs mt-1">Create organization units to map staff and policies.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($departments->hasPages())
        <div class="bg-slate-50/50 px-8 py-5 border-t border-slate-200 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Total: {{ $departments->total() }} Units</span>
            <div>
                {{ $departments->links('pagination::simple-tailwind') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Department Modal -->
<div id="addDeptModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-10 overflow-hidden transform transition-all border border-slate-100">
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Add Department</h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-650 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('admin.departments.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="add_department_input" class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Department Name <span class="text-red-500">*</span></label>
                <input type="text" id="add_department_input" name="department" required placeholder="e.g. Finance"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all font-medium">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" 
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-md hover:shadow-indigo-100">
                    Save Department
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Department Modal -->
<div id="editDeptModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 z-10 overflow-hidden transform transition-all border border-slate-100">
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Edit Department</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-650 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editDeptForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_department_input" class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Department Name <span class="text-red-500">*</span></label>
                <input type="text" id="edit_department_input" name="department" required placeholder="e.g. Operations"
                       class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all font-medium">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" 
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-md hover:shadow-indigo-100">
                    Update Department
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        const modal = document.getElementById('addDeptModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('add_department_input').focus();
    }

    function closeAddModal() {
        const modal = document.getElementById('addDeptModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.getElementById('add_department_input').value = '';
    }

    function openEditModal(id, currentName) {
        const modal = document.getElementById('editDeptModal');
        const form = document.getElementById('editDeptForm');
        const input = document.getElementById('edit_department_input');

        form.action = `/admin/departments/${id}`;
        input.value = currentName;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        input.focus();
    }

    function closeEditModal() {
        const modal = document.getElementById('editDeptModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.getElementById('edit_department_input').value = '';
    }
</script>
@endsection
