$(document).ready(function() {

	$("input.datepicker").each(function( index, element ) {
	
	if($(this).attr('rel')!=""){	
	dateinfo = $.parseJSON($(this).attr('rel'));
	dateformat = dateinfo.format;
	
	$(this).datepicker({
			 dateFormat: eval(dateformat)
			 }).val();
	}
	});
	

});


$(document).on("click","span.add-on",function(event){
		dpinput = $(this).prev();
		dpinput.datepicker('show');
});
