<table class="table table-bordered tree-basic" id="myTable">
	<thead>
		<th>Item Group</th>
		<th>Item Group Name</th>
		<th>Item Code</th>
		<th>Item Name</th>
		<th>Variant Name</th>
		<th>Price</th>
	</thead> 
	<tbody>
	
	<?php 
	$no = 1;
	$dataGrp = $aryCont['dataGrp'];
	$content = "";
	foreach($dataGrp as $key => $val){		
		if($val['parent'] == 0){
			
			$idX = str_replace(".","",$val['id']); 
			
			$content .= '<tr class="treegrid-'.$idX.'">';
			$content .= '<td>'.$val['id'].'</td>';
			$content .= '<td>'.$val['itm_group_name'].'</td>';
			$content .= '<td>'.$val['itm_code'].'</td>';
			$content .= '<td>'.$val['itm_name'].'</td>';
			$content .= '<td>'.$val['itm_varian'].'</td>';
			$content .= '<td>'.$val['price'].'</td>';
			$content .= '</tr>';
		}else{
			$idX = str_replace(".","",$val['id']); 
			$parent = str_replace(".","",$val['parent']); 
			$content .= '<tr class="treegrid-'.$idX.' treegrid-parent-'.$parent.'">';
			$content .= '<td>'.$val['id'].'</td>';
			$content .= '<td>'.$val['itm_group_name'].'</td>';
			$content .= '<td>'.$val['itm_code'].'</td>';
			$content .= '<td>'.$val['itm_name'].'</td>';
			$content .= '<td>'.$val['itm_varian'].'</td>';
			$content .= '<td>'.$val['price'].'</td>';
			$content .= '</tr>';

			
		}
		
		$no++;
	}
		parseContent($content);
	?>
	</tbody>
</table>

<script> 
$(document).ready(function() {
	$('.tree-basic').treegrid();
	// $('#myTable').DataTable();
});
</script>











