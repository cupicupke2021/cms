@php echo $aryCont['genform'] @endphp
<?php 
  $link_module 		= url('/').'/system/'.$aryCont['sys'].'/add/dummy/headless/ajax?setpost_module=';
?>
<script>
$( document ).ready(function() {
	$('#module').change(function(){
		var modul = $(this).val();
		$.ajax({
			url : "<?php echo $link_module ?>"+modul,
			method : "GET",
			async : false,
			dataType : 'json',
			success: function(data){
				if(data != 'empty') {
					$('#trsID').children("option").remove();
					$('#trsID').append($("<option/>", {
						value: '',
						text: '--Select One--'
					}));
					
					$.each(data, function(key, value) {
						$('#trsID').append($("<option/>", {
						value: value.id,
						text: value.docnum
						}));
					})
					$('#trsID').trigger("chosen:updated");

				} else {
					$('#trsID').children("option").remove();
					$('#trsID').append($("<option/>", {
						value: '',
						text: '--Select One--'
					}));
					$('#trsID').trigger("chosen:updated");
				}
			}
		});
  	});

	$("#trsID").change(function(){
    	var text = $("#trsID option:selected").text();
    	$("#trsDocnum").val(text);
  	});
});

</script>