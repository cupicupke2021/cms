<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
	
	include_once 'entity.php';
	
    function sysList($sys,$aryCnt){
        $table_sitemenu 	= config('tables.table_sitemenu');
        $mst_docnum 		= config('tables.mst_docnum');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
		
		$sSQL = "select a.*,b.description
				 from $mst_docnum a,$table_sitemenu	b
				 where 1=1 
				 and a.id_menu = b.id
				";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("id","type","start_num","description");
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
        $mst_docnum 	= config('tables.mst_docnum');
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
		
		$type					 = array(1=>"AutoNumber",2=>"Template");
		$form['set_type']	 	 = Form::select('setpost_type',$type,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'type']);
		$menu					 = selectMenu();
		$form['set_menu']		 = Form::select('setpost_id_menu',$menu,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'menu']);
		$form['set_start_num']	 = Form::number('setpost_start_num', '', ['class' => 'form-control']);
		
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
            $add 				= $gen->addRowData($mst_docnum,$data);
          
			$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY ADD DATA","success");

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $mst_docnum 		= config('tables.mst_docnum');
        $mst_docnum_dtl 	= config('tables.mst_docnum_dtl');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($mst_docnum,$id,'id');
		
		$form                   = array();
        $form['input_size']     = "col-sm-6";
		
		$type					 = array(1=>"AutoNumber",2=>"Template");
		$form['set_type']	 	 = Form::select('setpost_type',$type,$data['type'],['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'type']);
		$menu					 = selectMenu();
		$form['set_menu']		 = Form::select('setpost_id_menu',$menu,$data['id_menu'],['class' => 'form-control dropdown_box1 chosen-select','data-live-search'=>'true','required'=>'required','id'=>'menu']);
		$form['set_start_num']	 = Form::number('setpost_start_num',$data['start_num'], ['class' => 'form-control']);
        $form['seth_id']    	 = Form::text('setunique_id',$data['id'], ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);
		
		$sSQL = "
					 select a.*
					 from $mst_docnum_dtl a
					 where 1=1 
					 and a.pid= '$id'
				";
		
        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("row_order","col_type","format");
        $confGrid['data']   = array("id","pid","row_order","col_type","format");
        $confGrid['id']     = "id";
        $confGrid['tabname']= "detail";
        $confGrid['link']   = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl            = $gen->createGridDtl($confGrid,$menuProp,$sys);
		
		$modal 				= array(); 
		$modal['header'] 	= "Edit Data";
		$modal['type'] 		= "edit_detail";
		$modal['tabname'] 	= "detail";
		$tabname			= $modal['tabname'];
		
		$type					= array("date"=>"date","month"=>"month","year"=>"year","number"=>"number","text"=>"text","divider"=>"divider","comp_id"=>"company_id");
		
		$modal['set_col_type']	= Form::select('setpost_col_type',$type,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>''.$tabname.'col_type']);
		$modal['set_format']  	= Form::text('setpost_format','', ['class' => 'form-control','id'=>''.$tabname.'format']);
		$modal['set_order']  	= Form::text('setpost_row_order','', ['class' => 'form-control','id'=>''.$tabname.'row_order']);
		
		$modal['seth_pid']  	= Form::text('setpost_pid',$id, ['class' => 'form-control hidden','id'=>''.$tabname.'pid']);
        
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
            $edit 	= $gen->editRowData($mst_docnum,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
            erpRedirect($url,"toast","SUCCESSFULLY UPDATE DATA","success");


        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
          $mst_docnum 		= config('tables.mst_docnum');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$delete 	= $gen->deleteRowData($mst_docnum,'id',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}
	
	function sysAddDtl($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
		 $mst_docnum_dtl 	= config('tables.mst_docnum_dtl');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
			
		$data				= $request->input();
		$data['setpost_id']	= erpUniqueId(9);
       
		$add 				= $gen->addRowData($mst_docnum_dtl,$data);
		
		
		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		// return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA DETAIL.']);
        
        erpRedirect($url,"toast","SUCCESSFULLY ADD DATA DETAIL","success");
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		 $mst_docnum_dtl 	= config('tables.mst_docnum_dtl');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$data				= $request->input();
		$id 	= $request->input('setunique_id');
        $edit 	= $gen->editRowData($mst_docnum_dtl,'id',$id,$data);
		
		$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
		return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA DETAIL.']);
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
		//return redirect()->intended($url);
	}

    function sysOther($sys,$aryCnt){ 
		$table_sitemenu_prop = config('tables.table_sitemenu_prop');
        $mst_docnum_count = config('tables.mst_docnum_count');
        //base variable 
        $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode           = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp       = $aryCnt['menuProp'];
        $gen            = $aryCnt['gen'];
        $ssp            = $aryCnt['ssp'];
        $request        = $aryCnt['request'];
        $userPropMain   = $aryCnt['userPropMain'];
        $comp_id        = $userPropMain['comp_id']; 		
        $sysProp        = $request->input('sys'); 

        $sSQL = "select a.*,a.id as docid,b.name,c.*
                from mst_docnum a,ucp_site_menu b,mst_docnum_dtl c
                where 1=1 
                and a.id_menu = b.id
                and a.id = c.pid 
                and b.name = '$sysProp'
                order by c.row_order asc
        "; 
        $results 		= DB::select(DB::raw($sSQL));
        $data 	    = json_decode(json_encode($results), true);
        // wvd($data);exit;
        //generate menu
        $docnum = "";		
        if (!empty($data)) {
            foreach($data as $k => $v){
                $docid 	= $v['id'];
                switch($v['col_type']){
                    case "date":
                        $format = $v['format'];
                        $docnum .= date($format);
                    break;
                    case "text":
                        $format = $v['format'];
                        $docnum .= $format;
                    break;
                    case "divider":
                        $format = $v['format'];
                        $docnum .= $format;
                    break;
                    case "number":
						$sSQLC = " 
								select a.lastnum,a.is_use 
								from 
								$mst_docnum_count a, 
								(
									select max(b.lastnum) lastnum
									from $mst_docnum_count b
									where 1=1
									and b.sys 	= '$sysProp'
								) b
								where 1=1 
								and a.lastnum = b.lastnum
                                and a.sys 	= '$sysProp'
								";
						
                        $queryC 		= DB::select(DB::raw($sSQLC));
                        $resultC 	    = json_decode(json_encode($queryC), true);
						// wvd($resultC);exit;
						if(!empty($resultC)){
                        // wvd($resultC);exit;
                            if($resultC[0]['is_use'] === "true" OR $resultC[0]['is_use'] === "self") {
                                $nums = $resultC[0]['lastnum'];
                                $num  = $nums+1;
                            } else {
                                $num = $resultC[0]['lastnum'];
                                $insert = 0;
                            }
						}else{ 
							$num = 1;
						}
                        $docnum .= $num;
                    break;
                    case "comp_id":
                        $docnum .= $comp_id;
                    break;

                }
            }
        }else{
            $docnum .= "0x00";
            $num 	= 0;
        }
        
        header('Content-Type: application/json; charset=utf-8', true,200);
        $aryDoc = array("docnum"=>$docnum,"lastnum"=>$num);
        $JSON = array_map('utf8_encode', $aryDoc);
        // wvd($aryDoc);exit;
        $dataExist = DB::table($mst_docnum_count)->select('docnum')
                        ->where('sys', $sysProp)
                        ->where('lastnum', $num)
                        ->first();
        // wvd($num);
        $dataExist = json_decode(json_encode($dataExist), true);
        if(empty($dataExist['docnum'])) {
            $docnumCount['setpost_id']	    = erpUniqueId(9);
            $docnumCount['setpost_docnum']	= $aryDoc['docnum'];
            $docnumCount['setpost_lastnum']	= $aryDoc['lastnum'];
            $docnumCount['setpost_sys']	    = $sysProp;
            
            $adds 		    				= $gen->addRowData($mst_docnum_count,$docnumCount);
        }
        
        echo json_encode($JSON,true);
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
        case "other":
            return sysOther($sys,$aryCnt);
        break;
    }
}