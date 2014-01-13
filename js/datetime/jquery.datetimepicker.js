$(document).on("click","span.datetimebutton",function(event){
		dpinput = $(this).prev();
		
		opts = $(dpinput).attr('data');
		opts = $.parseJSON(opts);
		$(dpinput).datetimepicker(opts)
		$(dpinput).datetimepicker('show');//opts
});

$(document).ready(function() {
	$("input.datetimepicker").each(function( index, element ) {
	opts = $(this).attr('data');
	opts = $.parseJSON(opts);
	$(this).datetimepicker(opts);
	});
	
});

