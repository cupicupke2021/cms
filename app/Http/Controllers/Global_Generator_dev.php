<?php
namespace App\Http\Controllers;

use         DB;
use         PDO;
use         URL;
use         Form;
use         route;
use         Illuminate\Http\Request;

class Global_Generator_dev extends Controller
{
    public function index()
    {   
        return array("test1"=>"test1");
    }

	public function getId(){
	$id    	= route::input('id');
	$id 	= base64url_decode($id);
	return $id;
	}
	
	public function getRowData($table_name,$id,$column){
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
		GLOBAL $request;
		$array 		= prePost($array);
		$session 	= $request->session()->get('dSession');
		$array['data']['rec_user'] =  $session['sUserid'];
		$array['data']['rec_date'] =  date('Y-m-d H:i:s');
		$query		= DB::table($table_name)->insert($array['data']);
		return $query;
	}
	
	public function editRowData($table_name,$key,$id,$array){
		GLOBAL $request;
		$array 						= prePost($array);
		$session 	= $request->session()->get('dSession');
		$array['data']['mod_user'] 	=  $session['sUserid'];
		$array['data']['mod_date'] 	=  date('Y-m-d H:i:s');
		$query		= DB::table($table_name)->where($key,$array[$key])->update($array['data']);
		return $query;
	}
	
	public function editRowDataPid($table_name,$key,$id,$array){
		$array 		= prePost($array);
		$query		= DB::table($table_name)->where($key,$id)->update($array['data']);
		return $query;
	}
	
	public function deleteRowData($table_name,$key,$id){
		$query 		= DB::table($table_name)->where($key,$id)->delete();
		return $query;
	}
	
	public function getParent($table_name,$key,$id,$column){
		
		$query = DB::table($table_name)->select($column)->where($key,$id)->first();
		return $query;

	}
		
