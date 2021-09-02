<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $trs_sign_staging 		= config('tables.trs_sign_staging');
        $trs_sign_staging_dtl 	= config('tables.trs_sign_staging_dtl');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		$sSQL 		= "select a.*
					   from $trs_sign_staging a
					   where 1=1
					  ";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","description","comp_id","departement","position_id");
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
        $table_groupmenu 	= config('tables.table_groupmenu');
        $mst_buss_part 		= config('tables.mst_buss_part');
        $mst_buss_dept 		= config('tables.mst_buss_dept');
        $mst_position 		= config('tables.mst_position');
		
		$trs_sign_staging 		= config('tables.trs_sign_staging');
        $trs_sign_staging_dtl 	= config('tables.trs_sign_staging_dtl');
        //base variable
		
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		$menu					 = selectMenu();
		$company				 = selectBusPart("O");
		$form['set_menu']	     = Form::select('setpost_id_menu',$menu,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
		$form['set_description'] = Form::text('setpost_description', '', ['class' => 'form-control']);

		$department				 = DB::table($mst_buss_dept)->pluck('name','id');
		$department->prepend('--Select One--');
		$position				 = DB::table($mst_position)->pluck('name','id');
		$position->prepend('--Select One--');
		$form['set_company']	 = Form::select('setpost_comp_id',$company,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'company']);
		$form['set_department']	 = Form::select('setpost_departement',$department,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'departement']);
		$form['set_position']	 = Form::select('setpost_position_id',$position,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'position_id']);
		
        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$data				= $request->input();
			$data['setpost_id']	= erpUniqueId(9);
            $add 				= $gen->addRowData($trs_sign_staging,$data);
			$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			return redirect()->intended($url);
        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_groupmenu 		= config('tables.table_groupmenu');
        $mst_buss_part 			= config('tables.mst_buss_part');
        $mst_buss_dept 			= config('tables.mst_buss_dept');
        $mst_position 			= config('tables.mst_position');
		$trs_sign_staging 		= config('tables.trs_sign_staging');
        $trs_sign_staging_dtl 	= config('tables.trs_sign_staging_dtl');
        $mst_apprv  	        = config('tables.mst_apprv');
        $mst_position  	        = config('tables.mst_position');
		
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($trs_sign_staging,$id,'id');
        
		
		$form 					 = array();
		$form['input_size']      = "col-sm-6";
		$menu					 = selectMenu();
		$company				 = selectBusPart("O");
		$form['set_menu']	     = Form::select('setpost_id_menu',$menu,$data['id_menu'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
		$form['set_description'] = Form::text('setpost_description',$data['description'], ['class' => 'form-control']);

		$department				 = DB::table($mst_buss_dept)->pluck('name','id');
		$department->prepend('--Select One--');
		$position				 = DB::table($mst_position)->pluck('name','id');
		$position->prepend('--Select One--');
		
		$form['set_company']	 = Form::select('setpost_comp_id',$company,$data['comp_id'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'company']);
		$form['set_department']	 = Form::select('setpost_departement',$department,$data['departement'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'departement']);
		$form['set_position']	 = Form::select('setpost_position_id',$position,$data['position_id'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'position_id']);
		
        $form['seth_id']    	= Form::text('setunique_id',$data['id'],['class' => 'form-control hidden']);
        $genForm                = $gen->createStdForm($form);
		
		$sSQL = "
				 select a.*
				 from $trs_sign_staging_dtl a
				 where 1=1 
				 and a.pid = '$id'
				";

        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("userid", "stage", "apprv_id", "position_id","options");
        $confGrid['data']   = array("id","pid","userid","stage","apprv_id","position_id","options");
        $confGrid['id']     = "id";
        $confGrid['tabname']= "detail";
        $confGrid['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl            = $gen->createGridDtl($confGrid,$menuProp,$sys);
		
		$modal 				    = array(); 
		$modal['header'] 	    = "Edit Data";
		$modal['type'] 		    = "edit_detail";
		$modal['tabname'] 	    = "detail";
		$modal['input_size']    = "col-sm-6";
		$tabname			    = $modal['tabname'];
		$modal['set_userid']	= Form::select('setpost_userid',selectUser(),'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>''.$tabname.'userid']);
        $modal['set_stage']     = Form::text('setpost_stage','', ['class' => 'form-control','id'=>''.$tabname.'stage']);
        $approve				= DB::table($mst_apprv)->pluck('description','id');
		$approve->prepend('--Select One--');
        $modal['set_apprv_id']	= Form::select('setpost_apprv_id',$approve,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>''.$tabname.'apprv_id']);
        $positionDtl		    = DB::table($mst_position)->pluck('name','id');
		$positionDtl->prepend('--Select One--');
        $modal['set_position_id'] = Form::select('setpost_position_id',$positionDtl,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>''.$tabname.'position_id']);
        $option		    = array("" => "--Select One--","AND" => "AND", "OR" => "OR");
        $modal['set_options'] = Form::select('setpost_options',$option,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>''.$tabname.'options']);

        
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
            $edit 	= $gen->editRowData($trs_sign_staging,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $trs_sign_staging 		= config('tables.trs_sign_staging');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($trs_sign_staging,'userid',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}
	
	function sysAddDtl($sys,$aryCnt){ 
        $trs_sign_staging_dtl 	= config('tables.trs_sign_staging_dtl');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$data				= $request->input();
		$data['setpost_id']	= erpUniqueId(9);
        $add 				= $gen->addRowData($trs_sign_staging_dtl,$data);
		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url);
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		$trs_sign_staging_dtl 	= config('tables.trs_sign_staging_dtl');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$id 	= $request->input('setunique_id');
		$data	= $request->input();
        $edit 	= $gen->editRowData($trs_sign_staging_dtl,'id',$id,$data);
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url);
	}
	
	function sysDelDtl($sys,$aryCnt){ 
		$trs_sign_staging_dtl 	= config('tables.trs_sign_staging_dtl');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 	= $gen->getId();
		$delete = $gen->deleteRowData($trs_sign_staging_dtl,'id',$id);
		$parent = $gen->getParent($trs_sign_staging_dtl,'id',$id,'pid');
		
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