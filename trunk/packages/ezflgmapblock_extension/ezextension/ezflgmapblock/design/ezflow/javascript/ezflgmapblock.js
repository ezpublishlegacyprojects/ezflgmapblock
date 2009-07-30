Array.prototype.contains = function (element) 
{
    for (var i = 0; i < this.length; i++) 
    {
        if (this[i] == element) 
        {
            return true;
        }
    }
    return false;
};
  

YAHOO.namespace( 'ezflgmapblock' );

YAHOO.ezflgmapblock.GeoSearchState = function () 
{
    var _properties = new Array();
    _properties['originWasFound'] = false;

    return {
        getState : function ( stateName )
        {
            if ( typeof _properties[stateName] !== 'undefined' )
            {
                //console.log( 'reading state ' + stateName + ' : ' + _properties[stateName] );
                return _properties[stateName];
            }
        },
        
        setState : function ( stateName, value )
        {
            console.log( 'Setting state ' + stateName + ' to : ' + value );
            _properties[stateName] = value;
        }
    }
}();

// Alias for YAHOO.ezflgmapblock.GeoSearchState.getState, for better readability when 
// embedded in DOM elements. 
gs = YAHOO.ezflgmapblock.GeoSearchState.getState;

YAHOO.ezflgmapblock.GeoSearchManager = function () 
{
    // private properties
    var _maxAmountOfAttempts = 5;
    var _baseURL = null;
    var _searchButtonId = null;
    var _gmapMaterialId = null;
    var _map = null;
    var _longitudeElementId = null;
    var _latitudeElementId = null;
    var _radiusElementId = null; 
    var _originElementId = null;
    var _mapId = null;
    var _seed = null;
    var _size = null;
    var _showPopupsOnPage = null;         
    var _locationAttribute = null;
    var _shortDescriptiveAttribute = null;
    var _feedbackId = null;      
    
    // handy shortcuts
    var Dom = YAHOO.util.Dom;
    var Connect = YAHOO.util.Connect;
    var Event = YAHOO.util.Event;
    var Json = YAHOO.lang.JSON;
    
    // private methods
    
    var _createMarker = function ( lat, lng, info, bounds, icon)
    {
      var point = new GLatLng(lat, lng);
      var marker = new GMarker( point, icon );
      GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml(info);
      });
      if (bounds)
      {
          bounds.extend(point);
      }
      return marker;      
    }    
    
    // public methods
    return {
        init : function ( arguments ) 
        {
            _baseURL = arguments.baseURL + "/ajax/geosearch/";
            _searchButtonId = arguments.searchButtonId;
            _gmapMaterialId = arguments.gmapMaterialId;
            _map = arguments.map;
            _latitudeElementId = arguments.latitudeElementId;
            _longitudeElementId = arguments.longitudeElementId;    
            _radiusElementId = arguments.radiusElementId;        
            _originElementId = arguments.originElementId;
            _mapId = arguments.mapId;
            _seed = arguments.seed;
            _size = arguments.size;
            _showPopupsOnPage = arguments.showPopupsOnPage;
            _locationAttribute = arguments.locationAttribute;
            _feedbackId = arguments.feedbackId;
            if ( typeof arguments.shortDescriptiveAttribute != 'indefined' )
                _shortDescriptiveAttribute = arguments.shortDescriptiveAttribute;
                
            _points = null;            
            // console.log( 'YAHOO.ezflgmapblock.GeoSearchManager::init() ' + _baseURL );
            
            // add 'click' listener on search button
            Event.addListener( _searchButtonId , "click" , this.search, 0 );
        },
        
        search : function ( e, attempts )
        {
            // console.log( "YAHOO.ezflgmapblock.GeoSearchManager::search() " + e + " #" + attempts );
            latitude = Dom.get( _latitudeElementId );
            longitude = Dom.get( _longitudeElementId );
            radius = Dom.get( _radiusElementId );
            
            if ( YAHOO.ezflgmapblock.GeoSearchState.getState( 'originWasFound' ) )
            {            
                if ( isNaN( parseInt( radius.value ) ) )
                {
                    var message = 'How far is it acceptable to travel for you ? ( enter the value in meters )';
                    alert( message );
                    Dom.get( _radiusElementId ).focus();
                }
                else
                {
                    var params = "?lat=" + latitude.value + 
                                 "&long=" + longitude.value + 
                                 "&radius=" + radius.value +
                                 "&seed=" + _seed +
                                 "&mapId=" + _mapId +
                                 "&showPopupsOnPage=" + _showPopupsOnPage +
                                 "&width=" + _size[0] +
                                 "&height=" + _size[1] +
                                 "&locationAttribute=" + _locationAttribute;
                    if ( _shortDescriptiveAttribute != null )
                         params += "&shortDescriptiveAttribute=" + _shortDescriptiveAttribute;
                         
                    var uri = _baseURL + params; 
                    uri = encodeURI( uri );

                    var handleFailure = function (o)
                    {
                        // do nothing for now.
                    }

                    var updateMapWithSearchResults = function (o)
                    {
                        if( o.responseText !== undefined )
                        {
                            try {
                                var result = Json.parse( o.responseText );

                                if ( result.searchCount != 0 )
                                {
                                    // WARNING : the returned variable only is available in the local scope ! Store them in the object's property.
                                    eval( result.gmapMaterialPoints );
                                    eval( '_points=' + 'points' + _seed );
                                    YAHOO.ezflgmapblock.GeoSearchState.setState( 'points' + _seed, _points );                                    
                                     
                                    var materialEl = Dom.get( _gmapMaterialId );
                                    materialEl.innerHTML = result.gmapMaterialListing;
                                                                        
                                    // regenerate Map here :
                                    if ( GBrowserIsCompatible() ) 
                                    {
                                      _map = new GMap2( document.getElementById( _mapId ) );
                                      _map.addControl(new GMapTypeControl());
                                      _map.addControl(new GLargeMapControl());
                                      _map.setCenter(new GLatLng(0,0), 0);
                                      var bounds = new GLatLngBounds();
                                
                                      for ( i = 0; i < _points.length; i++ )
                                      {    
                                          _map.addOverlay(_createMarker( _points[i][0].lat(), _points[i][0].lng(), unescape( _points[i][1] ), bounds ) );
                                      }
                                
                                      _map.setMapType( G_NORMAL_MAP );
                                      var center = bounds.getCenter();
                                      var zoom = _map.getBoundsZoomLevel(bounds);
                                      _map.setCenter(center,zoom);
                                      
                                      Dom.get( _feedbackId ).innerHTML = '';
                                    }
                                }
                                else
                                {
                                    Dom.get( _feedbackId ).innerHTML = 'No search result';
                                    // @TODO : notify user that there was not search result
                                    //         using result.feedback
                                }
                            }
                            catch (e) 
                            {
                                console.log( e.description );
                            }
                            // console.log( result );
                        }        
                    }
                                        
                    var callback =
                    {
                        success:updateMapWithSearchResults,
                        failure:handleFailure,
                        scope:this
                    };
                    Connect.asyncRequest( 'GET', uri, callback );
                }
            }
            else
            {
                if ( attempts < _maxAmountOfAttempts )
                {
                    // wait a few seconds and retry, addres resolution is probably running ( against GMaps ).
                    // @FIXME : the line below gives an error. Figure out why.
                    //          see ./code_bits.js
                    //setTimeout( 'this.search( ' + e + ', ' + (++attempts) + ' )', 1000 );
                }
            }
        }
    }
}; // DO NOT execute the function ( returning the object literal ). You need to instantiate the object to use it :
   //        var helpers = new YAHOO.ezflgmapblock.Helpers();