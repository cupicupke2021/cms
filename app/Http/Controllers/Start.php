<?php

namespace   App\Http\Controllers;

use         App\Http\Controllers\Controller;
use         App\Http\Controllers\Global_Function;

use         Illuminate\Http\Request;
use         View;
use         Config;
use         DB;
use         Redirect;


include_once base_path().'/app/Erp/Global_Tools.inc.php';
include_once base_path().'/app/Erp/General_Function.inc.php';

class Start extends Controller
{
    public function index(Request $request){
        
        //set table auth
        $table_session  = config('tables.table_session');
        $dSession       = $request->session()->get('dSession');
		$dSession['sSession'] = (empty($dSession['sSession'])?"":$dSession['sSession']);
        $sSession       = $dSession['sSession'];
        $func           = new Global_Function;
        $isUser         = $func->checkSession($table_session,$sSession);
        
        if($isUser == 0){
            if($dSession['sSession'] == "" ){
                $sSession  = $func->generateSession(); 
                $dSession  = array("sSession"=>$sSession);
                $request->session()->put('dSession',$dSession);
                return redirect(url('/'));
            }else{
                include(app_path().'/Systems/Login/content.php');
                $aryCnt     = ['request'=>$request];
                $content    = erpSysList($aryCnt);
                parseContent($content);
            }
        }else{
				return Redirect::to("/system/home/List")
				->header('Cache-Control', 'no-store, no-cache, must-revalidate');
				//return redirect(url('/')."/system/home/List");
        }
    }
}
