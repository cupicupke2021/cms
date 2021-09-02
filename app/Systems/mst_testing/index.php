<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $mst_units = config('tables.mst_units');

        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];

        // generate DBgrid
        $confGrid['table']  = "SELECT a.*, a.id as id_unit FROM $mst_units a ORDER BY rec_date DESC";
        $confGrid['list']   = array("id","id_unit","symbol");
        $confGrid['id']     = "id";
        $confGrid['link']   = url('/').'/system/'.$sys.'/List/parent';
        $grid               = $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);

        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        // view::addLocation(app_path('Systems/'.$sys));
        $aryCont = array("grid" => $grid);
        $view   .=View::make('flist')->with('aryCont',$aryCont);
        // wvd($aryCont);exit;

        return $view;
    }
	
	function sysAdd($sys,$aryCnt){
        $mst_units  = config('tables.mst_units');

        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $form                   = array();
        $form['input_size']     = "col-md-6";

        $form['set_id']         = Form::text('setpost_id','', ['class' => 'form-control','required' => 'required','id' => 'idUnit']);
        $form['set_symbol']    = Form::text('setpost_symbol','',['class' => 'form-control','required' => 'required', 'id' => 'symbol']);

        $form['seth_id']        = Form::text('setuniq_id','',['class' => 'form-control hidden']);
        $genForm                = $gen->createStdForm($form);

        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"sys"=>$sys,"subsys"=>$subsys);
        $view   .=View::make('finput')->with('aryCont',$aryCont);

        $subsyspart  = $request->input("subsyspart");
        if (isset($subsyspart)) {
            
            $data       = $request->input();
            $id         = $request->input('setpost_id');
            $IDexist    = DB::table($mst_units)->select('id')->where('id',$id)->first();
            $IDexist    = json_decode(json_encode($IDexist),true);
            if (!empty($IDexist)) {
                $url    = url('/').'/system/'.$sys.'/add';
                return redirect()->intended($url)
                                ->withInput($request->input())
                                ->with(['error' => 'ID '.$IDexist['id']. ' Is Exists. Please Change. !!']);
            }

            $add    = $gen->addRowData($mst_units,$data);

            $url    = url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
            return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
        }
        // wvd($subsyspart);exit;
        return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $mst_units  = config('tables.mst_units');
        $mst_units_dtl  = config('tables.mst_units_dtl');

        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $id = $gen->getId();
        $data = $gen->getRowData($mst_units,$id,'id');

        $grid       = "";
        $form       = array();
        $form['input_size']       = "col-xs-6";

        $form['set_id']    	        = Form::text('setpost_id',$data['id'], ['class' => 'form-control','readonly' => true,'required'=>'required']);
        $form['set_symbol'] = Form::text('setpost_symbol',$data['symbol'],['class' => 'form-control','required' => 'required']);

        $form['seth_id']        = Form::text('setunique_id',$data['id'],['class' => 'form-control hidden']);
        $genForm                = $gen->createStdForm($form);

        $sSQL = "SELECT * FROM $mst_units_dtl WHERE pid = '$id'";

        $confGrid['table']  = $sSQL;
        $confGrid['list']   = array("unit","value");
        $confGrid['data']   = array("unit","value");
        $confGrid['id']     = "id";
        $confGrid['tabname']     = "detail";
        $confGrid['link']     = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);

        $gridDtl    = $gen->createGridDtl($confGrid,$menuProp,$sys);

        $modal      = array();
        $modal['header']    = "Edit Data";
        $modal['type']      = "edit_detail";
        $modal['tabname']   = "detail";
        $tabname            = $modal['tabname'];
        $unit               = array();

        $unit               = DB::table($mst_units)->pluck('symbol','id')->prepend('--Select One--','')->toArray();
        $modal['set_unit']  = Form::select('setpost_unit',$unit,'',['class' => 'form-select chosen-select','required' => 'required', 'id' => ''.$tabname.'unit']);
        $modal['set_value'] = Form::number('setpost_value','',['class' => 'form-control','required' => 'required','id' => ''.$tabname.'value']);

        $modal['seth_pid']  = Form::text('setpost_pid',$id,['class' => 'form-control hidden','id' => ''.$tabname.'pid']);

        $view = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"gridDtl"=>$gridDtl,"sys"=>$sys,"subsys"=>$subsys);
        $view .= View::make('finput')->with('aryCont',$aryCont);

        $genModal   = $gen->generateModal($modal);
        view()->addLocation(app_path('System/'.$sys));
        $aryCont    = array("genModal"=>$genModal);
        $view       .= View::make('fmodal')->with('aryCont',$aryCont);

        $subsyspart = $request->input("subsyspart");
        if (isset($subsyspart)) {
            $id     = $request->input('setunique_id');
            $data   = $request->input();

            $edit   = $gen->editRowData($mst_units,'id',$id,$data);

            $datadtl    = array();
            $datadtl['setpost_id']  = $data['setpost_id'];
            $datadtl['setunique_id']  = $data['setpost_id'];

            $edits  = $gen->editRowDataPid($mst_units_dtl,'pid',$id,$datadtl);

            $url    = url('/').'/system/'.$sys.'/'.$subsys.'/'/base64url_encode($data['setpost_id']);
            return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);
        }
        return $view;
    }
	
    function sysDelete($sys,$aryCnt){
        $mst_units      = config('tables.mst_units' );
        $mst_units_dtl  = config('tables.mst_units_dtl');
        
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $id         = $gen->getId();
        $useData    = DB::table($mst_units_dtl)->where('unit',$id)->first();
        $useData    = json_decode(json_encode($useData),true);
        if (!empty($useData)) {
            $url    = url('/').'/system/'.$sys.'/List';
            return redirect()->intended($url)->withInput($request->input())->with(['error' => 'Cannot Delet Unit '.$useData['unit'].' Because Is Use In Detail.']);
        }

        $deleteHeader     = $gen->deleteRowData($mst_units,'id',$id);
        $deleteDetail     = $gen->deleteRowData($mst_units_dtl,'pid',$id);

        $url    = url('/').'/system/'.$sys.'/List';
        return redirect()->intended($url);
        
	}

    function sysAddDtl($sys,$aryCnt){
        
        $mst_units_dtl  = config('tables.mst_units_dtl');
        
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $data       = $request->input();
        $data['setpost_id'] = erpUniqueId(9);
        $add                = $gen->addRowData($mst_units_dtl,$data);
		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
        return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA DETAIL.']);
	}
	
	function sysEditDtl($sys,$aryCnt){  
        $mst_units_dtl  = config('tables.mst_units_dtl');
        
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $id         = $request->input('setunique_id');
        $data       = $request->input();
        $edit       = $gen->editRowData($mst_units_dtl,'id',$id,$data);

		$url  				=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
        return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA DETAIL.']);

	}
	
	function sysDelDtl($sys,$aryCnt){ 
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