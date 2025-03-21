<?php

namespace App\Http\Controllers\Myaccounting\Menu;

use App\Http\Controllers\Controller;

use App\Models\PmsModels\Accounts\Menu\AccountsMenu;
use App\Models\PmsModels\Accounts\Menu\AccountsSubMenu;
use App\Models\PmsModels\Accounts\Menu\AccountsSubSubMenu;
use Illuminate\Http\Request;

use App\Http\Requests;

use Image,DB,Validator;
use Spatie\Permission\Models\Permission;

class AccountsMenuController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title='Accounts Menu';
        $allData=AccountsMenu::orderBy('serial_num','DESC')->paginate(30);

        return view('accounting.backend.pages.menu.index',compact('title','allData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title='Create New Menu';
        $max_serial=AccountsMenu::max('serial_num');
        $permissions = Permission::orderBy('id','DESC')->pluck('name', 'name');
        $menuFor=[
            AccountsMenu::ADMIN_MENU => AccountsMenu::ADMIN_MENU,
            AccountsMenu::CLIENT_MENU => AccountsMenu::CLIENT_MENU,
            AccountsMenu::USER_MENU => AccountsMenu::USER_MENU
        ];
        $status=[AccountsMenu::ACTIVE  => AccountsMenu::ACTIVE ,
            AccountsMenu::INACTIVE  => AccountsMenu::INACTIVE];

        $openTab=[AccountsMenu::NO_OPEN_NEW_TAB  => AccountsMenu::NO_OPEN_NEW_TAB ,
            AccountsMenu::OPEN_NEW_TAB  => AccountsMenu::OPEN_NEW_TAB];
        return view('accounting.backend.pages.menu.create',compact('title','max_serial','permissions','menuFor','status','openTab'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try{
            $input = $request->all();

            if(isset($input['page'])){
                $page=Page::select('name','name_bn')->where('link',$input['page'])->first();
                $input['name']=$page['name'];
                $input['name_bn']=$page['name_bn'];
                $input['url']="page/".$input['page'];
            }


            $validator = Validator::make($input, [
                'name'=> 'required',
                'url'=> 'required',
                'icon' => 'mimes:jpeg,jpg,bmp,png'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $input['slug']=json_encode($request->slug);

            if ($request->hasFile('icon')){
                $input['icon']=$this->photoUpload($request->file('icon'),'images/menu/icon',32);
                //$input['big_icon']=$this->photoUpload($request->file('icon'),'images/menu/big-icon/',128);
            }

            AccountsMenu::create($input);

            return $this->backWithSuccess('Menu created successfully');

        }catch(\Exception $e){
            return $this->backWithError($e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title='Edit Menu Data';
        $max_serial=AccountsMenu::max('serial_num');
        $data=AccountsMenu::findOrFail($id);
        $permissions = Permission::orderBy('id','DESC')->pluck('name', 'name');
        $menuFor=[
            AccountsMenu::ADMIN_MENU => AccountsMenu::ADMIN_MENU,
            AccountsMenu::CLIENT_MENU => AccountsMenu::CLIENT_MENU,
            AccountsMenu::USER_MENU => AccountsMenu::USER_MENU
        ];
        $status=[AccountsMenu::ACTIVE  => AccountsMenu::ACTIVE ,
            AccountsMenu::INACTIVE  => AccountsMenu::INACTIVE];

        $openTab=[AccountsMenu::NO_OPEN_NEW_TAB  => AccountsMenu::NO_OPEN_NEW_TAB ,
            AccountsMenu::OPEN_NEW_TAB  => AccountsMenu::OPEN_NEW_TAB];

        return view('accounting.backend.pages.menu.edit',compact('title','data','max_serial','permissions','menuFor','status','openTab'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $input = $request->all();
        $data=AccountsMenu::findOrFail($id);

        $validator = Validator::make($input, [
            'name'    => 'required',
            'url'          => 'required',
            'icon' => 'mimes:jpeg,jpg,bmp,png'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input['slug']=json_encode($request->slug);
        try{

            if ($request->hasFile('icon')){
                $input['icon']=$this->photoUpload($request->file('icon'),'images/menu/icon',32);

                if (file_exists(asset($data->icon))){ unlink(asset($data->icon));  }
                if (file_exists(asset($data->big_icon))){ unlink(asset($data->big_icon));  }
            }

            $data->update($input);
            return $this->backWithSuccess('User created successfully');
        }catch(\Exception $e){
            return $this->backWithError($e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = AccountsMenu::findOrFail($id);
        DB::beginTransaction();
        try {

            AccountsSubMenu::whereIn('menu_id', [$id])->delete();

            AccountsSubSubMenu::whereIn('menu_id', [$id])->delete();

            $data->delete();
            $bug = 0;
            if (file_exists(asset($data->icon))){ unlink(asset($data->icon)); }
            if (file_exists(asset($data->big_icon))){ unlink(asset($data->big_icon)); }


            DB::commit();
            return $this->backWithSuccess('User created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }
    }

    public function page(){
        $max_serial=AccountsMenu::max('serial_num');
        $page=Page::where('status',1)->pluck('name','link');
        return view('backend.menu.pageMenu',compact('max_serial','page'));
    }
}
