<form method="POST" action="{{ $action }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="first_name" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">First Name</label>
            <input id="first_name" name="first_name" type="text" required value="{{ old('first_name', $lead->first_name) }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="last_name" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Last Name</label>
            <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $lead->last_name) }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="email" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $lead->email) }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="phone" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Phone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $lead->phone) }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="company_name" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Company Name</label>
            <input id="company_name" name="company_name" type="text" value="{{ old('company_name', $lead->company_name) }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="location" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Location</label>
            <input id="location" name="location" type="text" value="{{ old('location', $lead->location) }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="status" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Status</label>
            <input id="status" name="status" type="text" value="{{ old('status', $lead->status ?: 'New') }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>
    </div>

    <div class="mt-4">
        <label for="address" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Address</label>
        <textarea id="address" name="address" rows="3"
            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">{{ old('address', $lead->address) }}</textarea>
    </div>

    <div class="mt-4">
        <label for="remarks" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Remarks</label>
        <textarea id="remarks" name="remarks" rows="4"
            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">{{ old('remarks', $lead->remarks) }}</textarea>
    </div>

    <div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ $cancelUrl }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i>
            Cancel
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>
