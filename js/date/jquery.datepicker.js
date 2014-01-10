$(document).on("focusin",".datepicker",function(event){
		
		 if (false == $(this).hasClass('hasDatepicker')) {
			dateformat = $(this).attr('rel');
			$(this).datepicker({
			 dateFormat: eval(dateformat)
			 }).val();			 
		}
		
});


