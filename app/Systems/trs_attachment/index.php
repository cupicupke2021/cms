<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $trs_attachment = config('tables.trs_attachment');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        $id 		= $gen->getId();
		/*
        $sSQL  				= "SELECT a.* FROM $trs_attachment a
							   where 1=1 
							  and a.trs_id = '$id'
							   ";
							   
		$confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","trs_id","module_name","attachment");
		$confGrid['id']     = "id";
        $confGrid['link']   = url('/').'/system/'.$sys.'/List/parent';
		$grid               = $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);
		*/
		
		$sSQL  				= "SELECT a.* FROM $trs_attachment a
							   where 1=1 
							   and a.trs_id = '$id'
							   ";

		$confGrid['table']          = $sSQL;
        $confGrid['list']   		= array("module_name","attachment");
        $confGrid['data']   		= array("id","trs_id","module_name","attachment");
        $confGrid['id']             = "id";
        $confGrid['tabname']        = "dinvent";
        $confGrid['populate_ajax']  = true;
        $confGrid['link']           = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
		
	
		
        $grid	                    = $gen->createGridDtl($confGrid,$menuProp,$sys);
		
		
		
		
		
		
		
		/*
        $confGrid['list']   = array("id","trs_id","module_name","attachment");
        $confGrid['id']     = "id";
        $confGrid['link']   = url('/').'/system/'.$sys.'/List/parent';
        $grid               = $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);
		*/
        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont= array("grid"=>$grid);
        $view   .=View::make('flist')->with('aryCont',$aryCont);
        
        return $view;
    }
	
	function sysAdd($sys,$aryCnt){
        $trs_attachment = config('tables.trs_attachment');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$mode       = route::input('mode');
		
		
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
        /*
        $form['set_id']    	        = Form::text('setpost_id','', ['class' => 'form-control','required'=>'required']);
        $form['set_symbol']    		= Form::text('setpost_symbol','',['class' => 'form-control','required'=>'required']);
		*/
        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);
		
		
		if($mode == "headless"){ 
			$file				= $request->file('file'); 
			$data 				= $request->input();
			if($request->hasFile('file')) {
				
				   $destinationPath 	= '/home/dev/htdocs/public/files';
				   $extension 			= $request->file('file')->getClientOriginalExtension();
				   $validextensions 	= array("jpeg","jpg","png","pdf");
				   if(in_array(strtolower($extension), $validextensions)){
				   //$fileName 			= $data[''].'-'.$request->file('file')->getClientOriginalName();
				   $fileName 			= $data['setpost_module_name'].'-'.$data['setpost_trs_id'].'-'.erpUniqueId(20).'.'.$extension;
				   $request->file('file')->move($destinationPath, $fileName);
				   $data['setpost_id'] 			=  erpUniqueId(9); 
				   $data['setpost_attachment'] 	=  $fileName; 
				   $data['setpost_trs_id'] 		=  base64url_decode($data['setpost_trs_id']); 
				   $add 						= $gen->addRowData($trs_attachment,$data);
				   
				   }	
			}
		}
		
        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genForm"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        // wvd($request->input());exit;
        if(isset($subsyspart)){
			
			$data					= $request->input();
            $add 	= $gen->addRowData($trs_attachment,$data);
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			return redirect()->intended($url);
        }
		
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $trs_attachment = config('tables.trs_attachment');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($trs_attachment,$id,'id');
        $grid                    = "";
        $form                    = array();
        $form['input_size']      = "col-xs-6";
        /*
        $form['set_id']    	 = Form::text('setpost_id',$data['id'], ['class' => 'form-control','required'=>'required']);
		*/
        $form['set_Image']   = '<img src="'.url('/').'/public/files/'.$data['attachment'].'" width="400" class=""/>';
        $form['seth_id']     = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
		$genForm          	 = $gen->createStdForm($form);
		
		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genForm"=>$genForm);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
        

        return $view;
    }
	
    function sysDelete($sys,$aryCnt){
        $trs_attachment = config('tables.trs_attachment');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($trs_attachment,'id',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}

    function sysAddDtl($sys,$aryCnt){ 
		$mst_units_dtl = config('tables.mst_units_dtl');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$data				= $request->input();
		$data['setpost_id']	= erpUniqueId(9);
        $add 				= $gen->addRowData($mst_units_dtl,$data);
		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url);
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		$mst_units_dtl = config('tables.mst_units_dtl');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$id 	= $request->input('setunique_id');
		$data	= $request->input();
        $edit 	= $gen->editRowData($mst_units_dtl,'id',$id,$data);
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url);
	}
	
	function sysDelDtl($sys,$aryCnt){ 
		$mst_units_dtl = config('tables.mst_units_dtl');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 	= $gen->getId();

		$delete = $gen->deleteRowData($mst_units_dtl,'id',$id);
		$parent = $gen->getParent($mst_units_dtl,'id',$id,'pid');

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