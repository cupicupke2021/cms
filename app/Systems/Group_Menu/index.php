<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $table_groupmenu 	= config('tables.table_groupmenu');
        $table_sitemenu 	= config('tables.table_sitemenu');
        $table_group 		= config('tables.table_group');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
		$sSQL = "select a.id,c.groupname,b.name,b.description
							 from $table_groupmenu a,$table_sitemenu b,$table_group c
							 where 1=1 
							 and a.id_group = c.id
							 and a.id_menu = b.id
							";
	
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","groupname","name","description");
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
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
		$group					 = selectGroup();
		$form['set_groupname']	 = Form::select('setpost_id_group',$group,'',['class' => 'chosen-select','required'=>'required','id'=>'groupname']);
		
		$menu					 = selectMenu();
		$form['set_menu']		 = Form::select('setpost_id_menu',$menu,'',['class' => 'chosen-select','required'=>'required','id'=>'menu']);
	
		
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
            $add 				= $gen->addRowData($table_groupmenu,$data);
          
			$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");


        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_groupmenu 	 = config('tables.table_groupmenu');
        $table_sitemenu_prop = config('tables.table_sitemenu_prop');
		
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($table_groupmenu,$id,'id');
		
		$form                   = array();
        $form['input_size']     = "col-sm-6";
		$group					= selectGroup();
		$form['set_groupname']	= Form::select('setpost_id_group',$group,$data['id_group'],['class' => 'chosen-select','required'=>'required','id'=>'groupname']);
		$menu					= selectMenu();
		$form['set_menu']		= Form::select('setpost_id_menu',$menu,$data['id_menu'],['class' => 'chosen-select','required'=>'required','id'=>'menu']);
	
        $form['seth_id']    	= Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        $genForm                = $gen->createStdForm($form);
		
		$sSQL = "
				 select a.id,a.prop,a.id_groupmenu
				 from $table_sitemenu_prop a,$table_groupmenu b
				 where 1=1 
				 and a.id_groupmenu = b.id 
				 and a.id_groupmenu = '$id'
				";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("prop");
        $confGrid['data']   = array("id","prop","id_groupmenu");
        $confGrid['id']     = "id";
        $confGrid['tabname']= "detail";
        $confGrid['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl            = $gen->createGridDtl($confGrid,$menuProp,$sys);
		
		$modal 				= array(); 
		$modal['header'] 	= "Edit Data";
		$modal['type'] 		= "edit_detail";
		$modal['tabname'] 	= "detail";
		$tabname			= $modal['tabname'];
		$modal['set_prop']	= Form::select('setpost_prop[]',genMenuProp(),'',['class' => 'chosen-select','multiple','required'=>'required','id'=>''.$tabname.'prop']);
		$modal['seth_pidd']  = Form::text('setpost_id_groupmenu',$id, ['class' => 'form-control hidden','id'=>''.$tabname.'id_groupmenu']);
        
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
            $edit 	= $gen->editRowData($table_groupmenu,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");

        }
        return $view;
    }
	
    function sysDelete($sys,$aryCnt){
        $table_groupmenu = config('tables.table_groupmenu');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($table_groupmenu,'id',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}
	
	function sysAddDtl($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$data				= $request->input();
        $prop = array();
        foreach ($data['setpost_prop'] as $key => $value) {
            $prop[$key] = $data;
            $prop[$key]['setpost_prop'] = $value;
            $prop[$key]['setpost_id']	= erpUniqueId(9);
        }
        
        foreach ($prop as $key => $values) {
            $dataExist = DB::table($table_sitemenu_prop)
                        ->select('id','prop','id_groupmenu')
                        ->where('prop','=',$values['setpost_prop'])
                        ->where('id_groupmenu','=',$values['setpost_id_groupmenu'])
                        ->first();
            $dataExist = json_decode(json_encode($dataExist), true);
            
            if(!empty($dataExist)){
                $url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id_groupmenu']);
                return redirect()->intended($url)
                        ->withInput($data)
                        ->with(['error' => 'Prop '.$values['setpost_prop'].' Is Already Exist.']);
            }

            $add  = $gen->addRowData($table_sitemenu_prop,$values);
        }
        
		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id_groupmenu']);
		// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA DETAIL.']);
        
        erpRedirect($url,"toast","SUCCESSFULLY ADD DATA DETAIL","success");
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$id 	= $request->input('setunique_id');
		$data	= $request->input();
        $edit 	= $gen->editRowData($table_sitemenu_prop,'id',$id,$data);
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id_groupmenu']);
		// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA DETAIL.']);
        erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA DETAIL","success");
	}
	
	function sysDelDtl($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 	= $gen->getId();
        
		$parent = $gen->getParent($table_sitemenu_prop,'id',$id,'id_groupmenu');
		$delete = $gen->deleteRowData($table_sitemenu_prop,'id',$id);
	
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