<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use App\Models\wallet;
use Illuminate\Http\Request;

class WalletsController extends Controller
{
    function create(Request $request){
        // dd($request->all());
        $reference_id = $request->group_id;
        $type = $request->type;
        $doesExist = wallet::where('reference_id', '=', $reference_id)->where('type', '=', 1)->get()->first();
        if($doesExist){
            return redirect('AdminTool/wallet/'. $doesExist->id .'/transactions');
        }
        $walletArr = array(
            "reference_id" => $reference_id,
            "type" => $type
        );
        $wallet = wallet::create($walletArr);
        return redirect('AdminTool/wallet/'. $wallet->id .'/transactions');
    }
}
