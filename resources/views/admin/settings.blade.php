@extends('layouts.admin')

@section('content')
<div class="w-full animate-in fade-in duration-500 px-8 py-6">
    <div class="mb-8">
        <h1 class="text-[28px] font-bold text-[#1e293b] tracking-tight">Settings</h1>
        <p class="text-[14px] text-[#64748b] font-medium mt-1">Manage system configuration and preferences</p>
    </div>

    <div class="flex items-center gap-10 border-b border-slate-200 mb-8">
        <button class="pb-4 text-[14px] font-bold text-[#2563eb] border-b-2 border-[#2563eb] flex items-center gap-2">
            <i class="fas fa-cog text-xs"></i> General
        </button>
        <button class="pb-4 text-[14px] font-bold text-[#94a3b8] hover:text-[#64748b] flex items-center gap-2 transition-colors">
            <i class="fas fa-link text-xs"></i> Integrations
        </button>
        <button class="pb-4 text-[14px] font-bold text-[#94a3b8] hover:text-[#64748b] flex items-center gap-2 transition-colors">
            <i class="fas fa-users text-xs"></i> Users
        </button>
        <button class="pb-4 text-[14px] font-bold text-[#94a3b8] hover:text-[#64748b] flex items-center gap-2 transition-colors">
            <i class="fas fa-bell text-xs"></i> Notifications
        </button>
    </div>

    <div class="space-y-12">
        <section>
            <h3 class="text-[18px] font-bold text-[#1e293b] mb-4">Account Information</h3>
            <div class="bg-white rounded-xl border border-slate-100 divide-y divide-slate-100 shadow-sm">
                <div class="flex justify-between items-center p-5">
                    <span class="text-[14px] font-medium text-slate-500">Email</span>
                    <span class="text-[14px] font-medium text-slate-700">sal@it-investmentrecoveries.com</span>
                </div>
                <div class="flex justify-between items-center p-5">
                    <span class="text-[14px] font-medium text-slate-500">Name</span>
                    <span class="text-[14px] font-medium text-slate-700">Sal</span>
                </div>
                <div class="flex justify-between items-center p-5">
                    <span class="text-[14px] font-medium text-slate-500">Role</span>
                    <span class="bg-blue-50 text-[#2563eb] px-3 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider">Admin</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-[18px] font-bold text-[#1e293b] mb-4">System Information</h3>
            <div class="bg-white rounded-xl border border-slate-100 divide-y divide-slate-100 shadow-sm">
                <div class="flex justify-between items-center p-5">
                    <span class="text-[14px] font-medium text-slate-500">Application Version</span>
                    <span class="text-[14px] font-medium text-slate-700">1.0.0</span>
                </div>
                <div class="flex justify-between items-center p-5">
                    <span class="text-[14px] font-medium text-slate-500">Environment</span>
                    <span class="text-[14px] font-medium text-slate-700">Production</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-[18px] font-bold text-[#1e293b] mb-4">Preferences</h3>
            <div class="bg-white rounded-xl border border-slate-100 divide-y divide-slate-100 shadow-sm">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <p class="text-[15px] font-bold text-slate-800">Dark Mode</p>
                        <p class="text-[12px] text-slate-400">Switch to dark theme</p>
                    </div>
                    <button class="w-12 h-6 bg-slate-200 rounded-full relative transition-colors">
                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow-sm"></div>
                    </button>
                </div>
                <div class="flex justify-between items-center p-6">
                    <div>
                        <p class="text-[15px] font-bold text-slate-800">Email Notifications</p>
                        <p class="text-[12px] text-slate-400">Receive email updates</p>
                    </div>
                    <button class="w-12 h-6 bg-[#2563eb] rounded-full relative transition-colors">
                        <div class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full shadow-sm"></div>
                    </button>
                </div>
            </div>
        </section>

        <div class="pt-4">
            <button class="bg-[#f1f5f9] hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-lg font-bold text-[14px] transition-colors">
                Logout
            </button>
        </div>
    </div>
</div>
@endsection