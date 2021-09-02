<?php 

function selectMenu(){ 
$table_sitemenu = config('tables.table_sitemenu');

$sSQL = "select * from $table_sitemenu order by description asc"; 

$results 		= DB::select(DB::raw($sSQL));

if(!empty($results)){ 
    $data 	    = json_decode(json_encode($results), true);
	$row['']	= '--Select One--';
	foreach($data as $k => $v){
	$row[$v['id']] = $v['description'];
	}

}

return $row;
}


function getVendor($type=null){
	$mst_buss_part 	= config('tables.mst_buss_part');
	
	if($type == null){
		$sSQLTy	= "and type='V'";
	}else{ 
		$sSQLTy	= "and type='$type'";
	}
	
	$sSQL = "
			 SELECT * FROM $mst_buss_part WHERE 1=1
			 $sSQLTy 
			 order by  name 
			 ";
	$results = DB::select(DB::raw($sSQL));	
	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['id']] = $v['id'].' - '.$v['name'];
		}
	}
	return $row;
}

function containsDecimal( $value ) {
    if ( strpos( $value, "." ) !== false ) {
		if(is_numeric( $value )){ 
        return true;
		}else{ 
		return false;
		}
    }
    return false;
}


function getCurrFormat($curr){
$mst_currency =  config('tables.mst_currency');

$sSQLCD		= "select num_sep,dec_sep from $mst_currency where id = '$curr'"; 
$results 	= DB::select(DB::raw($sSQLCD));
$data 	   	= json_decode(json_encode($results), true);

return $data;
}

function selectCurrency(){
 $table_currency =  config('tables.mst_currency');	
 $sSQL = "select * from $table_currency order by  name asc";
 $results 		= DB::select(DB::raw($sSQL));
  if(!empty($results)){ 
    $data 	    = json_decode(json_encode($results), true);
	$row['']	= '--Select One--';
	foreach($data as $k => $v){
	$row[$v['id']] = $v['name'];
	}

 }
 return $row;
}

function genMenuProp(){ 
	$data = array(
				  "add"=>"add",
				  "edit"=>"edit",
				  "delete"=>"delete",
				  "add_detail"=>"add_detail",
				  "edit_detail"=>"edit_detail", 
				  "delete_detail"=>"delete_detail",
				  "print"=>"print",
				  "excel"=>"export_excel",
				  "excelimp"=>"import_excel",
				  "excelimpdtl"=>"import_excel_dtl",
				  "other"=>"other"
	);
return $data;
}

function getInterval(){ 
	$data = array(
				""=>"--Select One--",
				"Daily"=>"Daily",
				"Monthly"=>"Monthly",
				"Yearly"=>"Yearly"
	);
return $data;
}

function getProcure(){ 
	$data = array(
				""=>"--Select One--",
				"Buy"=>"Buy",
				"Make"=>"Make",
	);
return $data;
}

function getSeries(){ 
	$data = array(
				""=>"--Select One--",
				"Manual"=>"Manual",
				"Automatic"=>"Automatic",
	);
return $data;
}

function getItemType(){
	$data = array(
				""=>"--Select One--",
				"RM"=>"Raw Material",
				"FG"=>"Finish Good",
				"SPM"=>"Support Material",
				"GEN"=>"General",
	);
return $data;
}

function getProductType($type) {
	$result = '';
	if($type == 'RM') {
		$result = 'Raw Material';
	} elseif ($type == 'FG') {
		$result = 'Finish Good';
	} elseif ($type == 'SPM') {
		$result = 'Support Material';
	} elseif ($type == 'GEN') {
		$result = 'General';
	}
	
	return $result;
}

function getItemTypeDtl($id){
	switch($id){
		case "RM":
		return "Raw Material"; 
		break;
		case "FG":
		return "Finish Good"; 
		break;
		case "SPM":
		return "Support Material"; 
		break;
		case "GEN":
		return "General"; 
		break;
		
	}
}

