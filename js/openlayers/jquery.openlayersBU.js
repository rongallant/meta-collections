$ = jQuery.noConflict();


$.widget( "custom.OpenLayer", {
 options: {
field: null
},

_create: function() {

$(this.element).html("");
			this.mapoptions = { controls: [
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.KeyboardDefaults(),
            new OpenLayers.Control.Zoom()],
            projection: new OpenLayers.Projection("EPSG:3857"),
			displayProjection: new OpenLayers.Projection("EPSG:4326")
    
            };

	this.map = new OpenLayers.Map($(this.element).attr('id'), this.mapoptions);
//    this.map.projection = "EPSG:3857";
 //   this.map.displayProjection = new OpenLayers.Projection("EPSG:4326");
    //this.map.displayProjection = new OpenLayers.Projection("EPSG:4326");

    //this.map.addLayer(new OpenLayers.Layer.OSM());
    this.wgs84 = new OpenLayers.Projection("EPSG:4326");
    this.defStyle = {strokeColor: "#f54305", strokeOpacity: "0.7", strokeWidth: 1, fillColor: "#f54305", pointRadius: 3, cursor: "pointer"};
    
    this.sty = OpenLayers.Util.applyDefaults(this.defStyle, OpenLayers.Feature.Vector.style["default"]);
    this.sm = new OpenLayers.StyleMap({
            'default': this.sty,
            'select': {strokeColor: "#0074A4", fillColor: "#0074A4", cursor: 'move', pointRadius: 5}
            
        });
    //   "featuresadded": function(request) {}
    this.renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
    this.renderer = (this.renderer) ? [this.renderer] : OpenLayers.Layer.Vector.prototype.renderers;               
                
    this.vectorLayer = new OpenLayers.Layer.Vector("Vector Layer", {
    styleMap: this.sm,
    eventListeners: {
                "loadend": function(request) {
                console.log("> "+this.getDataExtent());
 	             //  this.map.zoomToExtent(this.getDataExtent());
                }
             },
    renderers: this.renderer
    });
    
	this.feature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(-111.04, 45.68).transform(
	new OpenLayers.Projection("EPSG:4326"),this.map.getProjectionObject()),{"title": "Bij het meer", "date": "10-11-2014", "time": "10:13", "amount": 20});
    //this.feature.fid=12;
	
    this.vectorLayer.addFeatures(this.feature);

/*

    this.vectorLayers = new OpenLayers.Layer.Vector("Line Vectors", {
            styleMap: this.sm,
            eventListeners: {
                "loadend": function(request) {
 	               this.map.zoomToExtent(this.getDataExtent());
                },
             },
            projection: this.wgs84,
            strategies: [
                new OpenLayers.Strategy.Fixed(),
                //saveStrategy
            ],
           
            protocol: new OpenLayers.Protocol.HTTP({
                url: "/OpenLayers/data/",
                format: new OpenLayers.Format.GeoJSON({
                    ignoreExtraDims: true
                }
                 
                )
             
                
                
            })
        });
  */
    
     this.osm = new OpenLayers.Layer.OSM();            
    //this.gmap = new OpenLayers.Layer.Google("Google Streets");
    // create Google Mercator layers
    
    this.gphy = new OpenLayers.Layer.Google(
        "Google Physical",
        {type: google.maps.MapTypeId.TERRAIN}
    );
    
    this.gmap = new OpenLayers.Layer.Google(
        "Google Streets", // the default
        {numZoomLevels: 20}
    );
    this.ghyb = new OpenLayers.Layer.Google(
        "Google Hybrid",
        {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
    );
    
    this.gsat = new OpenLayers.Layer.Google(
        "Google Satellite",
        {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
    );

    
    this.map.addLayers([this.osm, this.vectorLayer, this.gphy, this.gmap, this.ghyb, this.gsat]);
	
    
    
    this.map.addControl(new OpenLayers.Control.MousePosition( {id: "ll_mouse", formatOutput: this.formatLonlats} ));
    this.map.addControl(new OpenLayers.Control.MousePosition( {id: "utm_mouse", prefix: "Mercator ", displayProjection: this.map.baseLayer.projection, numDigits: 0} ));
    
	this.editPanel 		= new OpenLayers.Control.Panel({displayClass: 'editPanel'});
    this.navControl 	= new OpenLayers.Control.Navigation({title: 'Kaart bewegen / zoomen'});
    this.editControl 	= new OpenLayers.Control.ModifyFeature(this.vectorLayer, {title: 'Punten bewerken'});
    this.drawControl	= new OpenLayers.Control.DrawFeature(this.vectorLayer, OpenLayers.Handler.Point, {displayClass: 'pointButton', title: 'Punt toevoegen', handlerOptions: {style: this.sty}});
    this.deleteControl  = new DeleteFeature(this.vectorLayer, {title: 'punten wissen'});         
    //this.shortdelete	= new OpenLayers.Handler.Feature(OpenLayers.Control, this.vectorLayer, {click: this.clickFeature});this.modifyFeatures(feature)
    obj= this;//this.modifyFeatures(feature)
    this.dragfeature	= new OpenLayers.Control.SelectFeature(this.vectorLayer, {clickFeature: function(feature){this.obj.modifyFeatures(feature, this.obj)}, title: 'Punt eigenschappen bewerken'}); 	////this.modifyFeatures()
    //map.addControl(new OpenLayers.Control.LayerSwitcher());
	
    this.switcher = new OpenLayers.Control.LayerSwitcher({"title": "Basislagen"}); 
    this.dragfeature.obj = this;
    this.editPanel.addControls([
            this.navControl,
            this.editControl,
            this.drawControl,
            this.deleteControl,
			this.dragfeature,
			this.switcher                           
        ]);
        
     this.editPanel.defaultControl = this.dragfeature;
     this.map.addControl(this.editPanel);
	 this.map.zoomToMaxExtent();
    
	 },
	 
	
	
 	modifyFeatures: function (feature, obj){
	//parentEL	= this.element;
	//console.log(obj.capitaliseFirstLetter("taart"));
	//this 		= obj;
	console.log(feature);

	props 		= new Array("title", "date", "time", "amount");
	hasprops	= (Object.keys(feature.attributes).length > 0) ? 1 : 0;

	selFeature 	= feature;
	title		= "Eigenschappen";
	mform		= "<form class='propertyform' id='form_"+feature.fid+"'>";
	for (var e=0;e<props.length;e++) {
	an 			= (props[e]=="amount") ? "an":"a";
	ititle 		= obj.capitaliseFirstLetter(props[e]);
	v 			= (feature.attributes[props[e]]!=undefined)? feature.attributes[props[e]]: "";
	mform		+= "<div class='property'><label for='"+feature.attributes[props[e]]+"'>"+ititle+":</label>";
	mform		+= "<input type='text' name='"+props[e]+"' class='"+props[e]+"' rel='"+feature.fid+"' placeholder='type "+an+" "+props[e]+"' value='"+v+"'/></div>";
	//onfocus='OpenlayerMap.initFieldfuncs(this)'
	}
	//OpenlayerMap.submit("+feature.fid+")
	mform		+= "<div class='property'><input type='submit' value='save' class='button button-primary button-large'></div></form>";
	
	popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                                         feature.geometry.getBounds().getCenterLonLat(),
                                         new OpenLayers.Size(100,100),
                                         "<h2>"+ title +"</h2>" +
                                         mform,
                                         null, true, this.onPopupClose);
    feature.popup = popup;
    popup.feature = feature;
    this.map.addPopup(popup, true);
    
    $("#form_"+feature.fid).find( ".submit" ).click(function() {
		//console.log(obj.element);
	$("#form_"+feature.fid+" input[type=text]").each(function(index){
	feature.attributes[$(this).attr('name')] = $(this).val(); 
	})
	$('#featurePopup_contentDiv').html("<div class='popupmessage'>Properties modified...<br/>Save the page in order to save modifies marker properties.</div>")
	setTimeout(function(){
	popup.destroy();		
	}, 3500);
	
		return false;
	});
    
	},
	
capitaliseFirstLetter: function (string){
    return string.charAt(0).toUpperCase() + string.slice(1);
},
	
formatLonlats: function(lonLat){
		var lat = lonLat.lat;
            var long = lonLat.lon;
            var ns = OpenLayers.Util.getFormattedLonLat(lat);
            var ew = OpenLayers.Util.getFormattedLonLat(long,'lon');
            return ns + ', ' + ew + ' (' + (Math.round(lat * 10000) / 10000) + ', ' + (Math.round(long * 10000) / 10000) + ')';
}
 
 
 

});

DeleteFeature = OpenLayers.Class(OpenLayers.Control, {
    initialize: function(layer, options) {
        OpenLayers.Control.prototype.initialize.apply(this, [options]);
        this.layer = layer;
        this.handler = new OpenLayers.Handler.Feature(
            this, layer, {click: this.clickFeature}
        );
    },

clickFeature: function(feature) {
        // if feature doesn't have a fid, destroy it
        if(feature.fid == undefined) {
            this.layer.destroyFeatures([feature]);
        } else {
            feature.state = OpenLayers.State.DELETE;
            this.layer.events.triggerEvent("afterfeaturemodified", {feature: feature});
            feature.renderIntent = "select";
            this.layer.drawFeature(feature);
        }
    },
    setMap: function(map) {
        this.handler.setMap(map);
        OpenLayers.Control.prototype.setMap.apply(this, arguments);
    },
    CLASS_NAME: "OpenLayers.Control.DeleteFeature"
}) 



$(document).ready(function() {
//fields = new Array("locations", "plekken");
$('#map').OpenLayer({field: "plekken" }); 
$('#map2').OpenLayer({field: "locatie" }); 
});


