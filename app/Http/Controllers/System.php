<?php
//:::::::::::::::::::::::::::::
//Erp CMS made with love
//eCMS//
//Build on laravel 8
//Yusuf Syaefudin 2020
//:::::::::::::::::::::::::::::
namespace   App\Http\Controllers;

use         App\Http\Controllers\Controller;
use         App\Http\Controllers\Global_Function;
use         App\Http\Controllers\Global_Generator;
use         App\Http\Controllers\Global_Generator_dev;
use         App\Http\Controllers\Global_Perm;
use         App\Http\Controllers\Global_Perm_dev;
use         App\Http\Controllers\SSP;

use 		PhpOffice\PhpSpreadsheet\Spreadsheet;
use 		PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use         Symfony\Component\HttpFoundation\StreamedResponse;

use         Illuminate\Http\Request;
use         View;
use         Config;
use         DB;
use         route;
use         Form;

include_once base_path().'/app/Erp/Global_Tools.inc.php';
include_once base_path().'/app/Erp/General_Function.inc.php';


class System extends Controller
{
    public function index(Request $request){
		
		
        $sys        = route::input('sys');
        $subsys     = route::input('subsys');
        $id         = route::input('id');
        $mode       = route::input('mode');
        
        //generate Menu
        $func       = new Global_Function;
        $perm       = new Global_Perm;   
        $permDev    = new Global_Perm_dev;   
        $gen        = new Global_Generator; 
        $genDev     = new Global_Generator_dev; 
        $ssp        = new SSP;
		$sheet 		= new Spreadsheet();
		$xlsx		= new Xlsx($sheet);
		
		///mode REST API check token generated by user manager (NEW!!!!)..temporary.. 
		if($mode == 'rest'){
			
			$table_user     	= config('tables.table_user');
			$table_session  	= config('tables.table_session');
			$mst_biodata    	= config('tables.mst_biodata');
			$trs_his_position 	= config('tables.trs_his_position');
			
			$token   	= $request->input('token');
			$sSQL		= "select a.*,b.name as emp_name,c.username,c.userid
						   from 
						   $trs_his_position a,$mst_biodata b,$table_user c 
						   where 1=1 
						   and a.emp_id = b.id
						   and c.token = '$token'
						   and a.userid = c.userid
						   and a.active_now = 'YES'
						  "; 
	
			$results 			= DB::select(DB::raw($sSQL)); 
			//$userData 		= json_decode(json_encode($results[0]),true);
			if(!empty($results)){
			$userData 			= json_decode(json_encode($results[0]),true);
			}else{
			$arrayOK = array("status"=>"error","code"=>"0","desc"=>"Token Not Match!!"); 
			echo json_encode($arrayOK); 
			exit;

			$userData			= "";
			}
			//$userData    		= DB::table($trs_his_position)->where('userid', $ary['sUserid'])->where('active_now','yes')->first();
			
			$emp_number 		= (empty($userData['emp_number'])?"DEFF":$userData['emp_number']);
			$emp_name 			= (empty($userData['emp_name'])?"DEFF":$userData['emp_name']);
			$compid 			= (empty($userData['comp_id'])?"DEFF":$userData['comp_id']);
			$depart				= (empty($userData['department'])?"DEFF":$userData['department']);
			$position			= (empty($userData['position_id'])?"DEFF":$userData['position_id']);
			
			
            $dSession = $request->session()->get('dSession');
			
			if($dSession['sSession'] == "" ){
                $sSession  = $func->generateSession();
				$dSession['sSession']	= $sSession;
			}
		
            //replace session 
            $dSession = [
                        'sSession'  => $dSession['sSession'], 
                        'sUserid'   => $userData['userid'],
                        'sUserid'   => $userData['username'],
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
				'userid' 		=> $userData['userid'],
				'date' 			=> date("Y-m-d H:i:s"),
				'last_activity' => date("Y-m-d H:i:s"),
				'rec_user'   	=> $userData['userid'],
				'rec_date'   	=> date("Y-m-d H:i:s"),
				'rec_comp_id'   => $compid,
				'rec_dept'   	=> $depart,
				'rec_pos'   => $position,
				'rec_emp_id'    => $emp_number,
				'rec_emp_name'  => $emp_name,
				'id' 			=> erpUniqueId(8)
			]);
		}
		
		$content    = "";
        $dSession   = $request->session()->get('dSession');
        $sSession   = $dSession['sSession'];
		
        $isUser     = $perm->checkSession($sSession);
        $sysDet		= array("sys"=>$sys,"subsys"=>$subsys,"mode"=>$mode,"session"=>$sSession);
		