    public function createGridAjax($confGrid,$mode,$ssp,$menuProp,$sys){
		
	GLOBAL $request;
	$ucp_site_header_set = config('tables.ucp_site_header_set');
	
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
			$rewrite .= '<th width="1%" class="no-sort"><i class="fa fa-trash-o"/></th>';
			$rewrite .= '<th width="1%" class="no-sort"><i class="fa fa-pencil"/></th>';
			
			$aryList   = array();
			foreach($data as $kiy => $koy){
			//wvd($koy); 
			$rewrite  .= '<th>'.ucwords(str_replace("_"," ",$koy['label'])).'</th>';
			$aryList[] = $koy['label'];
			$aryListReal[] = $koy['header'];
			
			}
			 $rewrite .= '
			</tr>
			</thead>';
		}else{
			$rewrite  = '<thead><tr>';
			$aryList  = $confGrid['list'];
			foreach($confGrid['list'] as $val){
			$rewrite .= '<th>'.ucwords(str_replace("_"," ",$val)).'</th>';
			$rewrite .= '</tr></thead>';
			}
			$aryList 	  = $confGrid['list'];
			$aryListReal  = $confGrid['list'];
		}
		
		}else{
			$rewrite  		= '';
			$aryList  		= $confGrid['list'];
			$aryListReal  	= $confGrid['list'];
		}
	
	
    if($mode   != "headless"){
    $content    = '';
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
	
    $content .= '<table id="parentTable" class="display table" style="width:100%">';
	if($rewrite != ''){
	$content .= $rewrite;
	}else{
	$content .= '<thead style="'.$visibility.';border:1px solid #000"><tr >';
    $content .= '<th width="1%" class="no-sort"><i class="fa fa-trash-o"/></th>';
    $content .= '<th width="1%" class="no-sort"><i class="fa fa-pencil"/></th>';
    foreach($aryListReal as $val){
    $content .= '<th>'.ucwords(str_replace("_"," ",$val)).'</th>';
    };

    $content .= '</tr>';
	$content .= '</thead>';
	}
	$content .= '</table>';
	
	
    $content .= '<script>
                $(document).ready(function() {
                    $(\'#parentTable\').DataTable( {
						"columnDefs": [{ className: "my_class", "targets": [ 2 ],"visible":false }],
                        "processing": true,
                        "serverSide": true,
                        "ajax": "'.$confGrid['link'].'/headless/ajax",
						stateSave: true
                    } );
                } );
                </script>
                ';

    return $content;
    }else{
		
        //ajax datagrid
        $sSQL            = $confGrid['table'];
        $primaryKey      = $confGrid['id'];
		$confGrid['list'] = $aryListReal;
		
        $sSQLB  = "";
        $x      = 1;
        $count  = count($confGrid['list']);
        
                $sSQLB      .= "concat('$sys','@',$primaryKey) as ucpid,";
                $sSQLB      .= "concat('$sys','@',$primaryKey) as ucpide,";
                
             
        foreach($confGrid['list'] as $val){
            if($x == $count){ 
                $sSQLB      .= "a.".$val;
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
									return '<a class="fa fa-trash-o" href="#" onclick="return isconfirm(\''.$del_link.'\')"></a>';
                            
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
                                    return '<a class="fa fa-pencil" href="'.url('/').'/system/'.$sys.'/edit/'.$id.'"></a>';
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
        foreach($confGrid['list'] as $val){
		    $columns[]  = array("db"=>$val,"dt"=>$no);
        
		$no++;
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

    public function createStdForm($form){
        $sys        = route::input('sys');
        $subsys     = route::input('subsys');
        $id         = route::input('id');
        $mode       = route::input('mode');

            $input_size   = $form['input_size'];
            $content      = "";
            $content      = Form::open(array('url' => url('/').'/system/'.$sys.'/'.$subsys, 'method' => 'post','autocomplete'=>'off'));

        foreach($form as $key => $val){
            if(substr($key,0,4)== 'set_'){
			$explode  = explode("set_",$key); 
            $content .= '<div class="form-group row">';
            $content .= '<label for="'.$explode[1].'" class="col-sm-2 col-form-label text-right">'.ucwords(str_replace("_"," ",$explode[1])).'</label>';
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
            $content .= Form::submit('Save',['class'=>'btn btn-primary']);
            $content .= '&nbsp;&nbsp;';
			
			$cancel_link = url('/').'/system/'.$sys.'/List';
            $content .= Form::button('Close',['class'=>'btn btn-primary','onclick'=>'onCancelInput("'.$cancel_link.'")']);
			$content .= '&nbsp;&nbsp;';
		foreach($form as $keyx => $valx){
			

			if(substr($keyx,0,5)== 'setb_'){
			$content .= $valx;
			$content .= '&nbsp;&nbsp;';
			}
		}reset($form);
			$content .= Form::close();

    return $content;
    }
	
	public function createGridDtl($confGrid,$menuProp,$sys){
			
			$tabname  	= $confGrid['tabname'];
			$primaryKey = $confGrid['id'];
			
			$content  = "";
			$content .= "<script>
						var dataAdd$tabname={'".$tabname."modeucp':'add_detail','data':null};
						var $tabname = \"$tabname\";
						</script>";
						
			$add_data 	 = '<div onclick="modalShow'.$tabname.'(dataAdd'.$tabname.','.$tabname.')" class="btn btn-primary" ><i class="fa fa-plus"></i>&nbsp;Add Detail</div>';
			$content    .= $add_data;
			$content    .= '&nbsp;&nbsp;&nbsp;';
			$content    .= '<br><br>';
			
			$content .= '
						<table id="'.$tabname.'dtlTable" class="display table" style="width:100%">
						<thead>
						<tr>
						';
			$content .= '<th width="1%"></th>';
			$content .= '<th width="1%"></th>';
			
				
			foreach($confGrid['list'] as $val){
			if($confGrid['id'] == $val){
			$content .= '<th style="display:none">'.ucwords(str_replace("_","",$val)).'</th>';
			}else{ 
			$content .= '<th>'.ucwords(str_replace("_","",$val)).'</th>';
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
					if(mode!=\'add_detail\'){
					var entries = Object.entries(data);
					for (var [data, count] of entries) {
					$(\'#\'+`${data}`).val(`${count}`);
					$(\'.\'+`${data}`).val(`${count}`);
					}
					}else{
					var entries = Object.entries(data);
					$(\'#'.$tabname.'modeucp\').val(\'add_detail\');
					document.getElementById("'.$tabname.'modalForm").reset();
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
				$content .= '<td width="1%">'.'<a class="fa fa-trash-o" href="" onclick="return isconfirm(\''.$del_link.'\')"></a>'.'</td>';
				}else{ 
				$content .= '<td width="1%">'.'<a class="fa fa-ban" href=""></a>'.'</td>';
				}
				
				if(isset($menuProp['edit_detail']) == 1){
				$content .= '<td width="1%">'.'<a class="fa fa-pencil" href="#" onclick="modalShow'.$tabname.'(data'.$tabname.$id.','.$tabname.')"></a>'.'</td>';
				}else{ 
				$content .= '<td width="1%">'.'<a class="fa fa-ban"></a>'.'</td>';
				}
				
				foreach($value as $k => $v){
				if($k != "ucpid"){
					
					foreach($confGrid['list'] as $h){
						if($k == $h){
							$content .= '<td>'.$v.'</td>';
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
						
			$add_data 	 = '<div id="dtlTableInlineaddRow" class="btn btn-primary" ><i class="fa fa-plus"></i>&nbsp;Add Detail</div>';
			$content    .= $add_data;
			$content    .= '&nbsp;&nbsp;&nbsp;';
			$content    .= '<br><br>';
			
			$content .= '
						<table id="dtlTableInline" class="display table "  style="width:100%">
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
				$content .= '<td width="1%">'.'<a class="fa fa-trash-o" href="" onclick="return isconfirm(\''.$del_link.'\')"></a>'.'</td>';
				}else{ 
				$content .= '<td width="1%">'.'<a class="fa fa-trash-o disabled" href=""></a>'.'</td>';
				}
				
				
				if(isset($menuProp['edit_detail']) == 1){
				$content .= '<td width="1%">';
				$content .= '<a class="fa fa-play-circle" href="#" id="edit_'.base64url_encode($value['ucpid']).'"></a>'; 
				$content .= '<a class="fa fa-check-circle" href="#" style="display:none" id="saveedit_'.base64url_encode($value['ucpid']).'"></a>'; 
				$content .= '</td>';
				$content .= '<script>'."\r\n";
				$content .= '$(\'#edit_'.base64url_encode($value['ucpid']).'\').click(function(){'."\r\n".'
							
							 $(\'#edit_'.base64url_encode($value['ucpid']).'\').hide();'."\r\n".'
							 $(\'#saveedit_'.base64url_encode($value['ucpid']).'\').show();'."\r\n".'
							';
				$button 		= 'saveedit_'.base64url_encode($value['ucpid']);
				$arySaveEdit  	= ["name"=>"$button"];
				
		

				$listNormal 	= $list;
				$script 		= "";
				$colums			= "";
				$saveno 		= 1;
				
				
					$aryJson = array();
				foreach ($list as $kH=>$kI){
					$explk 	= explode("_",$kI['name']);
					$column = $kI['type'];
					$kIX 	= $explk[1];
					$expl[1]=$kIX;
					 
					switch($column){
						case "text":
							$colums .= '$(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').replaceWith(function(){ '."\r\n";
							$colums .= 'return $("<input class=\"bordernone\" type=\"text\" id=\"row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\" />", {html: $(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html()})';
							$colums .= '.val($(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html());'."\r\n";
							$colums .= '});'."\r\n";
							$aryJson[] = array("colId"=>'row_'.base64url_encode($value['ucpid']).'_'.$expl[1],
												"colName"=>'setpost_'.$expl[1]
											   );
						break;
						case "hidden":
							$colums .= '$(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').replaceWith(function(){ '."\r\n";
							$colums .= 'return $("<input class=\"bordernone\" type=\"text\" id=\"row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\" />", {html: $(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html()})';
							$colums .= '.val($(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html());'."\r\n";
							$colums .= '});'."\r\n";
							$aryJson[] = array("colId"=>'row_'.base64url_encode($value['ucpid']).'_'.$expl[1],
												"colName"=>'setpost_'.$expl[1]
											   );
						break;
						case "select":
							$colums .= '$(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').replaceWith(function(){ '."\r\n";
							$colums .= 'return $("';
							$colums .= '<select class=\"bordernone\" name=\"ultimatePowerRanger\" id=\"row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\">';
							$data 	 = $kI['data'];
							foreach($data as $ky => $ku){
							$colums .= '<option value=\"'.$ky.'\">'.$ku.'</option>';
							}reset($data);
							$colums .= '</select>",';
							$colums .= '{html: $(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html()})';
							$colums .= '.val($(\'#row_'.base64url_encode($value['ucpid']).'_'.$expl[1].'\').html());'."\r\n";
							$aryJson[] = array("colId"=>'row_'.base64url_encode($value['ucpid']).'_'.$expl[1],
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
						$content .= '$(\'#saveedit_'.base64url_encode($value['ucpid']).'\').click(function(){'."\r\n";
						
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
						$jsond   .=  $i['colName'].': '.$i['colId'];
						}else{ 
						$jsond   .=  $i['colName'].': '.$i['colId'].',';
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
									 $(\'#edit_'.base64url_encode($value['ucpid']).'\').show();'."\r\n".'
									 $(\'#saveedit_'.base64url_encode($value['ucpid']).'\').hide();'."\r\n".'
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
							$content .= '<td class="row_data_x"><div style="width:100%" id="row_'.base64url_encode($value['ucpid']).'_'.$k.'">'.$v.'</div></td>';
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
									$(document).ready(function() {
										 
										var t = $(\'#dtlTableInline\').DataTable({
											"columnDefs": [{ "aTargets": [ 2,3 ],"bVisible":false}],
											"processing": true,
											"stateSave": true
										});
										 var counter = 1;
										$(\'#dtlTableInlineaddRow\').on( \'click\', function () {
											 if (counter == 1) {
											  t.row.add( ['; 
											$content .='\'\',';
											$content .='\'<input type="button" onclick="saveWoi()"/>\',';
											$nos = 1;
											
											
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
														$content .= '\'<input id="n_'.$ker.'_'.$noe.'" class=\"bordernone\" type="text"/>\'';
														}else{
														$content .= '\'<input id="n_'.$ker.'_'.$noe.'" class=\"bordernone\" type="text"/>\',';
														}
														break;
														case "select":
														$content .= '\'<select id="n_'.$ker.'_'.$noe.'" class=\"bordernone\">';
														foreach($vbtm['data'] as $yu => $iu){
														$content .= '<option value=\"'.$yu.'\">'.$iu.'</option>';
														}reset($vbtm);
														if($counte == $noe){
														$content .= '</select>\'';
														}else{
														$content .= '</select>\',';	
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
											
											$content .='] ).draw( false );';
											$content .='';
											
											/*
											$non = 1;
											foreach($listBtm as $ll => $pp){
												$expl = explode("_",$pp['name']);
												$idEx = 'n_'.$expl[1]."_".$non;
												$idNew = $expl[1]."_".$non;
												$content .= '$(\'#'.$idEx.'\').attr(\'id\', counter+\''.$idNew.'\');';
											
											$non++;
											}reset($listBtm);
											*/
											
											$content .= '}';
											$content .= 'counter++;';
											$content .= '
										 });';
											$content .='
									} );
									</script>
									';	
									$content .= '<script>';
									
									
									$content .= 'function saveWoi(){';
									
										$jsonH	  = "";
										$noi 	  = 1;
									foreach($listBtm as $uxi => $ixi){
										$explodi  = explode("_",$ixi['name']);
										$jsonH   .= 'var n_'.$explodi[1].'_'.$noi.' = $(\'#n_'.$explodi[1].'_'.$noi.'\').val();'."\r\n";
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
									$jsonA   .=  '"'.$ix['name'].'"'.': n_'.$explod[1].'_'.$noAdd."\r\n";
									}else{ 
									$jsonA   .=  '"'.$ix['name'].'"'.': n_'.$explod[1].'_'.$noAdd.','."\r\n";
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
	
	public function generateModal($modalProp){
		
			$sys        = route::input('sys');
			$subsys     = route::input('subsys');
			$id         = route::input('id');
			$mode       = route::input('mode');
			
			$modalPropEdit = $modalProp;
			$modalPropAdd  = $modalProp;
			//wvd($modalProp['tabname']); 
			if(isset($modalProp['tabname'])){ 
				$tabname = $modalProp['tabname'];
			}else{ 
				$tabname = "covid19";
			}
			
			$type 	  = ($modalProp['type'] == "edit_detail" ? "editDtl" : null);
		
			$content  = "";
			$content .= '
			<div class="modal fade" id="dataEdit'.$tabname.'">
					<div class="modal-dialog">
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
								//generate modal
								if(substr($k,0,4)== 'set_'){
									$explode  = explode("set_",$k);
									$content .= '<div class="row">';
									$content .= '<div class="form-group">';
									$content .= '<label >'.ucfirst(str_replace("_","",$explode[1])).'</label>';
									$content .= $v;
									$content .= '<small id="'.$k.'" class="form-text text-muted"></small>';
									$content .= '</div>';
									$content .= '</div>';
								}else if(substr($k,0,5)== 'seth_'){
									$explode  = explode("seth_",$k);
									$content .= '<div class="row">';
									$content .= '<div class="form-group">';
									$content .= $v;
									$content .= '<small id="'.$tabname.$k.'" class="form-text text-muted"></small>';
									$content .= '</div>'; 
									$content .= '</div>';
								}
							}
							
							$content .= '<input type="hidden" name="setunique_id" class="form-control" id="'.$tabname.'ucpid" value="">';
							$content .= '<input type="hidden" name="'.$tabname.'modeucp" class="form-control" id="'.$tabname.'modeucp" value="">';
							reset($modalPropAdd);
							$content .= '</div>';
				$content .='
						</div>
						<div class="modal-footer justify-content-between">
						  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  <input type="submit" value="submit"/>
						</div>
				
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
}