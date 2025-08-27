<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('building_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->string('number')->index();
            $table->integer('floor')->nullable()->default(1)->index();
            $table->decimal('total_area', 8, 2);
            $table->decimal('living_area', 8, 2);
            $table->integer('rooms_count')->default(0)->nullable();
            $table->integer('registered_residents')->default(0)->nullable();
            $table->string('status', 15)->nullable()->index();
            $table->string('type', 15)->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
