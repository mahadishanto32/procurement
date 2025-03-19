<?php
use \App\Models\PmsModels\Accounts\AccountGroup;
use \App\Models\PmsModels\Accounts\ChartOfAccount;
use \App\Models\PmsModels\Accounts\EntryItem;

function accountGroups($groups = 0, $step = 0){
    if($step == 0){
        $groups = AccountGroup::doesntHave('parent')->orderBy('code')->get();
    }

    $data = '';
    foreach ($groups as $key => $group) {
        $delete = (!isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0)) || (isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0)) ? false : true;
        $data .= '<tr class="group-'.$group->id.'">
                        <td><strong>'.spaces($step).$group->code.'</strong></td>
                        <td><strong>'.spaces($step).$group->name.'</strong></td>
                        <td class="text-center"><strong>Group</strong></td>
                        <td class="text-right"><strong>'.groupOpeningBalance($group->id).'</strong></td>
                        <td class="text-center">';
            if(auth()->user()->can('account-groups-edit')){
                $data .= '<a class="btn btn-xs btn-primary" href="'.url('accounting/account-groups/'.$group->id.'/edit').'"><i class="la la-edit"></i></a>';
            }
                            
            if($delete && auth()->user()->can('account-groups-delete')){
                $data .= '&nbsp;<a class="btn btn-xs btn-danger deleteBtn" data-src="'.route('accounting.account-groups.destroy', $group->id).'" data-row-class="group-'.$group->id.'"><i class="la la-trash"></i></a>';
            }
        
        $data .= '      </td>
                   </tr>';

        $accounts = getAccounts($group->id);
        if(isset($accounts[0])){
            foreach($accounts as $key => $account){
                $data .= '<tr class="account-'.$account->id.'">
                            <td>'.spaces($step, 1).$account->code.'</td>
                            <td>'.spaces($step, 1).$account->name.'</td>
                            <td class="text-center">Ledger</td>
                            <td class="text-right">'.ledgerOpeningBalance($account->id).'</td>
                            <td class="text-center">';
                if(auth()->user()->can('chart-of-accounts-edit')){
                    $data .= '<a class="btn btn-xs btn-primary" href="'.url('accounting/chart-of-accounts/'.$account->id.'/edit').'"><i class="la la-edit"></i></a>';
                }

                if(auth()->user()->can('chart-of-accounts-delete')){
                    $data .= '&nbsp;<a class="btn btn-xs btn-danger deleteBtn" data-src="'.route('accounting.chart-of-accounts.destroy', $account->id).'" data-row-class="account-'.$account->id.'"><i class="la la-trash"></i></a>';
                }
                                
                                
                $data .= '  </td>
                        </tr>';
            }
        }
        if($group->childrenGroups->count() > 0){
            $data .= accountGroups($group->childrenGroups, $step+1);
        }
    }

    return $data;
}

function accountGroupOptions($groups, $chosen = 0, $step = 0){
    if($step == 0){
        $groups = AccountGroup::doesntHave('parent')->orderBy('code')->get();
    }

    $data = '';
    foreach ($groups as $key => $group) {
        $data .=    '<option value="'.$group->id.'" '.($chosen == $group->id ? 'selected' : '').'>'.spaces($step).'['.$group->code.'] '.$group->name.'</option>';

        if($group->childrenGroups->count() > 0){
            $data .= accountGroupOptions($group->childrenGroups, $chosen, $step+1);
        }
    }

    return $data;
}

function chartOfAccountsOptions($groups, $chosen = 0, $step = 0){
    if($step == 0){
        $groups = AccountGroup::doesntHave('parent')->orderBy('code')->get();
    }

    $data = '';
    foreach ($groups as $key => $group) {
        $data .= '<option disabled style="color: black !important">'.spaces($step).'['.$group->code.'] '.$group->name.'</option>';
        
        $chartOfAccounts = ChartOfAccount::where('account_group_id', $group->id)->get();
        if(isset($chartOfAccounts[0])){
            foreach($chartOfAccounts as $key => $account){
                $data .=    '<option data-closing-balance="'.ledgerClosingBalance($account->id)['balance'].'" data-account-type="'.$account->type.'" value="'.$account->id.'" '.($chosen == $account->id ? 'selected' : '').'>'.spaces($step+2).'['.$account->code.'] '.$account->name.'</option>';
            }
        }

        if($group->childrenGroups->count() > 0){
            $data .= chartOfAccountsOptions($group->childrenGroups, $chosen, $step+1);
        }
    }

    return $data;
}

function getAccounts($account_group_id){
    return ChartOfAccount::where('account_group_id', $account_group_id)->get();
}

