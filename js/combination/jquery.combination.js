function toggle_row(event, nonce){
	event.preventDefault();	
	$('#td_'+nonce).toggle(200, function() {
	$('#close_'+nonce).html( ($('#td_'+nonce).is(":hidden"))? "Edit" : "Close");
	});
}


function delete_row(event, el){
	event.preventDefault();
	
	$(el).parent().parent().parent().parent().fadeOut(400, function() {
	rows = $(this).parent().children().length;
	$(this).next().remove();
	$(this).remove();
	if(rows==3){
	$('#no-collections').fadeIn(400);	
	}
	});
		
}


function add_subfield(event, element){
	event.preventDefault();
	
	rows	=$('.subfields>table').length;
	element['row'] = rows;
	
	$('#no-collections').css({"display":"none"});
	
	$.post( ajaxurl, element, function( data ) {
	$(data).appendTo('#subfield_'+element['ID']);
	
	
		});
	
}

$(document).on("keydown",".label",function(event){
nonce = $(event.target).attr("rel");

setTimeout(function(){
 $('.spanlabel_'+nonce).html($('#label_'+nonce).val()) }, 200)

});
