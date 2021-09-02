<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title></title>
<div align="center" id="preload" style="position:fixed;width:100%;height:100%;top:0;padding:100px;z-index:9999;background:#ffff;color:#000">
	<img src="<?php echo url('/')?>/public/assets/images/loading.gif"/></div>
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/js/google_font.css">
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/js/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/fonts/css/font-awesome.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/admin_lte/dist/css/AdminLTE.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/admin_lte/bower_components/Ionicons/css/ionicons.min.css">
<link rel="stylesheet" href="<?php //echo url('/') ?>/public/assets/admin_lte/dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="<?php //echo url('/') ?>/public/assets/js/bootstrap-select.css">
<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/jquery.min.js"></script>

<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/autocomplete.js"></script>
<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/jquery.mask.js"></script>

<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/maxan/js/jquery.treegrid.js"></script>
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/maxan/css/jquery.treegrid.css">	
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/js/jquery.highlight-within-textarea.css">

<script src="<?php echo url('/') ?>/public/assets/js/jquery-1.12.4.js"></script>
<script src="<?php echo url('/') ?>/public/assets/js/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo url('/') ?>/public/assets/js/jquery-ui.css"/>
<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/jquery.validate.min.js"></script>

<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/js/dataTables.bootstrap.min.css">
<script src="<?php echo url('/') ?>/public/assets/js/jquery.dataTables.min.js"></script>
<script src="<?php echo url('/') ?>/public/assets/js/dataTables.bootstrap.min.js"></script>
  
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/js/bootstrap-select.min.css">
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/js/bootstrap-select.css">
<!-- Google Font -->
<script src="<?php echo url('/') ?>/public/assets/js/tinymce.min.js" referrerpolicy="origin"></script>
<link rel="stylesheet" href="<?php echo url('/') ?>/public/assets/js/sweetalert.min.css">
<script src="<?php echo url('/') ?>/public/assets/js/sweetalert.min.js"></script>
<link href="<?php echo url('/') ?>/public/assets/js/dropzone.min.css" rel="stylesheet">
<script src="<?php echo url('/') ?>/public/assets/js/dropzone.min.js"></script>
<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/jquery.treegrid.js"></script>
<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/jquery.treegrid.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo url('/') ?>/public/assets/js/chosen.min.css">
<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/ajax-chosen.js"></script>

<script type="text/javascript" src="<?php echo url('/') ?>/public/assets/js/jquery.toast.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo url('/') ?>/public/assets/js/jquery.toast.css">


<style>
 .chosen-container{
    width: 100% !important;
}
.error {
    color: red;
}


.with-select > span,
.with-select > label.error {
    vertical-align: top;
    margin-top: 5px;
    display: inline-block;
}

footer {
    position: fixed;
    bottom: 0;
    width: 100%;
}

</style>

<?php 
  $sys = Route::input('sys');
  ?>
<script>
  // $(".chosen-select").chosen({width: '350px'}).show();
  tinymce.init({
    selector: '.wswg',
    menubar: true
  });

function onCancelInput(link){
    window.location.href = link;
}

function ExportExcel(link){
      window.location.href = link;
}