function getIncoTerm(){
	$data = array(
	               ""=>"--Select One--",
				   "COD"=>"(COD) Cash On Delivery",
				   "FOB"=>"(FOB) Free On Board",
				   "CIF"=>"(CID) Cost,Insurance and Freight",
				   "EXW"=>"(EXW) EX-WORKS"
	              );
	return $data;			  
}

function getItemGroup($dept=null,$itm_group=null){ 
	$mst_itm_group = config('tables.mst_itm_group');
	$mst_buss_dept = config('tables.mst_buss_dept');
	
	if($dept != null){
		$sSQLDept = "and a.dept_id = '$dept'";
	}else{ 
		$sSQLDept ="";
	}
	
	if($itm_group != null){
		$sSQLGroup ="and a.id = '$itm_group'";
	}else{ 
		$sSQLGroup = "";
	}
	
	$sSQL 			= "select a.*, b.name as dept_name
					   from $mst_itm_group a left join $mst_buss_dept b ON a.dept_id = b.id 
					   where 1=1
					   $sSQLDept
					   $sSQLGroup
					   order by id asc"; 
	$results 		= DB::select(DB::raw($sSQL));
	
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
		if($itm_group == null){
		$row[''] = '--Select One--';
		}
		foreach($data as $k => $v){
		
		$row[$v['id']] = $v['id'].'::'.$v['dept_name'].'::'.$v['name'];
		}
	}else{ 
		$row = array();
	}
	return $row;
}
function getItemDept(){
	$mst_itm_group = config('tables.mst_itm_group');
	$mst_buss_dept = config('tables.mst_buss_dept');
	
	$sSQL 			= "select a.id,a.name
					   from  $mst_buss_dept a 
					   where 1=1 
					   order by a.name asc";
					   
	$results 		= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
		$row[''] = '--Select One--';
		foreach($data as $k => $v){
		
		$row[$v['id']] = $v['name'];
		}
	}
	return $row;
}

function getTax(){ 
	$mst_tax = config('tables.mst_tax');	
	$sSQL = "select * from $mst_tax order by id asc"; 
	
	$results 		= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
		$row[''] = '--Select One--';
		foreach($data as $k => $v){
		
		$row[$v['id']] = $v['name'];
		}
	}
	return $row;
}


function getEmp(){ 
	$trs_his_position = config('tables.trs_his_position');	
	$mst_biodata = config('tables.mst_biodata');	
	$sSQL = "select a.*,b.name as emp_names from $trs_his_position a,$mst_biodata b 
			where 1=1 
			and a.emp_id = b.id 
			and a.active_now = 'YES'
			"; 
	
	$results 		= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
		$row[''] = '--Select One--';
		foreach($data as $k => $v){
		
		$row[$v['id']] = $v['emp_names'];
		}
	}
	return $row;
}

function getEmpDtl($his_id){ 
	$trs_his_position = config('tables.trs_his_position');	
	$mst_biodata = config('tables.mst_biodata');	
	$sSQL = "select a.*,b.name as emp_names from $trs_his_position a,$mst_biodata b 
			where 1=1 
			and a.emp_id = b.id 
			and a.active_now = 'YES'
			and a.id = '$his_id'
			"; 
	
	$results 		= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
		return array("emp_names"=>$data[0]['emp_names']);
	}else{ 
		return array("");
	}
	
}

function getGender(){ 
	$data = array(
				""=>"--Select One--",
				"Male"=>"Male",
				"Female"=>"Female",
	);
return $data;
}

function getGrade(){
	$data = array(
				""=>"--Select One--",
				"Junior"=>"Junior",
				"Senior"=>"Senior",
	);
return $data;
}

function getStatusEmp(){
	$data = array(
				""=>"--Select One--",
				"Contract"=>"Contract",
				"Probation"=>"Probation",
				"Permanent"=>"Permanent",
	);
return $data;
}

function getEndStatusEmp(){
	$data = array(
				""=>"--Select One--",
				"Dismissal"=>"Dismissal",
				"End_Contract"=>"End_Contract",
				"Resign"=>"Resign",
				"Retire"=>"Retire",
				"Dead"=>"Dead",
	);
return $data;
}

