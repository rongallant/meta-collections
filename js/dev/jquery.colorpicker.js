var colortitle ;

$(document).on("focusin",".colorpickers",function(event){//span.colorpickerbutton,

	$(this).colorpicker({
                	parts: 'full',
					alpha: true,
					color: $(this).val(),
					title: colortitle,

					select: function(data, color){
					$(this).prev().css({background: '#'+color.formatted});
					}
					});
		
		  
		  
			
});