function isconfirm(url_val){
    swal({
      title: "Are you sure to delete it ?",
      text: "",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      closeOnConfirm: false,
      closeOnCancel: false
    }, function(isConfirm) {
      if(isConfirm) {
        swal("Deleted", "", "success");
        window.location.href = url_val;
      } else {
        swal("Canceled", "", "error");
      }
    });
    
    // if(confirm('Are you sure ?') == false)
    // {
		// // alert(url_val); 
		// return false;
    // }
    // else
    // {
    //   // alert(url_val); 
    //   //   window.location.href = url_val;
		// // redirected(url_val);
    // }
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
  $(".chosen-select").chosen({
    search_contains: true
  });
  $.validator.setDefaults({ ignore: ":hidden:not(select)" });
  
  	$('#stdForm').validate({
		errorPlacement: function (error, element) {
			//console.log("placement");
			if (element.is("select.chosen-select")) {
				//console.log("placement for chosen");
				//placement for chosen
				element.next("div.chosen-container").append(error);
			} else {
				//standard placement
				error.insertAfter(element);
			}
		}
	});
	
	$('#stdForm2').validate({
		errorPlacement: function (error, element) {
			//console.log("placement");
			if (element.is("select.chosen-select")) {
				//console.log("placement for chosen");
				//placement for chosen
				element.next("div.chosen-container").append(error);
			} else {
				//standard placement
				error.insertAfter(element);
			}
		}
	});
	
	
  
  $("#multipleDelBut").click(function(){
      if(confirm("Are you sure you want to delete All?")){
          $("#delmultiply").submit();
      
      }else{
          return false;
      }
  });

  // INI FUNGSI UNTUK RESET VALUE DROPDOWN KETIKA MODAL DI CLOSE
  <?php if($sys != 'Report_Position'){ ?>
  $(".modal").on("hidden.bs.modal", function () {
	location.reload();
    //$(this).find('input[type=text],select').val('').trigger("chosen:updated");
  });
  <?php }?>

//$('.numberForm').mask('000.000.000.000.000,00', {reverse: true});

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


function popitup(url,windowName,width,height) {
       newwindow=window.open(url,windowName,'height=400,width=700');
       if (window.focus) {newwindow.focus()}
       return false;
}


function PopupCenter(url, title, w, h){
    // Fixes dual-screen position                         Most browsers      Firefox  
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;  
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;  
              
    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;  
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;  
              
    var left = ((width / 2) - (w / 2)) + dualScreenLeft;  
    var top = ((height / 2) - (h / 2)) + dualScreenTop;  
    var newWindow = window.open(url,title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);  
  
    // Puts focus on the newWindow  
    if (window.focus) {  
        newWindow.focus();  
    }  
} 


function PopupVar(url, title, w, h,l,t) {  
    // Fixes dual-screen position                         Most browsers      Firefox  
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;  
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;  
              
    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;  
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;  
              
    var left = l;  
    var top = t;  
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);  
  
    // Puts focus on the newWindow  
    if (window.focus) {  
        newWindow.focus();  
    }  
}

//multi upload 
 $(document).ready(function () {
    $("html").on("dragover", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });
 
    $("html").on("drop", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });
 
    $('#drop_file_area').on('dragover', function () {
      $(this).addClass('drag_over');
      return false;
    });
 
    $('#drop_file_area').on('dragleave', function () {
      $(this).removeClass('drag_over');
      return false;
    });
 
    $('#drop_file_area').on('drop',function (e) {
      e.preventDefault();
      $(this).removeClass('drag_over');
      var formData = new FormData();
      var files = e.originalEvent.dataTransfer.files;
	  console.log(files);
      for (var i = 0; i < files.length; i++){
        formData.append('file[]', files[i]);
      }
	  
      uploadFormData(formData);
    });
 
    function uploadFormData(form_data) {
		
	$.ajaxSetup({
	headers: {
	'X-CSRF-TOKEN': "<?php echo csrf_token() ?>"
	}
	});	
      $.ajax({
        url: "<?php echo url('/') ?>/system/trs_attachment/add/parent/headless",
        method: "POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
		dataType: 'json',
        success: function (data) {
          $('#uploaded_file').append(data);
        }
      });
    }
  });
  
  
</script>
<style>

  .form-control{ 
  	height:30px;
  	border:1px solid #acacac;
  	border-radius:2px !important;
  }
  .chosen-select,.chosen-single,.chosen-container {
  	height:30px !important;	
  	background:#fff !important;
  	border-radius:2px !important;
  }
  #drop_file_area {
    height: 200px;
    border: 2px dashed #ccc;
    line-height: 200px;
    text-align: center;
    font-size: 20px;
    background: #f9f9f9;
    margin-bottom: 15px;
  }
 
  .drag_over {
    color: #000;
    border-color: #000;
  }
 
  .thumbnail {
    width: 100px;
    height: 100px;
    padding: 2px;
    margin: 2px;
    border: 2px solid lightgray;
    border-radius: 3px;
    float: left;
  }
 
  #upload_file {
    display: none;
  }

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
#alert008{ 
background:green;
padding:20;
z-index:999;
display:none;
color:#fff;
}