function getActive(){
	$data = array(
				""=>"--Select One--",
				"Yes"=>"Yes",
				"No"=>"No",
	);
return $data;
}

function selectDept(){ 
	$mst_buss_dept 		= config('tables.mst_buss_dept');
	$sSQL				= "select * from $mst_buss_dept "; 
	
	$results 	= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
	}else{ 
		$data 		= array();
	}

	$row[null] = "--Select One--";
	foreach($data as $k=>$v){
			$row[$v['id']] = $v['name'];
	}
	
	return $row;
}

function selectBusPart($where=null){ 
	$mst_buss_part 		= config('tables.mst_buss_part');
	$sSQL				= "select a.* from $mst_buss_part a 
							where 1=1 
							and a.type = '$where'
							"; 
	
	$results 	= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
	}else{ 
		$data 		= array();
	}

	$row[null] = "--Select One--";
	foreach($data as $k=>$v){
			$row[$v['id']] = $v['name'];
	}
	
	return $row;
}

function getWhsLocType(){
	$data = array(
				""=>"--Select One--",
				"Rak"=>"Rak",
				"Tingkat"=>"Tingkat",
				"Partisi"=>"Partisi",
	);
return $data;
}

function getWhsLocDepth($type){
	$depth = "";
	switch($type){
		case 'Rak':
		  $depth = "1"; break;
		case 'Tingkat':
		  $depth = "2"; break;
		case 'Partisi':
		  $depth = "3"; break;
	}
return $depth;
}

function getProcess(){
	$data = array(
				""=>"--Select One--",
				"SEND"=>"SEND",
				"RECEIVE"=>"RECEIVE",
	);
return $data;
}

function getDataInv(){ 
	$trs_inventory 		= config('tables.trs_inventory');
	$sSQL				= "select * from $trs_inventory "; 
	
	$results 	= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
	}else{ 
		$data 		= array();
	}

	$row[''] = "--Select One--";
	foreach($data as $k=>$v){
			$row[$v['id']] = $v['itm_name'].'_ _'.$v['qty'].'_ _'.$v['wh_code'].'_ _'.number_format($v['price'],2,",",".");
	}
	
	return $row;
}

function getDataInvDtl(){ 
	$trs_inventory_dtl 	= config('tables.trs_inventory_dtl');
	$sSQL				= "select * from $trs_inventory_dtl "; 
	
	$results 	= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
	}else{ 
		$data 		= array();
	}

	$row[''] = "--Select One--";
	foreach($data as $k=>$v){
			$row[$v['id']] = $v['itm_name'].'_ _'.$v['qty'].'_ _'.$v['wh_code'].'_ _'.number_format($v['price'],2,",",".");
	}
	
	return $row;
}

function getWarehouseLocV($wh_loc){ 
$v_wh_loc  						= config('tables.v_wh_loc');

$sSQL = "select a.* from $v_wh_loc a 
		where 1=1 
		and a.id = '$wh_loc'
		";
	
$results 	= DB::select(DB::raw($sSQL)); 
$data 	    = json_decode(json_encode($results), true);
	
return $data;
}

function getWarehouseLocParent($parent,$wh_loc){ 
$v_wh_loc  	= config('tables.v_wh_loc');

$sSQL = "select a.* from $v_wh_loc a 
		 where 1=1 
		 and a.loc_name = '$wh_loc'
		 and a.parent = '$parent'
		 ";

$results 	= DB::select(DB::raw($sSQL)); 
$data 	    = json_decode(json_encode($results), true);


return $data;
}