        if($isUser == 0){
            view()->addNamespace('themes', app_path('Systems/Themes/error'));
            include(app_path().'/Systems/'.$sys.'/index.php');
            //return view('themes::no_authorize');
			return redirect(url('/'));
        }else{
			
            $userProp      = $perm->checkUserProp($sSession);
			if(empty($userProp)){
					 view()->addNamespace('themes', app_path('Systems/Themes/error'));
					 include(app_path().'/Systems/'.$sys.'/index.php');
					 return view('themes::no_properties');
			}
			
            $userPropMain       = $perm->checkUserPropMain($sSession);
            $sysInfo 			= array("sysdet"=>$sysDet,"userprop"=>$userProp);
			//wvd($userPropMain);
            $check_menu_perm 	= $perm->checkMenuPerm($sysInfo);
			$check_menu_prop 	= $perm->checkMenuProp($sysInfo);
			
			$checkAccessMenu	= $perm->checkAccess($check_menu_prop,$subsys);
			if($checkAccessMenu == 0){
				if($mode == 'rest'){
					
				$arrayOK = array("status"=>"error","code"=>"0","desc"=>"You Not Authorize!"); 
				echo json_encode($arrayOK); 
				exit;
					
				}else{
					 view()->addNamespace('themes', app_path('Systems/Themes/error'));
					 include(app_path().'/Systems/'.$sys.'/index.php');
					 return view('themes::no_authorize');
				}
			}else{
            
            $sys 				= $sysInfo['sysdet']['sys'];
            $sys_desc			= $perm->checkSysDesc($sys);
            $sys_prop			= $perm->checkSysEnv();
            $sys_desc			= (isset($sys_desc[0]['description'])?$sys_desc[0]['description']:"");
			
			$check_menu_account = $perm->checkSysAccount($sysInfo,$sys_prop);
			$document_property  = $perm->checkDocProp($sys,$id);
			//wvd($check_menu_account);
			
            //$check_menu_account = $this->general_accounting->checkSysAccount($sysInfo,$sys_prop);
				$menu = "";
            if($mode != "headless"){
				
				if($mode == 'rest'){
				 view()->addNamespace('themes', app_path('Systems/Themes/headless'));
				}else{
				//`id`, `id_trans`, `hit`, `session`, `descr`, `date`, `user`, `username`, `rec_user`, `rec_date`, `mod_user`, `mod_date`SELECT * FROM `ucp_site_log`
				$data_log['id_trans'] 	=  base64url_decode($id);
				$data_log['hit'] 		=  $sys;
				$data_log['subsys'] 	=  $subsys;
				$data_log['session'] 	=  $sSession;
				$data_log['date'] 		=  date("Y-m-d");
				$data_log['user'] 		=  $userPropMain['userid'];
				$data_log['username'] 	=  $userProp[0]['username'];
				$data_log['rec_user'] 	=  $userPropMain['userid'];
				$data_log['rec_date'] 	=  date("Y-m-d H:i:s");
				$add 					=  $gen->addRowDataX("ucp_site_log",$data_log);
                
				if($mode == "popup"){
				view()->addNamespace('themes', app_path('Systems/Themes/popup'));
				
				}else{
				view()->addNamespace('themes', app_path('Systems/Themes/default'));
				$menu               = $perm->createMenu($sSession);
				}
				
				}
            }else{
                 view()->addNamespace('themes', app_path('Systems/Themes/headless'));
            }
                include(app_path().'/Systems/'.$sys.'/index.php');
		
            $ctrlGrid    = $perm->ctrlButton($sys,$check_menu_prop);
            $aryCnt      =  [
                            'request'=>$request,
                            'sysinfo'=>$sysInfo,
                            'ctrlgrid' => $ctrlGrid,
                            'gen'=>$gen,
                            'genDev'=>$genDev,
                            'ssp'=>$ssp, 
                            'menuProp'=>$check_menu_prop, 
							'userProp'=>$userProp,
							'userPropMain'=>$userPropMain,
							'sheet'=>$sheet,
							'xlsx'=>$xlsx,
							'docProp'=>$document_property,
							'func'=>$func
                            ];
	
            $content    .= erpSysIndex($aryCnt);
            $aryThme     = ['body'=>$content,'menu'=>$menu,'userProp'=>$userProp,
							'userpropmain'=>$userPropMain,
							'sysdesc'=>$sys_desc,
							'menuaccperm'=>$check_menu_account,
							'sysinfo'=>$sysInfo,
							'menuProp'=>$check_menu_prop,
							'func'=>$func
							];
            
            return view('themes::theme',$aryThme); 
			}
        }
    }
}