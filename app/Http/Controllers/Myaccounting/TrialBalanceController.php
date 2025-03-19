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

class TrialBalanceController extends Controller
{
    public function index()
    {
        $title = 'Report - Trial Balance';

        try {
            $fiscalYear = FiscalYear::where('closed', 0)->first();
            $data = [
                'title' => $title,
                'fiscalYear' => $fiscalYear,
                'trialBalance' => trialbalance([], $fiscalYear->start, $fiscalYear->end),
            ];

            if(request()->has('pdf')){
                return downloadPDF($data['title'], $data, 'accounting.backend.pages.reports.trialBalance.pdf', 'legal', 'portrait');
            }
            return view('accounting.backend.pages.reports.trialBalance.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
