@extends('layouts.admin')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    actionUrl: '{{ route('admin.expenses.store') }}',
    formData: {
        category: '', amount: '', date: '{{ date('Y-m-d') }}', 
        payment_method: '', vendor: '', receipt_url: '', 
        description: '', notes: ''
    },
        editExpense(expense) {
        this.editMode = true;
        this.openModal = true;
        
        this.formData = { ...expense };
        
        if (this.formData.date) {
            this.formData.date = this.formData.date.split('T')[0];
        }
        
        this.actionUrl = `/admin/expenses/${expense.id}`;
    },
    resetForm() {
        this.editMode = false;
        this.formData = { category: '', amount: '', date: '{{ date('Y-m-d') }}', payment_method: '', vendor: '', receipt_url: '', description: '', notes: '' };
        this.actionUrl = '{{ route('admin.expenses.store') }}';
    }
}">
    
    {{-- Success Message Alert --}}
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         class="mb-6 bg-emerald-500 text-white px-6 py-4 rounded-2xl flex items-center justify-between shadow-lg animate-in slide-in-from-top-4">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="hover:scale-110 transition-transform">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#1e293b] tracking-tight">Expenses</h1>
            <p class="text-[13px] text-[#94a3b8] font-bold mt-1">Track and manage business expenses</p>
        </div>
        <div class="flex gap-3">
            <button class="bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#475569] px-6 py-3 rounded-xl font-bold text-[14px] flex items-center transition-colors">
                <i class="fas fa-download mr-2 opacity-40"></i> Export CSV
            </button>
            <button @click="openModal = true" class="bg-[#2563eb] hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold text-[14px] flex items-center shadow-lg shadow-blue-500/20 transition-all">
                <i class="fas fa-plus mr-2 text-xs"></i> Add Expense
            </button>
        </div>
    </div>

    {{-- Stats Cards (Dynamic) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#f1f5f9] flex justify-between items-center">
            <div>
                <p class="text-[11px] font-black text-[#94a3b8] uppercase tracking-[0.15em] mb-1">Total Expenses</p>
                <h3 class="text-3xl font-black text-[#1e293b] tracking-tighter">${{ number_format($totalExpenses, 2) }}</h3>
            </div>
            <i class="fas fa-dollar-sign text-3xl text-blue-500 opacity-20"></i>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#f1f5f9] flex justify-between items-center">
            <div>
                <p class="text-[11px] font-black text-[#94a3b8] uppercase tracking-[0.15em] mb-1">Total Entries</p>
                <h3 class="text-3xl font-black text-[#1e293b] tracking-tighter">{{ $totalEntries }}</h3>
            </div>
            <i class="fas fa-chart-line text-3xl text-emerald-500 opacity-20"></i>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#f1f5f9] flex justify-between items-center">
            <div>
                <p class="text-[11px] font-black text-[#94a3b8] uppercase tracking-[0.15em] mb-1">Average Expense</p>
                <h3 class="text-3xl font-black text-[#1e293b] tracking-tighter">${{ number_format($averageExpense, 2) }}</h3>
            </div>
            <i class="fas fa-calendar-alt text-3xl text-purple-500 opacity-20"></i>
        </div>
    </div>
             
    {{-- Filters Section --}}
    <form action="{{ route('admin.expenses') }}" method="GET" class="bg-white p-5 rounded-2xl shadow-sm border border-[#f1f5f9] mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="text-[10px] font-black text-[#94a3b8] uppercase tracking-widest mb-2 block">Category</label>
            <select name="category" class="w-full bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-4 py-3 text-[13px] font-bold text-[#475569] outline-none">
                <option>All Categories</option>
                @foreach(['Worker Pay', 'Rent', 'Utilities', 'Software', 'Office Supplies', 'Equipment', 'Marketing', 'Shipping', 'Professional Services', 'Insurance', 'Taxes', 'Other'] as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] font-black text-[#94a3b8] uppercase tracking-widest mb-2 block">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-4 py-3 text-[13px] font-bold text-[#475569] outline-none">
        </div>
        <div>
            <label class="text-[10px] font-black text-[#94a3b8] uppercase tracking-widest mb-2 block">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-4 py-3 text-[13px] font-bold text-[#475569] outline-none">
        </div>
        <div>
            <label class="text-[10px] font-black text-[#94a3b8] uppercase tracking-widest mb-2 block">Sort By</label>
            <select name="sort" class="w-full bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-4 py-3 text-[13px] font-bold text-[#475569] outline-none">
                <option {{ request('sort') == 'Date (Newest)' ? 'selected' : '' }}>Date (Newest)</option>
                <option {{ request('sort') == 'Amount (High to Low)' ? 'selected' : '' }}>Amount (High to Low)</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors">Search</button>
            <a href="{{ route('admin.expenses') }}" class="bg-slate-100 p-3 rounded-xl text-slate-400 hover:bg-slate-200"><i class="fas fa-undo"></i></a>
        </div>
    </form>

    {{-- Table Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-[#f1f5f9] overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f8fafc] border-b border-[#f1f5f9] text-[10px] uppercase font-black text-[#94a3b8] tracking-[0.18em]">
                        <th class="px-8 py-5">Date</th>
                        <th class="px-8 py-5">Category</th>
                        <th class="px-8 py-5">Description</th>
                        <th class="px-8 py-5">Amount</th>
                        <th class="px-8 py-5">Payment Method</th>
                        <th class="px-8 py-5">Vendor</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f8fafc]">
                    @foreach($expenses as $expense)
                    <tr class="hover:bg-[#f8fafc]/80 transition-colors">
                        <td class="px-8 py-5 text-[12px] text-[#94a3b8] font-bold">{{ $expense->date->format('m/d/Y') }}</td>
                        <td class="px-8 py-5">
                            <span class="inline-flex items-center px-3 py-1 rounded-md text-[9px] font-black uppercase 
                                {{ $expense->category == 'Software' ? 'bg-blue-100 text-blue-600' : 
                                   ($expense->category == 'Marketing' ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-600') }}">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-[13px] text-[#475569] font-bold">{{ $expense->description }}</td>
                        <td class="px-8 py-5 text-[13px] text-[#1e293b] font-black">${{ number_format($expense->amount, 2) }}</td>
                        <td class="px-8 py-5 text-[12px] text-[#94a3b8] font-bold">{{ $expense->payment_method }}</td>
                        <td class="px-8 py-5 text-[12px] text-[#94a3b8] font-bold">{{ $expense->vendor ?? '—' }}</td>
                        <td class="px-8 py-5 text-right flex justify-end gap-3">
                            <button @click="editExpense({{ $expense->toJson() }})" class="text-blue-500 hover:text-blue-700 transition-all">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition-all"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Summary by Category (Full Width Grid) --}}
    <div class="mt-12">
        <h4 class="text-[14px] font-black text-[#1e293b] mb-6 uppercase tracking-widest flex items-center">
            <i class="fas fa-pie-chart mr-2 text-blue-500"></i> Expenses by Category
        </h4>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($expensesByCategory as $category => $total)
            <div class="bg-white p-5 rounded-2xl border border-[#f1f5f9] shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-[#94a3b8] uppercase tracking-wider mb-2">{{ $category }}</span>
                    <div class="flex justify-between items-end">
                        <h3 class="text-xl font-black text-[#1e293b] tracking-tight">${{ number_format($total, 2) }}</h3>
                        <div class="h-1 w-12 bg-blue-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: 65%"></div> {{-- Optional: Decorative Progress bar --}}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Add Expense Modal --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak x-transition>
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] p-10 shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-black text-[#1e293b]">Add New Expense</h2>
                <button @click="openModal = false" class="text-slate-400 hover:rotate-90 transition-transform"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form action="{{ route('admin.expenses.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Category *</label>
                        <select name="category" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none">
                            <option value="">Select Category</option>
                            <option value="Worker Pay">Worker Pay</option>
                            <option value="Rent">Rent</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Software">Software</option>
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Shipping">Shipping</option>
                            <option value="Professional Services">Professional Services</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Taxes">Taxes</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Amount *</label>
                        <input type="number" step="0.01" name="amount" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Payment Method *</label>
                        <select name="payment_method" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none">
                            <option value="">Select Payment Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Check">Check</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Vendor</label>
                        <input type="text" name="vendor" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none" placeholder="Company or person paid">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Receipt URL</label>
                        <input type="url" name="receipt_url" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none" placeholder="https://...">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Description *</label>
                    <input type="text" name="description" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none" placeholder="Brief description of the expense">
                </div>

                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Notes</label>
                    <textarea name="notes" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold focus:bg-white focus:border-blue-500 transition-all outline-none h-24" placeholder="Additional notes..."></textarea>
                </div>

                <div class="flex justify-end gap-4 mt-10">
                    <button type="button" @click="openModal = false" class="px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all">Cancel</button>
                    <button type="submit" class="bg-[#2563eb] hover:bg-blue-600 text-white px-12 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-blue-500/20 transition-all active:scale-95">Add Expense</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Single Dynamic Modal --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak x-transition>
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] p-10 shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-black text-[#1e293b]" x-text="editMode ? 'Edit Expense' : 'Add New Expense'"></h2>
                <button @click="openModal = false; resetForm()" class="text-slate-400 hover:rotate-90 transition-transform"><i class="fas fa-times text-xl"></i></button>
            </div>
    
            <form :action="actionUrl" method="POST" class="space-y-6">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
    
                <div class="grid grid-cols-2 gap-6">
                    {{-- Category Dropdown --}}
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Category *</label>
                        <select name="category" x-model="formData.category" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none focus:border-blue-500">
                            <option value="">Select Category</option>
                            @foreach(['Worker Pay', 'Rent', 'Utilities', 'Software', 'Office Supplies', 'Equipment', 'Marketing', 'Shipping', 'Professional Services', 'Insurance', 'Taxes', 'Other'] as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
    
                    {{-- Amount Field --}}
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Amount *</label>
                        <input type="number" step="0.01" name="amount" x-model="formData.amount" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none focus:border-blue-500">
                    </div>
    
                    {{-- Date Field --}}
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Date *</label>
                        <input type="date" name="date" x-model="formData.date" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none focus:border-blue-500">
                    </div>
    
                    {{-- Payment Method Dropdown --}}
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Payment Method *</label>
                        <select name="payment_method" x-model="formData.payment_method" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none focus:border-blue-500">
                            <option value="">Select Payment Method</option>
                            @foreach(['Cash', 'Credit Card', 'Debit Card', 'Bank Transfer', 'Check', 'PayPal', 'Other'] as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Vendor</label>
                        <input type="text" name="vendor" x-model="formData.vendor" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Receipt URL</label>
                        <input type="url" name="receipt_url" x-model="formData.receipt_url" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none focus:border-blue-500">
                    </div>
                </div>
    
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Description *</label>
                    <input type="text" name="description" x-model="formData.description" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none focus:border-blue-500">
                </div>
    
                <div>
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 block px-1">Notes</label>
                    <textarea name="notes" x-model="formData.notes" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold outline-none h-24 focus:border-blue-500"></textarea>
                </div>
    
                <div class="flex justify-end gap-4 mt-10">
                    <button type="button" @click="openModal = false; resetForm()" class="px-10 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all">Cancel</button>
                    <button type="submit" class="bg-[#2563eb] hover:bg-blue-600 text-white px-12 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl transition-all active:scale-95" x-text="editMode ? 'Update Expense' : 'Add Expense'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection