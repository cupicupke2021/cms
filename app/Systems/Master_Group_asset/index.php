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
        $mst_ship 	= config('tables.mst_ship');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		$userProp  = $aryCnt['userProp'];
		$userid     = $userProp[0]['userid'];
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		/*
        $group = selectGroup();
        $form['set_groupname']	  = Form::select('setpost_id_group',$group,'',['class' => 'form-control dropdown_box1','required'=>'required','id'=>'groupname']);
        $menu = selectMenu();
        $form['set_menu']	    = Form::select('setpost_id_menu',$menu,'',['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'menu']);
        $form['seth_id']    	    = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        */
		
		$office = selectOffice();
		$form['set_name'] 		= Form::text('setpost_name', '', ['class' => 'form-control']);
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
			
			$data['setpost_rec_date']	= date('Y-m-d H:i:s');
			$data['setpost_rec_user']	= $userid;
			$data['setpost_id'] =  erpUniqueId(8);
			$add 	= $gen->addRowData($mst_ship,$data);
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			return redirect()->intended($url);
		}
		
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
		$data		= $gen->getRowData($mst_location,$id,'id');
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
            $data['setpost_mod_date']	= date('Y-m-d H:i:s');
            $data['setpost_mod_user']	= $userid;
            $edit 	= $gen->editRowData($mst_location,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
       
		$mst_location 	= config('tables.mst_location');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($mst_location,'id',$id);
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