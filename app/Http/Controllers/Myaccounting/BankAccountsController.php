<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\BankAccount;

use App,DB;
use Illuminate\Support\Facades\Auth;

class BankAccountsController extends Controller
{
    public function index()
    {
        $title = 'Bank Accounts';
        try {
            $data = [
                'title' => $title,
                'bankAccounts' => BankAccount::all(),
            ];
            return view('accounting.backend.pages.bankAccounts.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function create()
    {
        $data = [
            'title' => 'New Bank Account',
            'code' => uniqueCodeWithoutPrefix(2,'bank_accounts','code'),
        ];

        return view('accounting.backend.pages.bankAccounts.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:bank_accounts',
            'type' => 'required',
            'name' => 'required',
            'number' => 'required',
            'bank_name' => 'required',
            'bank_address' => 'required',
        ]);

        try{
            BankAccount::create($request->all());
            return $this->redirectBackWithSuccess("Bank Account has been created successfully", 'accounting.bank-accounts.create');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Bank Account',
            'bankAccount' => BankAccount::findOrFail($id),
        ];

        return view('accounting.backend.pages.bankAccounts.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:bank_accounts,code,'.$id,
            'type' => 'required',
            'name' => 'required',
            'number' => 'required',
            'bank_name' => 'required',
            'bank_address' => 'required',
        ]);

        try{
            BankAccount::find($id)->fill($request->all())->save();
            return $this->redirectBackWithSuccess("Bank Account has been updated successfully", 'accounting.bank-accounts.index');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            BankAccount::find($id)->delete();
            return response()->json([
                'success' => true,
                'message' => "Bank Account has been Deleted!"
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
