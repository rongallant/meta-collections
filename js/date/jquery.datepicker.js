$(document).ready(function() {
	
	$("input.datepicker").each(function( index, element ) {
	dateinfo = $.parseJSON($(this).attr('rel'));
	dateformat = dateinfo.format;
	
	$(this).datepicker({
			 dateFormat: eval(dateformat)
			 }).val();
	
	});

});


$(document).on("click","span.add-on",function(event){
		dpinput = $(this).prev();
		//console.log(dpinput);
		
		dpinput.datepicker('show');
				
		/*opts = $(dpinput).attr('data');
		opts = $.parseJSON(opts);
		$(dpinput).datetimepicker(opts)
		$(dpinput).datetimepicker('show');//opts*/
});
