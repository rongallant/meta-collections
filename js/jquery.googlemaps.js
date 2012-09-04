
var mapInstances={};

var googleMaps;//google maps function put into an object

(function($) {
	googleMaps = {


init : function(args) {
		//var self = this;
		//this.args = args;
		//this.self = self;
		$.extend( this, args || {} );
		//mapInstances[this.name] = this.name;
			//console.log('ai');		
	this.geocoder = new google.maps.Geocoder();
},

showmap: function(args) {
	var self = this;
	self.init(args);
	
	mapInstances[this.name] = new google.maps.Map(document.getElementById(this.name), this.mapOptions);
	if(typeof this.placeMarks == 'object'){
	this.getplacemarks();
	}
	
},


getplacemarks: function() {

	for( var m in this.placeMarks){
		this.createMarker(m);
	}

},


createMarker: function(m) {
marker  = this.placeMarks[m];
//console.log($('#table_'+this.elementID+' #longitude'));

if( isNaN( marker.latitude ) || isNaN( marker.longitude ) ){
				if( window.console )
					console.log( "googleMaps::createMarker(): "+ title +" latitude and longitude weren't valid." );					
				return false;
			}


marker = new google.maps.Marker( {
					'position'	: new google.maps.LatLng( marker.latitude, marker.longitude ),
					'map'		: mapInstances[this.name],
					'title'		: marker.title,
					 draggable	: true,
					 objectname	: this.name
				} );

 // Add dragging event listeners.
  google.maps.event.addListener(marker, 'dragstart', function() {
  //  updateMarkerAddress('Dragging...');
  });
  
  google.maps.event.addListener(marker, 'drag', function() {
    //updateMarkerStatus('Dragging...');
    //updateMarkerPosition(marker.getPosition());
  });
  
  google.maps.event.addListener(marker, 'dragend',function() {
    //updateMarkerStatus('Dragging...');
    //updateMarkerPosition(marker.getPosition());
    self 	= eval(marker.objectname);
    //address = ;
    self.reversegeocode($("#table_"+self.elementID+" #address"), marker.getPosition().lat(), marker.getPosition().lng());
    $("#table_"+self.elementID+" #latitude").val(marker.getPosition().lat());
    $("#table_"+self.elementID+" #longitude").val(marker.getPosition().lng());
   
  });

},
	
		
		
geocode: function(addressfield, latfield, longfield) {
		
		CurrentAddress = $(addressfield).val()
		if ($(addressfield).data("LastAddressValidated") != CurrentAddress) {//else fieldvalidation action
		var geocoder = new google.maps.Geocoder();
          
       
            
          geocoder.geocode({ 'address': $(addressfield).val() }, function (results, status) {
          
           if (status == 'OK') {
           
           var address 		= results[0].formatted_address;
           var latitude 	= results[0].geometry.location.lat();
           var longitude 	= results[0].geometry.location.lng();
                      
           $(addressfield).val(address);
           $(latfield).val(latitude);
           $(longfield).val(longitude);
           
           $(addressfield).data("LastAddressValidated", address);//save address in field
           }else{
	       // console.log('invalid address action');   
	        alert("Geocoder failed due to: " + status);

           } 
           
           });
		//console.log(geocoder);
		
		}
		
		},
		
		
reversegeocode: function(addressfield, latitude, longitude) {
		 var latitude 	= parseFloat(latitude);
		 var longitude 	= parseFloat(longitude);
		 var latlng 	= new google.maps.LatLng(latitude, longitude);
		 
		 var geocoder = new google.maps.Geocoder();
		 
		 geocoder.geocode({'latLng': latlng}, function(results, status) {
		 
		 if (status == google.maps.GeocoderStatus.OK) {
		  var address 		= results[0].formatted_address;
		  $(addressfield).val(address);
		 }else{
	       // console.log('invalid address action');   
	     alert("Geocoder failed due to: " + status);

         }
		
		});
		
		}
	};

}(jQuery));

