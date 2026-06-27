<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class AccountController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function index() {
        $assets   = ['datatable'];
        $accounts = Account::all();
        return view('backend.admin.account.list', compact('accounts', 'assets'));
    }

    public function create(Request $request) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.account.modal.create');
        }
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'account_name'    => 'required|max:50',
            'opening_date'    => 'required|date',
            'account_number'  => 'nullable|max:50',
            'currency'        => 'required',
            'opening_balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('accounts.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::beginTransaction();

        $account                  = new Account();
        $account->account_name    = $request->input('account_name');
        $account->opening_date    = $request->input('opening_date');
        $account->account_number  = $request->input('account_number');
        $account->currency        = $request->input('currency');
        $account->description     = $request->input('description');
        $account->opening_balance = $request->input('opening_balance');
        $account->save();
        $account->currency = $account->currency . ' (' . currency_symbol($account->currency) . ')';

        if ($account->opening_balance > 0) {
            $transaction              = new Transaction();
            $transaction->trans_date  = $request->input('opening_date');
            $transaction->account_id  = $account->id;
            $transaction->dr_cr       = 'cr';
            $transaction->type        = 'income';
            $transaction->amount      = $request->opening_balance;
            $transaction->description = _lang('Account Opneing Balance');

            $transaction->save();
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('accounts.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $account, 'table' => '#accounts_table']);
        }

    }

    public function show(Request $request, $id) {
        $account = Account::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.account.modal.view', compact('account', 'id'));
        }

    }

    public function edit(Request $request, $id) {
        $account = Account::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.account.modal.edit', compact('account', 'id'));
        }
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'account_name'   => 'required|max:50',
            'opening_date'   => 'required|date',
            'account_number' => 'nullable|max:50',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('accounts.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $account                 =  Account::find($id);
        $account->account_name   =  $request->input('account_name');
        $account->opening_date   =  $request->input('opening_date');
        $account->account_number =  $request->input('account_number');
        $account->description    =  $request->input('description');

        $account->save();
        $account->currency = $account->currency . ' (' . currency_symbol($account->currency) . ')';

        if (!$request->ajax()) {
            return redirect()->route('accounts.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $account, 'table' => '#accounts_table']);
        }
    }

    public function convert_due_amount(Request $request, $accountId, $amount) {
        $account      = Account::find($accountId);
        $rawAmount    = convert_currency($request->activeBusiness->currency, $account->currency, $amount);
        $formatAmount = formatAmount($rawAmount, currency_symbol($account->currency));
        return response()->json(['rawAmount' => $rawAmount, 'formatAmount' => $formatAmount]);
    }

    public function destroy($id) {
        DB::beginTransaction();

        $account = Account::find($id);
        try {
            Transaction::where('account_id', $id)
                ->where('transaction_category_id', null)
                ->where('ref_id', null)
                ->delete();
            $account->delete();

            DB::commit();
            return redirect()->route('accounts.index')->with('success', _lang('Deleted Successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('accounts.index')->with('error', _lang('Sorry, This account is already exists in transactions'));
        }
    }

}