function getWarehouseLoc($id=null, $depth=null,$storage=null,$id_storage=null){
	$mst_warehouse_loc  			= config('tables.mst_warehouse_loc');
	$v_wh_loc  						= config('tables.v_wh_loc');
	$trs_storaging  				= config('tables.trs_storaging');
	$trs_inventory_dtl  			= config('tables.trs_inventory_dtl');
	
	if($id != null) {
		$sSQLID	 = "and id = '$id'";
	}else{ 
		$sSQLID = "";
		$row[''] = "--Select One--";
	}
	
	if($depth != null) { 
		$sSQLDepth	 = " and depth > '$depth'";
	}else{ 
		$sSQLDepth = "";
		$row[''] = "--Select One--";
	}
	
	if($storage != null) { 
		$sSQLSto	 = " and id not in (select a.wh_loc  from $trs_inventory_dtl a where 1=1 group by a.wh_loc)";
		$sSQLSto	 .= " and id not in (select a.wh_loc  from $trs_storaging a where 1=1 group by a.wh_loc)";
		$sSQLSto 	 .= "
						union all 
						select b.wh_loc,b.wh_path 
						from $trs_storaging b 
						where 1=1 
						and b.id = '$id_storage'
						";
	}else{ 
		$sSQLSto 	 = "";
		$row[''] 	 = "--Select One--";
	}
	
	
	$sSQL  = "select a.id,a.path from $v_wh_loc a 
			  where 1=1 
			  $sSQLID
			  $sSQLDepth
			  $sSQLSto
			  ";

	$results 	= DB::select(DB::raw($sSQL));
	// wvd($sSQL);exit;
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
	}else{ 
		$data 		= array();
	}
	
	if($id != null) {
		$row = $data;
	}else{
	foreach($data as $k=>$v){
		$row[$v['id']] = $v['path'];
	}
	}
	
	return $row;
}


function selectBio(){
 $mst_biodata = config('tables.mst_biodata');
 
 $sSQL = "select a.* from $mst_biodata a where 1=1 order by a.name asc";
 $results 	= DB::select(DB::raw($sSQL));
 if(!empty($results)){ 
 	$data 	    = json_decode(json_encode($results), true);
 }else{ 
 	$data 		= array();
 }
 
 $row[''] = "--Select One--";
 foreach($data as $k=>$v){
 		$row[$v['id']] = $v['name'];
 }
 
 return $row;	
}

function selectAsset(){
 $mst_asset = config('tables.mst_asset');
 
 $sSQL = "select a.* from $mst_asset a 
		  where 1=1 
		  and a.in_use != '1'
		  order by a.name asc";
 $results 	= DB::select(DB::raw($sSQL));
 if(!empty($results)){ 
 	$data 	    = json_decode(json_encode($results), true);
 }else{ 
 	$data 		= array();
 }
 
 $row[''] = "--Select One--";
 foreach($data as $k=>$v){
 		$row[$v['id']] = $v['code'];
 }
 
 return $row;	
}

function selectWarehouse($status=null){
 $mst_warehouse = config('tables.mst_warehouse');
 
 
 if($status != null){
	$sSQLS = "and a.status = '$status'";
 }else{ 
	$sSQLS = "";
 }
 
 
 
 $sSQL = "select a.* from $mst_warehouse a 
		  where 1=1 
		  and a.status != 'INTRANSIT' 
		  $sSQLS
		  order by a.name asc";
		  
 $results 	= DB::select(DB::raw($sSQL));
 if(!empty($results)){ 
 	$data 	    = json_decode(json_encode($results), true);
 }else{ 
 	$data 		= array();
 }
 
 $row[''] = "--Select One--";
 foreach($data as $k=>$v){
 		$row[$v['id']] = $v['name'];
 }
 
 return $row;	
}

function selectAssetGrp($id){
 $mst_asset = config('tables.mst_asset');
 
 $sSQL = "select a.* from $mst_asset a where id ='$id'";
 $results 	= DB::select(DB::raw($sSQL));
 if(!empty($results)){ 
 	$data 	    = json_decode(json_encode($results), true);
	 $row 			= $data[0]['asset_group'];
 }else{ 
 	$data 		= "sales";
	$row		= $data;	
 }

 return $row;	
}

function getShift(){
	$mst_shift = config('tables.mst_shift');
	$sSQL = "SELECT * FROM $mst_shift
			 WHERE  CURRENT_TIME() >= start_time 
			    AND CURRENT_TIME() <= end_time";
	$results 	= DB::select(DB::raw($sSQL));
	 if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
	 }else{ 
		$data 		= array();
	 }
   // $row = $data[0]['id'];
    $row = array("id" =>$data[0]['id'],
	"name"=>$data[0]['name']
	);
    return $row;		 
}

