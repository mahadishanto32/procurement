<?php

use App\Models\PmsModels\Accounts\Menu\AccountsSubMenu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsSubMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_sub_menus', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('menu_id',false,20);
            $table->foreign('menu_id')->references('id')->on('menus');

            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('url');
            $table->string('icon_class')->nullable();
            $table->string('icon')->nullable();
            $table->string('big_icon')->nullable();
            $table->tinyInteger('serial_num',false,4);

            $table->string('status')->default(AccountsSubMenu::ACTIVE);
            $table->string('slug')->nullable();
            $table->string('menu_for')->default(AccountsSubMenu::ADMIN_MENU);
            $table->string('open_new_tab')->default(AccountsSubMenu::NO_OPEN_NEW_TAB);

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
        Schema::dropIfExists('accounts_sub_menus');
    }
}
