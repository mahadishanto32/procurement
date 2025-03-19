<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\AccountGroup;
use \App\Models\PmsModels\Accounts\FiscalYear;
use \App\Models\PmsModels\Accounts\EntryType;
use \App\Models\PmsModels\Accounts\Tag;
use \App\Models\PmsModels\Accounts\ChartOfAccount;
use \App\Models\PmsModels\Accounts\Entry;
use \App\Models\PmsModels\Accounts\EntryItem;

use App,DB;
use Illuminate\Support\Facades\Auth;

class ProfitLossController extends Controller
{
    public function index()
    {
        $title = 'Trading and Profit & Loss Statement';

        $from = request()->has('from') ? request()->get('from') : date('Y-m-01');
        $to = request()->has('to') ? request()->get('to') : date('Y-m-t');

        $income = AccountGroup::doesntHave('parent')->skip(2)->first();
        $expense = AccountGroup::doesntHave('parent')->skip(2)->where('id', '!=', isset($income->id) ? $income->id : 0)->first();

        try {
            $data = [
                'title' => $title,
                'from' => $from,
                'to' => $to,
                'income' => $income,
                'expense' => $expense,
                'incomes' => isset($income->id) ? balanceSheet($income, $from, $to) : '',
                'expenses' => isset($expense->id) ? balanceSheet($expense, $from, $to) : ''
            ];

            if(request()->has('pdf')){
                return downloadPDF($data['title'], $data, 'accounting.backend.pages.reports.profitLoss.pdf', 'legal', 'portrait');
            }
            return view('accounting.backend.pages.reports.profitLoss.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