function ledgerOpeningBalance($account_id){
	$account = ChartOfAccount::find($account_id);
    return isset($account->id) ? ($account->type == "D" ? $account->opening_balance : -($account->opening_balance) ) : 0;
}

function ledgerDebitBalance($account_id, $from = false, $to = false){
    return EntryItem::where('chart_of_account_id', $account_id)
    ->when($from, function($query) use($from){
        return $query->whereHas('entry', function($query) use($from){
            return $query->where('date', '>=', $from);
        });
    })
    ->when($to, function($query) use($to){
        return $query->whereHas('entry', function($query) use($to){
            return $query->where('date', '<=', $to);
        });
    })->where('debit_credit', 'D')->sum('amount');
}

function ledgerCreditBalance($account_id, $from = false, $to = false){
    return EntryItem::where('chart_of_account_id', $account_id)
    ->when($from, function($query) use($from){
        return $query->whereHas('entry', function($query) use($from){
            return $query->where('date', '>=', $from);
        });
    })
    ->when($to, function($query) use($to){
        return $query->whereHas('entry', function($query) use($to){
            return $query->where('date', '<=', $to);
        });
    })->where('debit_credit', 'C')->sum('amount');
}

function ledgerClosingBalance($account_id, $from = false, $to = false){
    $debit = ledgerDebitBalance($account_id, $from, $to);
    $credit = ledgerCreditBalance($account_id, $from, $to);
	$balance = ($debit-$credit)+ledgerOpeningBalance($account_id);

    return [
        'debit' => $debit,
        'credit' => $credit,
        'balance' => $balance,
    ];
}

function groupOpeningBalance($account_group_id){
    $accounts = call_user_func_array('array_merge', getAllAccounts($account_group_id) ?: [[]]);
    $accounts = array_merge($accounts, ChartOfAccount::where('account_group_id', $account_group_id)->pluck('id')->toArray());

    $debit = ChartOfAccount::whereIn('id', $accounts)
    ->where('type', "D")->sum('opening_balance');

    $credit = ChartOfAccount::whereIn('id', $accounts)
    ->where('type', "C")->sum('opening_balance');

    return $debit-$credit;
}

function getAllAccounts($account_group_id, $accounts = []){
    $groups = AccountGroup::with('chartOfAccounts', 'childrenGroups')->where('parent_id', $account_group_id)->orderBy('code')->get();
    foreach ($groups as $key => $group) {
        if($group->chartOfAccounts->count() > 0){
            array_push($accounts, $group->chartOfAccounts->pluck('id')->toArray());
        }

        if($group->childrenGroups->count() > 0){
            return getAllAccounts($group->id, $accounts);
        }
    }

    return $accounts;
}

function groupDebitBalance($account_group_id, $from = false, $to = false, $accounts = []){
    $accounts = call_user_func_array('array_merge', getAllAccounts($account_group_id) ?: [[]]);
    $accounts = array_merge($accounts, ChartOfAccount::where('account_group_id', $account_group_id)->pluck('id')->toArray());

    return EntryItem::whereIn('chart_of_account_id', $accounts)
    ->when($from, function($query) use($from){
        return $query->whereHas('entry', function($query) use($from){
            return $query->where('date', '>=', $from);
        });
    })
    ->when($to, function($query) use($to){
        return $query->whereHas('entry', function($query) use($to){
            return $query->where('date', '<=', $to);
        });
    })->where('debit_credit', 'D')->sum('amount');
}

function groupCreditBalance($account_group_id, $from = false, $to = false, $accounts = []){
    $accounts = call_user_func_array('array_merge', getAllAccounts($account_group_id) ?: [[]]);
    $accounts = array_merge($accounts, ChartOfAccount::where('account_group_id', $account_group_id)->pluck('id')->toArray());

    return EntryItem::whereIn('chart_of_account_id', $accounts)
    ->when($from, function($query) use($from){
        return $query->whereHas('entry', function($query) use($from){
            return $query->where('date', '>=', $from);
        });
    })
    ->when($to, function($query) use($to){
        return $query->whereHas('entry', function($query) use($to){
            return $query->where('date', '<=', $to);
        });
    })->where('debit_credit', 'C')->sum('amount');
}

function groupClosingBalance($account_group_id, $from = false, $to = false, $accounts = []){
    $debit = groupDebitBalance($account_group_id, $from, $to, $accounts);
    $credit = groupCreditBalance($account_group_id, $from, $to, $accounts);
    $balance = ($debit-$credit)+groupOpeningBalance($account_group_id);

    return [
        'debit' => $debit,
        'credit' => $credit,
        'balance' => $balance,
    ];
}

