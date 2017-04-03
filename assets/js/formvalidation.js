$(document).ready(function(){
	$('#formParam').on("submit",function(){
		if($("input[name='file']").val()==""){
			alert('fichier');
			evt.preventDefault();
			window.history.back();

		}
	});	
});