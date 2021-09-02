<?php
//::::::::::::::::::::::UCP_FRAMEWORK VER 1.0 for Laravel 8.0::::::::::::::::::::::::::::::::::// 

namespace App\Http\Controllers;

use         DB;
use         PDO;
use         URL;
use         Form;
use         route;
use         Illuminate\Http\Request;
use         App\Http\Controllers\Global_Perm;
use 		Session;

class Global_Generator extends Controller
{
    public function index()
    {
        return array("test1"=>"test1");
    }

	public function getId(){
		$id    	= route::input('id');
		$id 	= base64url_decode($id);
		// wvd($id);exit;
		return $id;
	}
	
	public function getRowData($table_name,$id,$column){
		// wvd($table_name);exit;
		$sSQL       = " select temp.* from 
					    $table_name temp
						where 1=1 
						and temp.$column = '$id'
						";
        $results 	= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
        $data 	    = json_decode(json_encode($results[0]), true);
	}else{ 
		$data 		= null;
	}
		return $data;
	}
	
	public function addRowData($table_name,$array){
		GLOBAL $request,$sysinfo;
		$array 				= prePost($array);
		$mst_docnum_count 	= config('tables.mst_docnum_count');
		
		$perm       = new Global_Perm;
		$sys        = route::input('sys');
        $subsys     = route::input('subsys');
        $id         = route::input('id');
        $mode       = route::input('mode');
		$sSession 	= $request->session();
		
		if($mode != 'headless'){
		$isDocnum	= $perm->checkDocPerm($sys);
		}else{
		$syss 		= $request->input('sys');
		$isDocnum	= $perm->checkDocPerm($syss);
		}
		
		$isDocnum['docnum']	= (!empty($isDocnum['docnum'])?$isDocnum['docnum']:"false");	
		if($mode != 'headless'){
			if($subsys == 'add' or $subsys == 'edit'){
				if(isset($array['data']['docnum'])){
					if($isDocnum['docnum'] == "true"){
						
						$docNum       = $array['data']['docnum'];
						$docNumExist  = DB::table($table_name)->select('docnum')->where('docnum',$docNum)->first();
						$docNumExist  = json_decode(json_encode($CodeExist), true);
						$validate = 0;
						if(!empty($docNumExist)){
							$url  	=  url('/').'/system/'.$sys.'/add';
							return redirect()->intended($url)
									->withInput($request->input())
									->with(['error' => 'Document Number '.$docNumExist['docnum'].' Is Exists. Please Change. !!']);
						}
						
						$docnum 			= $array['data']['docnum'];
						$dataC['is_use']	= "true";
						$queryE				= DB::table($mst_docnum_count)->where('docnum',$docnum)->update($dataC);
					}else {
						$docnum 			= $array['data']['docnum'];
						$dataC['is_use']	= "self";
						$queryE				= DB::table($mst_docnum_count)->where('docnum',$docnum)->update($dataC);
					}
				}
			}
		}
		$session 	= $request->session()->get('dSession');
	
		$array['data']['rec_user'] 		=  $session['sUserid'];
		$array['data']['rec_date'] 		=  date('Y-m-d H:i:s');
		$array['data']['rec_comp_id'] 	=  $session['sCompid'];
		$array['data']['rec_dept'] 		=  $session['sDepart'];
		$array['data']['rec_pos'] 		=  $session['sPosition'];
		$array['data']['rec_emp_id'] 	=  $session['sEmpNum'];
		$array['data']['rec_emp_name'] 	=  $session['sEmpName'];
		
		$query		= DB::table($table_name)->insert($array['data']);
		return $query;
	}
	
	public function addRowDataX($table_name,$array){
		GLOBAL $request;
		$session 	= $request->session()->get('dSession');
		$array['rec_user'] 		=  $session['sUserid'];
		$array['rec_date'] 		=  date('Y-m-d H:i:s');
		$array['rec_comp_id'] 	=  $session['sCompid'];
		$array['rec_dept'] 		=  $session['sDepart'];
		$array['rec_pos'] 		=  $session['sPosition'];
		$array['rec_emp_id'] 	=  $session['sEmpNum'];
		$array['rec_emp_name'] 	=  $session['sEmpName'];
		$query		= DB::table($table_name)->insert($array);
		return $query;
	}
	
	public function editRowData($table_name,$key,$id,$array){
		GLOBAL $request;
		$array 						= prePost($array);
		// wvd($array);exit;
		$session 	= $request->session()->get('dSession');
		$array['data']['mod_user'] 	=  $session['sUserid'];
		$array['data']['mod_date'] 	=  date('Y-m-d H:i:s');
		
		$query		= DB::table($table_name)->where($key,$array[$key])->update($array['data']);
		return $query;
	}
	
	public function editRowDataX($table_name,$key,$id,$array){
		GLOBAL $request; 
		$session 	= $request->session()->get('dSession');
		$array['mod_user'] 	=  $session['sUserid'];
		$array['mod_date'] 	=  date('Y-m-d H:i:s');
		// DB::enableQueryLog();
		$query		= DB::table($table_name)->where($key,$id)->update($array);
		//dd(DB::getQueryLog()); 
		return $query;
	}
	
	public function editRowDataPid($table_name,$key,$id,$array){
		$array 		= prePost($array);
		$query		= DB::table($table_name)->where($key,$id)->update($array['data']);
		return $query;
	}
	
	public function deleteRowData($table_name,$key,$id){
		GLOBAL $request,$sysinfo;

		$perm       = new Global_Perm;
		$sys        = route::input('sys');
		$sSession 	= $request->session();
		$docProp	= $perm->checkDocProp($sys,$id);
		
		
		
		
		if($docProp['posting'] == 1){ 
			
			$content = '
					<script type="text/javascript" src="'.url('/').'/public/assets/js/jquery.min.js"></script>
					<link rel="stylesheet" href="'.url('/').'/public/assets/js/sweetalert.min.css">
					<script src="'.url('/').'/public/assets/js/sweetalert.min.js"></script>
					';
			$content .= '<script>
							setTimeout(function() {
								swal({
									title: "Whoops !",
									text: "Cannot Delete, Document Already Posted!!",
									type: "error"
								}, function() {
									window.history.go(-1);
								});
							}, 500);
						</script>';
			parseContent($content);
			exit;	
		}

		$query 		= DB::table($table_name)->where($key,$id)->delete();

		return $query;
	}
	
	public function getParent($table_name,$key,$id,$column){
		$query 		= DB::table($table_name)->select($column)->where($key,$id)->first();
		$data 	    = json_decode(json_encode($query), true);
		
		return $data[$column];

	}
		
    public function createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys){
	GLOBAL $request;
		
		$posting = $menuProp['posting'];
		
		$ucp_site_header_set = config('tables.ucp_site_header_set');
		// wvd($confGrid);
		if(isset($confGrid['header'])){
		
		$session 	= $request->session()->get('dSession');
		$userid		= $session['sUserid'];
		$table_name = $confGrid['table_name'];
		$sSQL       = " select a.* from $ucp_site_header_set a
						where 1=1 
						and a.module = '$sys'
						and a.table_name = '$table_name'
						and a.userid = '$userid'
						and a.action = 'SHOW'
						order by a.ord asc
						";

        $results 	= DB::select(DB::raw($sSQL));
		if(!empty($results)){
			$rewrite = "";
			$data 	    = json_decode(json_encode($results), true);
			$rewrite .= '<thead><tr>';
			
			$rewrite .= '<th width="1%" class="no-sort"><i class="fa fa-trash-o" title="delete" /></th>';
			$rewrite .= '<th width="1%" class="no-sort"><i class="fa fa-pencil" title="edit" /></th>';
			
			//$rewrite .= '<th width="2%" class="no-sort"><i class="fa fa-trash-o"/> &nbsp;|&nbsp;<i class="fa fa-pencil"/></th>';
			
			$aryList   = array();
			foreach($data as $kiy => $koy){
			//wvd($koy); 
			$rewrite  .= '<th>'.ucwords(str_replace("_"," ",$koy['label'])).'</th>';
			$aryList[] = $koy['label'];
			$aryListReal[] = $koy['header'];
			}
			if(!empty($posting)){
			$rewrite  .= '<th>posting</th>';
			}
			$rewrite  .= '
			</tr>
			</thead>';
			
		}else{ 
			$rewrite  		= '';
			$aryList  		= $confGrid['list'];
			$aryListReal  	= $confGrid['list'];
		}
		}else{
			$rewrite  		= '';
			$aryList  		= $confGrid['list'];
			$aryListReal  	= $confGrid['list'];
		}
	
	
    if($mode   != "headless"){

		$content    = '';
		$alert    = '';
		$sessionError 	= Session::get('error');
		$sessionSuccess	= Session::get('success');
		$sessionInfo	= Session::get('info');
		$sessionWarning	= Session::get('warning');
		
		if($sessionError) {
			$alert .=	'<div class="alert alert-danger  alert-block">
							<button type="button" class="close" data-dismiss="alert">×</button>	
							<strong>'.$sessionError.'</strong>
						</div>';
		}
		if($sessionSuccess) {
			$alert .= '<div class="alert alert-success  alert-block">
							<button type="button" class="close" data-dismiss="alert">×</button>	
							<strong>'.$sessionSuccess.'</strong>
						</div>';
		}
		if($sessionInfo) {
			$alert .= '<div class="alert alert-info  alert-block">
							<button type="button" class="close" data-dismiss="alert">×</button>	
							<strong>'.$sessionInfo.'</strong>
						</div>';
		}
		if($sessionWarning) {
			$alert .= '<div class="alert alert-warning  alert-block">
							<button type="button" class="close" data-dismiss="alert">×</button>	
							<strong>'.$sessionWarning.'</strong>
						</div>';
		}

		$content .= $alert;

	if(isset($menuProp['add']) == 1){
		$add_link 	= url('/')."/system/$sys/add";
		$add_data 	=  '<button type="button" class="btn btn-info" onclick="redirected(\''.$add_link.'\')"><i class="fa fa-plus"></i>&nbsp;Add New</button>';
		$content   .= $add_data;
		$content   .= '&nbsp;&nbsp;&nbsp';
	}
	
	if(isset($confGrid['header'])){
		
	$header 	 = base64url_encode(json_encode($confGrid['header'])."_azra_".json_encode($confGrid['list']));

	$header_link = url('/')."/system/Header_Set/edit/".base64url_encode($confGrid['table_name']).'/'.$sys.'/'.$header;
	$header_set  = '<button type="button" class="btn btn-dark" onclick="popitup(\''.$header_link.'\',\'window\')"><i class="fa fa-columns"></i></button>';
	$content   	.= $header_set;
	$content   	.= '&nbsp;&nbsp;&nbsp<br><br>';
	$visibility  = "visibility:hidden";
	
	}else{
	$content   	.= '&nbsp;&nbsp;&nbsp<br><br>';
	$visibility  = "";
	}
    $content .= '<table id="parentTable" class="display table table-hover" style="width:100%">';
	if($rewrite != ''){
	$content .= $rewrite;
	}else{
	$visibility  = "visibility";
	$content .= '<thead style="'.$visibility.';border:1px solid #000"><tr >';
	
    $content .= '<th width="1%" class="no-sort"><i class="fa fa-trash-o " title="delete"/></th>';
    $content .= '<th width="1%" class="no-sort"><i class="fa fa-pencil" title="edit" /></th>';
	
    foreach($aryListReal as $val){
    $content .= '<th>'.ucwords(str_replace("_"," ",$val)).'</th>';
    };
	
	if(!empty($posting)){
	$content .= '<th>Posting</th>';
	}
	
    $content .= '</tr>';
	$content .= '</thead>';
	}
	
	$content .= '</table>';
	
    $content .= '<script>
                $(document).ready(function(){
                    $(\'#parentTable\').DataTable({
						"columnDefs": [{ className: "my_class", "targets": [ 2 ],"visible":false }],
                        "processing": true,
                        "serverSide": true,
                        "ajax": "'.$confGrid['link'].'/headless/ajax",
						stateSave: true
                    });
                } );
                </script>
                ';

    return $content;
    }else{
		
        //ajax datagrid
        $sSQL            	= $confGrid['table'];
        $primaryKey      	= $confGrid['id'];
		$confGrid['list']	= $aryListReal;
		
        $sSQLB  = "";
        $x      = 1;
        $count  = count($confGrid['list']);
        
                $sSQLB      .= "concat('$sys','@',a.$primaryKey) as ucpid,";
                $sSQLB      .= "concat('$sys','@',a.$primaryKey) as ucpide,";
                
             
        foreach($confGrid['list'] as $val){
            if($x == $count){ 
				if(!empty($posting)){
                $sSQLB      .= "a.".$val.",";
				$sSQLB      .= "b.date as posting ";
				}else{ 
				$sSQLB      .= "a.".$val;
				}
            }else{ 
                $sSQLB      .= "a.".$val.",";  
            }
				
        $x++;
        };

        $query   = ""; 
        $query  .= "select ".$sSQLB; 
        $query  .= " FROM (";
        $query  .= $sSQL;
        $query  .= ") a ";
		if(!empty($posting)){
		$query  .= "left join (select a.* from trs_posting a 
					where 1=1 
					and a.module = '$sys'
					order by a.id desc) b on a.$primaryKey = b.trs_id ";
		}
        $query  .= "where 1=1"; 
        $table = <<<EOT
