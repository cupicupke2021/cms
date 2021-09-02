<!DOCTYPE html>
<?php echo "test" ?>
{{ $body }}
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="@php public_path() @php/assets/fonts/css/font-awesome.min.css">
<!-- Theme style -->
<!-- <link rel="stylesheet" href="https://adminlte.io/themes/v3/dist/css/adminlte.min.css">-->
<link rel="stylesheet" href="@php public_path() @php/assets/admin_lte/dist/css/AdminLTE.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="@php public_path() @php/assets/admin_lte/bower_components/Ionicons/css/ionicons.min.css">

<link rel="stylesheet" href="@php public_path() @php/assets/admin_lte/dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="@php public_path() @php/assets/js/bootstrap-select.css">
  <!--
  <script src="https://publiclab.github.io/Leaflet.DistortableImage/node_modules/leaflet/dist/leaflet.js" type="text/javascript" charset="utf-8"></script>

  <script type="text/javascript" src="@php public_path() @php/assets/leaflet/leaflet.label.js"></script>
  <script type="text/javascript" src="@php public_path() @php/assets/leaflet/leaflet-realtime.js"></script>
  -->
<script type="text/javascript" src="@php public_path() @php/assets/js/jquery.min.js"></script>

<script type="text/javascript" src="@php public_path() @php/assets/js/autocomplete.js"></script>
<script type="text/javascript" src="@php public_path() @php/assets/js/jquery.mask.js"></script>
  
<link rel="stylesheet" href="@php public_path() @php/assets/inputpicker/inputpick/src/jquery.inputpicker.css">
<script type="text/javascript" src="@php public_path() @php/assets/inputpicker/inputpick/src/jquery.inputpicker.js"></script>

<script type="text/javascript" src="@php public_path() @php/assets/maxan/js/jquery.treegrid.js"></script>
<link rel="stylesheet" href="@php public_path() @php/assets/maxan/css/jquery.treegrid.css">

<script type="text/javascript" src="@php public_path() @php/assets/js/jquery.highlight-within-textarea.js"></script>
  <!-- 
  <script type="text/javascript" src="https://www.jqueryscript.net/demo/store-form-values-cookies-inputstore/inputStore.jquery.js"></script>
  -->
<link rel="stylesheet" href="@php public_path() @php/assets/js/jquery.highlight-within-textarea.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css">
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"></script>
  
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="@php public_path() @php/assets/js/bootstrap-select.css">
<!-- Google Font -->
<link rel="stylesheet" href="@php public_path() @php/assets/js/font_google.css">
<script src="@php public_path() @php/assets/js/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '.wswg',
    menubar: true
  });

$(function() {
	//alert("test");
    //$(".chosen").chosen();
})

function onCancelInput(link){
    window.location.href = link;
}

function isconfirm(url_val){
    //alert(url_val); 
    if(confirm('Are you sure ?') == false)
    {
        return false;
    }
    else
    {
        location.href=url_val;
    }
}

function redirected(url_val){
    location.href=url_val;
}

function goBack() {
  window.history.back();
}

$(document).on('click', '.gridCheck', function() {
		//alert($(this).val());
		valueCh = $(this).val();
		var outputCek = "";
        $(".gridCheck:checked").each(function() {
            outputCek += $(this).val() + ",";
        }); 
        $("#bufferCheckGrid").val(outputCek.trim().slice(0,-1));
});
$(document).on('click', '.gridCheckAll', function() {
		$('.gridCheck').not(this).prop('checked', this.checked);
		var outputCek = "";
        $(".gridCheck:checked").each(function() {
            outputCek += $(this).val() + ",";
        }); 
        $("#bufferCheckGrid").val(outputCek.trim().slice(0,-1));
});
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

$( document ).ready(function() {
$("#multipleDelBut").click(function(){
    if(confirm("Are you sure you want to delete All?")){
        $("#delmultiply").submit();
		
    }else{
        return false;
    }
});
$('.numberForm').mask('000.000.000.000.000,00', {reverse: true});

var tabname = getCookie("tabactive");
if (tabname !== '') {
	$('.'+tabname).addClass("active");
}else{ 
	$(".tabclass1").addClass("active");
}

$(document).ready(function(){
    $('form').attr('autocomplete', 'off');
});


});

