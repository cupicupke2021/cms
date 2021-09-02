<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $table_group = config('tables.table_group');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
        $confGrid['table']  = "select a.* from $table_group a";
        $confGrid['list']   = array("id","groupname");
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
        $table_group = config('tables.table_group');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		$form['set_groupname']	 = Form::text('setpost_groupname','',['class' => 'form-control','required'=>'required','id'=>'groupname']);

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
            $IDexist = DB::table($table_group)->select('groupname')->where('groupname',$data['setpost_groupname'])->first();
            $IDexist = json_decode(json_encode($IDexist), true);
            if(!empty($IDexist)){
                $url  	=  url('/').'/system/'.$sys.'/add';
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'Groupname '.$IDexist['groupname'].' Is Exists. Please Change. !!']);
            }

			$data['setpost_id']	= erpUniqueId(8);
            $add 	= $gen->addRowData($table_group,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
            
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_group = config('tables.table_group');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($table_group,$id,'id');
		

		$form                    = array();
        $form['input_size']      = "col-sm-6";
		$form['set_groupname']	 = Form::text('setpost_groupname',$data['groupname'],['class' => 'form-control','required'=>'required','id'=>'groupname']);

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

            $IDexist = DB::table($table_group)->select('groupname')->where('groupname',$data['setpost_groupname'])->first();
            $IDexist = json_decode(json_encode($IDexist), true);
            if(!empty($IDexist)){
                $url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'Groupname '.$IDexist['groupname'].' Is Exists. Please Change. !!']);
            }

            $edit 	= $gen->editRowData($table_group,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $table_group = config('tables.table_group');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($table_group,'id',$id);
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