@forelse ($products as $product)
    @include('admin.ebay.partials.product-card', ['product' => $product])
@empty
    <div class="col-span-1 md:col-span-2 rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-55 shadow-sm text-slate-450 border border-slate-100">
            <i class="fa-regular fa-folder-open text-2xl"></i>
        </div>
        <p class="mt-4 text-sm font-bold text-slate-800">No product folders found</p>
        <p class="mt-1 text-xs text-slate-400 font-medium">Create a new Ebay Product folder or adjust your search filters.</p>
    </div>
@endforelse
