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
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('number', 50)->index()->unique();
            $table->string('name')->nullable();
            $table->string('finance_owner_type', 15)->index();
            $table->unsignedBigInteger('finance_owner_id')->index();
            $table->string('finance_type', 15)->index();
            $table->decimal('debit', 15, 4)->default(0)->index();
            $table->decimal('credit', 15, 4)->default(0)->index();
            $table->decimal('balance', 15, 4)->default(0)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id',  'finance_owner_type', 'finance_owner_id'], 'fa_owner_idx');
            $table->index(['tenant_id', 'number']);

        });

        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('status')->index();
            $table->string('name')->index();
            $table->foreignId('debit_account_id')->constrained('finance_accounts')->cascadeOnDelete();
            $table->foreignId('credit_account_id')->constrained('finance_accounts')->cascadeOnDelete();
            $table->dateTime('posted_at')->index()->nullable();
            $table->decimal('total', 15, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::create('finance_transaction_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('finance_transaction_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 4)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('apartment_id')->constrained('apartments')->cascadeOnDelete();
            $table->foreignId('apartment_owner_id')->constrained('owners')->cascadeOnDelete();
            $table->string('period', 10)->index();
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('debt', 15, 4)->default(0);
            $table->foreignId('initial_finance_transaction_entry_id')->nullable()->constrained('finance_transaction_entries')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('tariff_id')->constrained('tariffs')->cascadeOnDelete();
            $table->foreignId('measure_id')->constrained('measures')->cascadeOnDelete();
            $table->string('quantity');
            $table->decimal('amount', 15, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('third_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name')->index();
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name')->index();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('finance_transaction_entries');
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('finance_accounts');
        Schema::dropIfExists('third_parties');
        Schema::dropIfExists('companies');

    }
};
