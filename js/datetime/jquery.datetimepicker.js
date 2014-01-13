$(document).on("click","span.datetimebutton",function(event){
		dpinput = $(this).prev();
		//console.log($(this));
		//opts = $(dpinput).attr('data');
		//opts = $.parseJSON(opts);
		//$(dpinput).datetimepicker(opts)
		$(dpinput).datetimepicker('show');
});

$(document).ready(function() {
	$("input.datetimepicker").each(function( index, element ) {
	opts = $(this).attr('data');
	opts = $.parseJSON(opts);
	$(this).datetimepicker(opts);
	});
	
});

