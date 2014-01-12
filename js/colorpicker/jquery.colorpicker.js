var colortitle ;

$(document).on("focusin",".colorpickers",function(event){//span.colorpickerbutton,

		//element = ($(this).hasClass('colorpickers')) ? $(this) : $(this).prev();

		//if(element.data("colorpicker")!=1){	
		$(this).colorpicker({
                	parts: 'full',
					alpha: true,
					color: $(this).val(),
					title: colortitle,

					select: function(data, color){
					$(this).prev().css({background: '#'+color.formatted});
					}
					});
		
		//element.data("colorpicker", 1);
		//}		
		
		//if($(this).hasClass('colorpickers')==false){
		//$(this).prev().trigger("focusin");
		//$(this).prev().colorpicker("show");
		//}
		  
		  
			
});


