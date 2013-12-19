var jtabs;//quick and dirty javascript tabs

(function($) {
	jtabs = {
		init : function(args) {
		var self = this;
		this.args = args;
		$.extend( this, args || {} );
		
		this.tabs = $('#'+this.tabID+" .nav-tab");
		this.cdivs = $('#'+this.tabID+" .tab-content"); 
		
		
		
		$(this.tabs).click(function(){
		
		$.each(self.tabs, function(index, tab) { 
		$(tab).removeClass('nav-tab-active');
		$(self.cdivs[index]).removeClass('content-active');
		
		
		//console.log(index);
		
		});
		
		
		//maybe sAVE COOKIE FOR LATER
		$(self.cdivs[this.id.split("-")[1]]).addClass('content-active');
		$(this).addClass('nav-tab-active');
		
		});
		
		
		
		},
		
	activate : function(index) {
	
	$.each(this.tabs, function(i, tab) { 
	$(tab).removeClass('nav-tab-active');
	$('#c-'+i).removeClass('content-active');
	
	//console.log($(self));
	});
	$('#nav-'+index).addClass('nav-tab-active');
	$('#c-'+index).addClass('content-active');
	
	}	
		
	
		
	}
	
	
	

}(jQuery));