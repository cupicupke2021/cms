<?php 
function wvd($value){ 
    echo "<pre>";
    print_r($value);
    echo "</pre>";
}

function parseContent($content){ 
    echo $content;
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}


function erpUniqueId($length) {
	
	$sys        = route::input('sys');
    $characters = date('Ymdhis').'0123456789';
    $charactersLength = strlen($characters);
    $uniqueID = '';
    for ($i = 0; $i < $length; $i++) {
        $uniqueID .= $characters[rand(0, $charactersLength - 1)];
    }
	
	$explode = explode("_",$sys); 
	
	$char= "";
	foreach($explode as $key => $val){
	$char .= substr($val, 0, 1);
	}
	
	$uniqueID = $char.$uniqueID;
    return $uniqueID;
}


function erpInsert($tablename,$ary,$method){
	/*
switch($method){ 
    case "SINGLE":
		$trs_his_position 	= config('tables.trs_his_position');
		
		
		$sSQL				= "select a.*,b.name as emp_name 
							   from 
							   $trs_his_position a,$mst_biodata b
							   where 1=1 
							   and a.emp_id = b.id
							   "; 
	
		
		$results 	= DB::select(DB::raw($sSQL)); 
		$userData 	= json_decode(json_encode($results),true);
		//$userData    		= DB::table($trs_his_position)->where('userid', $ary['sUserid'])->where('active_now','yes')->first();
		
		$emp_number 		= (empty($userData['emp_number'])?"DEFF":$userData['emp_number'];
		$emp_name 			= (empty($userData['emp_name'])?"DEFF":$userData['emp_name']);
		$compid 			= (empty($userData['comp_id'])?"DEFF":$userData['comp_id']);
		$depart				= (empty($userData['department'])?"DEFF":$userData['department']);
		$position			= (empty($userData['position_id'])?"DEFF":$userData['position_id']);
        DB::table($tablename)->insert([
            'session' 		=> $ary['sSession'],
            'userid' 		=> $ary['sUserid'],
            'date' 			=> date("Y-m-d H:i:s"),
            'last_activity' => date("Y-m-d H:i:s"),
            'comp_id'   	=> $compid,
            'department'   	=> $depart,
			'position_id'   => $position,
			'emp_number'    => $emp_number,
			'emp_name'   	=> $emp_name,
            'id' => erpUniqueId(8)
        ]);
    break;
    case "BATCH": 
    
    break;
		
}    
 */  
}
function prePost($data){
	// wvd($data);exit;
	
	$decimal_place = null;
	$datax = array();
	$id = null;
	foreach($data as $k => $v) {
	if(substr($k,0,8)== 'setpost_'){
		$expl 	= explode("setpost_",$k);
		$field 	= $expl[1];
		$datax[$field] = $v; 
	}else if(substr($k,0,16)== 'setpostnum_'){
		$expl 		= explode("setpostnum_",$k);

		$field 		= $expl[1];
		$tail 		= substr($v,$decimal_place);
		$xplode		= @explode($tail,$v);
		$strRep  	= preg_replace('~[\\\\/:*?",. <>|]~', '', $xplode[0]);
		$strRepT 	= preg_replace('~[\\\\/:*?",.<>|]~', '.', $tail);

		$val 	 = $strRep.$strRepT;
		//wvd($val); 
		$datax[$field] = $val.'00';
	}else if(substr($k,0,10)=='setunique_'){
		$id = $v;
	}
	
	}reset($data);
	return array("data"=>$datax,"id"=>$id);
} 


function selectUser(){ 
	$table_user 		= config('tables.table_user');
	$sSQL				= "select * from $table_user "; 
	
	
	$results 	= DB::select(DB::raw($sSQL));
	if(!empty($results)){ 
		$data 	    = json_decode(json_encode($results), true);
	}else{ 
		$data 		= array();
	}

	$row[null] = "--Select One--";
	foreach($data as $k=>$v){
			$row[$v['userid']] = $v['username'];
	}
	
	return $row;
}

function selectCurr(){
$mst_currency 	= config('tables.mst_currency');
$sSQL 			= "select * from $mst_currency
				   where 1=1 
				  "; 
		 
$results 		= DB::select(DB::raw($sSQL));
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

function uniqDocnum($sys,$table,$docnum) {

	$data = DB::table($table)->select('docnum')
			->where('docnum', trim($docnum))
			->first();
	// wvd($data);exit;
	$data = json_decode(json_encode($data), true);
	$result = false;
	if(!empty($data)) {
		$result = true;
	}
	
	return $result;
}