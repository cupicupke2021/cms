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
		
		$sSQL 		= $sSQL = "select a.id,c.groupname,b.name,b.description
							 from $table_groupmenu a,$table_sitemenu b,$table_group c
							 where 1=1 
							 and a.id_group = c.id_group 
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
		$form['set_groupname']	 = Form::select('setpost_id_group',$group,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'groupname']);
		
		$menu					 = selectMenu();
		$form['set_menu']		 = Form::select('setpost_id_menu',$menu,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
	
		
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
			return redirect()->intended($url);

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $table_site_prop 		= config('tables.table_site_prop');
		
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
	
		$sSQL 				= "select * from $table_site_prop
							   where 1=1
							  "; 
				 
		$results 	= DB::select(DB::raw($sSQL));
			if(!empty($results)){ 
				$data 	    = json_decode(json_encode($results[0]), true);
			}else{ 
				$data 		= array();
		}

		$currency				= selectCurr();
		$exrp					= array("D"=>"Direct","I"=>"Indirect");
		
		
		
		$form                   = array();
        $form['input_size']     = "col-sm-6";
        $form['set_sys_name']    	= Form::text('setpost_sys_name', $data['sys_name'], ['class' => 'form-control']);
        $form['set_company']    	= Form::text('setpost_company', $data['company'], ['class' => 'form-control']);
        $form['set_address']    	= Form::text('setpost_address', $data['address'], ['class' => 'form-control']);
        $form['set_contact_person'] = Form::text('setpost_contact_person', $data['contact_person'], ['class' => 'form-control']);
        $form['set_admin_email'] 	= Form::text('setpost_administrator', $data['administrator'], ['class' => 'form-control']);
        
		$form['seth_diva']			= '<hr size="1"/>';
		$form['set_decimal_places'] 		= Form::number('setpost_decimal_places', $data['decimal_places'], ['class' => 'form-control']);
		$form['set_reporting_currency']		= Form::select('setpost_currency',$currency,$data['currency'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
		$form['set_2nd_reporting_currency']	= Form::select('setpost_2nd_currency',$currency,$data['2nd_currency'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
		$form['set_exchange_rate_posting']	= Form::select('setpost_ex_rat_post',$exrp,$data['ex_rat_post'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
		$form['set_exchange_rate_period']	= Form::select('setpost_ex_rate_period',array("DAILY"=>"DAILY","MONTHLY"=>"MONTHLY"),$data['ex_rate_period'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
	
        
		
		$form['set_theme'] 					= Form::text('setpost_theme', $data['theme'], ['class' => 'form-control']);
		$form['seth_id']    	 			= Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
		
		$genForm                = $gen->createStdForm($form);
		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			//$request->request->add(["foo"=>"bar"]);
			$id 	= $request->input('setunique_id');
			$data	= $request->input();
            $edit 	= $gen->editRowData($table_site_prop,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url);

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
		$delete 	= $gen->deleteRowData($table_groupmenu,'userid',$id);
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
		$data['setpost_id']	= erpUniqueId(9);
        $add 				= $gen->addRowData($table_sitemenu_prop,$data);
		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id_groupmenu']);
		return redirect()->intended($url);
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
		return redirect()->intended($url);
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
		$delete = $gen->deleteRowData($table_sitemenu_prop,'id',$id);
		$parent = $gen->getParent($table_sitemenu_prop,'id',$id,'id_groupmenu');
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($parent);
		//return redirect()->intended($url);
	}

    switch($subsys){
        case "List":
            return sysEdit($sys,$aryCnt);
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