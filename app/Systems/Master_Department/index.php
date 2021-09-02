<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $mst_dept 	= config('tables.mst_buss_dept');
       
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
		$sSQL 		= "SELECT c.*
				   FROM $mst_dept c ";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","name","comp_id");
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
        $mst_dept 	= config('tables.mst_buss_dept');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		$userProp   = $aryCnt['userProp'];
		$userid     = $userProp[0]['userid'];
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		
		$busspart				= getVendor('O'); 
		$form['set_id'] 		= Form::text('setpost_id', '', ['class' => 'form-control','required' => 'required']);
		$form['set_name'] 		= Form::text('setpost_name', '', ['class' => 'form-control','required' => 'required']);
		$form['set_company'] 	= Form::select('setpost_comp_id',$busspart,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'comp_id']);
		$form['seth_unique'] 	    = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
		
        $genForm  = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			$data	= $request->input();
            $id   = $request->input('setpost_id');
            $IDexist = DB::table($mst_dept)->select('id')->where('id',$id)->first();
            $IDexist = json_decode(json_encode($IDexist), true);
            if(!empty($IDexist)){
                $url  	=  url('/').'/system/'.$sys.'/add';
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'ID '.$IDexist['id'].' Is Exists. Please Change. !!']);
            }

			$add 	= $gen->addRowData($mst_dept,$data);
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");

		}
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
     
		$mst_dept 	= config('tables.mst_buss_dept');
		
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
		$data		= $gen->getRowData($mst_dept,$id,'id');
		//wvd($data);
		$form                   = array();
		$office = selectOffice();
        $form['input_size']     = "col-sm-6";
		$busspart				= getVendor('O');
		$form['set_id'] 		= Form::text('setpost_id', $data['id'], ['class' => 'form-control','disabled'=>'disabled']);
		$form['set_name'] 		= Form::text('setpost_name', $data['name'], ['class' => 'form-control','required' => 'required']);
		$form['set_company'] 	= Form::select('setpost_comp_id',$busspart,$data['comp_id'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'comp_id']);
		$form['seth_id'] 	    = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
		
		$genForm  = $gen->createStdForm($form);
		
		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
		$aryCont = array("genform"=>$genForm);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
		
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){

            $id 	= $request->input('setunique_id');
            $data	= $request->input();
            $data['setpost_mod_date']	= date('Y-m-d H:i:s');
            $data['setpost_mod_user']	= $userid;
            $edit 	= $gen->editRowData($mst_dept,'id',$id,$data);
            $url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
            // return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
       
		$mst_dept 	= config('tables.mst_buss_dept');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
        
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($mst_dept,'id',$id);
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
        // return redirect()->intended($url);
        erpRedirect($url,"toast","SUCCESSFULLY ADD DATA DETAIL","success");
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
        // return redirect()->intended($url);
        erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA DETAIL","success");
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