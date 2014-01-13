$(document).ready(function() {
	
	$("input.datepicker").each(function( index, element ) {
	dateinfo = $.parseJSON($(this).attr('rel'));
	dateformat = dateinfo.format;
	
	$(this).datepicker({
			 dateFormat: eval(dateformat)
			 }).val();
	
	});

});


