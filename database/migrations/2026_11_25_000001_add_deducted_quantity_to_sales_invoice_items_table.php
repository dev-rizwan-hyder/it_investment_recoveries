<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_invoice_items') && !Schema::hasColumn('sales_invoice_items', 'deducted_quantity')) {
            Schema::table('sales_invoice_items', function (Blueprint $table) {
                $table->integer('deducted_quantity')->default(0)->after('quantity');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_invoice_items') && Schema::hasColumn('sales_invoice_items', 'deducted_quantity')) {
            Schema::table('sales_invoice_items', function (Blueprint $table) {
                $table->dropColumn('deducted_quantity');
            });
        }
    }
};
