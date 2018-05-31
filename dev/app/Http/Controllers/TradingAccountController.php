<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\TradingAccount;
use App\Models\StructureItems\Market;
use App\Http\Requests\TradingAccountRequest;
use App\Models\UserManagement\Email;

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
        $emails = $user->emails()->with('defaultLabel')->get();//get once that have alread been stored
		return view('trading_account.edit')->with(compact('user','markets','trading_accounts','emails'));
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
        $emails = $request->input('email');
        $savedModels = $user->tradingAccounts()->with('market')->get();//get once that have alread been stored
        $savedEmailModels = $user->emails()->with('defaultLabel')->get();//get once that have alread been stored

        $tradingAccountModels = [];

        foreach ($tradingAccounts as $tradingAccount) 
        {
            if(!array_key_exists('id', $tradingAccount) && ($tradingAccount['safex_number'] != null || $tradingAccount['sub_account'] !=null)) 
            {
                 $tradingAccountModel = array_key_exists('id', $tradingAccount) ? $savedModels->firstWhere('id',$tradingAccount['id']) : New TradingAccount(); 
                $tradingAccountModel->fill($tradingAccount);
                $tradingAccountModel->user_id = $user->id;
                $tradingAccountModels[] = $tradingAccountModel;   
            }	
        }

        foreach ($emails as $key => $email) 
        {
            $emailModel = $savedEmailModels->firstWhere('id',$email['id']); 
            $email['notifiable'] = $email['notifiable'] ? $email['notifiable'] : false;
            $emailModel->fill($email);
            $emailModels[] = $emailModel;
        }

        $user->emails()->saveMany($emailModels);
        $user->tradingAccounts()->saveMany($tradingAccountModels);
        return redirect()->back()->with('success', 'Trading account settings updated!');
    }
}
