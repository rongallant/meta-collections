$ = jQuery.noConflict();
var obj;


$.widget( "custom.OpenLayer", {
 options: {
field: null
},

_create: function() {

$(this.element).html("");
this.props = new Array("title", "date", "time", "amount");
		
obj			= this;

	DeleteFeature 	= OpenLayers.Class(OpenLayers.Control, {
    	initialize: function(layer, options) {
	        OpenLayers.Control.prototype.initialize.apply(this, [options]);
	        this.layer = layer;
	        this.handler = new OpenLayers.Handler.Feature(
	            this, layer, {click: obj.deleteFeature}
	        );
    },
    CLASS_NAME: "OpenLayers.Control.DeleteFeature"
}) 

this.mapoptions = { controls: [
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.KeyboardDefaults(),
            new OpenLayers.Control.Zoom(),
            ],
			displayProjection: new OpenLayers.Projection("EPSG:4326")
			};
	//console.log(this.options.layers);
	
	
	this.map 		= new OpenLayers.Map($(this.element).attr('id'), this.mapoptions);
    this.wgs84 		= new OpenLayers.Projection("EPSG:4326");
    this.defStyle 	= {strokeColor: "#f54305", strokeOpacity: "0.7", strokeWidth: 1, fillColor: "#f54305", pointRadius: 5, cursor: "pointer"};
    this.sty 		= OpenLayers.Util.applyDefaults(this.defStyle, OpenLayers.Feature.Vector.style["default"]);
    
    this.renderer 	= OpenLayers.Util.getParameters(window.location.href).renderer;
    this.renderer 	= (this.renderer) ? [this.renderer] : OpenLayers.Layer.Vector.prototype.renderers;                            
	    this.vectorLayer= new OpenLayers.Layer.Vector("Vector Layer", {
    		styleMap: new OpenLayers.StyleMap({
            'default': this.sty,
            'select': {strokeColor: "#0074A4", fillColor: "#0074A4", cursor: 'move', pointRadius: 8}
            
			}),
			eventListeners: {
                "loadend": function(request) {
 	               this.map.zoomToExtent(this.getDataExtent());
                }                
             },
			 renderers: this.renderer
    });
    
	this.osm = new OpenLayers.Layer.OSM();    	 
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
	//only load the choosen layers so build an array
    choosen_layers 		= new Array();
	for (var key in this.options.layers) {
    choosen_layers.push(key);
	}

    available_layers 	= new Array(this.osm, this.gphy, this.gmap, this.ghyb, this.gsat);
	visible_layers		= new Array();	
	

	$.each(available_layers, function( index, value ) {		
	if(jQuery.inArray(value.name, choosen_layers)!=-1){
	visible_layers.push(value);
	}

	});
	
	
	visible_layers.push(this.vectorLayer);
    this.map.addLayers(visible_layers);
    
    
	this.map.addControl(new OpenLayers.Control.MousePosition( {id: "ll_mouse", formatOutput: this.formatLonlats} ));
    this.map.addControl(new OpenLayers.Control.MousePosition( {id: "utm_mouse", prefix: "Mercator ", displayProjection: this.map.baseLayer.projection, numDigits: 0} ));
    
    this.drawPoints();
    //console.log(this.options.features);
    
	 
	this.editPanel 		= new OpenLayers.Control.Panel({displayClass: 'editPanel'});
    this.navControl 	= new OpenLayers.Control.Navigation({title: 'Kaart bewegen / zoomen'});
    this.editControl 	= new OpenLayers.Control.ModifyFeature(this.vectorLayer, {dragComplete:obj.modifyFeature,title: 'Punten bewerken'});//12obj.addFeature
    this.drawControl	= new OpenLayers.Control.DrawFeature(this.vectorLayer, OpenLayers.Handler.Point, {featureAdded: this.addnewFeature, displayClass: 'pointButton', title: 'Punt toevoegen', handlerOptions: {style: this.sty}});
    //this.deleteControl  = new DeleteFeature(this.vectorLayer, {title: 'punten wissen'});         
	this.deleteControl	= new DeleteFeature(this.vectorLayer, {title: 'punten wissen'});
    this.deleteControl	= new DeleteFeature(this.vectorLayer, {title: 'punten wissen'}); 
    this.selectfeature	= new OpenLayers.Control.SelectFeature(this.vectorLayer, {clickFeature: function(feature){obj.popupFeature(feature)}, title: 'Punt eigenschappen bewerken'}); 	////
    
    this.switcher = new OpenLayers.Control.LayerSwitcher({"title": "Basislagen"}); 
    this.selectfeature.obj = this;
    this.editPanel.addControls([
            this.navControl,
            this.editControl,
            this.drawControl,
            this.deleteControl,
			this.selectfeature,
			this.switcher                           
        ]);
        
     this.editPanel.defaultControl = this.editControl;//this.selectfeature;
     this.map.addControl(this.editPanel);
  
  
     this.map.setCenter(
        new OpenLayers.LonLat(this.options.longitude,this.options.latitude).transform(
            new OpenLayers.Projection("EPSG:4326"),
            this.map.getProjectionObject()
        ), 
        
        this.options.zoom
    );
    
    
	 },

drawPoints:function(){

savedfeatures = $.parseJSON(this.options.features);
features 	= new Array();
$.each(savedfeatures[0], function( index, feature) {

	point = new OpenLayers.Geometry.Point(feature.lon,feature.lat).transform(
      new OpenLayers.Projection("EPSG:4326"),
            obj.map.getProjectionObject()
       );

	f = new OpenLayers.Feature.Vector(point,{"title": feature.title, "date": feature.date, "time": feature.time, "amount": feature.amount});
    
    f.lonlat 	= new OpenLayers.LonLat(feature.lon,feature.lat).transform(
      new OpenLayers.Projection("EPSG:4326"),
       obj.map.getProjectionObject()
    );
	
	features.push(f);
	

});

//console.log(features)
this.vectorLayer.addFeatures(features)
this.updateFeatureInfo();
},

modifyFeature:function(Sfeature){		
	console.log(Sfeature);

	$.each(obj.vectorLayer.features, function( index, feature ) {
		if(Sfeature.id==feature.id){
		lonlat = new OpenLayers.LonLat(Sfeature.geometry.x, Sfeature.geometry.y);
		//.transform(
         //      obj.map.getProjectionObject(),
		//	   new OpenLayers.Projection("EPSG:4326")
		//);
		//feature.lonlat = lonlat 	
		}

	});
	
	obj.updateFeatureInfo();
	//console.log(obj.vectorLayer.features);
},
	
addnewFeature: function(feature){
//console.log(arguments);
	
	for (var e=0;e<obj.props.length;e++) {
	feature.attributes[obj.props[e]]="";
	}
	
	//latlon =	feature.geometry.transform(  
    //this.map.getProjectionObject(),
    //new OpenLayers.Projection("EPSG:4326")
    //);
	
    //feature.lonlat = new OpenLayers.LonLat(latlon.x,latlon.y);
	feature.lonlat = new OpenLayers.LonLat(feature.geometry.x,feature.geometry.y);
	//console.log(feature);

	obj.updateFeatureInfo();		
	 
	},
	
deleteFeature: function(feature){
	//alert(23)
	 if(feature.fid == undefined) {
            feature.state = OpenLayers.State.DELETE;
            obj.vectorLayer.destroyFeatures([feature]);
     } else {
            feature.state = OpenLayers.State.DELETE;
            obj.vectorLayer.events.triggerEvent("afterfeaturemodified", {feature: feature});
            feature.renderIntent = "select";
            obj.vectorLayer.drawFeature(feature);
    }
    // feature.destroy(); 
	obj.updateFeatureInfo();
	console.log(feature);
	},
	 
	 
updateFeatureInfo: function(sfeature){
//sync the feature list, attrubutes and latlongs with the input field
		
		
		features = {};
		$.each(this.vectorLayer.features, function( index, feature ) {

		lonlat = new OpenLayers.LonLat(feature.lonlat.lon,feature.lonlat.lat).transform(
               obj.map.getProjectionObject(),
			   new OpenLayers.Projection("EPSG:4326")
		);
		//feature.lonlat = new OpenLayers.LonLat(feature.lonlat.lon,feature.lonlat.lat);
		
		f 		= feature.attributes
		f.lon 	= lonlat.lon;
		f.lat 	= lonlat.lat;
		
		features[feature.id] = f
		
		});

//console.log(features);
		$("#"+obj.options.input).val(JSON.stringify(features));
		

	},
	
popupFeature: function (feature){
	
	feature.fid	= (feature.fid==null) ? 10 : feature.fid;
	hasprops	= (Object.keys(feature.attributes).length > 0) ? 1 : 0;
	selFeature 	= feature;
	title		= "Eigenschappen";
	mform		= "<form class='propertyform' id='form_"+feature.fid+"'>";
	for (var e=0;e<this.props.length;e++) {
	an 			= (this.props[e]=="amount") ? "an":"a";
	ititle 		= obj.capitaliseFirstLetter(this.props[e]);
	v 			= (feature.attributes[this.props[e]]!=undefined)? feature.attributes[this.props[e]]: "";
	mform		+= "<div class='property'><label for='"+feature.attributes[this.props[e]]+"'>"+ititle+":</label>";
	mform		+= "<input type='text' name='"+this.props[e]+"' class='"+this.props[e]+"' rel='"+feature.fid+"' placeholder='type "+an+" "+this.props[e]+"' value='"+v+"'/></div>";
	//onfocus='OpenlayerMap.initFieldfuncs(this)'
	}
	//OpenlayerMap.submit("+feature.fid+")
	mform		+= "<div class='property'><input type='submit' value='save' class='button button-primary button-large submit'></div></form>";
	
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
	
	
	$("#form_"+feature.fid+" input[type=text]").each(function(index){
	feature.attributes[$(this).attr('name')] = $(this).val(); 
	})
	$('#featurePopup_contentDiv').html("<div class='popupmessage'>Properties modified...<br/>Save the page in order to save modifies marker properties.</div>")
	setTimeout(function(){
	popup.destroy();		
	console.log(obj);
	obj.updateFeatureInfo();
	}, 2500);
	
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





$(document).ready(function() {
//fields = new Array("locations", "plekken");
$('#map').OpenLayer({field: "plekken" }); 
$('#map2').OpenLayer({field: "locatie" }); 
});


