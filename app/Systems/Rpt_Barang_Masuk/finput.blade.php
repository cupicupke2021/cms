@php echo $aryCont['genform'] @endphp

<br>
<br>
<script>
$( document ).ready(function() {

 $('input').attr('autocomplete','off');
 
var tabname = getCookie("toptabactive");
if (tabname !== '') {
	$('.'+tabname).addClass("active");
}else{ 
	$(".toptabclass1").addClass("active");
}

$('#type').change(function() {

   let type = $('#type').val();
    var $sub_type = $("#sub_type");
   if(type == 'C'){
	   //alert('C');
	   $sub_type.empty();
       $sub_type.append("<option value='U'>UMUM</option><option value='L'>LAIN-LAIN</option>");
   }else if(type == 'V'){
	   //alert('Not C');
	   $sub_type.empty();
       $sub_type.append("<option value='S'>SEAFOOD</option><option value='L'>LAIN-LAIN</option><option value='J'>BARANG JADI</option>");
   }else{
	   $sub_type.empty();
       $sub_type.append("<option value='-'></option>");
   }
});

@php 
$link = url('/').'/system/'.$aryCont['sys'].'/'.$aryCont['subsys'].'/dummy/headless/ajax?setpost_idpo=';
@endphp 

$('#po').change(function(){
  var id = null;
    let po_num = $('#po').val();
	let vendor = $('#vendor').val();
	let  = $('#currency').val();
	let remark = $('#remark').val();
	if(po_num != 'D'){
		$.ajax({
		url : "@php echo $link @endphp"+po_num,
		method : "GET",
		data : {ftype: id},
		async : false,
		dataType : 'json',
			success: function(data){
				//console.log(data);
				alert(data.remark);
				
			}
		});
	}else{
	   alert('D');	
	}
});


$("#pay").click(function(){
var valueTab = $("#toptabclass1").attr('id');
document.cookie = "toptabactive=toptabclass1";
//location.reload();
});

});
</script>


@php 
if(isset($aryCont['gridDtl'])){
@endphp
<div class="detail_dbgrid">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="pay">
        <li class="toptabclass1" ><a href="#toptabclass1" class="toptabclass1" id="pay" data-toggle="tab">Contact Person</a></li>
    </ul> 
    <!-- Tab panes -->
    <div class="tab-content">
    <div role="tabpanel" class="tab-pane toptabclass1" id="toptabclass1">
		<br>
		  @php
			parseContent($aryCont['gridDtl']);
		  @endphp
	</div>
    </div>
</div>
@php 
}
@endphp