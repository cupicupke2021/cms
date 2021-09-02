<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $ucp_module_sync = config('tables.ucp_module_sync');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
        $confGrid['table']  = "select a.* from $ucp_module_sync a";
        $confGrid['list']   = array("id","module","core","config","name","date","description");
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
        $ucp_module_sync = config('tables.ucp_module_sync');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		$parent						= selectParent();
		$form['set_module']			= Form::select('setpost_module',$parent, '',['class' => 'form-control dropdown_box1','id'=>'target']);
		$form['set_core']			= Form::text('setpost_core','',['class' => 'form-control','id'=>'core']);
		$form['set_config']			= Form::text('setpost_config','',['class' => 'form-control','id'=>'config']);
		$form['set_description']	= Form::text('setpost_description','',['class' => 'form-control','id'=>'description']);
		$form['set_your_name']		= Form::text('setpost_name','',['class' => 'form-control','id'=>'name']);
	
        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$data					= $request->input();
			$data['setpost_id']		= erpUniqueId(9);
			$data['setpost_date']	= date("Y-m-d H:i:s");
            $add 				= $gen->addRowData($ucp_module_sync,$data);
			
			//$commandm  = "scp -r /home/dev/htdocs/app/Systems/".$data['setpost_module']." dev@192.168.11.16:~dev/htdocs/app/Systems/";
			$commandm  = "ls -l";
			$commandc  = "scp -r /home/dev/htdocs/app/Http/Controllers/".$data['setpost_core']." dev@192.168.11.16:~dev/htdocs/app/Http/Controllers/";
			$commandf  = "scp -r /home/dev/htdocs/config/".$data['setpost_config']." dev@192.168.11.16:~dev/htdocs/config/";
			

			echo '<pre>';
			$last_line = system($commandm, $retvalm);
			echo '</pre>';
			
			echo '<pre>';
			$last_line = system($commandc, $retvalc);
			echo '</pre>';
			
			echo '<pre>';
			$last_line = system($commandc, $retvalf);
			echo '</pre>';
			
          
			//$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			//return redirect()->intended($url);

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
		
		$parent						= selectParent();
		$form['set_parent']			= Form::select('setpost_parent',$parent,$data['parent'],['class' => 'form-control dropdown_box1','required'=>'required','id'=>'parent']);
		
		$form['set_order']			= Form::number('setpost_urutan',$data['urutan'],['class' => 'form-control','required'=>'required','id'=>'order']);
		
		
		$form['set_docnumed']		= Form::checkbox('setpost_docnum','true',$data['docnum']);
		$form['set_posting']		= Form::checkbox('setpost_posting','true',$data['posting']);
		$form['set_docnum']			= Form::checkbox('setpost_journ_set','true',$data['journ_set']);
		
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
            $edit 	= $gen->editRowData($table_sitemenu,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url);

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