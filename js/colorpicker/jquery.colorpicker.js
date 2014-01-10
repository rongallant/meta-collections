var colortitle ;

$(document).on("click","span.colorpickerbutton",function(event){

		if($(this).prev().data("colorpicker")!=1){	
		$(this).prev().colorpicker({
                	parts: 'full',
					alpha: true,
					color: '#c0c0c0',
					title: colortitle,
					select: function(data, color){
					$(this).prev().css({background: '#'+color.formatted});
					}
					});
		
		$(this).prev().data("colorpicker", 1);
		}		
		
		$(this).prev().trigger("focus");
		  
		  
			
});