/*
$data = array(
				""=>"--Select One--",
				"Rak"=>"Rak",
				"Tingkat"=>"Tingkat",
				"Partisi"=>"Partisi",
	);
*/

function generateNeatDropdown($array,$id,$tabname,$add_link=null,$coa_code=null){

		$array_temp 	= $array;
		
		$content 		= "";
		/*
		$content 		= '<input class="form-control '.$tabname.$id.'" id="'.$tabname.$id.'" value="'.$coa_code.'" name="'.$name.'"/>';
		if($add_link != null){ 
		$content 		.= '<a style="margin-left:2px" onClick = window:open(\''.$add_link.'\',\'popUpWindow\',\'width=500,height=300\') href="#" class="float-right"><b>New</b></a>';
		}
		*/
		//$content 	    .= '<script language="javascript">';
		$content 		.= '
		$(\'#'.$id.'\').inputpicker({
		  data:[
				 ';
		foreach($array as $k=>$v){
		$content .='{';
				$content .= 'value:"'.$k.'",';
				foreach($v as $kk => $vv){
					$content .=  $kk.':"'.$vv.'",';
					$aryfield[] = $kk;
				}
				reset($v);
		$content .= '},'."\r\n";
		}
		
		foreach($aryfield as $t => $y){
			$aryG[$y] = $y;
		}
			$tempAryG = $aryG;

		reset($array);

		$content .='
		  ],
		  fields:[
		';
			$content .= '{name:\'value\',text:\'id\'},';
			//while(list($d,$f) = each($tempAryG)){
			foreach($tempAryG as $d => $f){
				$content .= '{name:\''.$d.'\',text:\''.$d.'\'},';
			}
		$content .= '
		  ],
		  headShow: true,
		  fieldText : \'\', 
		  fieldValue: \'value\'
		});
		';

		//$content .= '</script>';
		
		return $content;
	}
	
function getItemVal(){
	$mst_itm_mat 	= config('tables.mst_itm_mat');
	$sSQL = "SELECT * FROM $mst_itm_mat order by  name ";
	$results = DB::select(DB::raw($sSQL));	
	
	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['itm_code']] = $v['itm_code'].' - '.$v['itm_type'].' - '.$v['name'];
		}
	}
	return $row;
}	


function selectRackGroup(){
$v_wh_loc 		= config('tables.v_wh_loc');
$sSQL  			= "
						   select a.id,a.loc_name as name,a.wh_code
						   from $v_wh_loc a 
						   where 1=1 
						   and a.depth = '0'
						   group by a.id
						   ";
 $results 	= DB::select(DB::raw($sSQL));
 if(!empty($results)){ 
 	$data 	    = json_decode(json_encode($results), true);
 }else{ 
 	$data 		= array();
 }
					
 $row[''] = "--Select Rack Group--";
 foreach($data as $k=>$v){
 		$row[$v['id']] = $v['name'];
 }
 
 return $row;
}


function getItemVar($itm_code=null){
	$mst_itm_mat_varian 	= config('tables.mst_itm_mat_varian');
	
	$sSQL_ITM	= ($itm_code!=null?"and itm_code = '$itm_code'":""); 
	
	$sSQL = "SELECT * FROM 
			 $mst_itm_mat_varian 
			 where 1=1
			 $sSQL_ITM
			 order by  name asc";
	
	$results = DB::select(DB::raw($sSQL));	
	
	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['id']] = $v['name'];
		}
	}
	return $row;
}	

function getUom(){
	$mst_units 	= config('tables.mst_units');
	
	$sSQL = "SELECT * FROM $mst_units order by  id asc ";
	
	$results = DB::select(DB::raw($sSQL));	
	
	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['id']] = $v['symbol'];
		}
	}
	return $row;
}

