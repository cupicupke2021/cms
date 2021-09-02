<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $trs_inv_log 		= config('tables.trs_inv_log');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
		$sSQL 	= "select a.* FROM $trs_inv_log a where a.in_out = 'IN'";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","date","itm_code","itm_code_name","qty","in_out");
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
      
        $trs_inv_log 	= config('tables.trs_inv_log');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		
		$itm_code						= getItemVal();
		$itm_variant					= getItemVar();
		$uom							= getUom();
		$condition						= getCondition();
		
        $form['set_date'] 				= Form::date('setpost_date','', ['class' => 'form-control','id'=>'date']);
        $form['set_itm_code']			= Form::select('setpost_itm_code',$itm_code,'',['class' => 'chosen-select dropdown_box1 ','required'=>'required','id'=>'itm_code']);
		$form['set_qty']   				= Form::number('setpost_qty','',['class' => 'form-control','required'=>'required','id'=>'qty']);
		
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
			$data['setpost_id']	= erpUniqueId(9);
			$data['setpost_in_out'] 	= 'IN';
			$add 	  			= $gen->addRowData($trs_inv_log,$data);
			//return redirect()->intended($url);
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			erpRedirect($url,"toast","SUCCESSFULLY ADD DATA.","success");
        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $trs_inv_log = config('tables.trs_inv_log');
		
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
		$data		= $gen->getRowData($trs_inv_log,$id,'id');
		//wvd($data);exit;
		$form                   = array();
		$form['input_size']      = "col-sm-6";
		$itm_code						= getItemVal();
		$itm_variant					= getItemVar();
		$uom							= getUom();
		$condition						= getCondition();
		
        $form['set_date'] 				= Form::date('setpost_date',$data['date'], ['class' => 'form-control','id'=>'date']);
        $form['set_itm_code']			= Form::select('setpost_itm_code',$itm_code,$data['itm_code'],['class' => 'chosen-select dropdown_box1 ','required'=>'required','id'=>'itm_code']);
		$form['set_qty']   				= Form::number('setpost_qty',$data['qty'],['class' => 'form-control','required'=>'required','id'=>'qty']);
		
		$form['seth_unique'] 			= Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
		
      
        //$form['set_id'] = Form::text('setpost_id', $data['id'], ['class' => 'form-control text']);
        $genForm  = $gen->createStdForm($form);
		
		
		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"sys"=>$sys,"subsys"=>$subsys);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
            //$request->request->add(["foo"=>"bar"]);
            $id 	= $request->input('setunique_id');
            $data	= $request->input();
			$data['setpost_in_out'] = 'IN';
            $edit 	= $gen->editRowData($trs_inv_log,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        //$table_groupmenu = config('tables.table_groupmenu');
		$trs_inv_log 	   = config('tables.trs_inv_log');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($trs_inv_log,'id',$id);
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