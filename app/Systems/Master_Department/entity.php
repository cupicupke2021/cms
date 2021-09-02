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


