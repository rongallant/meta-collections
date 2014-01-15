
$ = jQuery.noConflict();

$.widget( "custom.OpenLayerField", {
 options: {
field: null
},

_create: function() {
	olObj = this;	
	this.map = new OpenLayers.Map($(this.element).attr('id'), 
	{ controls: [
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.KeyboardDefaults(),
            new OpenLayers.Control.Zoom()
        ],
        eventListeners:{ 
	"zoomend": this.saveZoomandLocation,
	"moveend": this.saveZoomandLocation
	}, 
	projection: new OpenLayers.Projection("EPSG:3857"),
	displayProjection: new OpenLayers.Projection("EPSG:4326"),
	parent: olObj
        }
	
	);
    
    this.osm = new OpenLayers.Layer.OSM();   
    this.map.addLayer(this.osm);
    
    this.zoom   = (this.options.zoom=="")? 9 : this.options.zoom;             
    this.lat 	= (this.options.latitude=="")? 53 : this.options.latitude ;
    this.lon 	= (this.options.longitude=="")? 6 : this.options.longitude ;
    
    
    this.map.setCenter(
        new OpenLayers.LonLat(this.lon, this.lat).transform(
            new OpenLayers.Projection("EPSG:4326"),
            this.map.getProjectionObject()
        ), 
        this.zoom
    );

	this.map.addControl(new OpenLayers.Control.MousePosition( {id: "ll_mouse", formatOutput: this.formatLonlats} ));
    this.map.addControl(new OpenLayers.Control.MousePosition( {id: "utm_mouse", prefix: "Mercator ", displayProjection: this.map.baseLayer.projection, numDigits: 0} ));
    $(this.element).data("olmap",this);

},

saveZoomandLocation:function(obj){


latlong= obj.object.getCenter().transform(
            obj.object.getProjectionObject(),
            new OpenLayers.Projection("EPSG:4326")
        );
	//$('#latitude').val(latlong.lat);
	//$('#longitude').val(latlong.lon);
	//console.log(this.options.parent.element.parent().parent().parent().find(".fieldzoom"))
	this.options.parent.element.parent().find(".ol_latitude").val(latlong.lat);
	this.options.parent.element.parent().find(".ol_longitude").val(latlong.lon);
	this.options.parent.element.parent().parent().parent().find(".fieldzoom").val(obj.object.getZoom());
	//$('#fieldzoom').val(obj.object.getZoom());fieldzoom


},

setZoom: function(level){
	this.map.zoomTo(level);
},


formatLonlats: function(lonLat){
		var lat = lonLat.lat;
	
	        var long = lonLat.lon;
            var ns = OpenLayers.Util.getFormattedLonLat(lat);
            var ew = OpenLayers.Util.getFormattedLonLat(long,'lon');
            return ns + ', ' + ew + ' (' + (Math.round(lat * 10000) / 10000) + ', ' + (Math.round(long * 10000) / 10000) + ')';
}

});    
