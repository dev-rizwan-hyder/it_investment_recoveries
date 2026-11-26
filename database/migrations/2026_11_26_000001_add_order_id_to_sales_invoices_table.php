<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_invoices') && !Schema::hasColumn('sales_invoices', 'order_id')) {
            Schema::table('sales_invoices', function (Blueprint $table) {
                $table->unsignedBigInteger('order_id')->nullable()->after('repair_entry_id')->index();
            });
        }

        // Backfill existing invoices linked by message "Order #..."
        if (Schema::hasTable('sales_invoices') && Schema::hasColumn('sales_invoices', 'order_id') && Schema::hasTable('orders')) {
            $invoices = DB::table('sales_invoices')
                ->whereNull('order_id')
                ->whereNotNull('message')
                ->where('message', 'like', '%Order #%')
                ->select('id', 'message')
                ->get();

            foreach ($invoices as $inv) {
                if (preg_match('/Order #([A-Za-z0-9\-]+)/', $inv->message, $matches)) {
                    $order = DB::table('orders')->where('order_number', trim($matches[1]))->first();
                    if ($order) {
                        DB::table('sales_invoices')->where('id', $inv->id)->update(['order_id' => $order->id]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_invoices') && Schema::hasColumn('sales_invoices', 'order_id')) {
            Schema::table('sales_invoices', function (Blueprint $table) {
                $table->dropColumn('order_id');
            });
        }
    }
};
