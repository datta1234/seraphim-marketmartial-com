<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\TradingAccount;
use App\Models\StructureItems\Market;

class TradingAccountController extends Controller
{
	/**
	* load the email fields that can be completed
	*
	* @return Response
	*/
	public function edit(Request $request)
	{
		$user = $request->user();
		$trading_accounts = $user->tradingAccounts()->get(); 
		$markets = Market::whereNotIn('id', $trading_accounts->pluck('market_id'))->where('is_selectable',true)->get();
		return view('trading_account.edit')->with(compact('user','markets','trading_accounts'));
	}

	 /**
     * Update the users email fields
     *
     * @return Response
     */
    public function update(EmailRequest $request)
    {
        $user = $request->user();
        return redirect()->back()->with('success', 'Profile updated!');
    }
}
