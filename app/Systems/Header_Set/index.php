<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $table_user = config('tables.table_user');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        $confGrid['table']  	= "select a.* from $table_user a";
        $confGrid['list']   	= array("userid", "username","email","password","posisiton_code");
        $confGrid['header']   	= array("userid", "username","email","password","posisiton_code");
        $confGrid['table_name'] = $table_user;
        $confGrid['id']     	= "userid";
        $confGrid['linkAdd']   	= url('/').'/system/'.$sys.'/List/parent';
        $confGrid['linkEdit']   	= url('/').'/system/'.$sys.'/List/parent';
        $grid               	= $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);

   
        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont= array("grid"=>$grid);
        $view   .=View::make('flist')->with('aryCont',$aryCont);
        

        return $view;
    }
	
	function sysAdd($sys,$aryCnt){
        $table_user = config('tables.table_user');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
								
        $form['set_userid']      = Form::text('setpost_userid','', ['class' => 'form-control','required'=>'required']);
        $form['set_username']    = Form::text('setpost_username','', ['class' => 'form-control','required'=>'required']);
        //$form['set_password']    = Form::text('setpost_password','', ['class' => 'form-control','required'=>'required']);
        $form['set_password']    = Form::password('setpost_password',['class' => 'form-control']);
        $form['set_email']    	 = Form::email('setpost_email','', ['class' => 'form-control','required'=>'required']);
								
        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$data	= $request->input();
			$data['setpost_password'] = md5($request->input('setpost_password'));
            $add 	= $gen->addRowData($table_user,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_userid']);
			return redirect()->intended($url);

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $ucp_site_header_set = config('tables.ucp_site_header_set');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
        $userProp   = $aryCnt['userProp'];
	
		$id 		= $gen->getId();
		$sys        = route::input('sys');
        $subsys     = route::input('subsys');
        $table_name = base64url_decode(route::input('id'));
        $sys_name   = route::input('mode');
        $header   	= route::input('subsyspart');
        $userid   	= $userProp[0]['userid'];
		
		$header 	= explode("_azra_",base64url_decode($header));
		$sSQL 		= " select a.* from $ucp_site_header_set a
						where 1=1 
						and a.table_name = '$table_name'
						and a.module 	 = '$sys_name'
						and a.userid 	 = '$userid'
						order by a.ord asc
						";
		$results 	= DB::select(DB::raw($sSQL));
		
		if(!empty($results)){ 
			$data 	    = json_decode(json_encode($results), true);
		}else{ 
			$data 		= array();
			$aryData 	= json_decode($header[1]);
			$aryLabel 	= json_decode($header[0],true);
			$no = 1;
			foreach($aryLabel as $kuy => $yuk){ 
					
					$aryInsert 			= array(); 
					$aryInsert['setpost_id']		= erpUniqueId(9);
					$aryInsert['setpost_ord']		= $no;
					$aryInsert['setpost_module']	= $sys_name;
					$aryInsert['setpost_table_name']= $table_name;
					$aryInsert['setpost_userid']	= $userid;
					$aryInsert['setpost_header']	= $kuy;
					$aryInsert['setpost_label']		= $yuk;
					$aryInsert['setpost_action']	= "SHOW";
					$aryInsert['setunique_id']		= "";
					$add 							= $gen->addRowData($ucp_site_header_set,$aryInsert);
			$no++;
			}
		}
		
		$confGrid['table']  		= $sSQL;
        $confGrid['list']   		= array("id","header","label","action");
        $confGrid['data']   		= array("id","module","table_name","userid","header","label","action");
        $confGrid['id']     		= "id";
        $confGrid['tabname']		= "detail";
        $confGrid['linkAdd']   		= url('/').'/system/'.$sys.'/edit_detail';
        $confGrid['linkEdit']   	= url('/').'/system/'.$sys.'/edit_detail';
		
		$action						= array("SHOW"=>"SHOW","HIDE"=>"HIDE");
		$form['seti_id']			= array("name"=>"setpost_id","type"=>"text","class"=>"form-control","data"=>$action);		
		$form['seti_label']			= array("name"=>"setpost_label","type"=>"text","class"=>"form-control","data"=>$action);		
		$form['sets_action']		= array("name"=>"setpost_action","type"=>"select","class"=>"form-control","data"=>$action);		
		$gridDtl            		= $gen->createGridDtlInline($confGrid,$menuProp,$form,$sys);
		
        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("grid"=>$gridDtl);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$id 						= $request->input('setunique_id');
			$data						= $request->input();
			$data['setpost_password'] 	= md5($request->input('setpost_password'));
            $edit 						= $gen->editRowData($ucp_site_header_set,'id',$id,$data);
            
			$url  						=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $table_user = config('tables.table_user');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($table_user,'userid',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}
	
	
	function sysEditDtl($sys,$aryCnt){
        $ucp_site_header_set 	= config('tables.ucp_site_header_set');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$data	= $request->input();

		$data['setunique_id'] = $data['setpost_id'];
		
		$id 	= $data['setpost_id'];
		$edit 	= $gen->editRowData($ucp_site_header_set,'id',$id,$data);

		$resp['status'] = '1';
		$resp['data']	= 1;
		
		$myJSON = json_encode($resp);
		parseContent($myJSON);
		exit;
		/*
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($table_user,'userid',$id);
		*/
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
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
		 case "edit_detail":
            return sysEditDtl($sys,$aryCnt);
        break;
    }
}