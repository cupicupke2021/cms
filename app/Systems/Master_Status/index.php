<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $trs_gr_status = config('tables.trs_gr_status');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        
        $confGrid['table']  = "SELECT a.*, a.id id_status FROM $trs_gr_status a order by rec_date desc";
        $confGrid['list']   = array("id","id_status","retur","descr");
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
        $trs_gr_status = config('tables.trs_gr_status');
        //base variable 
		
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                   = array();
        $form['input_size']     = "col-sm-6";
        
        $form['set_id']         = Form::text('setpost_id','', ['class' => 'form-control','required'=>'required']);
        $retur				    = array('0' => '0', '1' => '1');
		$form['set_retur']		= Form::select('setpost_retur',$retur,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'retur']);
        $form['set_descr']      = Form::text('setpost_descr','', ['class' => 'form-control','required'=>'required']);

        $form['seth_id']    	= Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        // wvd($request->input());exit;
        if(isset($subsyspart)){
			
			$data	    = $request->input();
            $id         = $request->input('setpost_id');
            $IDexist    = DB::table($trs_gr_status)->select('id')->where('id',$id)->first();
            $IDexist    = json_decode(json_encode($IDexist), true);
            if(!empty($IDexist)){
                $url  	=  url('/').'/system/'.$sys.'/add';
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'ID '.$IDexist['id'].' Is Exists. Please Change. !!']);
            }
		
            $add 	= $gen->addRowData($trs_gr_status,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $trs_gr_status = config('tables.trs_gr_status');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($trs_gr_status,$id,'id');
	
        $grid                    = "";
        $form                    = array();
        $form['input_size']      = "col-xs-6";
        
        $form['set_id']         = Form::text('setpost_id',$data['id'], ['class' => 'form-control','readonly' => true,'required'=>'required']);
        $retur				    = array('0' => '0', '1' => '1');
		$form['set_retur']		= Form::select('setpost_retur',$retur,$data['retur'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'retur']);
        $form['set_descr']      = Form::text('setpost_descr',$data['descr'], ['class' => 'form-control','required'=>'required']);        
		
        $form['seth_id']    	 = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			//$request->request->add(["foo"=>"bar"]);
			$id 	= $request->input('setunique_id');
			$data	= $request->input();
			// $data['setpost_password'] = md5($request->input('setpost_password'));
            $edit 	= $gen->editRowData($trs_gr_status,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $trs_gr_status = config('tables.trs_gr_status');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($trs_gr_status,'id',$id);
        // wvd($delete);exit;
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