<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $mst_itm_mat = config('tables.mst_itm_mat');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        
        $confGrid['table']  = "SELECT a.*,b.name AS item_group_name,b.dept_id as division,c.name as division_name 
								FROM mst_itm_mat a LEFT JOIN mst_itm_group b ON a.itm_group = b.id
                                LEFT JOIN mst_buss_dept c ON b.dept_id = c.id";
								
        $confGrid['list']   = array("id","itm_code", "name","uom", "itm_type","itm_group","item_group_name","division","division_name");
        $confGrid['header']   = array("id"=>"id","itm_code"=>"itm_code","name"=>"name","uom"=>"uom","uom_vol"=>"uom_vol","itm_type"=>"itm_type",
									"itm_group"=>"itm_group","item_group_name"=>"item_group_name","division"=>"division","division_name"=>"division_name");
		$confGrid['table_name'] = $mst_itm_mat;
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
        $mst_itm_mat        = config('tables.mst_itm_mat');
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
        $form['set_item_code']    	= Form::text('setpost_itm_code','', ['class' => 'form-control','required'=>'required','id'=>'itmCode']);
        $form['set_name']    	    = Form::text('setpost_name','', ['class' => 'form-control','required'=>'required','id'=>'name']);
        $form['set_foreign_name']   = Form::text('setpost_fr_name','', ['class' => 'form-control','required'=>'required']);
        $unit                       = DB::table('mst_units')->pluck('symbol','id')->prepend('--Select One--', '')->toArray();
		$form['set_uom']		    = Form::select('setpost_uom',$unit,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'uom']);
        $form['set_uom_vol']		= Form::select('setpost_uom_vol',$unit,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'uomVol']);
        
		$form['set_price']   		= Form::number('setpost_price','', ['class' => 'form-control','required'=>'required','id'=>'price'])
									  .Form::select('setpost_currency',selectCurrency(),'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'currency']);
        
		$itemType				    = getItemType();
		$form['set_item_type']		= Form::select('setpost_itm_type',$itemType,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'itm_type']);
        $itemGroup				    = getItemGroup();
		$form['set_item_group']		= Form::select('setpost_itm_group',$itemGroup,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'itm_group']);
		$form['set_active']		    = Form::checkbox('setpost_gen_active','true');
        $tax				        = getTax();
        $form['set_tax_group']		= Form::select('setpost_tax_group',$tax,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'taxGroup']);
        $form['set_sales_item']		= Form::checkbox('setpost_sls_itm','true');
        $form['set_inventory_item']	= Form::checkbox('setpost_inv_itm','true');
        $form['set_purchase_item']	= Form::checkbox('setpost_pur_itm','true');

        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"sys"=>$sys,"subsys"=>$subsys);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$data					= $request->input();

            $code         = $request->input('setpost_itm_code');
            $CodeExist    = DB::table($mst_itm_mat)->select('itm_code')->where('itm_code',$code)->first();
            $CodeExist    = json_decode(json_encode($CodeExist), true);
            $validate = 0;
            if(!empty($CodeExist)){
                $url  	=  url('/').'/system/'.$sys.'/add';
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'Item Code '.$CodeExist['itm_code'].' Is Exists. Please Change. !!']);
            }

            $data['setpost_id']   	= erpUniqueId(9);
            $data['setpost_itm_type_name']   	= getItemTypeDtl($data['setpost_itm_type']);

            $add 	= $gen->addRowData($mst_itm_mat,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $mst_itm_mat 		= config('tables.mst_itm_mat');
        $mst_itm_mat_varian = config('tables.mst_itm_mat_varian');
        $mst_itm_mat_spec   = config('tables.mst_itm_mat_spec');
        $mst_units          = config('tables.mst_units');
        

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
        $form['input_size']      = "col-sm-6";
        
        $form['set_item_code']    	= Form::text('setpost_itm_code',$data['itm_code'], ['class' => 'form-control','required'=>'required', 'readonly' => true]);
        $form['set_name']    		= Form::text('setpost_name',$data['name'],['class' => 'form-control','required'=>'required']);
        $form['set_foreign_name']   = Form::text('setpost_fr_name',$data['fr_name'], ['class' => 'form-control','required'=>'required']);
        $unit                       = DB::table('mst_units')->pluck('symbol','id')->prepend('--Select One--', '')->toArray();
		$form['set_uom']		    = Form::select('setpost_uom',$unit,$data['uom'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'uom']);
        $form['set_uom_vol']		= Form::select('setpost_uom_vol',$unit,$data['uom_vol'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'uomVol']);
        $form['set_price']   		= Form::number('setpost_price',$data['price'], ['class' => 'form-control','id'=>'price'])
									  .Form::select('setpost_currency',selectCurrency(),$data['currency'],['class' => 'form-control dropdown_box1 chosen-select','id'=>'currency']);
        
		$itemType				    = getItemType();
		$form['set_item_type']		= Form::select('setpost_itm_type',$itemType,$data['itm_type'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'itm_type']);
        $itemGroup				    = getItemGroup();
		$form['set_item_group']		= Form::select('setpost_itm_group',$itemGroup,$data['itm_group'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'itm_group']);
		$form['set_active']		    = Form::checkbox('setpost_gen_active','true',$data['gen_active']);
        $tax				        = getTax();
        $form['set_tax_group']		= Form::select('setpost_tax_group',$tax,$data['tax_group'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'taxGroup']);
        $form['set_sales_item']		= Form::checkbox('setpost_sls_itm','true',$data['sls_itm']);
        $form['set_inventory_item']	= Form::checkbox('setpost_inv_itm','true',$data['inv_itm']);
        $form['set_purchase_item']	= Form::checkbox('setpost_pur_itm','true',$data['pur_itm']);

        $form['seth_id']    	    = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        $genForm                    = $gen->createStdForm($form);

        // === GRID DETAIL 1 ===
        $sSQL = "
				 select *
				 from $mst_itm_mat_varian
				 where pid = '$id'
				";

        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("name","qty","remark","currency","price","uom","uom_vol");
        
        $confGrid['data']   = array("id","pid","itm_code","name","qty","remark","currency","price","uom","uom_vol");
        // wvd($confGrid);exit;
        $confGrid['id']     = "id";
        $confGrid['tabname']= "variant";
        $confGrid['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl            = $gen->createGridDtl($confGrid,$menuProp,$sys);
		
		$modal 				= array(); 
		$modal['header'] 	= "Edit Data";
		$modal['type'] 		= "edit_detail";
		
		$modal['tabname'] 	= "variant";
		$tabname			= $modal['tabname'];

        // $linkUrl  = url('/').'/system/trs_attachment/List/'.base64url_encode($sys);
        // $content = '';
        // $content .= '<div class="form-group row">';
        // $content .= '<label for="" class="col-sm-2 col-form-label text-right">Attachment</label>';
        // $content .= '<div class="'.$form['input_size'].'">';
        // $content .= '<form action="'.url('/').'/system/trs_attachment/add/parent/headless/ajax" class="dropzone form-control" id="drop"></form>
        //                 <a href="#" onClick="PopupCenter(\''.$linkUrl.'\',\'attachment\',\'500\',\'400\')" ><i class="fa fa-archive" aria-hidden="true"></i></a>
        //                 <input type="hidden" name="setpost_module_name" value="'.$sys.'"/>
        //                 <input type="hidden" name="setpost_trs_type" value="header"/>
        //                 <input type="hidden" name="setpost_trs_id" value="'.$id.'"/>
        //             ';
        // $content .= '</div>';
        // $content .= '</div>';
        // $modal['seth_image'] 		= $content;

        $modal['set_Name']  		= Form::text('setpost_name','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'name']);
        $modal['set_Qty']   		= Form::number('setpost_qty','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'qty']);
		$modal['set_currency']		= Form::select('setpost_currency',selectCurrency(),'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'currency']);

        $modal['set_Price']  		= Form::number('setpost_price','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'price']);
        $uom                 		= DB::table($mst_units)->pluck('symbol','id')->prepend('--Select One--', '')->toArray();
        $modal['set_uom']   		= Form::select('setpost_uom',$uom,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'uom']);
        $uomvol                 		= DB::table($mst_units)->pluck('symbol','id')->prepend('--Select One--', '')->toArray();
        $modal['set_uom_vol']   	= Form::select('setpost_uom_vol',$uomvol,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'uom_vol']);

        
        $modal['set_Remark'] 		= Form::text('setpost_remark','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'remark']);
        
        
        $modal['seth_pid']       	= Form::text('setpost_pid',$id, ['class' => 'form-control hidden','id'=>''.$tabname.'pid']);
        $modal['seth_itm_code']  	= Form::text('setpost_itm_code',$data['itm_code'], ['class' => 'form-control hidden','id'=>''.$tabname.'itm_code']);
        $modal['seth_tabname']   	= Form::text('setparam_tabname',$tabname, ['class' => 'form-control hidden','id'=>''.$tabname]);

        // === GRID DETAIL 2 ===
        $sSQL2 = "SELECT a.*, b.name as variant_name from $mst_itm_mat_spec a
                  left join $mst_itm_mat_varian b ON b.id = a.variant_id
				  where a.pid = '$id'
                  and a.variant_id is not null
				 ";

        $confGrid2['table']  = $sSQL2;
        $confGrid2['list']   = array("name","uom","value","variant_name");
        
        $confGrid2['data']   = array("id","pid","variant_id","name","uom","value","variant_name");
        // wvd($confGrid2);exit;
        $confGrid2['id']     = "id";
        $confGrid2['tabname']= "spec";
        $confGrid2['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl2            = $gen->createGridDtl($confGrid2,$menuProp,$sys);
		
		$modal2 				= array(); 
		$modal2['header'] 	    = "Edit Data";
		$modal2['type'] 		= "edit_detail";
		
		$modal2['tabname'] 	= "spec";
		$tabname2			= $modal2['tabname'];
        $link 				= url('/').'/system/Master_Item_Spec/List';

        $modal2['set_Name']      = Form::text('setpost_name','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname2.'name']);
        $variant                 = DB::table($mst_itm_mat_varian)->where('pid',$id)->pluck('name','id')->prepend('--Select One--', '')->toArray();
        $modal2['set_variant_id']   = Form::select('setpost_variant_id',$variant,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname2.'variant_id']);

        $uom                 = DB::table($mst_units)->pluck('symbol','id')->prepend('--Select One--', '')->toArray();
        $modal2['set_uom']   = Form::select('setpost_uom',$uom,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname2.'uom']);
        $modal2['set_value']  = Form::text('setpost_value','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname2.'value']);

        $modal2['seth_pid']  = Form::text('setpost_pid',$id, ['class' => 'form-control hidden','id'=>''.$tabname2.'pid']);
        $modal2['seth_tabname2']      = Form::text('setparam_tabname',$tabname2, ['class' => 'form-control hidden','id'=>''.$tabname2]);



		// === GRID DETAIL 3 ===
        $sSQL3 = "SELECT a.* from $mst_itm_mat_spec a
				  where a.pid = '$id'
                  and a.variant_id is null
				 ";

        $confGrid3['table']  = $sSQL3;
        $confGrid3['list']   = array("name","uom","value");
        $confGrid3['data']   = array("id","pid","name","uom","value");
        // wvd($confGrid2);exit;
        $confGrid3['id']     = "id";
        $confGrid3['tabname']= "itmspec";
        $confGrid3['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl3            = $gen->createGridDtl($confGrid3,$menuProp,$sys);
		
		$modal3 			= array(); 
		$modal3['header'] 	= "Edit Data";
		$modal3['type'] 	= "edit_detail";
		
		$modal3['tabname'] 	= "itmspec";
		$tabname3			= $modal3['tabname'];

        $modal3['set_Name']      	= Form::text('setpost_name','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname3.'name']);
        $uom                 		= DB::table($mst_units)->pluck('symbol','id')->prepend('--Select One--', '')->toArray();
        $modal3['set_uom']   		= Form::select('setpost_uom',$uom,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname3.'uom']);
        $modal3['set_value'] 		= Form::text('setpost_value','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname3.'value']);

        $modal3['seth_pid']  		= Form::text('setpost_pid',$id, ['class' => 'form-control hidden','id'=>''.$tabname3.'pid']);
        $modal3['seth_tabname3']    = Form::text('setparam_tabname',$tabname3, ['class' => 'form-control hidden','id'=>''.$tabname3]);
		

		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"gridDtl"=>$gridDtl,"gridDtl2"=>$gridDtl2,"gridDtl3"=>$gridDtl3,"sys"=>$sys,"subsys"=>$subsys);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
        
		
		$genModal 			= $gen->generateModal($modal);
        $genModal2 			= $gen->generateModal($modal2);
        $genModal3 			= $gen->generateModal($modal3);

        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genModal"=>$genModal,"genModal2"=>$genModal2,'genModal3'=>$genModal3);
        $view   .= View::make('fmodal')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			//$request->request->add(["foo"=>"bar"]);
			$id 	= $request->input('setunique_id');
			$data	= $request->input();
			
			$data['setpost_itm_type_name']   	= getItemTypeDtl($data['setpost_itm_type']);
            $edit 	= $gen->editRowData($mst_itm_mat,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
            
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $mst_itm_mat = config('tables.mst_itm_mat');
        $mst_itm_mat_varian = config('tables.mst_itm_mat_varian');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
        $deleteDetail = $gen->deleteRowData($mst_itm_mat_varian,'pid',$id);
		$delete 	= $gen->deleteRowData($mst_itm_mat,'id',$id);
        
		$url  	    =  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}

    function sysAddDtl($sys,$aryCnt){ 
		$mst_itm_mat_varian = config('tables.mst_itm_mat_varian');
        $mst_itm_mat_spec   = config('tables.mst_itm_mat_spec');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $param = $request->input('setparam_tabname');
        switch($param){
            case "variant":
                $data	    = $request->input();
                $data['setpost_id']	= erpUniqueId(9);
                $add 				= $gen->addRowData($mst_itm_mat_varian,$data);
                $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
        
            break;
            case "spec":
                $data	  = $request->input();
                $data['setpost_id']	        = erpUniqueId(9);
                $add 				        = $gen->addRowData($mst_itm_mat_spec,$data);
                $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
            break;
			case "itmspec":
                $data	  = $request->input();
                $data['setpost_id']	        = erpUniqueId(9);
                $add 				        = $gen->addRowData($mst_itm_mat_spec,$data);
                $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
            break;
        }
		
		// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA DETAIL.']);
        erpRedirect($url,"toast","SUCCESSFULLY ADD DATA DETAIL","success");
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		$mst_itm_mat_varian = config('tables.mst_itm_mat_varian');
		$mst_itm_mat_spec = config('tables.mst_itm_mat_spec');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $request->input('setunique_id');
		$data		= $request->input();
		
		$param 		= $request->input('setparam_tabname');
		switch($param){
            case "variant":
			$edit 	= $gen->editRowData($mst_itm_mat_varian,'id',$id,$data);
			break; 
			case "spec":
			$edit 	= $gen->editRowData($mst_itm_mat_spec,'id',$id,$data);
			break;
			case "itmspec":
			
			$edit 	= $gen->editRowData($mst_itm_mat_spec,'id',$id,$data);
			break; 
			
		}
        // wvd($data);exit;
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA DETAIL.']);
        erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA DETAIL","success");

	}
	
	function sysDelDtl($sys,$aryCnt){ 
		$mst_itm_mat_varian = config('tables.mst_itm_mat_varian');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 	= $gen->getId();
        $parent = $gen->getParent($mst_itm_mat_varian,'id',$id,'pid');
		$delete = $gen->deleteRowData($mst_itm_mat_varian,'id',$id);
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($parent);
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