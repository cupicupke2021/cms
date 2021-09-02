<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $mst_biodata = config('tables.mst_biodata');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        
        //generate DBgrid
        
        $confGrid['table']  = "SELECT * FROM $mst_biodata order by id desc";
        $confGrid['list']   = array("id","name", "address", "gender","birthday", "email", "id_number","tax_number");
        $confGrid['id']     = "id";
        $confGrid['link']   = url('/').'/system/'.$sys.'/List/parent';
        // wvd($ssp);exit;
        $grid               = $gen->createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys);

   
        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont= array("grid"=>$grid);
        $view   .=View::make('flist')->with('aryCont',$aryCont);
        

        return $view;
    }
	
	function sysAdd($sys,$aryCnt){
        $mst_biodata = config('tables.mst_biodata');
        $mst_buss_part = config('tables.mst_buss_part');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$form                    = array();
        $form['input_size']      = "col-sm-6";
        
        $form['set_name']    		= Form::text('setpost_name','',['class' => 'form-control','required'=>'required']);
        $form['set_address']   		= Form::text('setpost_address','', ['class' => 'form-control','required'=>'required']);
		$form['set_gender']		 	= Form::select('setpost_gender',getGender(),'',['class' => 'chosen-select','required'=>'required','id'=>'gender']);
        $form['set_birthday']   	= Form::date('setpost_birthday','', ['class' => 'form-control','required'=>'required']);
        $form['set_email']   		= Form::text('setpost_email','', ['class' => 'form-control','required'=>'required']);
        $form['set_id_number']   	= Form::text('setpost_id_number','', ['class' => 'form-control','required'=>'required']);
        $form['set_tax_number']  	= Form::text('setpost_tax_number','', ['class' => 'form-control','required'=>'required']);
        $company                    = DB::table($mst_buss_part)->where('type','O')->pluck('name','id')->prepend('--Select One--', '')->toArray();
		$form['set_company_start']	= Form::select('setpost_comp_id',$company,'',['class' => 'chosen-select','required'=>'required','id'=>'compID']);

        $form['seth_id']    	 = Form::text('setunique_id', '', ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        $view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm);
        $view   .=View::make('finput')->with('aryCont',$aryCont);
		
		//add
        $subsyspart  = $request->input("subsyspart");
        // wvd($request->input());exit;
        if(isset($subsyspart)){
			
			$data					= $request->input();
            $data['setpost_id']   	= erpUniqueId(9);

            $add 	= $gen->addRowData($mst_biodata,$data);
          
			$url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_id']);
			return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA.']);

        }
		
		return $view;
	}
	
    function sysEdit($sys,$aryCnt){
        $mst_biodata = config('tables.mst_biodata');
        $mst_buss_part = config('tables.mst_buss_part');
        $trs_his_position = config('tables.trs_his_position');
        $mst_position = config('tables.mst_position');
        $mst_employee_bank = config('tables.mst_employee_bank');
        $mst_bank = config('tables.mst_bank');
        $mst_employee_tax = config('tables.mst_employee_tax');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		
		$id 		= $gen->getId();
		$data		= $gen->getRowData($mst_biodata,$id,'id');
	
        $grid                    = "";
        $form                    = array();
        $form['input_size']      = "col-xs-6";
        
        $form['set_name']    		= Form::text('setpost_name',$data['name'],['class' => 'form-control','required'=>'required']);
        $form['set_address']        = Form::text('setpost_address',$data['address'], ['class' => 'form-control','required'=>'required']);
		$form['set_gender']		    = Form::select('setpost_gender',getGender(),$data['gender'],['class' => 'chosen-select','required'=>'required','id'=>'gender']);
        $form['set_birthday']       = Form::date('setpost_birthday',$data['birthday'], ['class' => 'form-control','required'=>'required']);
        $form['set_email']          = Form::text('setpost_email',$data['email'], ['class' => 'form-control','required'=>'required']);
        $form['set_id_number']      = Form::text('setpost_id_number',$data['id_number'], ['class' => 'form-control','required'=>'required']);
        $form['set_tax_number']     = Form::text('setpost_tax_number',$data['tax_number'], ['class' => 'form-control','required'=>'required']);
        $company                    = DB::table($mst_buss_part)->where('type','O')->pluck('name','id')->prepend('--Select One--', '')->toArray();
		$form['set_company_start']  = Form::select('setpost_comp_id',$company,$data['comp_id'],['class' => 'chosen-select','required'=>'required','id'=>'compID']);

        $form['seth_id']    	 = Form::text('setunique_id', $data['id'], ['class' => 'form-control hidden']);
        $genForm                 = $gen->createStdForm($form);

        // ===== GRID 1 ======
        $sSQL = "
				 select a.*
				 from $trs_his_position a
				 where a.emp_id = '$id'
				";
        $confGrid['table']          = $sSQL;
        $confGrid['list']           = array("emp_id","emp_number","userid","position_id","grade","date","end_date","active_now","comp_id","department","status","end_status");
        $confGrid['data']           = array("id","emp_id","emp_number","userid","position_id","grade","date","end_date","active_now","comp_id","department","status","end_status");
        $confGrid['id']             = "id";
        $confGrid['tabname']        = "org";
        $confGrid['link']           = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl                    = $gen->createGridDtl($confGrid,$menuProp,$sys);

		$modal 				        = array(); 
		$modal['header'] 	        = "Edit Data";
		$modal['type'] 		        = "edit_detail";
		$modal['tabname'] 	        = "org";
		$tabname			        = $modal['tabname'];

        $modal['set_emp_number']    = Form::text('setpost_emp_number','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'emp_number']);
        $modal['set_start_date']    = Form::date('setpost_date','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'date']);
        $modal['set_end_date']      = Form::date('setpost_end_date','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname.'end_date']);
        $position                   = DB::table($mst_position)->pluck('name','id')->prepend('--Select One--', '')->toArray();
		$modal['set_position']		= Form::select('setpost_position_id',$position,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'position_id']);
		$modal['set_grade']		    = Form::select('setpost_grade',getGrade(),'',['class' => 'chosen-select','required'=>'required','id'=>'grade','id'=>''.$tabname.'grade']);
        $modal['set_status']		= Form::select('setpost_status',getStatusEmp(),'',['class' => 'chosen-select','required'=>'required','id'=>'status','id'=>''.$tabname.'status']);
        $modal['set_end_status']    = Form::select('setpost_end_status',getEndStatusEmp(),'',['class' => 'chosen-select','id'=>''.$tabname.'end_status']);
        $modal['set_user']		    = Form::select('setpost_userid',selectUser(),'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'userid']);
        $modal['set_active']		= Form::select('setpost_active_now',getActive(),'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'active_now']);
        $company                    = DB::table($mst_buss_part)->where('type','O')->pluck('name','id')->prepend('--Select One--', '')->toArray();
		$modal['set_company_Start'] = Form::select('setpost_comp_id',$company,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'comp_id']);
		$modal['set_department']    = Form::select('setpost_department',selectDept(),'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname.'department']);
        $modal['seth_tabname']      = Form::text('setparam_tabname',$tabname, ['class' => 'form-control hidden','id'=>''.$tabname]);


		$modal['seth_emp_id']  = Form::text('setpost_emp_id',$id, ['class' => 'form-control hidden','id'=>''.$tabname.'emp_id']);
        

        // ===== GRID 2 ======
        $sSQL2 = "
				 select a.*
				 from $mst_employee_bank a
				 where a.emp_id = '$id'
				";
        $confGrid2['table']          = $sSQL2;
        $confGrid2['list']           = array("bank_acc_number","bank_acc_name","bank_code","bank_name","active_now");
        $confGrid2['data']           = array("id","bank_acc_number","bank_acc_name","bank_code","bank_name","active_now");
        $confGrid2['id']             = "id";
        $confGrid2['tabname']        = "bank";
        $confGrid2['link']           = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
        $gridDtl2                    = $gen->createGridDtl($confGrid2,$menuProp,$sys);

		$modal2 				     = array(); 
		$modal2['header'] 	         = "Edit Data";
		$modal2['type'] 		     = "edit_detail";
		$modal2['tabname'] 	         = "bank";
		$tabname2			         = $modal2['tabname'];

        $modal2['set_bank_acc_number']  = Form::text('setpost_bank_acc_number','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname2.'bank_acc_number']);
        $modal2['set_bank_acc_name']    = Form::text('setpost_bank_acc_name','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname2.'bank_acc_name']);
        $bankname                       = DB::table($mst_bank)->pluck('name','name')->prepend('--Select One--', '')->toArray();
        $modal2['set_bank_name']		= Form::select('setpost_bank_name',$bankname,'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname2.'bank_name']);
        $modal2['set_active']		    = Form::select('setpost_active_now',getActive(),'',['class' => 'chosen-select','required'=>'required','id'=>''.$tabname2.'active_now']);
        $modal2['seth_tabname']        = Form::text('setparam_tabname',$tabname2, ['class' => 'form-control hidden','id'=>''.$tabname2]);

		$modal2['seth_emp_id']          = Form::text('setpost_emp_id',$id, ['class' => 'form-control hidden','id'=>''.$tabname2.'emp_id']);

        // ===== GRID 3 ======
        $sSQL3 = "
            select a.*
            from $mst_employee_tax a
            where a.pid = '$id'
            ";
       $confGrid3['table']          = $sSQL3;
       $confGrid3['list']           = array("tax_number","type");
       $confGrid3['data']           = array("id","tax_number","type");
       $confGrid3['id']             = "id";
       $confGrid3['tabname']        = "tax";
       $confGrid3['link']           = url('/').'/system/'.$sys.'/edit/'.base64url_encode($id);
       $gridDtl3                    = $gen->createGridDtl($confGrid3,$menuProp,$sys);

       $modal3 				        = array(); 
       $modal3['header'] 	        = "Edit Data";
       $modal3['type'] 		        = "edit_detail";
       $modal3['tabname'] 	        = "tax";
       $tabname3			        = $modal3['tabname'];

       $modal3['set_tax_number']  = Form::text('setpost_tax_number','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname3.'tax_number']);
       $modal3['set_type']  = Form::text('setpost_type','', ['class' => 'form-control','required'=>'required','id'=>''.$tabname3.'type']);

       $modal3['seth_tabname']        = Form::text('setparam_tabname',$tabname3, ['class' => 'form-control hidden','id'=>''.$tabname3]);

       $modal3['seth_pid']             = Form::text('setpost_pid',$id, ['class' => 'form-control hidden','id'=>''.$tabname3.'pid']);


		$view    = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genform"=>$genForm,"gridDtl"=>$gridDtl,"gridDtl2"=>$gridDtl2,"gridDtl3"=>$gridDtl3);
        $view   .= View::make('finput')->with('aryCont',$aryCont);
        		
        $genModal 			= $gen->generateModal($modal,"col-sm-6","modal-md");
        $genModal2 			= $gen->generateModal($modal2);
        $genModal3 			= $gen->generateModal($modal3);
		
        view()->addLocation(app_path('Systems/'.$sys));
        $aryCont = array("genModal"=>$genModal,"genModal2"=>$genModal2,"genModal3"=>$genModal3);
        $view   .= View::make('fmodal')->with('aryCont',$aryCont);
        
        //save edit
        $subsyspart  = $request->input("subsyspart");
        if(isset($subsyspart)){
			//$request->request->add(["foo"=>"bar"]);
			$id 	= $request->input('setunique_id');
			$data	= $request->input();
			// $data['setpost_password'] = md5($request->input('setpost_password'));
            $edit 	= $gen->editRowData($mst_biodata,'id',$id,$data);
            
			$url  	=  url('/').'/system/'.$sys.'/'.$subsys.'/'.base64url_encode($id);
			return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA.']);

        }
        return $view;
    }
	
	
    function sysDelete($sys,$aryCnt){
        $mst_biodata = config('tables.mst_biodata');
        $trs_his_position = config('tables.trs_his_position');
        $mst_employee_bank = config('tables.mst_employee_bank');
        $mst_employee_tax = config('tables.mst_employee_tax');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
		$id 		= $gen->getId();
        $delete 	= $gen->deleteRowData($trs_his_position,'emp_id',$id);
        $delete 	= $gen->deleteRowData($mst_employee_bank,'emp_id',$id);
        $delete 	= $gen->deleteRowData($mst_employee_tax,'pid',$id);
		$delete 	= $gen->deleteRowData($mst_biodata,'id',$id);
		$url  	=  url('/').'/system/'.$sys.'/List';
		return redirect()->intended($url);
	
	}

    function sysAddDtl($sys,$aryCnt){ 
		$trs_his_position = config('tables.trs_his_position');
        $mst_employee_bank = config('tables.mst_employee_bank');
        $mst_bank = config('tables.mst_bank');
        $mst_employee_tax = config('tables.mst_employee_tax');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $param = $request->input('setparam_tabname');
        switch($param){
            case "org":
                $data	    = $request->input();
                // // validasi
                // $exist    = DB::table($trs_his_position)->select('id','emp_number')->where('emp_number',$data['setpost_emp_number'])->first();
                // $exist    = json_decode(json_encode($exist), true);
                // if(!empty($exist)){
                //     $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
                //     unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                //     return redirect()->intended($url)
                //             ->withInput($data)
                //             ->with(['error' => 'Emp Number '.$exist['emp_number'].' Is Exists. Please Change. !!']);
                // }
                
                $sdate                  = strtotime($data['setpost_date']);
                $edate                  = strtotime($data['setpost_end_date']);
                if($edate < $sdate){
                    $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
                    unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                    return redirect()->intended($url)
                            ->withInput($data)
                            ->with(['error' => 'End Date Cannot Be More Than Start Date.']);
                }

                $data['setpost_id']	= erpUniqueId(9);
                $add 				= $gen->addRowData($trs_his_position,$data);
                $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
        
            break;
            case "bank":
                $data	  = $request->input();
                $exist    = DB::table($mst_employee_bank)->select('id','bank_acc_number')->where('bank_acc_number',$data['setpost_bank_acc_number'])->first();
                $exist    = json_decode(json_encode($exist), true);
                if(!empty($exist)){
                    $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
                    unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                    return redirect()->intended($url)
                            ->withInput($data)
                            ->with(['error' => 'Bank Acc Number '.$exist['bank_acc_number'].' Is Exists. Please Change. !!']);
                }

                $dataBank                   = DB::table($mst_bank)->where('name', $data['setpost_bank_name'])->first();
                $data['setpost_bank_code']  = $dataBank->bank_code;
                $data['setpost_id']	        = erpUniqueId(9);
                $add 				        = $gen->addRowData($mst_employee_bank,$data);
                $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
        
            break;
            case "tax":
                $data     = $request->input();
                $exist    = DB::table($mst_employee_tax)->select('id','tax_number')->where('tax_number',$data['setpost_tax_number'])->first();
                $exist    = json_decode(json_encode($exist), true);
                if(!empty($exist)){
                    $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
                    unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                    return redirect()->intended($url)
                            ->withInput($data)
                            ->with(['error' => 'Tax Number '.$exist['tax_number'].' Is Exists. Please Change. !!']);
                }

                $data['setpost_id']	        = erpUniqueId(9);
                $add 				        = $gen->addRowData($mst_employee_tax,$data);
                $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
        
            break;
        }
        return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY ADDED DATA DETAIL.']);
	}
	
	function sysEditDtl($sys,$aryCnt){ 
		$trs_his_position = config('tables.trs_his_position');
        $mst_employee_bank = config('tables.mst_employee_bank');
        $mst_employee_tax = config('tables.mst_employee_tax');
        $mst_bank = config('tables.mst_bank');

        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];
		
        $param = $request->input('setparam_tabname');
        switch($param){
            case "org":
                $id 	= $request->input('setunique_id');
                $data	= $request->input();

                // $exist    = DB::table($trs_his_position)->select('id','emp_number')->where('emp_number',$data['setpost_emp_number'])->first();
                // $exist    = json_decode(json_encode($exist), true);
                // if(!empty($exist)){
                //     $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
                //     unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                //     return redirect()->intended($url)
                //             ->withInput($data)
                //             ->with(['error' => 'Emp Number '.$exist['emp_number'].' Is Exists. Please Change. !!']);
                // }
                
                $sdate                  = strtotime($data['setpost_date']);
                $edate                  = strtotime($data['setpost_end_date']);
                if($edate < $sdate){
                    $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
                    unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                    return redirect()->intended($url)
                            ->withInput($data)
                            ->with(['error' => 'End Date Cannot Be More Than Start Date.']);
                }

                $edit 	= $gen->editRowData($trs_his_position,'id',$id,$data);
                $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
            break;
            case "bank":
                $id 	                    = $request->input('setunique_id');
                $data	                    = $request->input();

                $exist    = DB::table($mst_employee_bank)->select('id','bank_acc_number')->where('bank_acc_number',$data['setpost_bank_acc_number'])->first();
                $exist    = json_decode(json_encode($exist), true);
                if(!empty($exist)){
                    $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
                    unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                    return redirect()->intended($url)
                            ->withInput($data)
                            ->with(['error' => 'Bank Acc Number '.$exist['bank_acc_number'].' Is Exists. Please Change. !!']);
                }

                $dataBank                   = DB::table($mst_bank)->where('name', $data['setpost_bank_name'])->first();
                $data['setpost_bank_code']  = $dataBank->bank_code;
                $data	                    = $request->input();
                $edit 	                    = $gen->editRowData($mst_employee_bank,'id',$id,$data);
                $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_emp_id']);
            break;
            case "tax":
                $id 	= $request->input('setunique_id');
                $data	= $request->input();

                $exist    = DB::table($mst_employee_tax)->select('id','tax_number')->where('tax_number',$data['setpost_tax_number'])->first();
                $exist    = json_decode(json_encode($exist), true);
                if(!empty($exist)){
                    $url  =  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
                    unset($data['setparam_tabname']); // agar tidak bentrok tabname nya
                    return redirect()->intended($url)
                            ->withInput($data)
                            ->with(['error' => 'Tax Number '.$exist['tax_number'].' Is Exists. Please Change. !!']);
                }

                $edit 	= $gen->editRowData($mst_employee_tax,'id',$id,$data);
                $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($data['setpost_pid']);
            break;
        }

        return redirect()->intended($url)->with(['success' => 'SUCCESSFULLY UPDATED DATA DETAIL.']);

	}
	
	function sysDelDtl($sys,$aryCnt){ 
		$trs_his_position   = config('tables.trs_his_position');
        $mst_biodata        = config('tables.mst_biodata');
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $request    = $aryCnt['request'];

        $param = $request->input('settabname');
        
        switch($param){
            case "org":
                $trs_his_position        = config('tables.trs_his_position');
                $id 	= $gen->getId();
                $data   = DB::Table($trs_his_position)->select('emp_id')->where('id', $id)->first();
                $data   = json_decode(json_encode($data), true);
                $parent = $gen->getParent($mst_biodata,'id',$data['emp_id'],'id');
                $delete = $gen->deleteRowData($trs_his_position,'id',$id);
                
                $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($parent);
            break;
            case "bank":
                $mst_employee_bank        = config('tables.mst_employee_bank');
                $id 	= $gen->getId();
                $data   = DB::Table($mst_employee_bank)->select('emp_id')->where('id', $id)->first();
                $data   = json_decode(json_encode($data), true);
                $parent = $gen->getParent($mst_biodata,'id',$data['emp_id'],'id');
                $delete = $gen->deleteRowData($mst_employee_bank,'id',$id);
                
                $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($parent);
            break;
            case "tax":
                $mst_employee_tax        = config('tables.mst_employee_tax');
                $id 	= $gen->getId();
                $data   = DB::Table($mst_employee_tax)->select('pid')->where('id', $id)->first();
                $data   = json_decode(json_encode($data), true);
                $parent = $gen->getParent($mst_biodata,'id',$data['pid'],'id');
                $delete = $gen->deleteRowData($mst_employee_tax,'id',$id);

                $url  	=  url('/').'/system/'.$sys.'/edit/'.base64url_encode($parent);
            break;
        }

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