<?php

namespace App\Providers;

use App\Models\MyProject\Menu\ProjectMenu;
use App\Models\PmsModels\Accounts\Menu\AccountsMenu;
use App\Models\PmsModels\Menu\Menu;
use Illuminate\Support\ServiceProvider;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer( // for admin menu --------------
            [
                'pms.backend.menus.left-menu',
            ],
            function ($view)
            {
                $menus=Menu::with('subMenu')->where(['menu_for'=>Menu::ADMIN_MENU,'status'=>Menu::ACTIVE])->orderBy('serial_num','ASC')->get();

                $view->with(['menus'=>$menus]);
            });
        View::composer( // for admin menu --------------
            [
                'my_project.backend.menus.left-menu',
            ],
            function ($view)
            {
                $menus=ProjectMenu::with('subMenu')->where(['menu_for'=>ProjectMenu::ADMIN_MENU,'status'=>ProjectMenu::ACTIVE])->orderBy('serial_num','ASC')->get();;

                $view->with(['menus'=>$menus]);
            });
        View::composer( // for admin menu --------------
            [
                'accounting.backend.menus.left-menu',
            ],
            function ($view)
            {
                $menus=AccountsMenu::with('subMenu')->where(['menu_for'=>AccountsMenu::ADMIN_MENU,'status'=>AccountsMenu::ACTIVE])->orderBy('serial_num','ASC')->get();

                $view->with(['menus'=>$menus]);
            });
    }
}
