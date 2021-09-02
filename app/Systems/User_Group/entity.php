<?php 

function selectGroup(){ 
$table_group 		= config('tables.table_group');
$sSQL 				= "select * from $table_group "; 

$sSQL 			= "select * from $table_group order by groupname asc";
$results 		= DB::select(DB::raw($sSQL));

if(!empty($results)){ 
    $data 	    = json_decode(json_encode($results), true);
	
	$row['0'] = "Top";
	foreach($data as $k => $v){
	$row[$v['id']] = $v['groupname'];
	}

}

return $row;
}