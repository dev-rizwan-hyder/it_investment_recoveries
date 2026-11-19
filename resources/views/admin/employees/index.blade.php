@extends('layouts.admin')

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
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Personnel Directory</h1>
            <p class="text-slate-500 mt-1 text-sm font-medium">Manage registered employees, policies, assignments, and dossier records.</p>
        </div>
        
        <a href="{{ route('admin.employees.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-200 active:scale-95">
            <i class="fas fa-plus mr-2 text-[10px]"></i>
            Add Employee
        </a>
    </div>

    <!-- Quick Stats Cards Grid -->
    <div class="max-w-8xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 no-print">
        <!-- Card 1: Total Employees -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Personnel</span>
                <span class="text-3xl font-black text-slate-900 mt-1 block">{{ $employees->total() }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg border border-indigo-100">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Card 2: Active Employees -->
        @php
            $activeCount = \App\Models\Employee::where('is_resigned', 0)->count();
            $offboardedCount = \App\Models\Employee::where('is_resigned', 1)->count();
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Active Staff</span>
                <span class="text-3xl font-black text-emerald-600 mt-1 block">{{ $activeCount }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg border border-emerald-100">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Card 3: Offboarded Employees -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Offboarded / Resigned</span>
                <span class="text-3xl font-black text-rose-600 mt-1 block">{{ $offboardedCount }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-lg border border-rose-100">
                <i class="fa-solid fa-user-slash"></i>
            </div>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="max-w-8xl mx-auto mb-6 no-print">
        <form action="{{ route('admin.employees.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="relative flex-grow max-w-xs">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..."
                    class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 shadow-sm font-medium transition-all">
            </div>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-650 outline-none shadow-sm min-w-[140px] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all cursor-pointer">
                <option value="">All Statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active Only</option>
                <option value="offboarded" @selected(request('status') === 'offboarded')>Offboarded</option>
            </select>

            <!-- Policy/Department Filter -->
            <select name="policy_id" onchange="this.form.submit()"
                class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-650 outline-none shadow-sm min-w-[200px] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all cursor-pointer">
                <option value="">All Policies / Departments</option>
                @foreach ($policies as $policy)
                    @php
                        $deptName = $policy->department ?? $policy->department_name ?? $policy->name ?? 'Department';
                        $polTitle = $policy->title ?? $policy->policy_title ?? $policy->name ?? 'Policy';
                    @endphp
                    <option value="{{ $policy->id }}" @selected(request('policy_id') == $policy->id)>
                        {{ $deptName }} - {{ $polTitle }}
                    </option>
                @endforeach
            </select>

            <!-- Reset Filters -->
            @if(request()->anyFilled(['search', 'status', 'policy_id']))
                <a href="{{ route('admin.employees.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center border border-slate-200 transition-all shadow-sm">
                    <i class="fas fa-times mr-2 opacity-70"></i> Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Main List Container -->
    <div class="max-w-8xl mx-auto bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] uppercase font-black text-slate-400 tracking-[0.12em]">
                        <th class="px-6 py-5 text-center w-16">Photo</th>
                        <th class="px-6 py-5 w-28">Employee ID</th>
                        <th class="px-6 py-5 min-w-[150px]">Name</th>
                        <th class="px-6 py-5 min-w-[200px]">Email</th>
                        <th class="px-6 py-5 w-32">Phone</th>
                        <th class="px-6 py-5 text-center w-28">DOB</th>
                        <th class="px-6 py-5 text-center min-w-[180px]">Designation/Policy</th>
                        <th class="px-6 py-5 text-center min-w-[130px]">Department</th>
                        <th class="px-6 py-5 text-center w-32">Joining Date</th>
                        <th class="px-6 py-5 text-center w-24">Status</th>
                        <th class="px-6 py-5 text-right w-44">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $employee)
                    <tr class="hover:bg-slate-50/40 transition-all duration-200 group">
                        <!-- Photo -->
                        <td class="px-6 py-5 text-center">
                            <div class="relative inline-block flex-shrink-0">
                                @if ($employee->employee_picture)
                                    @php
                                        $pictureUrl = (strpos($employee->employee_picture, '/') !== false || strpos($employee->employee_picture, 'employees/') === 0)
                                            ? asset('storage/' . $employee->employee_picture)
                                            : asset('employee-profile/' . $employee->employee_picture);
                                    @endphp
                                    <img src="{{ $pictureUrl }}" 
                                         class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-sm transition-transform duration-300 group-hover:scale-[1.05]">
                                @else
                                    @php
                                        $bgColors = ['bg-indigo-600', 'bg-purple-600', 'bg-blue-600', 'bg-teal-600', 'bg-emerald-600', 'bg-pink-600', 'bg-amber-600'];
                                        $bgIndex = crc32($employee->name) % count($bgColors);
                                        $avatarBg = $bgColors[$bgIndex];
                                    @endphp
                                    <div class="w-10 h-10 rounded-xl {{ $avatarBg }} flex items-center justify-center text-white text-xs font-bold border border-white/20 shadow-sm transition-transform duration-300 group-hover:scale-[1.05]">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="absolute -top-1 -right-1 w-3 h-3 bg-white p-0.5 rounded-full shadow-sm">
                                    <div class="w-full h-full rounded-full {{ $employee->is_resigned ? 'bg-slate-350' : 'bg-emerald-500 animate-pulse' }}"></div>
                                </span>
                            </div>
                        </td>

                        <!-- Employee ID -->
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="inline-block whitespace-nowrap px-2.5 py-1 bg-slate-100 text-[10px] font-bold text-slate-650 rounded-lg border border-slate-200/50 shadow-sm">
                                {{ $employee->employee_id }}
                            </span>
                        </td>

                        <!-- Name -->
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-650 transition-colors">
                                {{ $employee->name }}
                            </div>
                        </td>

                        <!-- Email -->
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="text-xs font-medium text-slate-500 whitespace-nowrap">
                                {{ $employee->email }}
                            </span>
                        </td>

                        <!-- Phone -->
                        <td class="px-6 py-5 whitespace-nowrap">
                            @if($employee->mobile_number)
                                <span class="text-xs font-bold text-slate-700 whitespace-nowrap">
                                    {{ $employee->mobile_number }}
                                </span>
                            @else
                                <span class="text-xs text-slate-300 italic">---</span>
                            @endif
                        </td>

                        <!-- DOB -->
                        <td class="px-6 py-5 text-center whitespace-nowrap">
                            @if($employee->dob)
                                <span class="text-xs font-medium text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($employee->dob)->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-xs text-slate-300 italic">---</span>
                            @endif
                        </td>

                        <!-- Policy / Designation -->
                        <td class="px-6 py-5 text-center">
                            @php
                                $policyTitle = data_get($employee->application_data, 'selected_policy_title', $employee->designation_id ?? 'General Staff');
                            @endphp
                            <div class="inline-block px-3 py-1 bg-indigo-50/50 text-indigo-750 border border-indigo-100/60 rounded-xl text-[11px] font-extrabold shadow-sm tracking-tight">
                                {{ $policyTitle }}
                            </div>
                        </td>

                        <!-- Department -->
                        <td class="px-6 py-5 text-center">
                            @php
                                $deptName = data_get($employee->application_data, 'selected_policy_department', $employee->employee_department_id);
                            @endphp
                            <span class="text-xs font-semibold text-slate-600">
                                {{ $deptName ?: '---' }}
                            </span>
                        </td>

                        <!-- Joining Date -->
                        <td class="px-6 py-5 text-center whitespace-nowrap">
                            @if($employee->joining_date)
                                <span class="text-xs font-bold text-slate-750 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-xs text-slate-300 italic">---</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-5 text-center whitespace-nowrap">
                            @if($employee->is_resigned)
                                <span class="inline-flex items-center px-2.5 py-0.5 bg-slate-50 text-slate-455 border border-slate-200/80 rounded-full text-[9px] font-black uppercase tracking-wider shadow-sm whitespace-nowrap">
                                    Offboarded
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-600 border border-emerald-100/80 rounded-full text-[9px] font-black uppercase tracking-wider shadow-sm whitespace-nowrap">
                                    Active
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('admin.employees.show', $employee->id) }}" 
                                   class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all border border-transparent hover:border-indigo-100 shadow-sm hover:shadow-indigo-50/50" title="View Dossier">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                                </a>
                                <a href="{{ route('admin.employees.profile-report', $employee->id) }}" 
                                   class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all border border-transparent hover:border-blue-100 shadow-sm hover:shadow-blue-50/50" title="Profile Report">
                                    <i class="fa-solid fa-file-lines text-[11px]"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" 
                                   class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all border border-transparent hover:border-amber-100 shadow-sm hover:shadow-amber-50/50" title="Edit Profile">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Archive employee record?')" 
                                            class="w-8 h-8 flex items-center justify-center text-slate-350 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all border border-transparent hover:border-rose-100 shadow-sm hover:shadow-rose-50/50" title="Archive">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-users-slash text-slate-200 text-5xl mb-4"></i>
                                <h3 class="text-slate-800 font-extrabold">No Records Found</h3>
                                <p class="text-slate-400 text-xs mt-1">Register new employees or adjust search criteria to populate directory.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="bg-slate-50/50 px-8 py-5 border-t border-slate-200 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Total: {{ $employees->total() }} Members</span>
            <div>
                {{ $employees->links('pagination::simple-tailwind') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection