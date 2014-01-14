$(document).on("click","span.datetimebutton",function(event){
		dpinput = $(this).prev();
		$(dpinput).datetimepicker('show');
});

$(document).ready(function() {
	$("input.datetimepicker").each(function( index, element ) {
	opts = $(this).attr('data');
	opts = $.parseJSON(opts);
	$(this).datetimepicker(opts);
	});
	
});

