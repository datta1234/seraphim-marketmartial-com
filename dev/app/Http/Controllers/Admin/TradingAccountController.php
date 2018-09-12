<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserManagement\User;
use App\Models\UserManagement\TradingAccount;
use App\Models\StructureItems\Market;
use App\Http\Requests\TradingAccountRequest;

class TradingAccountController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user_accounts = $user->tradingAccounts()->get(); 
        $markets = Market::all();
        $trading_accounts =  [];

        foreach ($markets as $market) 
        {
            $user_account =  $user_accounts->firstWhere('market_id',$market->id);
            if(!$user_account)
            {
                $trading_accounts[] = new TradingAccount([
                    'user_id' => $user->id,
                    'market_id' => $market->id,
                ]);  
            }else
            {
                $trading_accounts[] = $user_account;
            }     
        }

        $emails = $user->emails()->with('defaultLabel')->get();//get ones that have already been stored
        // Used to determine admin interest update for the view
        $is_admin_update = true;
        return view('trading_account.edit')->with(compact('user','markets','trading_accounts','emails','is_admin_update'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TradingAccountRequest $request, User $user)
    {
        //TODO - change SIZWE logic to fit admin
        $tradingAccounts = $request->input('trading_accounts');
        $emails = $request->has('email') ? $request->input('email') : [];
        $savedModels = $user->tradingAccounts()->with('market')->get();//get once that have alread been stored
        $savedEmailModels = $user->emails()->with('defaultLabel')->get();//get once that have alread been stored

        $tradingAccountModels = [];
        $emailModels = [];
        
        foreach ($tradingAccounts as $tradingAccount) 
        {
    
            if(array_key_exists('id', $tradingAccount) || (!array_key_exists('id', $tradingAccount) && ($tradingAccount['safex_number'] != null || $tradingAccount['sub_account'] !=null))) 
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
