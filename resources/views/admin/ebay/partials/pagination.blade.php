@if(isset($products) && method_exists($products, 'hasPages') && $products->hasPages())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-4 no-print">
        {{ $products->links() }}
    </div>
@endif
