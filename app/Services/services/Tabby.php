<?php

namespace App\Services\services;

use Illuminate\Http\Request;
use App\Models\PaymentGetway;
use App\Models\PaymentGeteway;
use Illuminate\Support\Facades\Config;
use App\Services\contracts\PaymentInterface;

class myFatoorahPayment implements PaymentInterface
{
    public function __construct()
    {

        $tabby = PaymentGetway::where([
            ['keyword', 'Tabby'],
        ])->first();
        $tabbyConf = json_decode($tabby->information, true);
        Config::set('services.tabby.api_token',$tabbyConf["api_token"]);
        Config::set('services.tabby.base_url','');


    }
    public function paymentProcess(
        $request,
        $_amount,
        $return,
        $callback
    ){
        $tabby =   Config::get('services.tabby.api_token');
    }
    public function successPayment(Request $request)
    {



    }
    public function calbackPayment(Request $request)
    {




    }
}
