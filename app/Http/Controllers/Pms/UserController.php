<?php

namespace App\Http\Controllers\Pms;

use App\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;
use App\Models\PmsModels\Warehouses;
use App\Models\Hr\Department;
use App\Models\Hr\Designation;
use App\Models\Hr\Unit;
use App\Models\Employee;
use DB,Hash,Validator,Image;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */

    public function index(Request $request)
    {
        $title='User List';
        $roles = Role::whereNotIn('name',['developer'])->orderBy('id','DESC')->pluck('name')->toArray();
        $users = User::orderBy('id','DESC')->paginate(User::count());
    
        return view('pms.backend.pages.users.index', compact('title','roles', 'users'));
    }

    public function deleted()
    {
        $title = 'Deleted User List';
        $users = User::onlyTrashed()->orderBy('id','DESC')->paginate(User::count());
    
        return view('pms.backend.pages.users.deleted', compact('title', 'users'));
    }

    public function restore($id)
    {
        try{
            User::onlyTrashed()->where('id', $id)->restore();
            return $this->backWithSuccess('User Restored successfully');
        }catch (\Exception $e){
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }
    }

    public function usersDataLoad(){
        $allData=User::orderBy('users.id','DESC')->select('users.*');

        return DataTables::of($allData)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex','')
        ->addColumn('department',function (User $user){
            return isset($user->employee->department->hr_department_name) ? $user->employee->department->hr_department_name : '';
        })
        ->addColumn('user_role',function (User $user){
            $roles= $user->getRoleNames()->toArray();
            return implode(',',$roles);
        })
        ->addColumn('created_at','<?php $created_at==\'null\'?\'\': "echo date(\'d-m-Y\',strtotime($created_at))"?>')

        ->addColumn('action','
           <!-- delete section -->
           {!! Form::open(array(\'route\'=> [\'pms.admin.users.destroy\',$id],\'method\'=>\'DELETE\',\'class\'=>\'deleteForm\',\'id\'=>"deleteForm$id")) !!}
           {{ Form::hidden(\'id\',$id)}}
           <a href="javascript:void(0)" onclick="return showUserDetails({{$id}})" class="btn btn-info btn-xs mb-2"><i class="la la-eye" title="Click to view details"></i></a>

           <a href="{{route(\'pms.admin.users.edit\',$id)}}" class="btn btn-warning btn-xs mb-2"><i class="la la-pencil-square" title="Click to Edit"></i></a>
           <button type="button" onclick=\'return deleteConfirm("deleteForm{{$id}}");\' class="btn btn-danger btn-xs mb-2">
           <i class="la la-trash"></i></button>
           {!! Form::close() !!}
           ')
        ->rawColumns(['department','user_role','action','created_at'])
        ->toJson();
    }

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create(Request $request)
    {

        $title='Create New User';
        $roles = Role::where('name','!=','developer')->orderBy('id','DESC')->pluck('name', 'name')->all();

        $warehouse=Warehouses::pluck('name', 'id')->all();

        $departments= [''=>'Select One']+Department::pluck('hr_department_name','hr_department_id')->all();
        $designations= [''=>'Select One']+Designation::pluck('hr_designation_name','hr_designation_id')->all();
        $units= [''=>'Select One']+Unit::pluck('hr_unit_name', 'hr_unit_id')->all();

        return view('pms.backend.pages.users.create',compact('title','roles','warehouse','departments','designations','units'));
    }



    /**

     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $this->validate($request, [

            'name' => 'required',
            'email'  => "nullable|unique:users|email|max:100",
            'phone'  => "nullable|unique:users|max:15",
            'user_id' => ['nullable', 'string', 'max:200', "unique:users"],
            'profile_photo_path' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:5048',
            'password' => 'required|same:confirm_password',
            'roles' => 'required',
            'as_department_id' => 'required',
            'as_designation_id' => 'required',
            'as_unit_id' => 'required',
            'associate_id' => 'required|unique:hr_as_basic_info|string|max:200',
        ]);



       //return $input = $request->all();
        $input = $request->except('_token');
        $input['password'] = Hash::make($input['password']);
        DB::beginTransaction();
        try{

            $avatarPath='';
            if ($request->hasFile('profile_photo_path'))
            {
                $avatarPath=$this->photoUpload($request->file('profile_photo_path'),'images/user-images',170);
                $input['profile_photo_path']=$avatarPath;
            }

            $user = User::create($input);
            $user->assignRole($request->input('roles'));
            if(!empty($request->warehouse_id)){
                $user->relUsersWarehouse()->sync($request->warehouse_id);
            }

            $basicInfo=Employee::create([
                'as_emp_type_id'=>'1',
                'as_designation_id'=>$input['as_designation_id'],
                'as_unit_id'=>$input['as_unit_id'],
                'as_department_id'=>$input['as_department_id'],
                'associate_id'=>$input['associate_id'],
                'as_name'=>$input['name'],
            ]);

            DB::commit();
            return $this->backWithSuccess('User created successfully');
        }catch (\Exception $e){
            DB::rollback();
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

        $user = User::findOrFail($id);
        $userRole = $user->roles->pluck('name')->toArray();
        return view('pms.backend.pages.users.show', compact('user', 'userRole'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {

        $title='Edit User Data';
        $user = User::findOrFail($id);
        $roles = Role::where('name','!=','developer')->pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $warehouse=Warehouses::pluck('name', 'id')->all();
        $userWarehouse=$user->relUsersWarehouse->pluck('id')->all();

        $departments= [''=>'Select One']+Department::pluck('hr_department_name','hr_department_id')->all();
        $designations= [''=>'Select One']+Designation::pluck('hr_designation_name','hr_designation_id')->all();
        $units= [''=>'Select One']+Unit::pluck('hr_unit_name', 'hr_unit_id')->all();

        return view('pms.backend.pages.users.edit', compact('title','user', 'roles', 'userRole','warehouse','userWarehouse','units','designations','departments'));
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

        $this->validate($request, [
            'name' => 'required',
            'phone' => ['required', 'string', 'max:15', "unique:users,phone," . $id],
            'email' => ['nullable', 'string', 'max:100', "unique:users,email," . $id],
            'user_id' => ['nullable', 'string', 'max:50', "unique:users,username," . $id],
            'profile_photo_path' => 'image|mimes:jpeg,jpg,png,gif|nullable|max:5048',
            'roles' => 'required',
            'as_department_id' => 'required',
            'as_designation_id' => 'required',
            'as_unit_id' => 'required',
            'associate_id' => 'required',
        ]);


        $input = $request->except('password','_token');
        DB::beginTransaction();
        try{
            $user = User::findOrFail($id);
            $avatarPath='';
            if ($request->hasFile('profile_photo_path'))
            {
                $avatarPath=$this->photoUpload($request->file('profile_photo_path'),'images/user-images',170);

                if (!empty($userProfile) && file_exists($userProfile->profile_photo_path)){
                    unlink($userProfile->profile_photo_path);
                }
                $input['profile_photo_path']=$avatarPath;
            }

            $user->update($input);
            DB::table('model_has_roles')->where('model_id', $id)->delete();

            $user->assignRole($request->input('roles'));

            if(!empty($request->warehouse_id)){
                $user->relUsersWarehouse()->sync($request->warehouse_id);
            }

            Employee::updateOrCreate([
                'associate_id'=>$input['associate_id'],
            ],
            [
                'as_emp_type_id'=>'1',
                'as_designation_id'=>$input['as_designation_id'],
                'as_unit_id'=>$input['as_unit_id'],
                'as_department_id'=>$input['as_department_id'],
                'as_name'=>$input['name'],
            ]);


            DB::commit();
            return $this->backWithSuccess('User Data Update successfully');

        }catch (\Exception $e){
            DB::rollback();
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
        try{
            $user = User::findOrFail($id);
            User::find($id)->delete();

            if (!empty($user) && file_exists($user->profile_photo_path)){
                unlink($user->profile_photo_path);
            }

            DB::table('model_has_roles')->where('model_id', $id)->delete();
            DB::commit();
            return $this->backWithSuccess('User Data Update successfully');
        }catch(Exception $e){
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }

    }


    protected function changeUserPassword($userId)
    {
        $user=User::findOrFail($userId);
        return view('admin.users.change-user-password',compact('user'));
    }


    protected function resetUserPassword(Request $request)
    {

        //return $request;
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try{
            $user=User::findOrFail($request->id);
            $user->update([
                'password'=>Hash::make($request->new_password),
            ]);
            $bug=0;
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();;
            $bug=$e->errorInfo[1];
        }

        if($bug==0){
            return redirect()->back()->with('success','Password successfully change');
        }else{
            return redirect()->back()->with('error','Something Error Found ! '.$bug);
        }

    }

    function photoUpload($photoData,$folderName,$width=null,$height=null)
    {

        $photoOrgName = $photoData->getClientOriginalName();
        $photoType = $photoData->getClientOriginalExtension();
        $fileName = substr($photoOrgName, 0, -4) . date('d-m-Y-i-s') . '.' . $photoType;
        $path2 = $folderName . date('/Y/m/d/');
        if (!is_dir(public_path($path2))) {
            mkdir(public_path($path2), 0777, true);
        }

        $photoData->move(public_path($path2), $fileName);
        if ($width != null && $height != null) { // width & height mention-------------------
            $img = \Image::make(public_path($path2 . $fileName));
            $img->encode('webp', 75)->resize($width, $height);
            $img->save(public_path($path2 . $fileName));
            return $photoUploadedPath = $path2 . $fileName;

        } elseif ($width != null) { // only width mention-------------------

            $img = \Image::make(public_path($path2 . $fileName));
            $img->encode('webp', 75)->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(public_path($path2 . $fileName));

            return $photoUploadedPath = $path2 . $fileName;

        } else {
            $img = \Image::make(public_path($path2 . $fileName));
            $img->save(public_path($path2 . $fileName));
            return $photoUploadedPath = $path2 . $fileName;
        }
    }
}