.bordernone{
  border:none;
}
.bordernone:focus {
   border:1px solid #acacac;
}

</style>
</head>
 <?php 
$sys = $sysinfo['sysdet']['sys'];

?>

<body class="skin-blue-light hold-transition sidebar-collapse">
<div class="wrapper">
  <!-- Left side column. contains the logo and sidebar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header" style="background:#fff">
		<ul class="nav nav-tabs"> 
		<li class="active"><a href="#tab_1-1" data-toggle="tab" id="tabHeader">@php echo $sysdesc @endphp</a></li>
		</ul>
    </section>
    <!-- Main content -->

  <section class="content" style="background:#fff">
	<div class="box box-success">
	
	<div id="alert008" class="box-body">
	</div>
	
	<div class="box-body">
	<br>
	@php 
	if($menuaccperm['docnum'] == 0){ 
		parseContent('<div class="attention" style="background:#ff0000">Document Number Not Set in This Module, document number will empty !!</div><br>');
	}
	echo $body @endphp  
	</div>
	
	</div>
  </section>
	
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Erp PopUp Version</b> 1.0
    </div>
    <strong>PopUp Page :: Copyright &copy; 2020 <a href=""></a>.</strong> All rights
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
<script src="<?php echo url('/') ?>/public/assets/admin_lte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo url('/') ?>/public/assets/admin_lte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo url('/') ?>/public/assets/admin_lte/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo url('/') ?>/public/assets/admin_lte/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo url('/') ?>/public/assets/admin_lte/dist/js/demo.js"></script>
<script src="<?php echo url('/') ?>/public/assets/js/bootstrap-select.js"></script>
 

<script>



// for treeview
/*
$(function(){
  var url = window.location;
  $('ul.sidebar-menu a').filter(function() {
     return this.href == url;
  }).parent().addClass('active');
  
  $('ul.treeview-menu a').filter(function() {
     return this.href == url;
  }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
  
});	
*/

$('#getDocNum').click(function(){
  var id = null;
	$.ajax({
	url : "<?php echo url('/');?>/system/Document_Number/other/dummy/headless/ajax?sys=<?php echo $sys ?>",
	method : "GET",
	// data : {ftype: id},
	async : false,
	dataType : 'json',
	success: function(data){
    if(data.docnum == 0x00){
      alert("Document Number Not Set");
      return false;
    }
    
		$("#docnum").val(data.docnum);
		// $("#lastnum").val(data.lastnum);
	}
	});
});

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
<script>

$( document ).ready(function(){
	  var erpToastParam = sessionStorage.getItem("erp_toastParam");
	  if (erpToastParam == "yes") {
		let erpToastMessage 	= sessionStorage.getItem("erp_toastMessage");
		let erpToastType 		= sessionStorage.getItem("erp_toastType");
		
		if(erpToastType == "success"){
			var bgColorx = '#059e45'; //03ff6c
		}else{
			var bgColorx = '#FF1356';
		}
		$.toast({
			heading: erpToastType,
			text: erpToastMessage,
			position: 'top-right',
			stack: false,
			bgColor: bgColorx,
			textColor: 'white'
		})
		
		 sessionStorage.removeItem("erp_toastParam");
		 sessionStorage.removeItem("erp_toastMessage");
		 sessionStorage.removeItem("erp_toastType");
		 
		 //alert("Welcome again " + erpToastParam);
	  } 
	  
$('.IDR').mask('000.000.000.000.000,00', {reverse: true});
$('.USD').mask('000,000,000,000,000.00', {reverse: true});

	$("#preload").fadeOut();
});

</script>

</body>

</html>
