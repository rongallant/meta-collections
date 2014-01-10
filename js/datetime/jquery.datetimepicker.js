$(document).on("click","span.datetimebutton",function(event){
		dpinput = $(this).prev();
		
		opts = $(dpinput).attr('data');
		opts = $.parseJSON(opts);
		$(dpinput).datetimepicker(opts)
		//console.log($(this).prev());
		$(dpinput).datetimepicker('show');//opts
});