function entryTypeRestrictions($key = false){
	$restrictions = [
		1 => [
			'name' => 'Unrestricted',
		],
		2 => [
			'name' => 'Atleast one Bank or Cash account must be present on Debit side',
		],
		3 => [
			'name' => 'Atleast one Bank or Cash account must be present on Credit side',
		],
		4 => [
			'name' => 'Only Bank or Cash account can be present on both Debit and Credit side',
		],
		5 => [
			'name' => 'Only NON Bank or Cash account can be present on both Debit and Credit side',
		],
	];

	return ($key && array_key_exists($key, $restrictions) ? $restrictions[$key] : $restrictions);
}

function bankAccountTypes($key = false){
    $accountTypes = [
        1 => [
            'name' => 'Current account',
        ],
        2 => [
            'name' => 'Savings account',
        ],
        3 => [
            'name' => 'Salary account',
        ],
        4 => [
            'name' => 'Fixed deposit account',
        ],
        5 => [
            'name' => ' Recurring deposit account',
        ],
    ];

    return ($key && array_key_exists($key, $accountTypes) ? $accountTypes[$key] : $accountTypes);
}

function balanceSheet($group, $from, $to, $step = 0){
    $groups = AccountGroup::where('parent_id', $group->id)->orderBy('code')->get();

    $data = '';
    foreach ($groups as $key => $group) {
        $delete = (!isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0)) || (isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0)) ? false : true;
        $data .= '<tr class="group-'.$group->id.'">
                        <td><strong>'.spaces($step).'['.$group->code.'] '.$group->name.'</strong></td>
                        <td class="text-right"><strong>'.groupClosingBalance($group->id, $from, $to)['balance'].spaces($step).'</strong></td>
                   </tr>';

        $accounts = getAccounts($group->id);
        if(isset($accounts[0])){
            foreach($accounts as $key => $account){
                $data .= '<tr class="account-'.$account->id.'">
                            <td><a href="'.url('accounting/ledger-statement?chart_of_account_id='.$account->id.'&from='.$from.'&to='.$to).'" target="_blank">'.spaces($step, 1).'['.$account->code.'] '.$account->name.'</a></td>
                            <td class="text-right amount">'.ledgerClosingBalance($account->id, false, $to)['balance'].spaces($step, 1).'</td>
                        </tr>';
            }
        }
        if($group->childrenGroups->count() > 0){
            $data .= balanceSheet($group, $from, $to, $step+1);
        }
    }

    return $data;
}

function trialbalance($groups, $start, $end, $step = 0){
    if($step == 0){
        $groups = AccountGroup::doesntHave('parent')->orderBy('code')->get();
    }

    $data = '';
    foreach ($groups as $key => $group) {
        $delete = (!isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0)) || (isset($group->parent->id) && ($group->childrenGroups->count() > 0 || $group->chartOfAccounts->count() > 0)) ? false : true;
        $balance = groupClosingBalance($group->id, $start, $end);
        $data .= '<tr class="group-'.$group->id.'">
                        <td><strong>'.spaces($step).'['.$group->code.'] '.$group->name.'</strong></td>
                        <td class="text-right"><strong>'.groupClosingBalance($group->id, false, $start)['balance'].'</strong></td>
                        <td class="text-right"><strong>'.$balance['debit'].'</strong></td>
                        <td class="text-right"><strong>'.$balance['credit'].'</strong></td>
                        <td class="text-right"><strong>'.groupClosingBalance($group->id, false, $end)['balance'].'</strong></td>
                   </tr>';

        $accounts = getAccounts($group->id);
        if(isset($accounts[0])){
            foreach($accounts as $key => $account){
                $balance = ledgerClosingBalance($account->id, $start, $end);
                $data .= '<tr class="account-'.$account->id.'">
                            <td><a href="'.url('accounting/ledger-statement?chart_of_account_id='.$account->id.'&from='.$start.'&to='.$end).'" target="_blank">'.spaces($step, 1).'['.$account->code.'] '.$account->name.'</a></td>
                            <td class="text-right">'.ledgerClosingBalance($account->id, $start)['balance'].'</td>
                            <td class="text-right">'.$balance['debit'].'</td>
                            <td class="text-right">'.$balance['credit'].'</td>
                            <td class="text-right">'.ledgerClosingBalance($account->id, false, $end)['balance'].'</td>
                        </tr>';
            }
        }
        if($group->childrenGroups->count() > 0){
            $data .= trialbalance($group->childrenGroups, $start, $end, $step+1);
        }
    }

    return $data;
}