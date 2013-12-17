var colortitle='';
var wysiwyg_num = new Array();
function add_value_instance(wrapperID, fieldtype){
	//console.log(fieldtype)
	if(fieldtype!="wysiwyg"){
	the_clone = jQuery('#'+wrapperID+' .metafield-value:last').clone().appendTo(jQuery('#'+wrapperID+' .metafield-body'))
	}
	
	switch(fieldtype){
		case "colorpicker":
				jQuery('#'+wrapperID+' .metafield-value:last input').prev().css('background:#fff');
				jQuery('#'+wrapperID+' .metafield-value:last input').colorpicker({
                	parts: 'full',
					alpha: true,
					color: '#c0c0c0',
					title: colortitle,
					select: function(data, color){
					jQuery(this).prev().css({background: '#'+color.formatted});
					}					
					});
		break;


  		case "date_and_or_time":
				jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				jQuery('#'+wrapperID+' .metafield-value:last input').scroller({
						preset: date_time_preset,
						
						display: 'modal',
						mode: 'mixed'
						});
  
		break;

  		case "date":
				jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				//ni = jQuery('#'+wrapperID+' .metafield-value:last input').attr('id')+10;
				//jQuery('#'+wrapperID+' .metafield-value:last input').attr('id', ni);
				//this trigger isnt working yet
				jQuery('#'+wrapperID+' .metafield-value:last input').datepicker({
					 
					 dateFormat: date_preset
					
					 }).val();
					// console.log(jQuery('#'+wrapperID+' .metafield-value:last input'));		 
		break;

		case "select":
				
				jQuery('#'+wrapperID+' .metafield-value:last select').children()[0].selected=true;
				
				break;
				
		case "wysiwyg":
		metafieldID = wrapperID.split("_")[0];
		jQuery.post('admin-ajax.php',  {action: 'add_wysiwyg_field', metafieldID: metafieldID+'----------'+wysiwyg_num[metafieldID], tabindex:wysiwyg_num[metafieldID]}, function(data){
		jQuery(data).appendTo(jQuery('#'+wrapperID+' .metafield-body'))
		wysiwyg_num[metafieldID]++;
		});
		
		
		break;
				

		default:

				jQuery('#'+wrapperID+' .metafield-value:last input').val('');
				jQuery('#'+wrapperID+' .metafield-value:last textarea').val('');

		break;
  }
	
	
	
	
}

function remove_value_instance(inputEL){
jQuery(inputEL).parent().remove();

}