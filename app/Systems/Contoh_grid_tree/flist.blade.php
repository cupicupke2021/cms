
<table class="table table-bordered tree-basica ">
	<thead>
		<th>No</th>
		<th>itm_group</th>
		<th>itm_group_name</th>
		<th>itm_code</th>
		<th>itm_name</th>
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
		$content .= '<td></td>';
		$content .= '<td></td>';
		$content .= '<td></td>';
		$content .= '</tr>';
		}else{
		$idX = str_replace(".","",$val['id']); 
		$parent = str_replace(".","",$val['parent']); 
		$content .= '<tr class="treegrid-'.$idX.' treegrid-parent-'.$parent.'">';
		$content .= '<td>'.$val['id'].'</td>';
		$content .= '<td></td>';
		$content .= '<td></td>';
		$content .= '<td></td>';
		$content .= '</tr>';
		}
		
		
		$no++;
	}
		parseContent($content);
	?>
	<!-- 
	<tr class="treegrid-23456">
			<td>test</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>

	<tr class="treegrid-23457 treegrid-parent-23456">
			<td>test</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</tr>
	-->
	</tbody>
</table>

<script> 
$(document).ready(function() {
	$('.tree-basica').treegrid();
});
</script>











