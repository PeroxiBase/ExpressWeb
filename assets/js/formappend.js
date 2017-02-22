$(document).ready(function(){
	$(document).scrollTop( $("#param").offset().top );
			$('#toggle2').fadeOut('slow');

// Fonction pour ajouter dynamiquement un input
	$("input[name='geneSelect']:checkbox").change(function(){
		if($("input[name='geneSelect']:checkbox:checked").val()=='geneChecked'){
			$('#toggle2').fadeIn('slow');
			$('#cluS').attr('disabled','true');
			$('#toggle1').css('background-color','lightgray');
		}
		if($("input[name='geneSelect']:checkbox:checked").val()== undefined){
			$('#toggle2').fadeOut('slow');
			$('#cluS').attr('disabled','false');
			$('#toggle1').css('background-color','white');
		}
	});
});
