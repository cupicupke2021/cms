<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class Global_Function extends Controller
{
    //
    public function generateSession(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
        $randomString = '';
        $length = 20;
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
        }
          
		$date	= date("Y-m-d");
		$string = 'ERP-'.$date.$randomString; 
		return $string;
    }

    public function checkSession($table_session,$sSession){
        if(!isset($sSession)){ 
			$isUser	 	= 0;
		}else{
            $session    = DB::table($table_session)->where('session','=',$sSession)->count();
            if($session > 0){ 
                $isUser     = 1;
            }else{ 
                $isUser     = 0;
            }
        }
    return $isUser;
    }


}
