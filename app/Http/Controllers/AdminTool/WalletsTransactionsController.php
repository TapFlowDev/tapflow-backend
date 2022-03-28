<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Models\wallet;
use App\Models\wallets_transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletsTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($wallet)
    {
        // dd($wallet);

        $transactions = wallets_transaction::where('wallet_id', '=', $wallet)->orderBy('created_at', 'desc')->paginate(20);
        $walletInfo = wallet::find($wallet);
        $groupAdminId = DB::table('group_members')->where('group_id', '=', $walletInfo->reference_id)->where('privileges', '=', 1)->get()->first();
        $groupInfo = DB::table('groups')->where('id', '=', $walletInfo->reference_id)->get()->first();
        $adminInfo = DB::table('users')->where('id', '=', $groupAdminId->user_id)->get()->first();
        $adminAndGroupData = array(
            'admin_id' => $adminInfo->id,
            'admin_email' => $adminInfo->email,
            'admin_name' => $adminInfo->first_name . " " . $adminInfo->last_name,
            'group_id' => $groupInfo->id,
            'group_name' => $groupInfo->name,
            'group_type' => $groupInfo->type
        );
        return view('AdminTool.Transactions.index', ['transactions' => $transactions, 'walletInfo' => $walletInfo, 'groupInfo' => $adminAndGroupData]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($wallet)
    {
        $walletInfo = wallet::find($wallet);
        return view('AdminTool.Transactions.add', ['wallet' => $wallet, 'current' => $walletInfo->balance]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($wallet, Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'wallet' => 'required|exists:wallets,id'
        ]);
        // dd($request->all());
        $transactionData = array(
            'amount' => $request->amount,
            'type' => 1,
            'wallet_id' => $wallet,
        );
        $transaction = wallets_transaction::create($transactionData);
        if ($transaction) {
            $transaction->status = 1;
            $transaction->save();
        }
        $walletInfo = wallet::find($wallet);
        $currentBalance = (float)$walletInfo->balance;
        $newBalance = $currentBalance + (float)$request->amount;
        $walletInfo->balance = number_format($newBalance, 2, '.', '');
        $walletInfo->save();
        return redirect('AdminTool/wallet/' . $wallet . '/transactions');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
