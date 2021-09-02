<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $table_sitemenu = config('tables.table_sitemenu');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        $confGrid['table']  = "SELECT a.*, b.name as parent_name FROM $table_sitemenu a
        LEFT JOIN $table_sitemenu b ON a.parent = b.id";
        $confGrid['list']   = array("id","name","description","parent_name","urutan","type","icon");
		$confGrid['header'] = array("id"=>"id","name"=>"name","description"=>"description","parent_name"=>"parent_name","urutan"=>"urutan","type"=>"type","icon"=>"icon");
		// wvd($confGrid);exit;
        $confGrid['table_name'] = $table_sitemenu;
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
        $table_sitemenu = config('tables.table_sitemenu');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		$form['set_name']			= Form::text('setpost_name','',['class' => 'form-control','required'=>'required','id'=>'name']);
		$form['set_description']	= Form::text('setpost_description','',['class' => 'form-control','required'=>'required','id'=>'description']);
		$form['set_type']			= Form::text('setpost_type','',['class' => 'form-control','required'=>'required','id'=>'type']);
		
		$parent						= selectParent();
		$form['set_parent']			= Form::select('setpost_parent',$parent, '',['class' => 'chosen-select','required'=>'required','id'=>'parent']);
		
		$form['set_order']			= Form::number('setpost_urutan','',['class' => 'form-control','required'=>'required','id'=>'order']);
		
		$form['set_icon']			= Form::text('setpost_icon','',['class' => 'form-control','id'=>'description']);
		$form['set_docnumed']		= Form::checkbox('setpost_docnum','true');
		$form['set_posting']		= Form::checkbox('setpost_posting','true');
		$form['set_attachment']		= Form::checkbox('setpost_attachment','true');
		$form['set_approval']		= Form::checkbox('setpost_approval','true');
		
		$form['set_filter_by_company']			= Form::checkbox('setpost_filt_comp','true');
		$form['set_filter_by_department']		= Form::checkbox('setpost_filt_dept','true');
		$form['set_filter_by_position']			= Form::checkbox('setpost_filt_position','true');
		$form['set_filter_by_user']				= Form::checkbox('setpost_filt_user','true');
		
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
            $add 	= $gen->addRowData($table_sitemenu,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_sitemenu = config('tables.table_sitemenu');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($table_sitemenu,$id,'id');
		
		$form                    	= array();
        $form['input_size']      	= "col-sm-6";
		$form['set_name']			= Form::text('setpost_name',$data['name'],['class' => 'form-control','required'=>'required','id'=>'name']);
		$form['set_description']	= Form::text('setpost_description',$data['description'],['class' => 'form-control','required'=>'required','id'=>'description']);
		$form['set_type']			= Form::text('setpost_type',$data['type'],['class' => 'form-control','required'=>'required','id'=>'type']);
		
		$parent						= selectParent();
		$form['set_parent']			= Form::select('setpost_parent',$parent,$data['parent'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'parent']);
		
		$form['set_order']			= Form::number('setpost_urutan',$data['urutan'],['class' => 'form-control','required'=>'required','id'=>'order']);
		
		$form['set_icon']			= Form::text('setpost_icon',$data['icon'],['class' => 'form-control','id'=>'description']);
		
		$form['set_docnumed']		= Form::checkbox('setpost_docnum','true',$data['docnum']);
		$form['set_posting']		= Form::checkbox('setpost_posting','true',$data['posting']);
		$form['set_attachment']		= Form::checkbox('setpost_attachment','true',$data['attachment']);
		$form['set_approval']		= Form::checkbox('setpost_approval','true',$data['approval']);
		
		$form['set_filter_by_company']			= Form::checkbox('setpost_filt_comp','true',$data['filt_comp']);
		$form['set_filter_by_department']		= Form::checkbox('setpost_filt_dept','true',$data['filt_dept']);
		$form['set_filter_by_position']			= Form::checkbox('setpost_filt_position','true',$data['filt_position']);
		$form['set_filter_by_user']				= Form::checkbox('setpost_filt_user','true',$data['filt_user']);
		
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
			
			
			$data['setpost_docnum'] 		= (isset($data['setpost_docnum'])?$data['setpost_docnum']:"");
			$data['setpost_posting'] 		= (isset($data['setpost_posting'])?$data['setpost_posting']:"");
			$data['setpost_attachment'] 	= (isset($data['setpost_attachment'])?$data['setpost_attachment']:"");
			$data['setpost_approval'] 		= (isset($data['setpost_approval'])?$data['setpost_approval']:"");
			$data['setpost_filt_comp'] 		= (isset($data['setpost_filt_comp'])?$data['setpost_filt_comp']:"");
			$data['setpost_filt_dept'] 		= (isset($data['setpost_filt_dept'])?$data['setpost_filt_dept']:"");
			$data['setpost_filt_position'] 	= (isset($data['setpost_filt_position'])?$data['setpost_filt_position']:"");
			//$data['setpost_filt_user'] 		= (isset($data['setpost_filt_position'])?$data['setpost_filt_user']:"");
			
            $edit 	= $gen->editRowData($table_sitemenu,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $table_sitemenu = config('tables.table_sitemenu');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($table_sitemenu,'id',$id);
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