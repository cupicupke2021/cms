<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $mst_ship 	= config('tables.mst_ship');
       
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
		$sSQL 		= "SELECT * FROM $mst_ship WHERE is_active = '1' ";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","name");
        $confGrid['id']     = "id";
        $confGrid['link']   = url('/').'/system/'.$sys.'/List/parent';
        $grid               = $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);

        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont= array("grid"=>$grid);
        $view   .=View::make('flist')->with('aryCont',$aryCont);
        
        return $view;
    }
	
    function sysAdd($sys,$aryCnt){
        $mst_ship 			= config('tables.mst_ship');
        $table_sitemenu 	= config('tables.table_sitemenu');
        $trs_sign 			= config('tables.trs_sign');
        $trs_sign_log 		= config('tables.trs_sign_log');
        $trs_external 		= config('tables.trs_external');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		$userProp   = $aryCnt['userProp'];
		$userPropMain   = $aryCnt['userPropMain'];
		$userid     	= $userProp[0]['userid'];
		
		$dataX 		= base64_decode($request->input('dXata')); 
		$explode 	= explode("#",$dataX);
		
		$module		= $explode[1];
		$trs_id		= base64_decode($explode[2]);
		$session	= $explode[3];
		$sSession	= Session::get('dSession');
	
		if($sSession['sSession'] != $session){
				$content  = '
							<script type="text/javascript" src="'.url('/').'/public/assets/js/jquery.min.js"></script>
							<link rel="stylesheet" href="'.url('/').'/public/assets/js/sweetalert.min.css">
							<script src="'.url('/').'/public/assets/js/sweetalert.min.js"></script>
							';
										
				$content .= '<script>
								setTimeout(function() {
									swal({
										title: "Whoops !",
										text: "Your Session Not Detected !!",
										type: "error"
									}, function() {
										window.close();
									});
								}, 500);
							</script>';
							parseContent($content);
							exit;
		}
		
		
		$moduled  		= DB::table($table_sitemenu)->select('id','description')->where('name',$module)->first();
		$module_id 		= $moduled->id;
		$module_desc 	= $moduled->description;
		$stage			= getStage($userPropMain['userid'],$trs_id,$module_id);
		
		
		$sSQLDOC	= "select a.id,a.docnum,a.table_name
					  from 
					  (
					   select a.id,a.docnum,'$trs_external' as table_name
					   from  $trs_external a
					  )a 
					  where 1=1 
					  and a.id = '$trs_id'
					  ";
		
		$resultsDOC = DB::select(DB::raw($sSQLDOC));
		
		if(empty($resultsDOC)){
				$content  = '
							<script type="text/javascript" src="'.url('/').'/public/assets/js/jquery.min.js"></script>
							<link rel="stylesheet" href="'.url('/').'/public/assets/js/sweetalert.min.css">
							<script src="'.url('/').'/public/assets/js/sweetalert.min.js"></script>
							';
										
				$content .= '<script>
								setTimeout(function() {
									swal({
										title: "Whoops !",
										text: "Docnum Not Found!!",
										type: "error"
									}, function() {
										window.close();
									});
								}, 500);
							</script>';
							parseContent($content);
							exit;
			
		
		}else{ 
		
		$dataDoc	= json_decode(json_encode($resultsDOC[0]), true);
		$docnum 	= $dataDoc['docnum'];
		}
		
		//`id`, `module`, `trs_id`, `docnum`, `total_stage`, `status`, `rec_date`, `rec_user`, `mod_date`, `mod_user`, `rec_pos`, `rec_emp_id`, `rec_emp_name`, `rec_comp_id`, `rec_dept`
		if(empty($stage)){ 
				
				$content  = '
							<script type="text/javascript" src="'.url('/').'/public/assets/js/jquery.min.js"></script>
							<link rel="stylesheet" href="'.url('/').'/public/assets/js/sweetalert.min.css">
							<script src="'.url('/').'/public/assets/js/sweetalert.min.js"></script>
							';
										
				$content .= '<script>
								setTimeout(function() {
									swal({
										title: "Whoops !",
										text: "Approval Not Set, Call Your System Administrator !!",
										type: "error"
									}, function() {
										window.close();
									});
								}, 500);
							</script>';
							parseContent($content);
							exit;
			
		
		}

		$total_stage 						= count($stage);
		$datay['setpost_id']				= erpUniqueId(9);
        $datay['setpost_trs_id']			= $trs_id;
        $datay['setpost_docnum']			= $docnum;
        $datay['setpost_total_stage']		= $total_stage;
        $datay['setpost_status']			= "OPEN";
		
		$add 							= $gen->addRowData($trs_sign,$datay);
		
		foreach($stage as $key=>$kuy){
			
		//   wvd($kuy);
		
		/// `id`, `id_staging`, `origin`, `destination`, `module_id`, `module_name`, `data_id`, `docnum`, `date_send`, `status_ref`, `statusresp`, `datestat`, `table_name`, 
		//  `stage`, `ack`, `finish`, `userid`, `comp_id`, 
		//  `read`, `rec_user`, `rec_date`, `mod_user`, `mod_date`, `rec_comp_id`, `rec_dept`, `rec_pos`, `rec_emp_id`, `rec_emp_name`
		/*
		destination] => stockadmin
		[stage] => 1
		[status_ref] => APPROVE
		[id_staging] => 1153410222
		[email] => 
		[name_email] => 
		[options] => AND
		*/
		
		$data['setpost_id']				= erpUniqueId(9);
		$data['setpost_pid']			= $datay['setpost_id'];
        $data['setpost_module']			= $module;
        $data['setpost_trs_id']			= $trs_id;
        $data['setpost_id_staging']		= $kuy['id_staging'];
        $data['setpost_origin']			= $userid;
        $data['setpost_destination']	= $kuy['destination'];
        $data['setpost_module_id']		= $module_id;
        $data['setpost_module_name']	= $module_desc;
        $data['setpost_date_send']		= date("Y-m-d");
        $data['setpost_status_ref']		= $kuy['status_ref'];
        $data['setpost_docnum']			= $docnum;
        $data['setpost_stage']			= $kuy['stage'];
        $data['setpost_table_name']		= $dataDoc['table_name'];
        $data['setpost_options']		= $kuy['options'];
		
		$add 							= $gen->addRowData($trs_sign_log,$data);
		
		}reset($stage);
		
		
		//$add 						= $gen->addRowData($trs_sign_log,$data);
		wvd($stage);
		exit;
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
     
		$mst_ship 	= config('tables.mst_ship');
		
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
        $userProp   = $aryCnt['userProp'];
		$userid     = $userProp[0]['userid'];
		$id 		= $gen->getId();
		//$data		= $gen->getRowData($table_groupmenu,$id,'id');
		$data		= $gen->getRowData($mst_ship,$id,'id');
		//wvd($data);
		$form                   = array();
		$office = selectOffice();
        $form['input_size']     = "col-sm-6";
		
		$form['set_name'] 		= Form::text('setpost_name', $data['name'], ['class' => 'form-control']);
		$form['seth_id'] 	        = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
		
		
        //$form['set_id'] = Form::text('setpost_id', $data['id'], ['class' => 'form-control text']);
        $genForm  = $gen->createStdForm($form);
		
		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
		$aryCont = array("genform"=>$genForm);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
		
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			//$request->request->add(["foo"=>"bar"]);
            $id 	= $request->input('setunique_id');
            $data	= $request->input();
            $setpost_name    = $request->input('setpost_name');
            $data['setpost_mod_date']	= date('Y-m-d H:i:s');
            $data['setpost_mod_user']	= $userid;

            $IDexist = DB::table($mst_ship)->select('name')->where('name',$setpost_name)->first();
            $IDexist = json_decode(json_encode($IDexist), true);
            if(!empty($IDexist)){
                
                $url  	=  url('/').'/system/'.$sys.'/add';
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'Name Ship '.$IDexist['name'].' Is Exists. Please Change. !!']);
            }

            $edit 	= $gen->editRowData($mst_ship,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
       
		$mst_ship 	= config('tables.mst_ship');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($mst_ship,'id',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}
	
	function sysAddDtl($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
        $data	= $request->input();
        $data['setpost_id']	= erpUniqueId(9);
        $add	= $gen->addRowData($table_sitemenu_prop,$data);
        $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id_groupmenu']);
        return redirect()->intended($url);
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
        $id 	= $request->input('setunique_id');
        $data	= $request->input();
        $edit 	= $gen->editRowData($table_sitemenu_prop,'id',$id,$data);
        $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id_groupmenu']);
        return redirect()->intended($url);
	}
	
	function sysDelDtl($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 	= $gen->getId();
		$delete = $gen->deleteRowData($table_sitemenu_prop,'id',$id);
		$parent = $gen->getParent($table_sitemenu_prop,'id',$id,'id_groupmenu');
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($parent);
		
	}

    switch($subsys){
        case "List":
            return sysList($sys,$aryCnt);
        break;
        case "edit":
            return sysEdit($sys,$aryCnt);
        break;
		 case "delete":
            return sysDelete($sys,$aryCnt);
        break;
		case "add":
            return sysAdd($sys,$aryCnt);
        break;
		case "add_detail":
            return sysAddDtl($sys,$aryCnt);
        break;
		case "edit_detail":
            return sysEditDtl($sys,$aryCnt);
        break;
		case "delete_detail":
            return sysDelDtl($sys,$aryCnt);
        break;
    }
}