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
        <li class="toptabclass1" ><a href="#toptabclass1" class="toptabclass1" id="pay" data-toggle="tab">Permision Detail</a></li>
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