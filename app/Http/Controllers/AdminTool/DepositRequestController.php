<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WalletsController;
use App\Models\deposit_request;
use App\Models\Group;
use Exception;
use Illuminate\Http\Request;

class DepositRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($company)
    {
        $walletObj = new WalletsController;
        $wallet = $walletObj->getOrCreateWallet($company, 1);
        $companyInfo = Group::find($company);
        $deposits = deposit_request::where('company_id', '=', $company)->latest()->paginate(20);
        return view('AdminTool.Companies.DepositRequests.index', ['deposits' => $deposits, 'company' => $companyInfo, 'walletInfo'=>$wallet]);
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
    public function edit($id)
    {
        $depositRequest = deposit_request::where('id', $id)->first();
        $company = Group::where('id', '=', $depositRequest->company_id)->first();
        return view('AdminTool.Companies.DepositRequests.edit', ['company' => $company, 'deposit' => $depositRequest]);
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
        try {
            $depositRequest = deposit_request::where('id', '=', $id)->first();
            $company = Group::where('id', '=', $depositRequest->company_id)->first();
            $company_id = $company->id;
            if ($request->hasFile('invoice')) {
                $destPath = 'images/invoices';
                $ext = $request->file('invoice')->extension();
                $imageName =   $company_id . "-" . $id . "." . $ext;
                $request->invoice->move(public_path($destPath), $imageName);
                // withdrawl_request::where('id', $id)->update(array('invoice' => $imageName));
                $depositRequest->invoice = $imageName;
            }
            $depositRequest->status = $request->status;
            $depositRequest->save();
            $request->session()->flash('success', 'deposit confirmed');
            return redirect('/AdminTool/companies/' . $company_id . '/depositRequests');
        } catch (Exception $error) {
            $request->session()->flash('fail', $error->getMessage());
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
}
