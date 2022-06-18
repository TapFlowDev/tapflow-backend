<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\BillingInfoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WalletsController;
use App\Models\Billing_info;
use App\Models\Countries;
use App\Models\Group;
use App\Models\User;
use App\Models\withdrawl_request;
use Exception;
use Illuminate\Http\Request;

class WithdrawlRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($group_id)
    {
        $billingInfoObj = new BillingInfoController;
        $walletObj = new WalletsController;
        $withdrawlRequests = $this->getWithdrawlData(withdrawl_request::where('group_id', '=', $group_id)->paginate(20));
        $billingInfo = $this->getBillingInfo(Billing_info::where('group_id', '=', $group_id)->get())->first();
        $wallet = $walletObj->getOrCreateWallet($group_id, 1);

        return view('AdminTool.Agencies.WithdrawalRequests.index', ['withdrawlRequests' => $withdrawlRequests, 'billingInfo' => $billingInfo, 'walletInfo' => $wallet]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($group_id, $id)
    {
        $walletObj = new WalletsController;
        $wallet = $walletObj->getOrCreateWallet($group_id, 1);
        $group = Group::where('id', $group_id)->first();
        $withdrawlRequest = withdrawl_request::where('id', '=', $id)->first();
        // return ($withdrawlRequest);
        return view('AdminTool.Agencies.WithdrawalRequests.edit', ['team' => $group, 'wallet' => $wallet->id, 'withdrawal' => $withdrawlRequest]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $group_id, $id)
    {
        try {
            $walletTransactionsObj = new WalletsTransactionsController;
            $validated = $request->validate([
                'amount' => 'required|numeric|gt:0',
                'wallet' => 'required|exists:wallets,id',
            ]);
            $withdrawlRequest = withdrawl_request::where('id', '=', $id)->first();
            $withdrawArray = array(
                'amount' => $request->amount,
                'wallet' => $request->wallet,
            );
            if ($request->hasFile('invoice')) {
                $destPath = 'images/invoices';
                $ext = $request->file('invoice')->extension();
                $imageName =  time() . "-" . $id . "." . $ext;
                $request->invoice->move(public_path($destPath), $imageName);
                // withdrawl_request::where('id', $id)->update(array('invoice' => $imageName));
                $withdrawlRequest->invoice = $imageName;
            }
            $walletTransactionResponse = $walletTransactionsObj->withdraw($withdrawArray);
            if ($walletTransactionResponse['code'] == 500) {
                $withdrawlRequest->status = 2;
                $withdrawlRequest->save();
                $request->session()->flash('fail', 'there was an error');
                return redirect()->back();
            }



            $withdrawlRequest->wallet_transactiond_id = $walletTransactionResponse['transaction']->id;
            $withdrawlRequest->status = 1;
            $withdrawlRequest->save();
            $request->session()->flash('success', 'action success');
            return redirect('/AdminTool/agencies/' . $group_id . '/withdrawal');
        } catch (Exception $error) {
            $request->session()->flash('fail', 'there was an error');
            return redirect()->back();
        }
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
    private function getWithdrawlData($array)
    {
        foreach ($array as $keyWithdrawl => &$withdrawl) {
            $user = User::where('id', '=', $withdrawl->user_id)->get()->first();
            $withdrawl->admin_name = $user->first_name . " " . $user->last_name;
        }
        return $array;
    }
    private function getBillingInfo($array)
    {
        foreach ($array as $keyBilling => &$billing) {
            if ($billing->country != '') {
                $country = Countries::where('id', $billing->country)->first();
                $billing->countryCode = $country->code;
            }
        }
        return $array;
    }
}
