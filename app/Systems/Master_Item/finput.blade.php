<style> 
#price { 
width:50% !important;
float:left;
}
#currency_chosen{ 
width:50% !important;
float:right !important;
}
</style>
@php echo $aryCont['genform'] @endphp

<br>
<br>
<script>
$( document ).ready(function() {

  var tabname = getCookie("toptabactive");
  if (tabname !== '') {
    $('.'+tabname).addClass("active");
  }else{
    $(".toptabclass1").addClass("active");
  }

  $("#variant").click(function(){
  var valueTab = $("#toptabclass1").attr('id');
  document.cookie = "toptabactive=toptabclass1";
  //location.reload();
  });
  $("#spec").click(function(){
  var valueTab = $("#toptabclass2").attr('id');
  document.cookie = "toptabactive=toptabclass2";
  //location.reload();
  });
  
  $("#itmspec").click(function(){
  var valueTab = $("#toptabclass3").attr('id');
  document.cookie = "toptabactive=toptabclass3";
  //location.reload();
  });

  $("#variantspec_id").change(function(){
        var text = $("#variantspec_id option:selected").text();
        $("#variantspec_name").val(text);
  });

});
</script>
@php 
if(isset($aryCont['gridDtl'])){
@endphp
<div class="detail_dbgrid">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="pay">
        <li class="toptabclass1" ><a href="#toptabclass1" class="toptabclass1" id="variant" data-toggle="tab">Varian</a></li>
        <li class="toptabclass2" ><a href="#toptabclass2" class="toptabclass2" id="spec" data-toggle="tab">Varian Spec</a></li>
        <li class="toptabclass3" ><a href="#toptabclass3" class="toptabclass3" id="itmspec" data-toggle="tab">Item Spec</a></li>
    </ul> 
    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane toptabclass1" id="toptabclass1">
      <br>
        @php
        parseContent($aryCont['gridDtl']);
        @endphp
      </div>
      <div role="tabpanel" class="tab-pane toptabclass2" id="toptabclass2">
      <br>
        @php
        parseContent($aryCont['gridDtl2']);
        @endphp
      </div>
	  <div role="tabpanel" class="tab-pane toptabclass3" id="toptabclass3">
      <br>
        @php
		parseContent($aryCont['gridDtl3']);
        @endphp
      </div>
    </div>
</div>
@php 
}
@endphp