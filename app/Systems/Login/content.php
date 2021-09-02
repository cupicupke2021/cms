<?php
include "entity.php";

function erpSysList($aryCnt){

    $ucup       = erpList();
    $view       = "";
   
    $request    = $aryCnt['request']; 
    $subsyspart = $request->input('subsyspart');

    if(isset($subsyspart)){ 
        
        $userName   	= $request->input('setpostvalue_username');
        $userPass   	= md5($request->input('setpostvalue_password'));
        
        $table_user     	= config('tables.table_user');
        $table_session  	= config('tables.table_session');
        $mst_biodata    	= config('tables.mst_biodata');
		$trs_his_position 	= config('tables.trs_his_position');
        
		$results    = DB::table($table_user)->where('username', $userName)->where('password',$userPass)->first();
		if(!empty($results)){ 
			$check 	    = json_decode(json_encode($results), true);
		}else{ 
			$check 		= null;
		}
		$url = url('/');
        //dd(DB::getQueryLog($check));
        if(isset($check)){
			
			
			$sSQL		= "select a.*,b.name as emp_name 
						   from 
						   $trs_his_position a,$mst_biodata b
						   where 1=1 
						   and a.emp_id = b.id
						   and a.userid = '$userName'
						   and a.active_now = 'YES'
							"; 
	
			$results 			= DB::select(DB::raw($sSQL)); 
			//$userData 		= json_decode(json_encode($results[0]),true);
			
			if(!empty($results)){
			$userData 			= json_decode(json_encode($results[0]),true);
			}else{ 
			$userData			= "";
			}
			
			//$userData    		= DB::table($trs_his_position)->where('userid', $ary['sUserid'])->where('active_now','yes')->first();
			
			$emp_number 		= (empty($userData['emp_number'])?"DEFF":$userData['emp_number']);
			$emp_name 			= (empty($userData['emp_name'])?"DEFF":$userData['emp_name']);
			$compid 			= (empty($userData['comp_id'])?"DEFF":$userData['comp_id']);
			$depart				= (empty($userData['department'])?"DEFF":$userData['department']);
			$position			= (empty($userData['position_id'])?"DEFF":$userData['position_id']);
			
			
            $dSession = $request->session()->get('dSession');
            //replace session 
            $dSession = [
                        'sSession'  => $dSession['sSession'], 
                        'sUserid'   => $check['userid'],
                        'sUserid'   => $check['username'],
                        'sCompid'   => $compid,
                        'sDepart'   => $depart,
                        'sPosition' => $position,
                        'sEmpNum'  	=> $emp_number,
                        'sEmpName'  => $emp_name,
						'id' 		=> erpUniqueId(8)
                        ];
            $request->session()->put('dSession',$dSession);
			
			DB::table($table_session)->insert([
				'session' 		=> $dSession['sSession'],
				'userid' 		=> $check['userid'],
				'date' 			=> date("Y-m-d H:i:s"),
				'last_activity' => date("Y-m-d H:i:s"),
				'rec_user'   	=> $check['userid'],
				'rec_date'   	=> date("Y-m-d H:i:s"),
				'rec_comp_id'   => $compid,
				'rec_dept'   	=> $depart,
				'rec_pos'   => $position,
				'rec_emp_id'    => $emp_number,
				'rec_emp_name'  => $emp_name,
				'id' 			=> erpUniqueId(8)
			]);
			$url 	= "/system/home/List";
			
			erpRedirect($url,"toast","Login Success !!","success");
			//return Redirect::to("$url")->header('Cache-Control', 'no-store, no-cache, must-revalidate');
            //return redirect(url('/')."/system/home/List");
        }else{
			// wvd($url);exit;
			// erpRedirect($url,"toast","Login Error !!","error");
			return redirect(url('/'))->with(['error' => 'Userid Or Password Maybe Wrong.']);
        }
 
    }

    view()->addLocation(app_path('Systems/Login'));
    $view   .= view('finput',["ucup"=>$ucup]); 
    return $view;
}