function refreshParent() {
  window.opener.location.reload();
}
</script>
<style> 
.table{ 
padding:17px;
}
.table-bordered { 
border:1px solid #acacac;
}

.clear{ 
	clear:both;
}
tr { height: 5px }

.detail_dbgrid{
  border-left:1px dotted #acacac;
  border-right:1px dotted #acacac;
  border-bottom:1px dotted #acacac;
  border-top:1px dotted #acacac;
  padding:10px;
  border-radius:3px;
background:#fff;
}
#mynetwork {
      width: 100%;
      height: 600px;
      border: 1px solid lightgray;
    }
.sort{ 
	color:#000;
}
.attention {
	background:#ff0000;
	padding:5px;
	font-size:12px;
	color:#fff;
	z-index:9000;
}
.dropdown-menu{ 
	z-index:9999 !important;
}   
.displaynone { 
  display:none;

}
.btn{
    --border-radius: 0 !important;
    padding-top:3px !important;
    padding-bottom:3px !important;
    padding-right:5px !important;
    padding-left:5px !important;
}
body,input,button,div,select{ 
  font-size:12px !important;
  font-family:arial !important;
  font-color:#000 !important;
}
.table tr td { 
    padding-top:4px !important;
    padding-bottom:4px !important;
}
.treeview a span{ 
  font-size:12px !important;
  padding:2px !important;
}
.treeview-menu li a{ 
  font-size:12px !important;
}
.treeview{ 
  padding-top:0px !important;
  padding-bottom:0px !important;
  border-bottom:0.5px solid #acacac;
}

.menu-open a span{ 
  border-radius:3px !important;
  background:#ff0000 !important;
  color:#fff;
}
.sidebar{ 
  mbackground-color:#4a4a4a;
}
.sidebar-toggle{ 
  background-color:#00a612 !important;
}
.main-sidebar{ 
  font-size: 12px !important;
}
.main-header{ 
  bbackground-color:#2e2e2e !important;
}
.text-shadow { 
  text-shadow: 1px 1px #000;
}
.detail_dbgrid {
    overflow-x: auto;
}
</style>
</head>

<body class="skin-purple-light sidebar-collapse hold-transition'">
<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
	  <span class="logo-mini"></span>
      <!-- logo for regular state and mobile devices -->
	  <font style="font-size:14pt;text-shadow: 1px 1px #000;"><b></b></font><font style="font-size:12px">&reg;</font>
	  </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
        </ul>
      </div>

  
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

    
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header" style="background:#fff">
      
		<ul class="nav nav-tabs">   
		</ul>
    </section>
	
    <!-- Main content -->

  <section class="content" style="background:#fff">
	<div class="box box-success">
	<div class="box-body">
	<br>
    {{ $body }} 
	</div>
	</div>
  </section>
	
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; 2020 <a href=""></a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>


<!-- ./wrapper -->

<!-- jQuery 3 -->
<!-- Bootstrap 3.3.7 -->
<script src="@php public_path() @php/assets/admin_lte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Slimscroll -->
<script src="@php public_path() @php/assets/admin_lte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="@php public_path() @php/assets/admin_lte/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="@php public_path() @php/assets/admin_lte/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="@php public_path() @php/assets/admin_lte/dist/js/demo.js"></script>
<script src="@php public_path() @php/assets/js/bootstrap-select.js"></script>
 
<script>





if (window.opener && window.opener !== window) {
  $( ".main-header" ).hide();
  $( ".sidebar-menu" ).hide();
  $( ".breadcrumb" ).hide();
  $( ".control-sidebar" ).hide();
  $( ".main-sidebar" ).hide();
  $( "body" ).addClass( "sidebar-collapse" );
}
</script>

<script>
$( document ).ready(function(){
$('.IDR').mask('000.000.000.000.000,00', {reverse: true});
$('.USD').mask('000,000,000,000,000.00', {reverse: true});
});
</script>
</body>

</html>
