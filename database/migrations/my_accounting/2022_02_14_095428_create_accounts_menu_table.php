<?php

use App\Models\PmsModels\Accounts\Menu\AccountsMenu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_menu', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('url');
            $table->string('icon_class')->nullable();
            $table->string('icon')->nullable();
            $table->string('big_icon')->nullable();
            $table->tinyInteger('serial_num',false,4);

            $table->string('status')->default(AccountsMenu::ACTIVE);
            $table->string('slug')->nullable();
            $table->string('menu_for')->default(AccountsMenu::ADMIN_MENU);
            $table->string('open_new_tab')->default(AccountsMenu::NO_OPEN_NEW_TAB);

            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_menu');
    }
}
