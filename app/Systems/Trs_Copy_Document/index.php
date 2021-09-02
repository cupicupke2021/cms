<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $trs_doc_copy = config('tables.trs_doc_copy');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        
        $confGrid['table']  = "SELECT a.*
							   FROM $trs_doc_copy a";
        $confGrid['list']   = array("id","date","module","trs_docnum", "to_module","to_trs_docnum","rec_date");
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
        $trs_doc_copy   = config('tables.trs_doc_copy');
        $table_sitemenu = config('tables.table_sitemenu');
        $trs_external   = config('tables.trs_external');
        $trs_external_dtl = config('tables.trs_external_dtl');
        //base variable 
		
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
        
        $mode       = route::input('mode');

        $paramSys = $request->get('sys');
        $paramType = $request->get('type');
        $form['seth_to_module']    	= Form::text('setpost_to_module', $paramSys, ['class' => 'form-control hidden', 'id' => 'toModule']);
        $form['seth_type']    	    = Form::text('setpost_type', $paramType, ['class' => 'form-control hidden', 'id' => 'type']);    
       
        $menu                       = DB::table($trs_external)->select('module')->distinct()->pluck('module','module')->prepend('--Select One--', '')->toArray();
		$form['set_module']		    = Form::select('setpost_module',$menu,'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'module']);
        $department				    = selectDept();
		$form['set_document_number'] = Form::select('setpost_trs_id',array('' => '--Select One--'),'',['class' => 'form-control dropdown_box1 chosen-select','required'=>'required','id'=>'trsID']);
        $form['seth_docnum']    	 = Form::text('setpost_trs_docnum', '', ['class' => 'form-control hidden', 'id' => 'trsDocnum']);

        $form['seth_id']    	    = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                    = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"sys"=>$sys,"subsys"=>$subsys);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
        $subsyspart  = $request->input("subsyspart");

        
		if($mode == 'headless'){
			$modul	= $request->input('setpost_module');
            $sSQL = "select a.id, a.module, a.docnum from $trs_external a where 1=1 and a.module = '$modul'";
            $results = DB::select(DB::raw($sSQL));
            if(!empty($results)){
                $data = json_decode(json_encode($results), true);
                echo json_encode($data);
            }else{ 
                echo json_encode('empty');
            }
            exit;
		}

        // wvd($request->input());exit;
        if(isset($subsyspart)){
			$dataCopy	= $request->input();
            // wvd($dataCopy);exit;
            $id         = erpUniqueId(9);
            $modul      = $dataCopy['setpost_module'];
            $docnum     = $dataCopy['setpost_trs_docnum'];

            $sSQL       = DB::table($trs_external)
                            ->where('module', $modul)
                            ->where('docnum', $docnum)
                            ->first();
            $data       = json_decode(json_encode($sSQL), true);
            if(!empty($data)) {
                $dataID = $data['id'];
                $sSQLDtl       = DB::table($trs_external_dtl)->where('pid', $dataID)->get();
                $dataDtl       = json_decode(json_encode($sSQLDtl), true);
                // wvd($dataDtl);exit;
                if(!empty($dataDtl)) {
                    // ID diubah menjadi id tujuan
                    $data['id']             = $id;
                    $data['module']         = $dataCopy['setpost_to_module'];
                    $data['module_type']    = $dataCopy['setpost_type'];
                    $data['docnum']         = '';

                    $add 	= $gen->addRowDataX($trs_external,$data);

                    foreach ($dataDtl as $key => $value) {
                        $value['id']    = erpUniqueId(9);
                        $value['pid']   = $id;
                        $add2 	= $gen->addRowDataX($trs_external_dtl,$value);
                    }

                    // save data copy doc
                    $dataCopy['setpost_id']     =  erpUniqueId(9);
                    $dataCopy['setpost_date']   =  date('Y-m-d');
                    
                    $dataCopy['setpost_to_trs_id']      =  $id;
                    $dataCopy['setpost_to_trs_docnum']  =  ''; // kosong karena belum ada docnum nya.
                    unset($dataCopy['setpost_type']);
                    $add3 	= $gen->addRowData($trs_doc_copy,$dataCopy);

                    $url  	=  url('/').'/system/'.$dataCopy['setpost_to_module'].'/edit/'.base64url_encode($data['id']);
                    $urlX	  = url('/')."/public/assets/js/jquery.min.js";
                    $content  = '<script type="text/javascript" src="'.$urlX.'"></script>';
                    $content .= "<script type='text/javascript'>"; 
                    $content .= "$( document ).ready(function() {";
                    $content .= "window.opener.location.replace('".$url."');";                    
                    $content .= "window.close()";
                    $content  .= "});";
                    $content  .= "</script>";
                    parseContent($content);
			        return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
                } else {
                    $url  	=  url('/').'/system/'.$dataCopy['setpost_to_module'].'/add';
                    $urlX	  = url('/')."/public/assets/js/jquery.min.js";
                    $content  = '<script type="text/javascript" src="'.$urlX.'"></script>';
                    $content .= "<script type='text/javascript'>"; 
                    $content .= "$( document ).ready(function() {";
                    $content .= "window.opener.location.reload(true);";                    
                    $content .= "window.close()";
                    $content  .= "});";
                    $content  .= "</script>";
                    parseContent($content);
                    return redirect()->intended($url)->with(['danger' => 'DATA DETAIL DOCUMENT IS EMPTY!!']);
                }
            }
        }
		return $view;
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