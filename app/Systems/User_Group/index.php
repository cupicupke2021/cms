<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $table_usergroup = config('tables.table_usergroup');
        $table_user 	 = config('tables.table_user');
        $table_group 	 = config('tables.table_group');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		$sSQL 			 	= "select a.id,b.userid,b.username,c.groupname,c.id as id_group
							   from $table_usergroup a,$table_user b,$table_group c
							   where 1=1 
							   and a.userid = b.userid 
							   and a.id_group = c.id 
							  ";

        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","userid","username","groupname","id_group");
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
        $table_usergroup = config('tables.table_usergroup');
        $table_user 	 = config('tables.table_user');
        $table_group 	 = config('tables.table_group');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		$user					= selectUser();
		$group					= selectGroup();
		
		$form['set_userid']		= Form::select('setpost_userid',$user,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'userid']);
		$form['set_group']		= Form::select('setpost_id_group',$group,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'id_group']);
		$form['set_main']		= Form::checkbox('setpost_main','1');
	
        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$data	= $request->input();
			$data['setpost_id']	= erpUniqueId(8);
            $add 	= $gen->addRowData($table_usergroup,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			// return redirect()->intended($url);
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_usergroup = config('tables.table_usergroup');
        $table_user 	 = config('tables.table_user');
        $table_group 	 = config('tables.table_group');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($table_usergroup,$id,'id');
		

		$form                   = array();
        $form['input_size']     = "col-sm-6";
		$user					= selectUser();
		$group					= selectGroup();
		
		$dataMain				= ($data['main']==1?true:"");
		
		$form['set_userid']		= Form::select('setpost_userid',$user,$data['userid'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'userid']);
		$form['set_group']		= Form::select('setpost_id_group',$group,$data['id_group'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'id_group']);
		$form['set_main']		= Form::checkbox('setpost_main','1',$dataMain);
	
        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

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
			
            $edit 	= $gen->editRowData($table_usergroup,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $table_usergroup = config('tables.table_usergroup');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($table_usergroup,'id',$id);
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