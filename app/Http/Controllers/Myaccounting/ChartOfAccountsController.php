<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\Company;
use \App\Models\PmsModels\Accounts\AccountGroup;
use \App\Models\PmsModels\Accounts\ChartOfAccount;

use App,DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ChartOfAccountsController extends Controller
{
    public function index()
    {
        // $groups = AccountGroup::all();
        // foreach($groups as $key => $group){
        //     $group->code = str_pad(($key+1), 3, '0', STR_PAD_LEFT);
        //     $group->save();
        // }

        // $accounts = ChartOfAccount::all();
        // foreach($accounts as $key => $account){
        //     $account->code = str_pad(($key+1), 4, '0', STR_PAD_LEFT);
        //     $account->save();
        // }

        $title = 'Chart of Accounts';
        $companies = Company::all();
        try {
            $data = [
                'title' => $title,
                'companies' => $companies,
                'accountGroups' => accountGroups([]),
            ];

            if(request()->has('pdf')){
                return downloadPDF($title, $data, 'accounting.backend.pages.chartOfAccounts.pdf', 'a4', 'portrait');
            }

            return view('accounting.backend.pages.chartOfAccounts.index', $data);
        }catch (\Throwable $th){
             return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $data = [
            'title' => 'New Ledger',
            'groups' => accountGroupOptions([], 0),
            'code' => uniqueCodeWithoutPrefix(4,'chart_of_accounts','code')
        ];

        return view('accounting.backend.pages.chartOfAccounts.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_group_id' => 'required',
            'code' => 'required|unique:chart_of_accounts',
            'name' => 'required',
            'notes' => 'required',
        ]);

        try{
            $account = new ChartOfAccount();
            $account->fill($request->all());
            $account->bank_or_cash = isset($request->bank_or_cash) ? 1 : 0;
            $account->reconciliation = isset($request->reconciliation) ? 1 : 0;
            $account->save();

            $notification = [
                'message' => "Ledger Account has been created successfully",
                'alert-type' => 'success'
            ];
            return redirect('accounting/chart-of-accounts/create?company_id='.$account->accountGroup->company_id)->with($notification);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($account_group_id)
    {
        if(request()->has('account_id')){
            $account = ChartOfAccount::find(request()->get('account_id'));
            if($account->account_group_id == $account_group_id){
                return $account->code;
            }
        }

        $group = AccountGroup::find($account_group_id);
        $prefix = isset($group->id) ? $group->code.'-' : '';
        $prev = ChartOfAccount::when(isset($group->id), function($query) use($group){
            return $query->where('account_group_id', $group->id);
        })->count();
        $count = (int)($prev)+1;
        $zeroos = str_repeat("0", (strlen($prefix)+4)-strlen($prefix)-strlen($count));
        return $prefix.$zeroos.$count;
    }

    public function edit($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $data = [
            'title' => 'Edit Ledger',
            'groups' => accountGroupOptions([], $account->account_group_id),
            'account' => $account
        ];

        return view('accounting.backend.pages.chartOfAccounts.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'account_group_id' => 'required',
            'code' => 'required|unique:chart_of_accounts,code,'.$id,
            'name' => 'required',
            'notes' => 'required',
        ]);

        try{
            $account = ChartOfAccount::find($id);
            $account->fill($request->all());
            $account->bank_or_cash = isset($request->bank_or_cash) ? 1 : 0;
            $account->reconciliation = isset($request->reconciliation) ? 1 : 0;
            $account->save();

            $notification = [
                'message' => "Ledger Account has been updated successfully",
                'alert-type' => 'success'
            ];
            return redirect('accounting/chart-of-accounts?company_id='.$account->accountGroup->company_id)->with($notification);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            ChartOfAccount::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Account has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
