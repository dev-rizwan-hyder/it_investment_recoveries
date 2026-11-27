<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_intakes', function (Blueprint $table) {
            $table->id();
            $table->date('intake_date')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('job_no')->nullable();
            $table->string('barcode_number')->nullable();
            // Pallet link (from universal waste entries)
            $table->unsignedBigInteger('universal_waste_item_id')->nullable();
            $table->string('pallet_number')->nullable(); // pallet barcode from UWE
            // Item details
            $table->text('description')->nullable();
            $table->integer('estimated_count')->nullable();
            $table->decimal('gross_weight', 10, 2)->nullable();
            $table->decimal('tare_weight', 10, 2)->nullable();
            $table->decimal('net_weight', 10, 2)->nullable();
            $table->string('put_away_location')->nullable();
            $table->string('marketing_reference')->nullable()->default('No reference');
            $table->string('receiving_employee')->nullable();
            $table->text('notes')->nullable();
            $table->json('photos_paths')->nullable();
            $table->string('status')->default('Received');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_intakes');
    }
};
