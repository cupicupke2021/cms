<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $mst_itm_mat    = config('tables.mst_itm_mat');
        $mst_itm_group  = config('tables.mst_itm_group');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        // //generate DBgrid\
		
		$sSQL = "SELECT a.id, a.name as itm_group_name, a.parent, b.itm_code, b.name as itm_name FROM $mst_itm_group a
                 LEFT JOIN $mst_itm_mat b ON a.id = b.itm_group
                 ORDER BY a.id ";
		$results 	        = DB::select(DB::raw($sSQL));
		$result 	        = json_decode(json_encode($results), true);		
		$dataGrp['query'] 	= $result;
        
        $dataGrp['list']    = array("itm_group","itm_group_name", "itm_code","itm_name");
		
        // $confGrid['list']   = array("itm_group","itm_group_name", "itm_code","itm_name");
        // $confGrid['id']     = "id";
        // $confGrid['link']   = url('/').'/system/'.$sys.'/List/parent';

        // $grid               = $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);
        // wvd($confGrid);exit;
   
        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont= array("dataGrp"=>$dataGrp);
        // wvd($aryCont);exit;
        $view   .= View::make('flist')->with('aryCont',$aryCont);
        

        return $view;
    }
	
	function sysAdd($sys,$aryCnt){
        $mst_itm_mat = config('tables.mst_itm_mat');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
        
        // $form['set_id']      = Form::text('setpost_id','', ['class' => 'form-control','required'=>'required']);
        $form['set_item_code']    	= Form::text('setpost_itm_code','', ['class' => 'form-control','required'=>'required']);
        $form['set_name']    		= Form::text('setpost_name','',['class' => 'form-control','required'=>'required']);
        $form['set_foreign_name']   = Form::text('setpost_fr_name','', ['class' => 'form-control','required'=>'required']);
        $unit                    = DB::table('mst_units')->pluck('symbol','id');
        $unit['']                = '--Select One--';
		$form['set_uom']		 = Form::select('setpost_uom',$unit,'',['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'unitID']);
        $itemType				    = getItemType();
		$form['set_item_type']		= Form::select('setpost_itm_type',$itemType,'',['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'itm_type']);
        $itemGroup				    = getItemGroup();
		$form['set_item_group']		= Form::select('setpost_itm_group',$itemGroup,'',['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'itm_group']);
		$form['set_active']		    = Form::checkbox('setpost_gen_active','true');
        $tax				        = getTax();
        $form['set_tax_group']		= Form::select('setpost_tax_group',$tax,'',['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'taxGroup']);
        $form['set_sales_item']		= Form::checkbox('setpost_sls_itm','true');
        $form['set_inventory_item']	= Form::checkbox('setpost_inv_itm','true');
        $form['set_purchase_item']	= Form::checkbox('setpost_pur_itm','true');
        

        // $form['set_uom']    	 	= Form::text('setpost_uom','', ['class' => 'form-control','required'=>'required']);


        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        // wvd($request->input());exit;
        if(isset($subsyspart)){
			
			$data					= $request->input();
            $data['setpost_id']   	= erpUniqueId(9);

            $add 	= $gen->addRowData($mst_itm_mat,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			return redirect()->intended($url);

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $mst_itm_mat 		= config('tables.mst_itm_mat');
        $mst_itm_mat_varian = config('tables.mst_itm_mat_varian');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($mst_itm_mat,$id,'id');
	
        $grid                    = "";
        $form                    = array();
        $form['input_size']      = "col-xs-6";
        
        $form['set_item_code']    	= Form::text('setpost_itm_code',$data['itm_code'], ['class' => 'form-control','required'=>'required']);
        $form['set_name']    		= Form::text('setpost_name',$data['name'],['class' => 'form-control','required'=>'required']);
        $form['set_foreign_name']   = Form::text('setpost_fr_name',$data['fr_name'], ['class' => 'form-control','required'=>'required']);
        $unit                    = DB::table('mst_units')->pluck('symbol','id');
        $unit['']                = '--Select One--';
		$form['set_uom']		 = Form::select('setpost_uom',$unit,$data['uom'],['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'unitID']);
        $itemType				    = getItemType();
		$form['set_item_type']		= Form::select('setpost_itm_type',$itemType,$data['itm_type'],['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'itm_type']);
        $itemGroup				    = getItemGroup();
		$form['set_item_group']		= Form::select('setpost_itm_group',$itemGroup,$data['itm_group'],['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'itm_group']);
		$form['set_active']		    = Form::checkbox('setpost_gen_active','true',$data['gen_active']);
        $tax				        = getTax();
        $form['set_tax_group']		= Form::select('setpost_tax_group',$tax,$data['tax_group'],['class' => 'form-control dropdown_box1 selectpicker','data-live-search'=>'true','required'=>'required','id'=>'taxGroup']);
        $form['set_sales_item']		= Form::checkbox('setpost_sls_itm','true',$data['sls_itm']);
        $form['set_inventory_item']	= Form::checkbox('setpost_inv_itm','true',$data['inv_itm']);
        $form['set_purchase_item']	= Form::checkbox('setpost_pur_itm','true',$data['pur_itm']);

        $form['seth_id']    	 = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $sSQL = "
				 select *
				 from $mst_itm_mat_varian
				 where pid = '$id'
				";

        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("name","qty","status","price");
        $confGrid['data']   = array("id","name","qty","status");
        $confGrid['id']     = "id";
        $confGrid['tabname']= "detail";
        $confGrid['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl            = $gen->createGridDtl($confGrid,$menuProp,$sys);
		
		$modal 				= array(); 
		$modal['header'] 	= "Edit Data";
		$modal['type'] 		= "edit_detail";
		$modal['tabname'] 	= "detail";
		$tabname			= $modal['tabname'];
        $modal['set_Name']   = Form::text('setpost_name','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'name']);
        $modal['set_Qty']   = Form::number('setpost_qty','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'qty']);
        $modal['set_Status']   = Form::text('setpost_status','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'status']);

		// $modal['set_prop']	= Form::select('setpost_name',genMenuProp(),'',['class' => 'form-control dropdown_box1','required'=>'required','id'=>''.$tabname.'prop']);
		$modal['seth_pid']  = Form::text('setpost_pid',$id, ['class' => 'form-control hidden','id'=>''.$tabname.'pid']);
        
		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"gridDtl"=>$gridDtl);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
        
		
		$genModal 			= $gen->generateModal($modal);
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genModal"=>$genModal);
        $view   .= View::make('fmodal')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			//$request->request->add(["foo"=>"bar"]);
			$id 	= $request->input('setunique_id');
			$data	= $request->input();
			// $data['setpost_password'] = md5($request->input('setpost_password'));
            $edit 	= $gen->editRowData($mst_itm_mat,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $mst_itm_mat = config('tables.mst_itm_mat');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($mst_itm_mat,'id',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}

    function sysAddDtl($sys,$aryCnt){ 
		$mst_itm_varian = config('tables.mst_itm_varian');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$data				= $request->input();
		$data['setpost_id']	= erpUniqueId(9);
        $add 				= $gen->addRowData($mst_itm_varian,$data);
		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url);
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		$mst_itm_varian = config('tables.mst_itm_varian');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		wvd($request->input());exit;
		$id 	= $request->input('setunique_id');
		$data	= $request->input();
        
        $edit 	= $gen->editRowData($mst_itm_varian,'id',$id,$data);
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url);
	}
	
	function sysDelDtl($sys,$aryCnt){ 
		$mst_itm_varian = config('tables.mst_itm_varian');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 	= $gen->getId();

		$delete = $gen->deleteRowData($mst_itm_varian,'id',$id);
		$parent = $gen->getParent($mst_itm_varian,'id',$id,'pid');

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