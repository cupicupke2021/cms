<?php 


function selectOffice(){ 
$buss_part 		= config('tables.mst_buss_part');
$sSQL = "select id,name from $buss_part where type = 'O' "; 

// $sSQL 	= "select * from $table_group order by groupname asc";
$results 		= DB::select(DB::raw($sSQL));

if(!empty($results)){ 
    $data 	    = json_decode(json_encode($results), true);
    $row[''] = "Please Choose";
    foreach($data as $k => $v){
      $row[$v['id']] = $v['name'];
    }
}

return $row;
}



function getMaxnum($type,$sub_type){
 $code = $type.'-'.$sub_type;	
 //$table_mst_buss_part = config('tables.mst_buss_part_dev');	
 $sSQL = "SELECT MAX(SUBSTR(id,4,3))+1 AS nomor_urut 
		  FROM mst_buss_part_dev
          WHERE id LIKE '".$code."%'";
 $results 	= DB::select(DB::raw($sSQL));	

 if(!empty($results)){ 
    $data = json_decode(json_encode($results), true);
	//wvd($data);exit;
	$row['0'] = "Top";
	foreach($data as $k => $v){
		$row['nomor_urut'] = $v['nomor_urut'];
	}
    
 } 
  $kode = $type.'-'.$sub_type.$row['nomor_urut'];
  return $kode;
  
}

function getStage($originator,$trs_id,$module_id){	
$trs_sign_staging 			= config('tables.trs_sign_staging');
$trs_sign_staging_dtl 		= config('tables.trs_sign_staging_dtl');
$trs_his_position 			= config('tables.trs_his_position');
$trs_sign_log 				= config('tables.trs_sign_log');
$mst_biodata 				= config('tables.mst_biodata');
//GLOBAL $trs_sign_staging,$trs_sign_staging_dtl,$trs_his_position,$trs_sign_log,$mst_biodata;
//position 
$sSQL 		= "select a.position_id,b.email,b.name
			   from 
			   $trs_his_position a,$mst_biodata b
			   where 1=1 
			   and a.emp_id = b.id
			   and a.userid = '$originator'
			   and a.active_now = 'YES'
			   ";

$results 		= DB::select(DB::raw($sSQL));
if(!empty($results)){ 
    $data 	    = json_decode(json_encode($results), true);
}else{ 
	$data 		= null;
}
$email			= $data[0]['email'];
$name_email 	= $data[0]['name'];

if ($data != null){
		//while(list($k,$v) = @each($data)){
		foreach($data as $k => $v){
		//cari staging yang sama sama modul
		$position_id = $v['position_id'];
		$sSQL = "
			   select b.*,c.email,c.name_email,b.options
			   from 
			   $trs_sign_staging a, 
			   $trs_sign_staging_dtl b left join 
			   (select b.email,a.userid,b.name as name_email
			   from $trs_his_position a,$mst_biodata b
			   where 1=1 
			   and a.emp_id = b.id 
			   and a.active_now = 'YES'
			   ) c on b.userid  = c.userid
			   where 1=1 
			   and a.id = b.pid
			   and a.position_id = '$position_id'
			   and a.id_menu = '$module_id'
			   and b.stage not in (
				select stage from 
				$trs_sign_log a 
				where 1=1 
				and trs_id = '$trs_id'
				and module_id = '$module_id'
			   )
			   order by b.stage asc
			   ";
		
		$results2 	= DB::select(DB::raw($sSQL));

		if(!empty($results2)){
				$result	= json_decode(json_encode($results2), true);
				foreach($result as $dy => $dh){
				if(!empty($result[0]['userid'])){
					$target = array(
							"type"=>"user",
							"id_staging"=>$dh['id'],
							"destination"=>$dh['userid'],
							"stage"=>$dh['stage'],
							"apprv_id"=>$dh['apprv_id']); 
				} else if (!empty($dh['position_id'])){
					$target = array(
							 "type"=>"pos",
							 "id_staging"=>$dh['id'],
							 "destination"=>$dh['position_id'],
							 "stage"=>$dh['stage'],
							 "apprv_id"=>$dh['apprv_id']);		
				}else{
					$target = 0;		
				}
				switch($target['type']){ 
					case "user": 
					//property user
						$datasign[$dy]['destination']	= $target['destination'];
						$datasign[$dy]['stage']			= $target['stage'];
						$datasign[$dy]['status_ref']	= $target['apprv_id'];
						$datasign[$dy]['id_staging']	= $target['id_staging'];
						$datasign[$dy]['email']			= $result[0]['email'];
						$datasign[$dy]['name_email']	= $result[0]['name_email'];
						$datasign[$dy]['options']		= $result[0]['options'];
					break;
					case "pos":
						//ntar aja 
						$datasign 					= array(); 
						$datasign[$dy]['destination']	= $target['destination']; 
						$datasign[$dy]['stage']			= $target['stage'];
						$datasign[$dy]['id_staging']	= $target['id_staging'];
					break;
				}
				}reset($result);
		}else{
			$datasign['email']			= $email;
			$datasign['name_email']		= $name_email;	
		}

	}
	return $datasign;
}

}
