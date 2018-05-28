<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\TradingAccount;
use App\Models\StructureItems\Market;
use App\Http\Requests\TradingAccountRequest;

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
		$markets = Market::whereNotIn('id', $trading_accounts->pluck('market_id'))->get();
		return view('trading_account.edit')->with(compact('user','markets','trading_accounts'));
	}

	 /**
     * Update the users email fields
     *
     * @return Response
     */
    public function update(TradingAccountRequest $request)
    {
        $user = $request->user();
    	$tradingAccounts = $request->input('trading_accounts');
        $savedModels = $user->tradingAccounts()->with('markets')->get();//get once that have alread been stored
        $tradingAccountModels = [];

        foreach ($tradingAccounts as $tradingAccount) 
        {
    		$tradingAccountModel = array_key_exists('id', $tradingAccount) ? $savedModels->firstWhere('id',$tradingAccount['id']) : New TradingAccount(); 
			$tradingAccountModel->fill($tradingAccount);
			$tradingAccountModel->user_id = $user->id;
			$tradingAccountModels[] = $tradingAccountModel;
        }

    	$user->tradingAccounts()->saveMany($tradingAccountModels);
        return redirect()->back()->with('success', 'Trading account settings updated!');
    }
}
