<?php 


function selectGroup(){ 
$table_group 		= config('tables.table_group');
$sSQL = "select * from $table_group "; 

$sSQL 			= "select * from $table_group order by groupname asc";
$results 		= DB::select(DB::raw($sSQL));

if(!empty($results)){ 
    $data 	    = json_decode(json_encode($results), true);
	
	$row['0'] = "Top";
	foreach($data as $k => $v){
	$row[$v['id_group']] = $v['groupname'];
	}

}

return $row;
}

function getMaxnum($type,$sub_type){
 if($type == 'O'){	
    $code = $type;	
    $sSQL = "SELECT MAX(SUBSTR(id,2,3)) AS nomor_urut 
		      FROM mst_buss_part_dev
		      WHERE id LIKE '".$code."%'";
 }else{
    $code = $type.'-'.$sub_type;	
    $sSQL = "SELECT MAX(SUBSTR(id,4,3)) AS nomor_urut 
             FROM mst_buss_part_dev
             WHERE id LIKE '".$code."%'";	 
 }		  
 $results = DB::select(DB::raw($sSQL));	

 if(!empty($results)){ 
    $data = json_decode(json_encode($results), true);
	//wvd($data);exit;
	$row['0'] = "Top";
	foreach($data as $k => $v){
		$row['nomor_urut'] = $v['nomor_urut'];
	}
	$no_urut = str_pad($row['nomor_urut']+1, 3, "0", STR_PAD_LEFT);  
  }else{
	$no_urut = '001';  
  }
  
  if($type == 'O'){
    $kode = $type.$no_urut; 
 }else{
	$kode = $type.'-'.$sub_type.$no_urut;
  } 
  return $kode;
  
}


