<?php 

function selectParent(){ 

$table_sitemenu = config('tables.table_sitemenu');

$sSQL 			= "select * from $table_sitemenu order by description asc";
$results 		= DB::select(DB::raw($sSQL));

if(!empty($results)){ 
    $data 	    = json_decode(json_encode($results), true);
	
	$row['0'] = "Top";
	foreach($data as $k => $v){
	$row[$v['id']] = $v['description'];
	}

}

return $row;
}