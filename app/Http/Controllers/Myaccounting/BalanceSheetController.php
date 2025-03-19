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

class BalanceSheetController extends Controller
{
    public function index()
    {
        $title = 'Report - Balance Sheet';

        $from = request()->has('from') ? request()->get('from') : date('Y-m-01');
        $to = request()->has('to') ? request()->get('to') : date('Y-m-t');

        $asset = AccountGroup::doesntHave('parent')->first();
        $liability = AccountGroup::doesntHave('parent')->where('id', '!=', isset($asset->id) ? $asset->id : 0)->first();

        try {
            $data = [
                'title' => $title,
                'from' => $from,
                'to' => $to,
                'asset' => $asset,
                'liability' => $liability,
                'assets' => request()->has('from') ? (isset($asset->id) ? balanceSheet($asset, $from, $to) : '') : '',
                'liabilities' => request()->has('from') ? (isset($liability->id) ? balanceSheet($liability, $from, $to) : '') : ''
            ];

            if(request()->has('pdf')){
                return downloadPDF($data['title'], $data, 'accounting.backend.pages.reports.balanceSheet.pdf', 'legal', 'portrait');
            }

            return view('accounting.backend.pages.reports.balanceSheet.index', $data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
