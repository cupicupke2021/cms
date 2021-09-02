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
        $confGrid['header']   	= array("userid"=>"userid","username"=>"username","email"=>"email","password"=>"password","posisiton_code"=>"posisiton_code");
        $confGrid['table_name'] = $table_user;
        $confGrid['id']     	= "userid";
        $confGrid['link']   	= url('/').'/system/'.$sys.'/List/parent';
		
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
        $func       = $aryCnt['func'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
								
        $form['set_userid']      = Form::text('setpost_userid','', ['class' => 'form-control','required'=>'required']);
        $form['set_username']    = Form::text('setpost_username','', ['class' => 'form-control','required'=>'required']);
        //$form['set_password']    = Form::text('setpost_password','', ['class' => 'form-control','required'=>'required']);
        $form['set_password']    = Form::password('setpost_password',['class' => 'form-control']);
        $form['set_email']    	 = Form::email('setpost_email','', ['class' => 'form-control','required'=>'required']);				
        $form['set_token_gen_auto']    	 = Form::email('setpost_token','', ['class' => 'form-control']);				
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
			$data['setpost_password']     = md5($request->input('setpost_password'));
            $data['setpost_id']   	 	  = erpUniqueId(9);
			$tToken  					  = $func->generateSession();
			$data['setpost_token']   	  = $tToken;
            $add 	= $gen->addRowData($table_user,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_userid']);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_user = config('tables.table_user');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		$func       = $aryCnt['func'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($table_user,$id,'userid');
        
        $grid                    = "";
        $form                    = array();
        $form['input_size']      = "col-xs-6";
        // wvd($id);exit;
        $form['set_userid']      = Form::text('setpost_userid', $data['userid'], ['class' => 'form-control', 'readonly' => true]);
        $form['set_username']    = Form::text('setpost_username', $data['username'], ['class' => 'form-control']);
        $form['set_password']    = Form::password('setpost_password',['class' => 'form-control']);
        $form['set_email']    	 		 = Form::email('setpost_email', $data['email'], ['class' => 'form-control']);
		$form['set_token_gen_auto']    	 = Form::text('setpost_token',$data['token'], ['class' => 'form-control']);	
        $form['seth_id']    	 = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$id 	= $request->input('setunique_id');
			$data	= $request->input();
			$data['setpost_password'] 	= md5($request->input('setpost_password'));
			
			$tToken  					= $func->generateSession();
			$data['setpost_token']  	= $tToken;
			
            $edit 	= $gen->editRowData($table_user,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

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
    }
}