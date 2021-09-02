
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SIF ERP</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="@php echo url('/') @endphp/public/assets/admin_lte/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="@php echo url('/') @endphp/public/assets/admin_lte/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="@php echo url('/') @endphp/public/assets/admin_lte/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="@php echo url('/') @endphp/public/assets/admin_lte/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="@php echo url('/') @endphp/public/assets/admin_lte/plugins/iCheck/square/blue.css">
  
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<style>

.background{
		background-image:url("@php echo url('/') @endphp/public/assets/images/gedung_dedpan.jpg");    
    background-position: center;
    background-repeat: no-repeat;
    background-color: #34abeb;
    background-size: cover;
    background-color:rgba(230,230,230,0.7);
    /* height: 100px; */
    /* opacity: 0.5; */
}
.transparent{
	background-color:rgba(230,230,230,0.7);
  border-radius:5px;
}
.login-box{
  
}
.form-control {
  color:#acacac;
}

</style>
<body class="hold-transition background">
<div class="login-box">
  <div class="login-logo">
   <font style="color:#ff1900;font-size:48px;text-shadow: 1px 1px #000;"><b>YUSUF</font> </b>
   <font style="color:#fff;font-size:32px;text-shadow: 1px 1px #000;">&reg;</font> 
   <font style="font-size:48px;color:#4272f5;text-shadow: 1px 1px #000;"><b>SYAEFUDIN</b></font><br>
  </div>


  <!-- /.login-logo -->
  <div class="login-box-body transparent">
    {{-- FLASH MESSAGE --}}
    @if ($message = Session::get('error'))
      <div class="alert alert-danger  alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>	
        <strong>{{ $message }}</strong>
      </div>
    @endif

    @if ($message = Session::get('success'))
      <div class="alert alert-danger  alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>	
        <strong>{{ $message }}</strong>
      </div>
    @endif

    <p class="login-box-msg" style="color:#fff;font-size:24px;text-shadow: 1px 1px #000;" ><b>Login</b></p> 
 
    <form action="/login" method="POST">
    {{ csrf_field() }}
    <input name="_method" type="hidden" value="POST">
      <div class="form-group has-feedback">
        <input type="text" name="setpostvalue_username" required class="form-control" placeholder="UserId">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="setpostvalue_password" required class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <!-- <font style="font-size:11px">Optional</font> -->
      <div class="form-group has-feedback">
        <?php //echo form_dropdown('setpostvalue_comp_id',$comp_id,'','class="form-control dropdown_box1" id="coa_code"'); ?>
        <span class="glyphicon  form-control-feedback"></span>
      </div>
      <br>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-6">
          <input type="submit" id="btnSignin" value="Sign In" style="font-weight:bold;text-shadow: 1px 1px #000;" class="btn btn-primary btn-block "/>
		     <input type="hidden" name="subsyspart" value="save">
        </div>
        <div class="col-xs-6" align="right">
        <font style="color:#666666;font-size:12px;">
          Forget Password ?
        </font>
        </div>
        <!-- /.col -->
      </div>
    </form> 
    <!-- /.social-auth-links -->
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="@php echo url('/') @endphp/public/assets/admin_lte/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="@php echo url('/') @endphp/public/assets/admin_lte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="@php echo url('/') @endphp/public/assets/admin_lte/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
<script type="text/javascript"> 
    $(window).on('load', function() {
      
        $("#btnSignin").click(function(e){
            var x = $("form").serializeArray();
            
            var data = [];
            $.each(x, function(i, field){
              data[field.name]  = field.value;
            });
            url = "<?php echo url('/') ?>/login";
            $.ajax({
              url : url,
              type : "POST",
              data: {
                   _token:     '{{ csrf_token() }}',
                  data: data
                  },
              async : false,
              dataType : 'json',
                success: function(data){
                  console.log(data);
                  if(data.status == 1) {
                    alert('Login Success..');
                    window.location.href = "<?php echo url('/') ?>/system/home/List";
                  } else {
                    alert('oooppsss...');
                    window.location.href = "<?php echo url('/') ?>/login";
                  }
                  // e.preventDefault();
                }
              });
 
        });
    });
</script> 
<?php 
//parseContent($modal);
?>



</body>
</html>