(
$query
) temp
EOT;
			$columns = array();
			$dtStart	= 0;
			
			if(isset($menuProp['delete']) == 1){
            $columns[]  = array("db"=>'ucpid',
                                "dt"=>0,
                                'formatter' => function( $d, $row ) {
									
									$explode= explode("@",$row['ucpid']);
									$sys    = $explode[0];
                                    $id     = base64url_encode($explode[1]);
									$del_link  = url('/').'/system/'.$sys.'/delete/'.$id;
									return '<a class="fa fa-trash-o" title="delete" href="#" onclick="return isconfirm(\''.$del_link.'\')"></a>';
                            
                                }
                               );
			   
			}else{ 
			$columns[]  = array("db"=>'ucpid',
                                "dt"=>0,
                                'formatter' => function( $d, $row ) {
									$explode= explode("@",$row['ucpid']);
									$sys    = $explode[0];
                                    $id     = base64url_encode($explode[1]);
									$del_link  = url('/').'/system/'.$sys.'/delete/'.$id;
									return '<a class="fa fa-ban" ></a>';
                            
                                }
                               );
			}
			if(isset($menuProp['edit']) == 1){
            $columns[]  = array("db"=>'ucpide',
                                "dt"=>1,
                                'formatter' => function( $d, $row ) {
									
									$explode= explode("@",$row['ucpide']);
									$sys    = $explode[0];
                                    $id     = base64url_encode($explode[1]);
                                    return '<a class="fa fa-pencil" title="edit" href="'.url('/').'/system/'.$sys.'/edit/'.$id.'"></a>';
                                }
                               );
	        }else{ 
			$columns[]  = array("db"=>'ucpide',
                                "dt"=>1,
                                'formatter' => function( $d, $row ) {
									
									$explode= explode("@",$row['ucpide']);
									$sys    = $explode[0];
                                    $id     = base64url_encode($explode[1]);
                                    return '<a class="fa fa-ban" ></a>';
                                }
                               );
			
			}
        $no             =  2;
		$countix		= count($confGrid['list']);
		
        foreach($confGrid['list'] as $val){
		    $columns[]  = array("db"=>$val,"dt"=>$no);
        
		$no++;
        }
			if(!empty($posting)){
			$columns[]  = array("db"=>"posting","dt"=>$countix+2);
			}
		
        $host       = env('DB_HOST');
        $db         = env('DB_DATABASE');
        $username   = env('DB_USERNAME');
        $password   = env('DB_PASSWORD');
		
		$host       = "localhost";
        $db         = "dinara";
        $username   = "erp";
        $password   = "kucingbudug@2021";

        $sql_details = array(
            'user' => $username,
            'pass' => $password,
            'db'   => $db,
            'host' => $host
        );
        
        echo json_encode(
            $ssp->simple( $_GET, $sql_details, $table, $primaryKey, $columns )
        );
        exit;
    }
    }

    public function createStdForm($form,$save=null, $btnclose=null){
			GLOBAL $request;
			
			$perm       = new Global_Perm;
			$sys        = route::input('sys');
			$subsys     = route::input('subsys');
			$id         = route::input('id');
			
			$mode       = route::input('mode');
			
			$session = $request->session()->all();

			$sessionError 	= Session::get('error');
			$sessionSuccess	= Session::get('success');
			$sessionInfo	= Session::get('info');
			$sessionWarning	= Session::get('warning');
			$sessionDanger	= Session::get('danger');
			$sSession		= Session::get('dSession');
			$docProp  		= $perm->checkDocProp($sys,$id);

			$postingDoc		= $docProp['posting'];
			
            $input_size = $form['input_size'];
            $content    = "";
			$alert    	= "";
			// wvd($sys);
			
			if(isset($session['sys'])) {
				// untuk yg modul bisa panggil popup
				if($session['sys'] == $sys) {
					if($sessionError) {
						$alert .=	'<div class="alert alert-danger  alert-block">
										<button type="button" class="close" data-dismiss="alert">×</button>	
										<strong>'.$sessionError.'</strong>
									</div>
									';
					}
					if($sessionSuccess) {
						$alert .= '<div class="alert alert-success  alert-block">
										<button type="button" class="close" data-dismiss="alert">×</button>	
										<strong>'.$sessionSuccess.'</strong>
									</div>
		
									';
					}
					if($sessionInfo) {
						$alert .= '<div class="alert alert-info  alert-block">
										<button type="button" class="close" data-dismiss="alert">×</button>	
										<strong>'.$sessionInfo.'</strong>
									</div>
									';
					}
					if($sessionWarning) {
						$alert .= '<div class="alert alert-warning  alert-block">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>'.$sessionWarning.'</strong>
									</div>
									';
					}
					
					if($sessionDanger) {
						$alert .= '<div class="alert alert-danger  alert-block">
										<button type="button" class="close" data-dismiss="alert">×</button>	
										<strong>'.$sessionDanger.'</strong>
									</div>';
					}
				}
			} else {
				if($sessionError) {
					$alert .=	'<div class="alert alert-danger  alert-block">
									<button type="button" class="close" data-dismiss="alert">×</button>	
									<strong>'.$sessionError.'</strong>
								</div>
								';
				}
				if($sessionSuccess) {
					$alert .= '<div class="alert alert-success  alert-block">
									<button type="button" class="close" data-dismiss="alert">×</button>	
									<strong>'.$sessionSuccess.'</strong>
								</div>
	
								';
				}
				if($sessionInfo) {
					$alert .= '<div class="alert alert-info  alert-block">
									<button type="button" class="close" data-dismiss="alert">×</button>	
									<strong>'.$sessionInfo.'</strong>
								</div>
								';
				}
				if($sessionWarning) {
					$alert .= '<div class="alert alert-warning  alert-block">
									<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>'.$sessionWarning.'</strong>
								</div>
								';
				}
				
				if($sessionDanger) {
					$alert .= '<div class="alert alert-danger  alert-block">
									<button type="button" class="close" data-dismiss="alert">×</button>	
									<strong>'.$sessionDanger.'</strong>
								</div>';
				}
			}
			
			$content	.= $alert;
			if(isset($form['form_name'])){
				$idForm = $form['form_name'];
			}else{ 
				$idForm	= 'stdForm';
			}
			if(isset($form['custom_submit'])){
			$url		 = $form['custom_url'];
            $content    .= Form::open(array('url' => $url, 'method' => 'post','autocomplete'=>'off','id'=>$idForm));
			}else{
			$content    .= Form::open(array('url' => url('/').'/system/'.$sys.'/'.$subsys, 'method' => 'post','autocomplete'=>'off','id'=>$idForm));
			}
			if($subsys == "edit"){
			$sSQL 		= "select a.* 
						   from ucp_site_menu a 
						   where 1=1 
						   and a.name = '$sys'";
			$results 	= DB::select(DB::raw($sSQL));
			$array 		= json_decode(json_encode($results), true);
			$posting    	= $array[0]['posting'];
			$attachment    	= $array[0]['attachment'];
			$approval    	= $array[0]['approval'];
			}

        foreach($form as $key => $val){
            if(substr($key,0,4)== 'set_'){
			$explode  = explode("set_",$key); 
            $content .= '<div id="div_'.$key.'" class="form-group row">';
            $content .= '<label id="'.$key.'" for="'.$explode[1].'" class="col-sm-2 col-form-label text-right">'.ucwords(str_replace("_"," ",$explode[1])).'</label>';
            $content .= '<div class="'.$input_size.'">';
			$content .= $val;
            $content .= '</div>';
            $content .= '</div>';
            }else if(substr($key,0,5)== 'seth_'){
            $explode  = explode("set_",$key); 
            $content .= $val;
            }
        }reset($form);

            $content .= Form::text('subsyspart', 'saveEdit', ['class' => 'form-control hidden']);
			$content .= '&nbsp;&nbsp;';
			if(!isset($form['custom_submit'])){
			if(!isset($form['input_submit'])){
			$content .= '<button type="submit" id="submitCool" class="hide"></button>';
			}
			}
			$content .= Form::close();
			if($subsys == "edit"){
			if(!empty($attachment)){
				if(!isset($form['dropzone'])){ 
				$content .= '<div class="form-group row">';
				$content .= '<label for="" class="col-sm-2 col-form-label text-right">Attachment</label>';
				$content .= '<div class="'.$input_size.'">';
				$linkUrl  = url('/').'/system/trs_attachment/List/'.$id.'/dummy?setpost_module='.$sys;
				$content .= '<form action="'.url('/').'/system/trs_attachment/add/parent/headless/ajax" class="dropzone form-control" >
							<a href="#" onClick="PopupCenter(\''.$linkUrl.'\',\'attachment\',\'500\',\'400\')" ><i class="fa fa-archive" aria-hidden="true"></i></a>
							<input type="hidden" name="setpost_module_name" value="'.$sys.'"/>
							<input type="hidden" name="setpost_trs_type" value="header"/>
							<input type="hidden" name="setpost_trs_id" value="'.$id.'"/>
							</form>';
				$content .= '</div>';
				$content .= '</div>';
				
				$content .= '<script>
							$(function() {
								Dropzone.autoDiscover = false;
								var myDropzone = new Dropzone(".dropzone",{ 
									maxFilesize: 3,  // 3 mb
									acceptedFiles: ".jpeg,.jpg,.png,.pdf",
								});
								myDropzone.on("sending", function(file, xhr, formData) {
								formData.append("_token", "'.csrf_token().'");
								}); 
							});
							</script>';
				}
				
			}
			}
			
			if(!isset($form['custom_submit'])){
			if(!isset($form['input_submit'])){
				// wvd($save);
			if($save == "0"){

				$content .= Form::submit('Save',['disabled'=>'disabled','class'=>'btn btn-secondary']);
				$content .= '&nbsp;&nbsp;';
			}else if ($save == null){
				// wvd($postingDoc);
				if($postingDoc == 1){
				$content .= Form::submit('Save',['disabled'=>'disabled','class'=>'btn btn-secondary']);
				$content .= '&nbsp;&nbsp;';
				}else{
				$content .= Form::submit('Save',['class'=>'btn btn-primary', 'id' => 'btnSubmit']);
				$content .= '&nbsp;&nbsp;';
				}
			
			}
			
			$cancel_link = url('/').'/system/'.$sys.'/List';
			if($btnclose == null) {
				
				if($mode == "popup"){
				$content .= Form::button('Close',['class'=>'btn btn-primary','onclick'=>'window.close();']);
				}else{ 
				$content .= Form::button('Close',['class'=>'btn btn-primary','onclick'=>'onCancelInput("'.$cancel_link.'")']);
				}
				
				$content .= '&nbsp;&nbsp;';
			}
			}
			
			}
			// wvd($form);exit;
			foreach($form as $keyx => $valx){
			if(substr($keyx,0,5)== 'setb_'){
			$content .= $valx;
			$content .= '&nbsp;&nbsp;';
			}
			}reset($form);
			
			
			if($subsys == "edit"){
			if(!empty($posting)){
				if($postingDoc == 0){
						$url_posting = url('/').'/system/Trs_Posting/add/dummy/popup?module='.$sys.'&trs_id='.$id;
						$content .= Form::button('Posting',['class'=>'btn btn-danger','onclick'=>'PopupCenter("'.$url_posting.'","Posting",300,400)']);
						$content .= '&nbsp;&nbsp;';
				}else{ 
						$content .= Form::button('Posting',['class'=>'btn btn-secondary']);
						$content .= '&nbsp;&nbsp;';
				}
			}
			if(!empty($approval)){
				if($postingDoc == 0){
						//http://erp.ptgenap.com/index.php/systems/Trs_Approval/Add/save?data_id=1110725160&module_id=1940610074&origin=hasan&docnum=PO/PT-AAI/2021-02-25/17&module_name=Trs_Puchase_Ord
						$contUrl   = '';
						$contUrl  .= '#'.$sys; //sys
						$contUrl  .= '#'.$id; //trs_id
						$contUrl  .= '#'.$sSession['sSession']; //session
						$sendUrl	= base64url_encode($contUrl);
						
						$url_approval = url('/').'/system/Trs_Approval/add/dummy/popup?dXata='.$sendUrl;
						$content .= Form::button('Send Approval',['class'=>'btn btn-dark fa fa-paper-plane','onclick'=>'PopupCenter("'.$url_approval.'","Approval",300,400)']);
						$content .= '&nbsp;&nbsp;';
				}else{ 
						$content .= Form::button('Send Approval',['class'=>'btn btn-dark fa fa-paper-plane']);
						$content .= '&nbsp;&nbsp;';
				}
			}
			}
			
			$content .= '<script>';
			
			$content .= '$( "#btnSubmit" ).click(function() {
						 $( "#submitCool" ).trigger(\'click\');
						});';
			
			$content .= '</script>';
			
	$mode	= Route::input('mode');
	
	if($mode == 'rest'){
		$content  = "";
	}else{ 
		$content  = $content;
	}
    return $content;
	
    }
	
	public function createGridDtl($confGrid,$menuProp,$sys){
			
			$tabname  	 = $confGrid['tabname'];
			$primaryKey  = $confGrid['id'];
			$decimal 	 = $menuProp['decimal_place'];
			$currency 	 = $menuProp['currency'];
			
			$resultCD 	 = getCurrFormat($currency); 
			$num_sep	 = $resultCD[0]['num_sep'];
			$dec_sep	 = $resultCD[0]['dec_sep'];
			
			
			$content  = "";
			$content .= "<script>
						var dataAdd$tabname={'".$tabname."modeucp':'add_detail','data':null};
						var $tabname = \"$tabname\";
						</script>";
						
			if(isset($menuProp['add_detail'])){
			$add_data 	 = '<div  id="'.$tabname.'detailModalButton" onclick="modalShow'.$tabname.'(dataAdd'.$tabname.','.$tabname.')" class="btn btn-primary '.$tabname.'detailModalButton" ><i class="fa fa-plus"></i>&nbsp;Add Detail</div>';
			$content    .= $add_data;
			$content    .= '&nbsp;&nbsp;&nbsp;';
			$content    .= '<br><br>';
			}
			$content .= '
						<table id="'.$tabname.'dtlTable" class="display table detailGrid" style="width:100%">
						<thead>
						<tr>
						';
			$content .= '<th width="1%"></th>';
			$content .= '<th width="1%"></th>';
			
			if(isset($confGrid['additional1'])){ 
			$content .= '<th width="1%"></th>';
			}
			
				
			foreach($confGrid['list'] as $val){
			if($confGrid['id'] == $val){
			$content .= '<th style="display:none">'.ucwords(str_replace("_"," ",$val)).'</th>';
			}else{ 
			$content .= '<th>'.ucwords(str_replace("_"," ",$val)).'</th>';
			}
			};
			
			$content .= '
				</tr>
				</thead>
				<tbody>
				';
				
				$sSQLH	 = "";
				$count	 = count($confGrid['data']);
				$no 	 = 1;
				$sSQLH	.= 'a.'.$primaryKey.' as ucpid,';
			foreach($confGrid['data'] as $valH){	
				if($no == $count){ 
				$sSQLH	.= 'a.'.$valH;
				}else{ 
				$sSQLH	.= 'a.'.$valH.',';
				}
				$no++;
			}
	
			$sSQL 		= "select $sSQLH 
						   from (".$confGrid['table'].") a 
						   where 1=1 
						   ";
	
			$results 	= DB::select(DB::raw($sSQL));
			if(!empty($results)){ 
				$data 	    = json_decode(json_encode($results), true);
			}else{ 
				$data 		= array();
			}
			//modal show
			//wvd($tabname);
			$content .='<script>';
				$content .= '
				function modalShow'.$tabname.'(data,tabname){
					
					var tabname   = '.$tabname.';
					var dataEdit  = "dataEdit";
					var modalid   = dataEdit+tabname;
					var mode      = data.'.$tabname.'modeucp;
					$(\'#\'+modalid).modal(\'show\');
					if(mode != \'add_detail\'){
						var entries = Object.entries(data);
						
						for (var [data, count] of entries){
							$(\'#\'+`${data}`).val(`${count}`).trigger("chosen:updated");
							$(\'.\'+`${data}`).val(`${count}`);
						}
					} else {
						var entries = Object.entries(data);
						$(\'#'.$tabname.'modeucp\').val(\'add_detail\');
						// document.getElementById("'.$tabname.'modalForm").reset();
					}
				}
				';
			$content .='</script>';
			
			//print for JS 
			$aryDataNameMod = $confGrid['data'];
			foreach($data as $kMod => $vMod){
				$id 		= $tabname.$vMod['ucpid'];
				$iddet		= $vMod['ucpid'];
				$pid 		= (isset($vMod['pid']) ? $vMod['pid'] : $id);
				
				unset($vMod['ucpid']);
				$lenghtV    = count($vMod);
				$varC	 	= "<script> var data$id={";  
				for($g=0;$g<$lenghtV;$g++){
				$field		= $aryDataNameMod[$g];
				$searchVal 	= array("'");
				$replaceVal = array("\'");
				$replac 	= str_replace($searchVal,$replaceVal,$vMod[$field]);
				
				$checkDec 	= containsDecimal($replac);
				if($checkDec == 1){
					$explodi	= explode(".",$replac); 
					$lastDig	= substr($explodi[1], 0, $decimal);
					
					$replac 	= $explodi[0].".".$lastDig;
				}else{ 
					$replac 	= $replac;
				}
				
				$varC	   .= $tabname.$field.':\''.$replac.'\',';
				}
				$varC	   .= $tabname.'modeucp:"edit_detail",';
				$varC	   .= $tabname.'ucpid:"'.$iddet.'"';
				$varC	   .= "}</script>";
				$content   .= $varC; // ngumpetin
			
			}
			
			foreach ($data as $key => $value){

				$content .= '<tr>';
				$primary  = $confGrid['id'];
				$id 	  = $value['ucpid'];
	
				if(isset($menuProp['delete_detail']) == 1){
				$del_link = url('/').'/system/'.$sys.'/delete_detail/'.base64url_encode($value['ucpid']).'?settabname='.$tabname;
				$content .= '<td width="1%">'.'<a class="fa fa-trash-o" href="#detail" onclick="return isconfirm(\''.$del_link.'\')"></a>'.'</td>';
				}else{ 
				$content .= '<td width="1%">'.'<a class="fa fa-ban" href=""></a>'.'</td>';
				}
				
				if(isset($confGrid['populate_ajax'])){ 
				
					$populate = 'modalPopulateEditDetail'.$tabname.'(data'.$tabname.$id.','.$tabname.');';
				}else{ 
					$populate ="";
				}
				
				if(isset($menuProp['edit_detail']) == 1){
				$content .= '<td width="1%">'.'<a class="fa fa-pencil '.$tabname.'" href="#" onclick="modalShow'.$tabname.'(data'.$tabname.$id.','.$tabname.');'.$populate.'"></a>'.'</td>';
				}else{ 
				$content .= '<td width="1%">'.'<a class="fa fa-ban"></a>'.'</td>';
				}
				
				if(isset($confGrid['additional1'])){
					$linkAdd	 = "";
					$noe 		 = 0;
					$count		 = count($confGrid['additional1_col']);
	
				foreach($confGrid['additional1_col'] as $juy => $yaj){
					if($count == $noe){
					$linkAdd 	.= "$juy=".base64url_encode($value[$yaj])."";
					}else{
					$linkAdd 	.= "$juy=".base64url_encode($value[$yaj])."&";
					}
					$noe++;
				}
				
				$linkAddl	= $confGrid['additional1_link'].$linkAdd;
				switch($confGrid['additional1_attr']['type']){ 
					case "popup":
						$button					= '<button class="btn btn-danger" onclick="PopupCenter(\''.$linkAddl.'\',\'Map\','.$confGrid['additional1_attr']['width'].','.$confGrid['additional1_attr']['height'].')" type="button">Detail</button>';
					break;
				}
				
				$content .= '<td width="1%">'.$button.'</td>';
				}
				
				
				foreach($value as $k => $v){
				if($k != "ucpid"){
			
					foreach($confGrid['list'] as $h){
						
						$k = strtolower($k);
						$h = strtolower($h);
						
						$searchHval 	= array("(",")","/");
						$replaceHval = array("");
						$h 	= str_replace($searchHval,$replaceHval,$h);
						if($k == $h){
							
							$temp_row   = "";
							$checkDec 	= containsDecimal($v);
							if($checkDec == 1){
								$align 	= 'right';
								$temp_row 	.= '<td align="'.$align.'">'.number_format((float)$v,$decimal,"$num_sep","$dec_sep").'</td>';
							}else{ 
								$align 	= '';
								$temp_row 	.= '<td align="'.$align.'">'.$v.'</td>';
							}
							//$content .= '<td>'.$v.'</td>';
							$content .= $temp_row;
						}
					}
				}
				}
				$content .= '</tr>';
			}
			$content .= '</tbody></table>';
			$content .= '<script>
                $(document).ready(function() {
                    $(\'#'.$tabname.'dtlTable\').DataTable( {
                        "processing": true
                    } );
                } );
				
				

				
				
                </script>
                ';
				
	return $content;
	}
	
	public function createGridDtlInline($confGrid,$menuProp,$form,$sys){
			$tabname  	 = $confGrid['tabname'];
			$primaryKey  = $confGrid['id'];
				
				$tempList 	= array();
			foreach($form as $key=>$valx){
				$tempList[]=$valx;
			}
			
				$list  = array();
			foreach($tempList as $kk=>$vv){
				$list[] 	= $vv;
			}
			
			$listBtm 	= $list;
			$content     = "";
			$content    .= "<script>
							var dataAdd$tabname={'".$tabname."modeucp':'add_detail','data':null};
							var $tabname = \"$tabname\";
							</script>";
							
			if(isset($menuProp['add_detail'])) {
				$add_data 	 = '<div id="'.$tabname.'dtlTableInlineaddRow" class="btn btn-primary" ><i class="fa fa-plus"></i>&nbsp;Add Detail</div>';
				$content    .= $add_data;
				$content    .= '&nbsp;&nbsp;&nbsp;';
				$content    .= '<br><br>';
	
			}
			
			$content .= '
						<table id="'.$tabname.'dtlTableInline" class="display table" style="width:100%">
						<thead>
						<tr>
						';
			
			$content .= '<th width="1%"></th>';
			$content .= '<th width="1%"></th>';
			
			foreach($confGrid['list'] as $val){
			$substr   = substr($val,0,2);
			$content .= '<th >'.ucwords(str_replace("_"," ",$val)).'</th>';
			};
			
			$content .= '
						</tr>
						</thead>
						<tbody>
						';
						
				$sSQLH	 = "";
				$count	 = count($confGrid['data']);
				$no 	 = 1;
				$sSQLH	.= 'a.'.$primaryKey.' as ucpid,';
			foreach($confGrid['data'] as $valH){	
				if($no == $count){ 
				$sSQLH	.= 'a.'.$valH;
				}else{ 
				$sSQLH	.= 'a.'.$valH.',';
				}
				$no++;
			}
	
			$sSQL 		= "select $sSQLH 
						   from (".$confGrid['table'].") a 
						   where 1=1 
						   ";

			$results 	= DB::select(DB::raw($sSQL));
			if(!empty($results)){ 
				$data 	    = json_decode(json_encode($results), true);
			}else{
				$data = array();
			}
			
				$arySaveEdit 	= array();
				foreach ($data as $key => $value){
					
					
				if(isset($value)){ 
					$value = $value;
				}else{ 
					$value = null;
				}
				
				
				$content .= '<tr>';
				$primary  = $confGrid['id'];
				$id 	  = $value['ucpid'];
				
				if(isset($menuProp['delete_detail']) == 1){
				$del_link = url('/').'/system/'.$sys.'/delete_detail/'.base64url_encode($value['ucpid']);
				$content .= '<td width="1%">'.'<a class="fa fa-trash-o" href="#" onclick="return isconfirm(\''.$del_link.'\')"></a>'.'</td>';
				}else{ 
				$content .= '<td width="1%">'.'<a class="fa fa-trash-o disabled" href=""></a>'.'</td>';
				}
				
				
				if(isset($menuProp['edit_detail']) == 1){
				$content .= '<td width="1%">';
				$content .= '<a class="fa fa-play-circle" href="#" id="'.$tabname.'edit_'.base64url_encode($value['ucpid']).'"></a>'; 
				$content .= '<a class="fa fa-check-circle" href="#" style="display:none" id="'.$tabname.'saveedit_'.base64url_encode($value['ucpid']).'"></a>'; 
				$content .= '</td>';
				$content .= '<script>'."\r\n";
				$content .= '$(\'#'.$tabname.'edit_'.base64url_encode($value['ucpid']).'\').click(function(){'."\r\n".'
							
							 $(\'#'.$tabname.'edit_'.base64url_encode($value['ucpid']).'\').hide();'."\r\n".'
							 $(\'#'.$tabname.'saveedit_'.base64url_encode($value['ucpid']).'\').show();'."\r\n".'
							';
				$button 		= $tabname.'saveedit_'.base64url_encode($value['ucpid']);
				$arySaveEdit  	= ["name"=>"$button"];
				
		

				$listNormal 	= $list;
				$script 		= "";
				$colums			= "";
				$saveno 		= 1;
				
				
					$aryJson = array();
				foreach ($list as $kH=>$kI){
					$explk 	= explode("setpost_",$kI['name']);
					$column = $kI['type'];
					$kIX 	= $explk[1];
					$expl[1]= $kIX;
					switch($column){
						case "text":
							$colums .= '$(\'#'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').replaceWith(function(){ '."\r\n";
							$colums .= 'return $("<input class=\"bordernone\" type=\"text\" id=\"'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\" />", {html: $(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html()})';
							$colums .= '.val($(\'#'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html());'."\r\n";
							$colums .= '});'."\r\n";
							$aryJson[] = array("colId"=>$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1],
												"colName"=>'setpost_'.$expl[1]
											   );
						break;
						case "hidden":
							$colums .= '$(\'#'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').replaceWith(function(){ '."\r\n";
							$colums .= 'return $("<input class=\"bordernone\" type=\"text\" id=\"'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\" />", {html: $(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html()})';
							$colums .= '.val($(\'#'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html());'."\r\n";
							$colums .= '});'."\r\n";
							$aryJson[] = array("colId"=>$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1],
												"colName"=>'setpost_'.$expl[1]
											   );
						break;
						case "select":
							$colums .= '$(\'#'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').replaceWith(function(){ '."\r\n";
							$colums .= 'return $("';
							$colums .= '<select class=\"bordernone\" name=\"ultimatePowerRanger\" id=\"'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\">';
							$data 	 = $kI['data'];
							foreach($data as $ky => $ku){
							$colums .= '<option value=\"'.$ky.'\">'.$ku.'</option>';
							}reset($data);
							$colums .= '</select>",';
							$colums .= '{html: $(\'#'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html()})';
							$colums .= '.val($(\'#'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html());'."\r\n";
							$aryJson[] = array("colId"=>$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$expl[1],
											   "colName"=>'setpost_'.$expl[1]
											   );
							$colums .= '});'."\r\n";
						break;
						
					}
					$saveno++;
					
					//$aryJson[] = array("colId"=>$kI
				}reset($list);
				
						$content .= $colums;
						$content .= '});'."\r\n";
						
						$aryName  = base64url_encode($value['ucpid']);
						$content .= '$(\'#'.$tabname.'saveedit_'.base64url_encode($value['ucpid']).'\').click(function(){'."\r\n";
						
						$jsonc	  = "";
						foreach($aryJson as $u => $i){
						$jsonc   .= 'var '.$i['colId'].' = $(\'#'.$i['colId'].'\').val();'."\r\n";
						}reset($aryJson);
						
						$content .= $jsonc;
						$link	  = $confGrid['linkEdit'];
						$content .= '
									$.ajax({
											url 	: "'.$link.'/'.base64url_encode($value['ucpid']).'/headless'.'",
											method 	: "POST",';
						
						$content .= 'data 	:  {';
						$jsond    = '_token:"'.csrf_token().'",';
						$nox = 1;
						$count = count($aryJson);
						foreach($aryJson as $u => $i){
						if($nox == $count){ 
						$jsond   .=  '"'.$i['colName'].'": '.$i['colId'];
						}else{ 
						$jsond   .=  '"'.$i['colName'].'": '.$i['colId'].',';
						}
						
						$nox++;
						}reset($aryJson);
						
						$content .= $jsond;
						$content .= '}';
						$content .= ',';
						
						$content .= '		
									async 	: false,
									dataType: \'json\',
									success: function(data){
											if(data.status == 1){
												$(\'#alert008\').show();
												document.getElementById("alert008").innerHTML = "Data Updated";
												$(\'#alert008\').delay(5000).hide(0);
												location.reload();
												data_response = data;
											}
									}
									});
									return data_response;
						';
						
						//$aryJson[] = array("colId"=>$kI
						$content .= $colums;
											
						$content .= '
									 $(\'#'.$tabname.'edit_'.base64url_encode($value['ucpid']).'\').show();'."\r\n".'
									 $(\'#'.$tabname.'saveedit_'.base64url_encode($value['ucpid']).'\').hide();'."\r\n".'
									 ';
						$content .=	'});';
						$content .= '</script>';
						
				}else{
				$content .= '<td width="1%">';
				$content .= '<a class="fa fa-play-circle" href="#"></a>'; 
				$content .= '<a class="fa fa-check-circle" href="#" style="display:none"></a>'; 
				$content .= '</td>';
				}
				
				foreach($value as $k => $v){
				if($k != "ucpid"){
					foreach($confGrid['list'] as $h){
						if($k == $h){
							$substr   = substr($h,0,2); 
							$content .= '<td class="row_data_x"><div style="width:100%" id="'.$tabname.'row_'.base64url_encode($value['ucpid']).'_'.$k.'">'.$v.'</div></td>';
						}
					}
				}
				}
				$content .= '</tr>';
			}reset($data);
				
				foreach($confGrid['data'] as $ty => $tu){
					$arrayBtm[$tu] = $tu;
				}reset($confGrid['data']);
				

			$content .= '</tbody></table>';
						$content .= '<button id="saveOi" />';
						$content .= '<script language="javascript">
									$(document).ready(function(){
										var t = $(\'#'.$tabname.'dtlTableInline\').DataTable({
											"columnDefs": [{ "aTargets": [  ],"bVisible":false}],
											"processing": true,
											"stateSave": true
										});
										 var counter = 1;
										$(\'#'.$tabname.'dtlTableInlineaddRow\').on( \'click\', function () {
											 if (counter == 1) {
											  t.row.add( ['; 
											$content .='\'\',';
											$content .='\'<a class="fa fa-plus-circle" onclick="saveWoi()"><a>\',';
											$nos = 1;
											
											$neat = "";
											foreach($arrayBtm as $ket => $ker){
												$noe = 1; 
												$counte = count($listBtm);	
												foreach($listBtm as $kbtm => $vbtm){
									
												$explode = explode("setpost_",$vbtm['name']);
												$field   = $explode[1];
												

												if($field == $ker){
													switch($vbtm['type']){
														case "text":
														if($counte == $noe){
														$content .= '\'<input id=\"'.$tabname.'n_'.$ker.'_'.$noe.'\" class=\"bordernone\" type=\"text\"/>\'';
														}else{
														$content .= '\'<input id=\"'.$tabname.'n_'.$ker.'_'.$noe.'\" class=\"bordernone\" type=\"text\"/>\',';
														}
														break;
														case "selectPick":
														if($counte == $noe){
														$content .= '\'<input id="'.$tabname.'n_'.$ker.'_'.$noe.'" class="bordernone" type="text"/>\'';
														}else{
														$content .= '\'<input id="'.$tabname.'n_'.$ker.'_'.$noe.'" class="bordernone" type="text"/>\',';
														}
														
														
														$idNeat  = $tabname.'n_'.$ker.'_'.$noe;
														$neat 	.= generateNeatDropdown($vbtm['data'],$idNeat,$tabname,$add_link=null,$coa_code=null);
														
														break;
														case "hidden":
														if($counte == $noe){
														$content .= '\'<input id=\"'.$tabname.'n_'.$ker.'_'.$noe.'\" class=\"bordernone\" type=\"text\"/>\'';
														}else{
														$content .= '\'<input id=\"'.$tabname.'n_'.$ker.'_'.$noe.'\" class=\"bordernone\" type=\"text\"/>\',';
														}
														break;
														case "selectbak":
														$content .= '\'<select id="'.$tabname.'n_'.$ker.'_'.$noe.'" class="">';
														foreach($vbtm['data'] as $yu => $iu){
														$content .= '<option value="'.$yu.'">'.$iu.'</option>';
														}reset($vbtm);
														if($counte == $noe){
														$content .= '</select>\'';
														}else{
														$content .= '</select>\',';	
														}														
														break;
														
														case "select":
														if($counte == $noe){
														//$content .= '\'<button value="pilih" onClick="selectWoi()" id=\"'.$tabname.'n_'.$ker.'_'.$noe.'\" class=\"\"  />\'';
														$content .= '\' <div onClick="selectWoi()" onstyle="width:100%">OKE</div> \'';
														}else{
														$content .= '\' <div onClick="selectWoi()" style="width:100%">OKE</div>  \',';
														}														
														break;
														
														
														case "dummy":
														if($counte == $noe){
														$content .= '\'<div></div>\'';
														}else{
														$content .= '\'<div></div>\',';
														}
														break;
													}
													
												}
												}reset($listBtm);
												
											$noe++;
											}reset($arrayBtm);
											$content .= '])}';
											$content .= 'counter++;';
											$content .= '
										 });';
											$content .='
									} );
									</script>
									';	
									
									$content .= '<script>';
									
									$content .= '$( document ).ready(function() {';
									$content .= $neat;
									$content .= '});';
									
									
									$content .= 'function saveWoi(){';
									
										$jsonH	  = "";
										$noi 	  = 1;
									foreach($listBtm as $uxi => $ixi){
										$explodi  = explode("_",$ixi['name']);
										$jsonH   .= 'var '.$tabname.'n_'.$explodi[1].'_'.$noi.' = $(\'#'.$tabname.'n_'.$explodi[1].'_'.$noi.'\').val();'."\r\n";
										$noi++;
									};
									
									$linkAdd  = $confGrid['linkAdd'];
									$content .= $jsonH;
									$content .= '	$.ajax({
														url 	: "'.$linkAdd.'/add/headless'.'",
														method 	: "POST",';
									
									$content .= 'data 	:  {';
									$jsonA    = '_token:"'.csrf_token().'",';
									$noAdd 	  = 1;
									$countA    = count($listBtm);
									foreach($listBtm as $ux => $ix){
									$explod   = explode("_",$ix['name']);
									if($noAdd == $countA){ 
									$jsonA   .=  '"'.$ix['name'].'"'.': '.$tabname.'n_'.$explod[1].'_'.$noAdd."\r\n";
									}else{ 
									$jsonA   .=  '"'.$ix['name'].'"'.': '.$tabname.'n_'.$explod[1].'_'.$noAdd.','."\r\n";
									}
									
									$noAdd++;
									}reset($listBtm);
									
									$content .= $jsonA;
									$content .= '},';
									
									$content .= '		
												async 	: false,
												dataType: \'json\',
												success: function(data){
														if(data.status == 1){
															$(\'#alert008\').show();
															document.getElementById("alert008").innerHTML = "Data Updated";
															$(\'#alert008\').delay(5000).hide(0);
															location.reload();
															data_response = data;
														}
												}
												});
												return data_response;
												';
									$content .= '
												 alert("ucup");
												 };';
									$content .= '</script>';
			
			//parseContent($content); 
	
			return $content;
	}
	
	public function generateModal($modalProp,$input_size=null,$modal_size=null){
		
			$sys        = route::input('sys');
			$subsys     = route::input('subsys');
			$id         = route::input('id');
			$mode       = route::input('mode');
			
			$modalPropEdit = $modalProp;
			$modalPropAdd  = $modalProp;
			// wvd($modalPropAdd); 
			if(isset($modalProp['tabname'])){ 
				$tabname = $modalProp['tabname'];
			}else{ 
				$tabname = "covid19";
			}
			
			$type 	  = ($modalProp['type'] == "edit_detail" ? "editDtl" : null);
		
			$content  = "";
			$content .= '
			<div class="modal fade" id="dataEdit'.$tabname.'">
					<div class="modal-dialog '.$modal_size.'">
					  <div class="modal-content">
						<div class="modal-header">
						  <h4 class="modal-title">Data</h4>
						  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						  </button>
						</div>
						<div class="modal-body p-3">';
									$content .= '<div style="padding:10px">';
									$content .= Form::open(array('url' => '','name'=>"modalForm",'method' => 'post','id'=>''.$tabname.'modalForm'));
						  	foreach($modalPropAdd as $k => $v){
								  
									/*
									$content .= '<div class="form-group row">';
									$content .= '<label for="'.$explode[1].'" class="col-sm-2 col-form-label text-right">'.ucwords(str_replace("_"," ",$explode[1])).'</label>';
									$content .= '<div class="'.$input_size.'">';
									$content .= $val;
									$content .= '</div>';
									$content .= '</div>';
								    */
								  
			
								  
								//generate modal
								if(substr($k,0,4)== 'set_'){
									
									if($input_size == null){ 
									$explode  = explode("set_",$k);
									$content .= '<div class="row">';
									$content .= '<div class="form-group" id="div_detail_'.$k.'">';
									$content .= '<label >'.ucwords(str_replace("_"," ",$explode[1])).'</label>';
									$content .= $v;
									$content .= '<small id="'.$k.'" class="form-text text-muted "></small>';
									$content .= '</div>';
									$content .= '</div>';
									}else{ 
									$explode  = explode("set_",$k);
							
									$content .= '<div class="form-group row" id="div_detail_'.$k.'">';
									$content .= '<label class="col-sm-2 col-form-label text-right" >'.ucwords(str_replace("_"," ",$explode[1])).'</label>';
									$content .= '<div class="'.$input_size.'">';
									$content .= $v;
									$content .= '</div>';
									$content .= '</div>';
									}
									
									
									
								}else if(substr($k,0,5)== 'seth_'){
									
									if($k == 'seth_pid'){
									$id 	  = base64url_decode($id);
									$content .= '<input type="radio" name="setpost_pid" value="'.$id.'" id="'.$tabname.'pid" checked class="hidden">';
									}else{
									$explode  = explode("seth_",$k);
									$content .= '<div class="row">';
									$content .= '<div class="form-group">';
									$content .= $v;
									$content .= '<small id="'.$tabname.$k.'" class="form-text text-muted"></small>';
									$content .= '</div>'; 
									$content .= '</div>';
									}
									
								}
							}
							$content .= '<script>';
							$content .= ' 
											$( document ).ready(function() {
											$.validator.setDefaults({ ignore: ":hidden:not(select)" });
											$(\'#'.$tabname.'modalForm\').validate({
											errorPlacement: function (error, element) {
												console.log(element);
												if (element.is("select.chosen-select")) {
													console.log("placement for chosen");
													// placement for chosen
													element.next("div.chosen-container").append(error);
													//(\'$'.$tabname.'_chosen\').append(error);
												} else {
													// standard placement
													error.insertAfter(element);
												}
											}
											});
											});
										';
							
							$content .= '</script>';
							$content .= '<input type="hidden" name="setunique_id" class="form-control" id="'.$tabname.'ucpid" value="">';
							$content .= '<input type="hidden" name="'.$tabname.'modeucp" class="form-control" id="'.$tabname.'modeucp" value="">';
							reset($modalPropAdd);
							$content .= '</div>';
							
				if(isset($modalProp['custom_submit'])){
				$content .='
						</div>
						<div class="modal-footer justify-content-between">
						  <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
						  <button type="button" class="btn btn-danger" id="'.$tabname.'ModalSubmit">Submit</button>
						</div>';
				
				}else{
				$content .='
						</div>
						<div class="modal-footer justify-content-between">
						  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  <input type="submit" value="submit" id="'.$tabname.'ModalSubmit"/>
						</div>';
				}
				$content .='
					  </div>
					  <!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
					</div>
				   <!-- /.modal -->
				';
				$content .= Form::close();
				$content .= '<script>';
				$content .= '  
				
				$(function(){
					$(\'#'.$tabname.'modalForm\').submit(function(){
						var mode = $(\'#'.$tabname.'modeucp\').val();
						
						if(mode != \'\'){
							var mod = mode;
						}else {
							var mod = \'add_detail\';
						}
						$(\'#'.$tabname.'modalForm\').attr(\'action\',"'.url('/').'/system/'.$sys.'/"+mod);
					});
				  });
				';
				$content .= '</script>';
			//parseContent($content);
			return $content;
		}
		
//public function createDataTablesGroup($obj,$configGrid,$dataSource,$sys,$subsys,$gridProp,$urlSegment,$dbProperty,$context=null){
	public function createDataTablesGroup($confGrid,$menuProp,$form,$sys){
			
			$tabname  	 = $confGrid['tabname'];
			$primaryKey  = $confGrid['id'];
			$decimal 	 = $menuProp['decimal_place'];
			$currency 	 = $menuProp['currency'];
			
			$resultCD 	 = getCurrFormat($currency); 
			$num_sep	 = $resultCD[0]['num_sep'];
			$dec_sep	 = $resultCD[0]['dec_sep'];
			
			
			$content  = "";
			$content .= "<script>
						var dataAdd$tabname={'".$tabname."modeucp':'add_detail','data':null};
						var $tabname = \"$tabname\";
						</script>";
						 
			if(isset($menuProp['add_detail'])){
			$add_data 	 = '<div  id="'.$tabname.'detailModalButton" onclick="modalShow'.$tabname.'(dataAdd'.$tabname.','.$tabname.')" class="btn btn-primary '.$tabname.'detailModalButton" ><i class="fa fa-plus"></i>&nbsp;Add Detail</div>';
			$content    .= $add_data;
			$content    .= '&nbsp;&nbsp;&nbsp;';
			$content    .= '<br><br>';
			}
			
			
			$content .= '
						<table class="display table detailGrid table-bordered" style="width:100%">
						<thead>
						<tr>
						';
			$content .= '<th width="1%"></th>';
			$content .= '<th width="1%"></th>';
			
			if(isset($confGrid['additional1'])){ 
			$content .= '<th width="1%"></th>';
			}
			
				
			foreach($confGrid['list'] as $val){
			if($confGrid['id'] == $val){
			$content .= '<th style="display:none">'.ucwords(str_replace("_"," ",$val)).'</th>';
			}else{ 
			$content .= '<th>'.ucwords(str_replace("_"," ",$val)).'</th>';
			}
			};
			
			$content .= '
				</tr>
				</thead>
				<tbody>
				';
				
				$sSQLH	 = "";
				$count	 = count($confGrid['data']);
				$no 	 = 1;
				$sSQLH	.= 'a.'.$primaryKey.' as ucpid,';
			foreach($confGrid['data'] as $valH){	
				if($no == $count){ 
				$sSQLH	.= 'a.'.$valH;
				}else{ 
				$sSQLH	.= 'a.'.$valH.',';
				}
				$no++;
			}
	
			$sSQL 		= "select $sSQLH 
						   from (".$confGrid['table'].") a 
						   where 1=1 
						   ";
	
			$results 	= DB::select(DB::raw($sSQL));
			if(!empty($results)){ 
				$data 	    = json_decode(json_encode($results), true);
			}else{ 
				$data 		= array();
			}
			
			if(!empty($data)){
			foreach($data as $ll=>$mm){
				$aryGrpMnt[$mm[$confGrid['group']]] = array('valmm'=>$mm[$confGrid['group']],'nmmm'=>$mm[$confGrid['group_dec']]);
			}
			}else{ 
				$aryGrpMnt["dummy"] = array('valmm'=>"",'nmmm'=>"");
			}
			
			$tempGrp	 = $aryGrpMnt;
			$content 	.= '<div class="tab-content">';
			$nox 		 = 1;
		
			foreach($tempGrp as $gi => $hi){
			//modal show
			//wvd($tabname);
			
			
			$content .='<script>';
				$content .= '
				//func tablegroup
				function modalShow'.$tabname.'(data,tabname){
					
					var tabname   = '.$tabname.';
					var dataEdit  = "dataEdit";
					var modalid   = dataEdit+tabname;
					var mode      = data.'.$tabname.'modeucp;
					$(\'#\'+modalid).modal(\'show\');
					if(mode != \'add_detail\'){
						var entries = Object.entries(data);
						
						for (var [data, count] of entries){
							$(\'#\'+`${data}`).val(`${count}`).trigger("chosen:updated");
							$(\'.\'+`${data}`).val(`${count}`);
						}
					} else {
						var entries = Object.entries(data);
						$(\'#'.$tabname.'modeucp\').val(\'add_detail\');
						// document.getElementById("'.$tabname.'modalForm").reset();
					}
				}
				';
			$content .='</script>';
			
			//print for JS 
			$aryDataNameMod = $confGrid['data'];
			foreach($data as $kMod => $vMod){
				$id 		= $tabname.$vMod['ucpid'];
				$iddet		= $vMod['ucpid'];
				$pid 		= (isset($vMod['pid']) ? $vMod['pid'] : $id);
				
				unset($vMod['ucpid']);
				$lenghtV    = count($vMod);
				$varC	 	= "<script> var data$id={";  
				for($g=0;$g<$lenghtV;$g++){
				$field		= $aryDataNameMod[$g];
				$searchVal 	= array("'");
				$replaceVal = array("\'");
				$replac 	= str_replace($searchVal,$replaceVal,$vMod[$field]);
				
				$checkDec 	= containsDecimal($replac);
				if($checkDec == 1){
					$explodi	= explode(".",$replac); 
					$lastDig	= substr($explodi[1], 0, $decimal);
					
					$replac 	= $explodi[0].".".$lastDig;
				}else{ 
					$replac 	= $replac;
				}
				
				$varC	   .= $tabname.$field.':\''.$replac.'\',';
				}
				$varC	   .= $tabname.'modeucp:"edit_detail",';
				$varC	   .= $tabname.'ucpid:"'.$iddet.'"';
				$varC	   .= "}</script>";
				$content   .= $varC; // ngumpetin
			
			}
			
			$countData		= count($data); 
			$noData			= 0;
			$contentTR 		= "";
			$countSub		= 0;
			$countSub2		= 0;
			foreach ($data as $key => $value){
				
				
				$keyGroup = $hi['valmm'];
				$keyValue = $value[$confGrid['group']];
				
				if($keyGroup == $keyValue){
				
				
				$contentTR .= '<tr>';
				$primary  	= $confGrid['id'];
				$id 	  	= $value['ucpid'];
	
				if(isset($menuProp['delete_detail']) == 1){
				$del_link = url('/').'/system/'.$sys.'/delete_detail/'.base64url_encode($value['ucpid']).'?settabname='.$tabname;
				$contentTR .= '<td width="1%">'.'<a class="fa fa-trash-o" href="#detail" onclick="return isconfirm(\''.$del_link.'\')"></a>'.'</td>';
				}else{ 
				$contentTR .= '<td width="1%">'.'<a class="fa fa-ban" href=""></a>'.'</td>';
				}
				
				if(isset($confGrid['populate_ajax'])){
					$populate = 'modalPopulateEditDetail'.$tabname.'(data'.$tabname.$id.','.$tabname.');';
				}else{ 
					$populate ="";
				}
				
				if(isset($menuProp['edit_detail']) == 1){
				$contentTR .= '<td width="1%">'.'<a class="fa fa-pencil '.$tabname.'" href="#" onclick="modalShow'.$tabname.'(data'.$tabname.$id.','.$tabname.');'.$populate.'" style="width:1%"></a>'.'</td>';
				//$contentTR .= '<td width="1%">'.'<a class="fa fa-pencil '.$tabname.'" href="#" 	></a>'.'</td>';
				//$contentTR .= '<td width="1%">'.'<a class="fa fa-ban"></a>'.'</td>';
				}else{ 
				$contentTR .= '<td width="1%">'.'<a class="fa fa-ban"></a>'.'</td>';
				}
				
				if(isset($confGrid['additional1'])){
					$linkAdd	 = "?";
				foreach($confGrid['additional1_col'] as $juy => $yaj){ 
					$linkAdd 	.= "&$juy=".base64url_encode($value[$yaj]);
				}
				
				$linkAddl	= $confGrid['additional1_link'].$linkAdd;
				switch($confGrid['additional1_attr']['type']){ 
					case "popup":
						$button	 = '<button class="btn btn-danger" onclick="PopupCenter(\''.$linkAddl.'\',\'Map\','.$confGrid['additional1_attr']['width'].','.$confGrid['additional1_attr']['height'].')" type="button">Detail</button>';
					break;
				}
				
				$contentTR .= '<td width="1%">'.$button.'</td>';
				}
				
				foreach($value as $k => $v){
				if($k != "ucpid"){
			
					foreach($confGrid['list'] as $h){
						
						$k = strtolower($k);
						$h = strtolower($h);
						
						$searchHval 	= array("(",")","/");
						$replaceHval = array("");
						$h 	= str_replace($searchHval,$replaceHval,$h);
						if($k == $h){
							
							$temp_row   = "";
							$checkDec 	= containsDecimal($v);
							if($checkDec == 1){
								$align 	= 'right';
								$temp_row 	.= '<td align="'.$align.'">'.number_format((float)$v,$decimal,"$num_sep","$dec_sep").'</td>';
							}else{ 
								$align 	= '';
								$temp_row 	.= '<td align="'.$align.'">'.$v.'</td>';
							}
							//$content .= '<td>'.$v.'</td>';
							$contentTR .= $temp_row;
						}
					}
				}
				}
				$contentTR 			.= '</tr>';
				$countSub			+= $value[$confGrid['addition']];
				$countSub2			+= $value[$confGrid['addition2']];
				
				$sumdatax[$keyValue]	= array("content"=>$contentTR,"addition"=>$countSub,"addition2"=>$countSub2);
				$noData++;
				}

			}reset($data);
				$nox++;	
			}
			
			if(empty($data)){ 
			
				$sumdatax = array();
			}
			
			
			foreach($sumdatax as $yus => $sup){
					$content .= $sup['content'];
					$content .= '<tr style="background:#daf2da"><td width="1%"></td>';
					$content .= '<td width="1%"></td>';
			
				if(isset($confGrid['additional1'])){
					$content .= '<td width="1%"></td>';
				}
				foreach($confGrid['list'] as $vals){
					if($confGrid['addition'] == $vals){
					$content .= '<td align="right"><b>'.number_format((float)$sup['addition'],$decimal,"$num_sep","$dec_sep").'</b></td>';
					}else if($confGrid['addition2'] == $vals){ 
					$content .= '<td align="right"><b>'.number_format((float)$sup['addition2'],$decimal,"$num_sep","$dec_sep").'</b></td>';
					}else{ 
					$content .= '<td></td>';
					}
				};
					$content .= '</tr>';
				
			}reset($sumdatax);
		
	
			$content .= '</tbody></table>';
			
			return $content;
	}

	public function createDataTablesGroupWithoutAction($confGrid,$menuProp,$form,$sys){
			
		$tabname  	 = $confGrid['tabname'];
		$primaryKey  = $confGrid['id'];
		$decimal 	 = $menuProp['decimal_place'];
		$currency 	 = $menuProp['currency'];
		
		$resultCD 	 = getCurrFormat($currency); 
		$num_sep	 = $resultCD[0]['num_sep'];
		$dec_sep	 = $resultCD[0]['dec_sep'];
		
		
		$content  = "";
		$content .= "<script>
					var dataAdd$tabname={'".$tabname."modeucp':'add_detail','data':null};
					var $tabname = \"$tabname\";
					</script>";
					 
		if(isset($menuProp['add_detail'])){
		$add_data 	 = '<div  id="'.$tabname.'detailModalButton" onclick="modalShow'.$tabname.'(dataAdd'.$tabname.','.$tabname.')" class="btn btn-primary '.$tabname.'detailModalButton" ><i class="fa fa-plus"></i>&nbsp;Add Detail</div>';
		$content    .= $add_data;
		$content    .= '&nbsp;&nbsp;&nbsp;';
		$content    .= '<br><br>';
		}
		
		
		$content .= '
					<table class="display table detailGrid table-bordered" style="width:100%">
					<thead>
					<tr>
					';
		// $content .= '<th width="1%"></th>';
		// $content .= '<th width="1%"></th>';
		
		if(isset($confGrid['additional1'])){ 
		$content .= '<th width="1%"></th>';
		}
		
			
		foreach($confGrid['list'] as $val){
		if($confGrid['id'] == $val){
		$content .= '<th style="display:none">'.ucwords(str_replace("_"," ",$val)).'</th>';
		}else{ 
		$content .= '<th>'.ucwords(str_replace("_"," ",$val)).'</th>';
		}
		};
		
		$content .= '
			</tr>
			</thead>
			<tbody>
			';
			
			$sSQLH	 = "";
			$count	 = count($confGrid['data']);
			$no 	 = 1;
			$sSQLH	.= 'a.'.$primaryKey.' as ucpid,';
		foreach($confGrid['data'] as $valH){	
			if($no == $count){ 
			$sSQLH	.= 'a.'.$valH;
			}else{ 
			$sSQLH	.= 'a.'.$valH.',';
			}
			$no++;
		}

		$sSQL 		= "select $sSQLH 
					   from (".$confGrid['table'].") a 
					   where 1=1 
					   ";

		$results 	= DB::select(DB::raw($sSQL));
		if(!empty($results)){ 
			$data 	    = json_decode(json_encode($results), true);
		}else{ 
			$data 		= array();
		}
		
		if(!empty($data)){
		foreach($data as $ll=>$mm){
			$aryGrpMnt[$mm[$confGrid['group']]] = array('valmm'=>$mm[$confGrid['group']],'nmmm'=>$mm[$confGrid['group_dec']]);
		}
		}else{ 
			$aryGrpMnt["dummy"] = array('valmm'=>"",'nmmm'=>"");
		}
		
		$tempGrp	 = $aryGrpMnt;
		$content 	.= '<div class="tab-content">';
		$nox 		 = 1;
	
		foreach($tempGrp as $gi => $hi){
		//modal show
		//wvd($tabname);
		
		
		$content .='<script>';
			$content .= '
			//func tablegroup
			function modalShow'.$tabname.'(data,tabname){
				
				var tabname   = '.$tabname.';
				var dataEdit  = "dataEdit";
				var modalid   = dataEdit+tabname;
				var mode      = data.'.$tabname.'modeucp;
				$(\'#\'+modalid).modal(\'show\');
				if(mode != \'add_detail\'){
					var entries = Object.entries(data);
					
					for (var [data, count] of entries){
						$(\'#\'+`${data}`).val(`${count}`).trigger("chosen:updated");
						$(\'.\'+`${data}`).val(`${count}`);
					}
				} else {
					var entries = Object.entries(data);
					$(\'#'.$tabname.'modeucp\').val(\'add_detail\');
					// document.getElementById("'.$tabname.'modalForm").reset();
				}
			}
			';
		$content .='</script>';
		
		//print for JS 
		$aryDataNameMod = $confGrid['data'];
		foreach($data as $kMod => $vMod){
			$id 		= $tabname.$vMod['ucpid'];
			$iddet		= $vMod['ucpid'];
			$pid 		= (isset($vMod['pid']) ? $vMod['pid'] : $id);
			
			unset($vMod['ucpid']);
			$lenghtV    = count($vMod);
			$varC	 	= "<script> var data$id={";  
			for($g=0;$g<$lenghtV;$g++){
			$field		= $aryDataNameMod[$g];
			$searchVal 	= array("'");
			$replaceVal = array("\'");
			$replac 	= str_replace($searchVal,$replaceVal,$vMod[$field]);
			
			$checkDec 	= containsDecimal($replac);
			if($checkDec == 1){
				$explodi	= explode(".",$replac); 
				$lastDig	= substr($explodi[1], 0, $decimal);
				
				$replac 	= $explodi[0].".".$lastDig;
			}else{ 
				$replac 	= $replac;
			}
			
			$varC	   .= $tabname.$field.':\''.$replac.'\',';
			}
			$varC	   .= $tabname.'modeucp:"edit_detail",';
			$varC	   .= $tabname.'ucpid:"'.$iddet.'"';
			$varC	   .= "}</script>";
			$content   .= $varC; // ngumpetin
		
		}
		
		$countData		= count($data); 
		$noData			= 0;
		$contentTR 		= "";
		$countSub		= 0;
		$countSub2		= 0;
		foreach ($data as $key => $value){
			
			
			$keyGroup = $hi['valmm'];
			$keyValue = $value[$confGrid['group']];
			
			if($keyGroup == $keyValue){
			
			
			$contentTR .= '<tr>';
			$primary  	= $confGrid['id'];
			$id 	  	= $value['ucpid'];

			// if(isset($menuProp['delete_detail']) == 1){
			// $del_link = url('/').'/system/'.$sys.'/delete_detail/'.base64url_encode($value['ucpid']).'?settabname='.$tabname;
			// $contentTR .= '<td width="1%">'.'<a class="fa fa-trash-o" href="#detail" onclick="return isconfirm(\''.$del_link.'\')"></a>'.'</td>';
			// }else{ 
			// $contentTR .= '<td width="1%">'.'<a class="fa fa-ban" href=""></a>'.'</td>';
			// }
			
			if(isset($confGrid['populate_ajax'])){
				$populate = 'modalPopulateEditDetail'.$tabname.'(data'.$tabname.$id.','.$tabname.');';
			}else{ 
				$populate ="";
			}
			
			// if(isset($menuProp['edit_detail']) == 1){
			// $contentTR .= '<td width="1%">'.'<a class="fa fa-pencil '.$tabname.'" href="#" onclick="modalShow'.$tabname.'(data'.$tabname.$id.','.$tabname.');'.$populate.'" style="width:1%"></a>'.'</td>';
			// //$contentTR .= '<td width="1%">'.'<a class="fa fa-pencil '.$tabname.'" href="#" 	></a>'.'</td>';
			// //$contentTR .= '<td width="1%">'.'<a class="fa fa-ban"></a>'.'</td>';
			// }else{ 
			// $contentTR .= '<td width="1%">'.'<a class="fa fa-ban"></a>'.'</td>';
			// }
			
			if(isset($confGrid['additional1'])){
				$linkAdd	 = "?";
			foreach($confGrid['additional1_col'] as $juy => $yaj){ 
				$linkAdd 	.= "&$juy=".base64url_encode($value[$yaj]);
			}
			
			$linkAddl	= $confGrid['additional1_link'].$linkAdd;
			switch($confGrid['additional1_attr']['type']){ 
				case "popup":
					$button	 = '<button class="btn btn-danger" onclick="PopupCenter(\''.$linkAddl.'\',\'Map\','.$confGrid['additional1_attr']['width'].','.$confGrid['additional1_attr']['height'].')" type="button">Detail</button>';
				break;
			}
			
			$contentTR .= '<td width="1%">'.$button.'</td>';
			}
			
			foreach($value as $k => $v){
			if($k != "ucpid"){
		
				foreach($confGrid['list'] as $h){
					
					$k = strtolower($k);
					$h = strtolower($h);
					
					$searchHval 	= array("(",")","/");
					$replaceHval = array("");
					$h 	= str_replace($searchHval,$replaceHval,$h);
					if($k == $h){
						
						$temp_row   = "";
						$checkDec 	= containsDecimal($v);
						if($checkDec == 1){
							$align 	= 'right';
							$temp_row 	.= '<td align="'.$align.'">'.number_format((float)$v,$decimal,"$num_sep","$dec_sep").'</td>';
						}else{ 
							$align 	= '';
							$temp_row 	.= '<td align="'.$align.'">'.$v.'</td>';
						}
						//$content .= '<td>'.$v.'</td>';
						$contentTR .= $temp_row;
					}
				}
			}
			}
			$contentTR 			.= '</tr>';
			$countSub			+= $value[$confGrid['addition']];
			$countSub2			+= $value[$confGrid['addition2']];
			
			$sumdatax[$keyValue]	= array("content"=>$contentTR,"addition"=>$countSub,"addition2"=>$countSub2);
			$noData++;
			}

		}reset($data);
			$nox++;	
		}
		
		if(empty($data)){ 
		
			$sumdatax = array();
		}
		
		
		foreach($sumdatax as $yus => $sup){
				$content .= $sup['content'];
				$content .= '<tr style="background:#daf2da">';
				// $content .= '<td width="1%"></td>';
				// $content .= '<td width="1%"></td>';
		
			if(isset($confGrid['additional1'])){
				$content .= '<td width="1%"></td>';
			}
			$aryTotal1 = 0;
			$aryTotal2 = 0;
			foreach($confGrid['list'] as $vals){
				if($confGrid['addition'] == $vals){
				$aryTotal1 += $sup['addition'];
				$content .= '<td align="right"><b>'.number_format((float)$sup['addition'],$decimal,"$num_sep","$dec_sep").'</b></td>';
				}else if($confGrid['addition2'] == $vals){ 
				$aryTotal2 += $sup['addition2'];
				$content .= '<td align="right"><b>'.number_format((float)$sup['addition2'],$decimal,"$num_sep","$dec_sep").'</b></td>';
				}else{ 
				$content .= '<td></td>';
				}
			};
				$content .= '</tr>';
			
		}reset($sumdatax);

		// 		$content .= '<tr>';
		// foreach($confGrid['list'] as $vals2){
		// 	if($confGrid['addition'] == $vals2){
		// 		$content .= '<td align="right"><b>'.number_format((float)$aryTotal1,$decimal,"$num_sep","$dec_sep").'</b></td>';
		// 	}else if($confGrid['addition2'] == $vals2){
		// 		$content .= '<td align="right"><b>'.number_format((float)$aryTotal2,$decimal,"$num_sep","$dec_sep").'</b></td>';
		// 	}else{ 
		// 		$content .= '<td></td>';
		// 	}
		// }
		// 		$content .= '</tr>';

		$content .= '</tbody></table>';
		
		return $content;
	}

}