$(document).on("click","span.datetimebutton",function(event){
		dpinput = $(this).prev();
		
		opts = $(dpinput).attr('data');
		opts = $.parseJSON(opts);
		$(dpinput).datetimepicker(opts)
		$(dpinput).datetimepicker('show');//opts
});


