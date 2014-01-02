$ = jQuery.noConflict();

$.widget( "custom.OpenLayerField", {
 options: {
field: null
},

_create: function() {
this.element.addClass( "OpenLayerField" );

	this.map = new OpenLayers.Map( this.element.attr('id'), {
	eventListeners:{ 
	"zoomend": this.saveZoomandLocation, 
	"moveend": this.saveZoomandLocation},
	 projection: new OpenLayers.Projection("EPSG:4326"),
     displayProjection: new OpenLayers.Projection("EPSG:4326")
	} 
	
	);  
	
	//this.map.projection = "EPSG:3857";
    //this.map.displayProjection = new OpenLayers.Projection("EPSG:4326");
    
    //this.osm = new OpenLayers.Layer.OSM();                   
	//this.map.addLayer(this.osm);
	//	console.log(this.map);
	//this.map.setBaseLayer(this.osm);
	this.zoom   = (this.options.zoom=="")? 9 : this.options.zoom;             
    this.lat 	= (this.options.latitude=="")? 53 : this.options.latitude ;
    this.lon 	= (this.options.longitude=="")? 6 : this.options.longitude ;
    
    //this.map.displayProjection = new OpenLayers.Projection("EPSG:4326");
    
    this.map.addLayer(new OpenLayers.Layer.OSM());
    this.map.setCenter( new OpenLayers.LonLat(this.lon,this.lat), this.zoom); 
	//$(this.element).data("olmap",this);
},

saveZoomandLocation:function(obj){
	latlong = obj.object.getCenter();
	 //var ns = OpenLayers.Util.getLonLat(latlong);
	 //var datapoint = new OpenLayers.LonLat(-71.0, 42.0);
//console.log(ll);
//latlong = new OpenLayers.LonLat(ll.lat, ll.lon);
//	 var proj_1 = new OpenLayers.Projection("EPSG:4326");
//var proj_2 = new OpenLayers.Projection("EPSG:900913");
//latlong.transform(proj_2, proj_1);

    console.log(latlong);
	
//	 var lat = lonLat.lat;
     //var long = lonLat.lon;
     //var ns = OpenLayers.Util.getFormattedLonLat(lat);
     //var ew = OpenLayers.Util.getFormattedLonLat(long,'lon');
     //return ns + ', ' + ew + ' (' + (Math.round(lat * 10000) / 10000) + ', ' + (Math.round(long * 10000) / 10000) + ')';

	/////////
	
	$('#latitude').val(latlong.lat);
	$('#longitude').val(latlong.lon);
	$('#fieldzoom').val(obj.object.getZoom());
},

setZoom: function(level){
	//console.log(this);
	this.map.zoomTo(level);
}

});    
