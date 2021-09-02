<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $table_groupmenu 	= config('tables.table_groupmenu');
        $table_sitemenu 	= config('tables.table_sitemenu');
        $mst_buss_part 		= config('tables.mst_buss_part');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
		$sSQL 	= "SELECT c.id,CONCAT(c.id,' - ',c.name) as name,c.alias
		             ,case c.type
					   when 'C' then 'CUSTOMER' 
					   when 'V' then 'VENDOR'
					   when 'O' then 'OFFICE'
					  end as type
					 ,case c.sub_type
					   when 'U' then 'UMUM' 
					   when 'L' then 'LAIN-LAIN'
					  end as category 
					 
		                ,c.address,c.delivery_address,c.bill_period
			   FROM $mst_buss_part c ";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","name","alias","type","category","address","delivery_address","bill_period");
        $confGrid['id']     = "id";
        $confGrid['link']   = url('/').'/system/'.$sys.'/List/parent';
		//wvd($confGrid);
        $grid               = $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);

        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont= array("grid"=>$grid);
        $view   .=View::make('flist')->with('aryCont',$aryCont);
        
        return $view;
    }
	
    function sysAdd($sys,$aryCnt){
      
        $table_mst_buss_part 	= config('tables.mst_buss_part');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		/*
        $group = selectGroup();
        $form['set_groupname']	  = Form::select('setpost_id_group',$group,'',['class' => 'form-control dropdown_box1','required'=>'required','id'=>'groupname']);
        $menu = selectMenu();
        $form['set_menu']	    = Form::select('setpost_id_menu',$menu,'',['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'menu']);
        $form['seth_id']    	    = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        */
		$currency = selectCurrency();
		$form['set_name'] = Form::text('setpost_name', '', ['class' => 'form-control', 'required' => 'required']);
		$form['set_alias'] = Form::text('setpost_alias', '', ['class' => 'form-control']);
		$form['set_address'] = Form::text('setpost_address', '', ['class' => 'form-control']);
		$form['set_delivery_address'] = Form::text('setpost_delivery_address', '', ['class' => 'form-control']);
		$form['set_type']	= Form::select('setpost_type',['C'=>"CUSTOMER",'V'=>"VENDOR",'O'=>"OFFICE"],'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'type']);
		$form['set_category']	= Form::select('setpost_sub_type',['U'=>"UMUM",'L'=>"LAIN-LAIN"],'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'sub_type']);
		$form['set_phone_number'] = Form::text('setpost_phone_number', '', ['class' => 'form-control']);
		$form['set_fax_number'] = Form::text('setpost_fax_number', '', ['class' => 'form-control']);
		$form['set_email'] = Form::email('setpost_email', '', ['class' => 'form-control']);
		$form['set_bill_period'] = Form::text('setpost_bill_period', '', ['class' => 'form-control']);
		$form['set_latitude'] = Form::text('setpost_lat', '', ['class' => 'form-control']);
		$form['set_longitude'] = Form::text('setpost_lon', '', ['class' => 'form-control']);
		$form['set_currency'] = Form::select('setpost_currency',$currency,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'sub_type']);
		$form['set_NPWP'] = Form::text('setpost_npwp', '', ['class' => 'form-control']);
		$form['set_PPN']	= Form::checkbox('setpost_ppn','1');
		// $form['set_init'] = Form::text('setpost_init', '', ['class' => 'form-control']);
		$form['set_is_active'] = Form::checkbox('setpost_is_active','1', true);
		$form['seth_unique'] = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
		
        $genForm    = $gen->createStdForm($form);
        
        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm, "sys"=>$sys,"subsys"=>$subsys);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$data				= $request->input();
			$setpost_id 		= getMaxnum($data['setpost_type'],$data['setpost_sub_type']);
			$data['setpost_id']	= $setpost_id;
            $data['setpost_is_active']  = (isset($data['setpost_is_active']) ? $data['setpost_is_active']:"");
	
			$name_vendor		= $data['setpost_name'];
			$add 	  = $gen->addRowData($table_mst_buss_part,$data);
			$url  	  = url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			$urlX	  = url('/')."/public/assets/js/jquery.min.js";
			$content  = '<script type="text/javascript" src="'.$urlX.'"></script>';
			$content .= "<script type='text/javascript'>"; 
			$content .= "$( document ).ready(function() {";
			$content .= "window.opener.$('#vendor').append($(\"<option/>\", {
										value: '$setpost_id',
										text: '$setpost_id - $name_vendor'
										}));
						 window.opener.$('#vendor').val('$setpost_id');
						 window.opener.$('#vendor_name').val('$name_vendor');
						 window.opener.$('#vendor').trigger(\"chosen:updated\");
                         alert('SUCCESSFULLY ADDED DATA.');
						 window.close();
						";
			$content  .= "});";
			$content  .= "</script>";
			parseContent($content);
			return redirect()->intended($url);
			
        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_mst_buss_part 	   = config('tables.mst_buss_part');
        $table_mst_contact_person 	   = config('tables.mst_contact_person');
        $table_sitemenu_prop = config('tables.table_sitemenu_prop');
		
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
        $userProp   = $aryCnt['userProp'];
		//wvd($userProp);
		$id 		= $gen->getId();
		//$data		= $gen->getRowData($table_groupmenu,$id,'id');
		$data		= $gen->getRowData($table_mst_buss_part,$id,'id');
		//wvd($data);exit;
		$form                   = array();
		$currency = selectCurrency();
        $form['input_size']     	= "col-sm-6";
		$form['set_name'] 			= Form::text('setpost_name', $data['name'], ['class' => 'form-control', 'required' => 'required']);
		$form['set_alias'] 			= Form::text('setpost_alias', $data['alias'], ['class' => 'form-control']);
		$form['set_address'] 		= Form::text('setpost_address', $data['address'], ['class' => 'form-control']);
		$form['set_delivery_address'] = Form::text('setpost_delivery_address', $data['delivery_address'], ['class' => 'form-control']);
		$form['set_type']			= Form::select('setpost_type',['C'=>"CLIENT",'V'=>"VENDOR",'O'=>"OFFICE"],$data['type'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'type']);
		$form['set_category']		= Form::select('setpost_sub_type',['U'=>"UMUM",'L'=>"LAIN-LAIN",'S'=>"SEAFOOD",],$data['sub_type'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'sub_type']);
		$form['set_phone_number'] 	= Form::text('setpost_phone_number', $data['phone_number'], ['class' => 'form-control']);
		$form['set_fax_number'] 	= Form::text('setpost_fax_number', $data['fax_number'], ['class' => 'form-control']);
		$form['set_email'] 			= Form::text('setpost_email', $data['email'], ['class' => 'form-control']);
		$form['set_bill_period'] 	= Form::text('setpost_bill_period', $data['bill_period'], ['class' => 'form-control']);
		$form['set_latitude'] 		= Form::text('setpost_lat', $data['lat'], ['class' => 'form-control']);
		$form['set_longitude'] 		= Form::text('setpost_lon', $data['lon'], ['class' => 'form-control']);
		$form['set_NPWP'] 			= Form::text('setpost_npwp', $data['npwp'], ['class' => 'form-control']);
		$form['set_PPN']			= Form::checkbox('setpost_ppn','1',$data['ppn']);
		$form['set_currency'] 		= Form::select('setpost_currency',$currency,$data['currency'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'sub_type']);
		$form['set_init'] 			= Form::text('setpost_init', $data['init'], ['class' => 'form-control']);
		$form['set_is_active'] 		= Form::checkbox('setpost_is_active','1',$data['is_active']);
        
        $form['seth_id'] = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        //$form['set_id'] = Form::text('setpost_id', $data['id'], ['class' => 'form-control text']);
        $genForm  = $gen->createStdForm($form);
		
		$sSQL = "SELECT *
				 FROM $table_mst_contact_person a
				 WHERE a.pid = '$id'
				";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("name","phone_number_1","phone_number_2","fax_number","email");
        $confGrid['data']   = array("name","phone_number_1","phone_number_2","fax_number","email");
        $confGrid['id']     = "id";
        $confGrid['tabname']= "detail";
        $confGrid['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
		
        $gridDtl            = $gen->createGridDtl($confGrid,$menuProp,$sys);
		
		$modal 	 = array(); 
		$modal['header'] 	 = "Edit Data";
		$modal['type'] 	 = "edit_detail";
		$modal['tabname'] 	 = "detail";
		$tabname	 = $modal['tabname'];
		
		$modal['seth_pid']  = Form::text('setpost_pid', $id, ['class' => 'form-control hidden','id' => ''.$tabname.'pid']);
		$modal['set_name'] = Form::text('setpost_name', '', ['class' => 'form-control', 'id' => ''.$tabname.'name' ]);
		$modal['set_phone_number_1'] = Form::text('setpost_phone_number_1', '', ['class' => 'form-control', 'id' => ''.$tabname.'phone_number_1' ]);
		$modal['set_phone_number_2'] = Form::text('setpost_phone_number_2', '', ['class' => 'form-control', 'id' => ''.$tabname.'phone_number_2' ]);
		$modal['set_fax_number'] = Form::text('setpost_fax_number', '', ['class' => 'form-control', 'id' => ''.$tabname.'fax_number' ]);
		$modal['set_email'] = Form::text('setpost_email', '', ['class' => 'form-control', 'id' => ''.$tabname.'email' ]);
		
		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"gridDtl"=>$gridDtl,"sys"=>$sys,"subsys"=>$subsys);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
        
		
		$genModal 		= $gen->generateModal($modal);
		
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genModal"=>$genModal);
        $view   .= View::make('fmodal')->with('aryCont',$aryCont);
		
		
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
            //$request->request->add(["foo"=>"bar"]);
            $id 	= $request->input('setunique_id');
            $data	= $request->input();
            $data['setpost_is_active']  = (isset($data['setpost_is_active']) ? $data['setpost_is_active']:"");
            $data['rec_date']	= date('Y-m-d H:i:s');
            $data['rec_user']	= '1';
            $data['mod_date']	= date('Y-m-d H:i:s');
            $data['mod_user']	= '1';
            $edit 	= $gen->editRowData($table_mst_buss_part,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url)->with(['success'=>'SUCCESSFULLY UPDATED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        //$table_groupmenu = config('tables.table_groupmenu');
		$table_mst_buss_part 	   = config('tables.mst_buss_part');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($table_mst_buss_part,'id',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}
	
	function sysAddDtl($sys,$aryCnt){ 
		
		$table_mst_contact_person = config('tables.mst_contact_person');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
        $data = $request->input();
        //$data['setpost_id']	= erpUniqueId(9);
       
        $add = $gen->addRowData($table_mst_contact_person,$data);
        $url =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url);
    }
	
	function sysEditDtl($sys,$aryCnt){ 
		$table_mst_contact_person = config('tables.mst_contact_person');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
        $id 	= $request->input('setunique_id');
        $data	= $request->input();
        $edit 	= $gen->editRowData($table_mst_contact_person,'id',$id,$data);
		
        $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
        return redirect()->intended($url);
    }
	
    function sysDelDtl($sys,$aryCnt){ 
		$mst_contact_person = config('tables.mst_contact_person');
		$mst_buss_part 	   = config('tables.mst_buss_part');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 	= $gen->getId();
		wvd($id);
		wvd($mst_contact_person);
		$delete = $gen->deleteRowData($mst_contact_person,'id',$id);
		
		$parent = $gen->getParent($mst_buss_part,'id',$id,'pid');
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($parent);
		//return redirect()->intended($url);
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