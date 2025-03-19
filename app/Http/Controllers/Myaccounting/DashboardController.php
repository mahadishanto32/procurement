<?php

namespace App\Http\Controllers\Myaccounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Models\PmsModels\Accounts\FiscalYear;
use \App\Models\PmsModels\Accounts\EntryType;
use \App\Models\PmsModels\Accounts\Tag;
use \App\Models\PmsModels\Accounts\ChartOfAccount;
use \App\Models\PmsModels\Accounts\Entry;
use \App\Models\PmsModels\Accounts\EntryItem;

use App,DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Accounting Dashboard';
        $fiscalYear = FiscalYear::where('closed', 0)->first();
        try {
            $data = [
                'title' => $title,
                'typeWiseEntries' => [
                    'overall' => typeWiseEntries(),
                    'last-7-days' => typeWiseEntries(date('Y-m-d', strtotime('-6 days')), date('Y-m-d')),
                    'this-month' => typeWiseEntries(date('Y-m-01'), date('Y-m-t')),
                    'last-month' => typeWiseEntries(date('Y-m-01', strtotime('-1 months')), date('Y-m-t', strtotime('-1 months'))),
                    'current-fiscal-year' => typeWiseEntries(date('Y-m-d', strtotime($fiscalYear->start)), date('Y-m-d', strtotime($fiscalYear->end))),
                ],
                'overallTransactions' => getDateWiseTotalTransactions(date('Y-m-d', strtotime('-30 days')), date('Y-m-d')),
                'entryTypes' => EntryType::all(),
                'incomes' => groupWiseBalance(date('Y-m-01'), date('Y-m-t'), 19),
                'expenses' => groupWiseBalance(date('Y-m-01'), date('Y-m-t'), 20),
                'balances' => balances(),
            ];

            return view('accounting.backend.pages.dashboard', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
