<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SupplierBankAccounts extends Migration
{
    public function up()
    {
        Schema::create('supplier_bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('supplier_id', false);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();

            $table->enum('currency', array('USD', 'BDT', 'EURO', 'YEN'))->default('BDT')->nullable();

            $table->string('security_check')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_bank_accounts');
    }
}