function getParentWhLoc($param){
	$mst_warehouse_loc 	= config('tables.mst_warehouse_loc');
	
	$sSQL = "SELECT a.id, a.code, b.code parent_code, c.code parent_codes FROM mst_warehouse_loc a
			LEFT JOIN mst_warehouse_loc b ON a.parent = b.id
			LEFT JOIN mst_warehouse_loc c ON b.parent = c.id
			";

	if(!empty($param)) {
		$sSQL .= "where a.id != $param";
	}
	// wvd($sSQL);exit;
	
	$results = DB::select(DB::raw($sSQL));	

	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		// wvd($data);exit;
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['id']] = $v['code'];
			if(!empty($v['parent_code'])) {
				$row[$v['id']] = $v['parent_code'].'/'.$v['code'];
			}
			if(!empty($v['parent_codes'])) {
				$row[$v['id']] = $v['parent_codes'].'/'.$v['parent_code'].'/'.$v['code'];
			}
			
		}
	}
	return $row;
}

function getRequestBy(){
	$mst_biodata        = config('tables.mst_biodata');
	$trs_his_position   = config('tables.trs_his_position');

	$sSQL = "SELECT a.id, a.name, b.emp_number FROM $mst_biodata a
			 LEFT JOIN $trs_his_position b ON a.id = b.emp_id
			 WHERE b.active_now = 'Yes'";
	
	$results = DB::select(DB::raw($sSQL));
	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['id']] = $v['name'].' - '.$v['emp_number'];
		}
	}
	return $row;
}

function selectAssetActive(){
	$trs_inventory_dtl 	= config('tables.trs_inventory_dtl');
	$mst_asset 			= config('tables.mst_asset');
	$trs_storaging 		= config('tables.trs_storaging');
	$trs_picking 		= config('tables.trs_picking');
	
	$sSQL 			= "select a.id,a.name
					   from $mst_asset a 
					   where 1=1
					   and a.in_use != '1'
					   order by name asc
					   ";
	
	$results 		= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
	$data 	    = json_decode(json_encode($results), true);
	$row[null] = "--Select--";
	foreach($data as $k => $v){
	$row[$v['id']] = $v['name'];
	}
	}else{ 
		$row = array();
	}
	return $row;
}

function getItemCondition(){
	$mst_condition 	= config('tables.mst_condition');
	$sSQL = "SELECT * FROM $mst_condition order by  id asc ";
	$results = DB::select(DB::raw($sSQL));
	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['id']] = $v['name'];
		}
		$row 	= $row;

	}else{ 
		$row = array();
	}
		return $row;
}

function getCondition(){
	$mst_condition 	= config('tables.mst_condition');
	
	$sSQL = "SELECT * FROM $mst_condition order by  id asc ";
	
	$results = DB::select(DB::raw($sSQL));	
	
	if(!empty($results)){
	    $data = json_decode(json_encode($results), true);
		$row[''] = "-- Select One --";
		foreach($data as $k => $v){
			$row[$v['id']] = $v['id'].":::".$v['name'];
		}
	}
	return $row;
}

function erpRedirect($url,$toast=null,$message=null,$type=null){
	
	if($message == null){
		$message = "default";
	}else{ 
		$message = $message;
	}
	
	if($type == null){
		$type = "default";
	}else{ 
		$type = $type;
	}
	
	if($toast == null){
		$toast = "no";
	}else{ 
		$toast = "yes";
	}
	
	
	/*
	$contentR  = '<script>';
	$contentR .= 'document.cookie = "erp_toastParam='.$toast.'";';
	$contentR .= 'document.cookie = "erp_toastMessage='.$message.'";';
	$contentR .= 'document.cookie = "erp_toastType='.$type.'";';
	$contentR .= 'window.location = "'.$url.'"';
	$contentR .= '</script>';
	*/ 
	$contentR  = '<script>';
	$contentR .= 'sessionStorage.setItem("erp_toastParam", "'.$toast.'");';
	$contentR .= 'sessionStorage.setItem("erp_toastMessage", "'.$message.'");';
	$contentR .= 'sessionStorage.setItem("erp_toastType", "'.$type.'");';
	$contentR .= 'window.location = "'.$url.'"';
	$contentR .= '</script>';
	
	parseContent($contentR);
}