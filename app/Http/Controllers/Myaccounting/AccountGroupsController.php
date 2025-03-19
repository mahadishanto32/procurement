<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\Company;
use \App\Models\PmsModels\Accounts\AccountGroup;

class AccountGroupsController extends Controller
{
    public function create()
    {
        $title = 'New Account Group';
        try {
            $groups = AccountGroup::doesntHave('parent')->orderBy('code')->get();

            $data = [
                'title' => $title,
                'accountGroupOptions' => accountGroupOptions([], 0),
                'code' => uniqueCodeWithoutPrefix(4,'account_groups','code')
            ];
            return view('accounting.backend.pages.accountGroups.create', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'parent_id' => 'required',
            'code' => 'required|unique:account_groups',
            'name' => 'required',
        ]);

        try{
            $group = AccountGroup::create($request->all());
            return $this->redirectBackWithSuccess("Account group has been created successfully", 'accounting.account-groups.create');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        if(request()->has('group_id')){
            $group = AccountGroup::find(request()->get('group_id'));
            if($group->parent_id == $id){
                return $group->code;
            }
        }

        $group = AccountGroup::find($id);
        $prefix = (isset($group->id) ? $group->code.'-' : '');
        $prev = AccountGroup::when(isset($group->id), function($query) use($group){
            return $query->where('parent_id', $group->id);
        })
        ->when(!isset($group->id), function($query){
            return $query->doesntHave('parent');
        })->count();

        $count = (int)($prev)+1;
        $zeroos = str_repeat("0", (strlen($prefix)+2)-strlen($prefix)-strlen($count));
        return $prefix.$zeroos.$count;
    }

    public function edit($id)
    {
        $title = 'Edit Account Group';
        try {
            $group = AccountGroup::find($id);
            $data = [
                'title' => $title,
                'accountGroupOptions' => accountGroupOptions([], $group->parent_id),
                'group' => $group
            ];
            return view('accounting.backend.pages.accountGroups.edit', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'parent_id' => 'required',
            'code' => 'required|unique:account_groups,code,'.$id,
            'name' => 'required',
        ]);

        try{
            $group = AccountGroup::find($id);
            $group->fill($request->all())->save();
            return $this->redirectBackWithSuccess("Account group has been updated successfully", 'accounting.chart-of-accounts.index');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            $group = AccountGroup::find($id);
            if((!isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0)) || (isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0))){
                return response()->json([
                    'success' => false,
                    'message' => "Account Group Cannot be Deleted!"
                ]);
            }
            $group->delete();

            return response()->json([
                'success' => true,
                'message' => "Account Group has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
