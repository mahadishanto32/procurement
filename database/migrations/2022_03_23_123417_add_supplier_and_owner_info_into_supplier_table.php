<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierAndOwnerInfoIntoSupplierTable extends Migration
{
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('tin')->after('mobile_no')->nullable();
            $table->string('trade')->after('tin')->nullable();
            $table->string('bin')->after('trade')->nullable();
            $table->string('vat')->after('bin')->nullable();
            $table->string('website')->after('vat')->nullable();
            $table->string('road')->after('address')->nullable();
            $table->string('village')->after('road')->nullable();

            $table->string('owner_name')->after('zipcode')->nullable();
            $table->string('owner_nid')->after('owner_name')->nullable();
            $table->string('owner_photo')->after('owner_nid')->nullable();
            $table->string('owner_email')->after('owner_photo')->nullable();
            $table->string('owner_contact_no')->after('owner_email')->nullable();

            $table->string('auth_person_letter')->after('owner_contact_no')->nullable();
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('tin');
            $table->dropColumn('trade');
            $table->dropColumn('bin');
            $table->dropColumn('vat');
            $table->dropColumn('website');
            $table->dropColumn('road');
            $table->dropColumn('village');

            $table->dropColumn('owner_name');
            $table->dropColumn('owner_nid');
            $table->dropColumn('owner_photo');
            $table->dropColumn('owner_email');
            $table->dropColumn('owner_contact_no');
            
            $table->dropColumn('auth_person_letter');
        });
    }
}
