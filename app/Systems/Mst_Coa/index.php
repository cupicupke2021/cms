<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $mst_coa = config('tables.mst_coa');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        
        $confGrid['table']  = "SELECT a.id,a.code,a.name,a.currency,b.name as parent, a.coa_grp_name FROM mst_coa a
                                LEFT JOIN mst_coa b ON a.parent = b.code";
        $confGrid['list']   = array("id", "code", "name", "currency","parent","coa_grp_name");
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
        $mst_coa        = config('tables.mst_coa');
        $mst_coa_grp    = config('tables.mst_coa_grp');
        
        //base variable 
		
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
        
        $form['set_code']    	    = Form::text('setpost_code','', ['class' => 'form-control','required'=>'required','id' => 'code']);
        $form['set_name']    	    = Form::text('setpost_name','', ['class' => 'form-control','required'=>'required']);
		$form['set_currency']		= Form::select('setpost_currency',selectCurrency(),'',['class' => 'chosen-select','required'=>'required','id'=>'currency']);
        $parent                     = DB::table($mst_coa)->pluck('name','code')->prepend('--Select One--', '')->toArray();
		$form['set_parent']		    = Form::select('setpost_parent',$parent,'',['class' => 'chosen-select','required'=>'required','id'=>'coaGroup']);
        
        $coaGroup                   = DB::table($mst_coa_grp)->pluck('name','id')->prepend('--Select One--', '')->toArray();
        // wvd($coaGroup);exit;
		$form['set_coa_grp']		= Form::select('setpost_coa_grp',$coaGroup,'',['class' => 'chosen-select','required'=>'required','id'=>'coaGroup']);

        $form['seth_id']    	    = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                    = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"sys"=>$sys,"subsys"=>$subsys);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
        $subsyspart  = $request->input("subsyspart");

        if($mode == 'headless'){
			$code = $request->input('setpost_code');
            $CodeExist = DB::table($mst_coa)->select('id')->where('code',$code)->first();
            $CodeExist = json_decode(json_encode($CodeExist), true);
            $validate = 0;
            if(!empty($CodeExist)){
                $validate = 1;
            }
			
			echo json_encode($validate);
			exit;
		}
        // wvd($request->input());exit;
        if(isset($subsyspart)){
			$data					= $request->input();

            $code       = $request->input('setpost_code');
            $CodeExist  = DB::table($mst_coa)->select('code')->where('code',$code)->first();
            $CodeExist  = json_decode(json_encode($CodeExist), true);
            if(!empty($CodeExist)){
                $url  	=  url('/').'/system/'.$sys.'/add';
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'Code '.$CodeExist['code'].' Is Exists. Please Change. !!']);
            }

            // wvd($data);exit;
            $coaGrpName             = DB::table($mst_coa_grp)->select('name')->where('id',$data['setpost_coa_grp'])->first();
            $coaGrpName             = json_decode(json_encode($coaGrpName), true);

            $data['setpost_coa_grp_name']  = $coaGrpName['name'];
            $data['setpost_id']   	       = erpUniqueId(9);
            $add 	                       = $gen->addRowData($mst_coa,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);
        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $mst_coa = config('tables.mst_coa');
        $mst_coa_grp    = config('tables.mst_coa_grp');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($mst_coa,$id,'id');
	
        $grid                    = "";
        $form                    = array();
        $form['input_size']      = "col-xs-6";
        
        $form['set_code']    	    = Form::text('setpost_code',$data['code'], ['class' => 'form-control','required'=>'required','id' => 'code']);
        $form['set_name']    	    = Form::text('setpost_name',$data['name'], ['class' => 'form-control','required'=>'required']);
		$form['set_currency']		= Form::select('setpost_currency',selectCurrency(),$data['currency'],['class' => 'chosen-select','required'=>'required','id'=>'currency']);
        $parent                     = DB::table($mst_coa)->pluck('name','code')->prepend('--Select One--', '')->toArray();
		$form['set_parent']		    = Form::select('setpost_parent',$parent,$data['parent'],['class' => 'chosen-select','required'=>'required','id'=>'coaGroup']);

        $coaGroup                   = DB::table($mst_coa_grp)->pluck('name','id')->prepend('--Select One--', '')->toArray();
		$form['set_coa_grp']		= Form::select('setpost_coa_grp',$coaGroup,$data['coa_grp'],['class' => 'chosen-select','required'=>'required','id'=>'coaGroup']);
		
        $form['seth_id']    	    = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        $genForm                    = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"sys"=>$sys,"subsys"=>$subsys);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			
			$id 	= $request->input('setunique_id');
			$data	= $request->input();

            $code       = $request->input('setpost_code');
            $CodeExist  = DB::table($mst_coa)->select('code')->where('code',$code)->first();
            $CodeExist  = json_decode(json_encode($CodeExist), true);
            if(!empty($CodeExist)){
                $url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			    return redirect()->intended($url)
                        ->withInput($request->input())
                        ->with(['error' => 'Code '.$CodeExist['code'].' Is Exists. Please Change. !!']);
            }
            
            $coaGrpName             = DB::table($mst_coa_grp)->select('name')->where('id',$data['setpost_coa_grp'])->first();
            $coaGrpName             = json_decode(json_encode($coaGrpName), true);
            $data['setpost_coa_grp_name']  = $coaGrpName['name'];
            // wvd($data);exit;

            $edit 	= $gen->editRowData($mst_coa,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $mst_coa = config('tables.mst_coa');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
        $code       = DB::table($mst_coa)->select('code','name')->where('id', $id)->first();
        $code 	= json_decode(json_encode($code), true);
        $useData    = DB::table($mst_coa)->select('code')->where('parent', $code['code'])->first();
        $useData 	= json_decode(json_encode($useData), true);
        if(!empty($useData)) {
            $url  	=  url('/').'/system/'.$sys.'/List';
            return redirect()->intended($url)
                    ->withInput($request->input())
                    ->with(['error' => 'Cannot Delete Code '.$code['code'].'-'.$code['name'].' Because Is Use In Parent.']);
        }

		$delete 	= $gen->deleteRowData($mst_coa,'id',$id);
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