<script type='text/javascript' src='<?php echo base_url();?>/assets/system_design/maps/jquery-migrate.js'></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyALpgIN4lLtLTmIUkRSLSRlyVfltW2xHs8">
</script>
<!--<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>-->
<script type='text/javascript' src='<?php echo base_url();?>/assets/system_design/maps/gmaps.js'></script>
<script>
				jQuery( document ).ready( function($) {

					/* Do not drag on mobile. */
					var is_touch_device = 'ontouchstart' in document.documentElement;
					
					var map = new GMaps({
						el: '#google-map',
						lat: -37.814107,//'<?php //echo $map_area_lat; ?>',
						lng: 144.963280,//'<?php //echo $map_area_lng; ?>',
						scrollwheel: false,
						zoom: 8,
						draggable: ! is_touch_device
					});
					
					/* Map Bound */
					var bounds = [];

					<?php /* For Each Location Create a Marker. */
					foreach( $locations as $location ){
						$name = $location['location_name'];
						$addr = $location['location_address'];
						$vehicle_id = $location['vehicle_id'];
						$vehicle_name = $location['veh_name'];
						$vehicle_name_model = $location['vehicle_name'];
						$content = $location['content'];
						$map_lat = $location['google_map']['lat'];
						$map_lng = $location['google_map']['lng'];
						?>
						/* Set Bound Marker */
						var latlng = new google.maps.LatLng(<?php echo $map_lat; ?>, <?php echo $map_lng; ?>);
						bounds.push(latlng);
						var marker;

						/* Add Marker */
						map.addMarker({
							lat: <?php echo $map_lat; ?>,
							lng: <?php echo $map_lng; ?>,
							title: '<?php echo $name; ?>',
							label: '<?php echo $vehicle_name; ?>',
							zoom: 8,
							animation: google.maps.Animation.DROP,
							infoWindow: {
								content: '<?php echo $content; ?>'
							}
						});
					<?php } //end foreach locations ?>

					/* Fit All Marker to map */
					map.fitLatLngBounds(bounds);
					//map.setZoom(2);
					/* Make Map Responsive */
					var $window = $(window);
					function mapWidth() {
						var size = $('.google-map-wrap').width();
						$('.google-map').css({width: size + 'px', height: (size/2) + 'px'});
					}
					mapWidth();

					$(window).resize(mapWidth);
					map.fitZoom();
					
					setInterval(function(){
						
						$.ajax({
							  url: "<?php echo site_url().'/admin/getNewLocations' ?>",
							  type: "POST",
							   data: {
								func: 'getNewLocations',
								'<?php echo $this->security->get_csrf_token_name(); ?>' : 
								'<?php echo $this->security->get_csrf_hash(); ?>'
								},
							  success: function(data) {
								  resetMarkers(data);
							  }
							});
						
					}, 10000); //30 seconds
					
					
					function resetMarkers(data)
					{
						map.removeMarkers();
					//	map.refresh(map);

						obj = JSON.parse(data);
						console.log(obj.length);
						
						$.each(obj, function(i, item) {
								
				map.addMarker({
						lat: item.google_map.lat, //-37.876964,//
						lng: item.google_map.lng,// 145.058542,//
						title: item.location_name,
						label: item.veh_name,
						zoom: 8,
						infoWindow: {
								content: item.content//content//'<p>'+item.location_name+'</p>'newContent//
							}
	
										});
						});
						
						//Retain Map Zoom Level
						google.maps.event.addListener(map, 'zoom_changed', saveMapState);
    						google.maps.event.addListener(map, 'dragend', saveMapState);
								 
					}


					// functions below

					function saveMapState() { 
					    var mapZoom=map.getZoom(); 
					    var mapCentre=map.getCenter(); 
					    var mapLat=mapCentre.lat(); 
					    var mapLng=mapCentre.lng(); 
					    var cookiestring=mapLat+"_"+mapLng+"_"+mapZoom; 
					    setCookie("myMapCookie",cookiestring, 30); 
					} 

					function loadMapState() { 
					    var gotCookieString=getCookie("myMapCookie"); 
					    var splitStr = gotCookieString.split("_");
					    var savedMapLat = parseFloat(splitStr[0]);
					    var savedMapLng = parseFloat(splitStr[1]);
					    var savedMapZoom = parseFloat(splitStr[2]);
					    if ((!isNaN(savedMapLat)) && (!isNaN(savedMapLng)) && (!isNaN(savedMapZoom))) {
					        map.setCenter(new google.maps.LatLng(savedMapLat,savedMapLng));
					        map.setZoom(savedMapZoom);
					    }
					}

					function setCookie(c_name,value,exdays) {
					    var exdate=new Date();
					    exdate.setDate(exdate.getDate() + exdays);
					    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
					    document.cookie=c_name + "=" + c_value;
					}

					function getCookie(c_name) {
					    var i,x,y,ARRcookies=document.cookie.split(";");
					    for (i=0;i<ARRcookies.length;i++)
					    {
					      x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
					      y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
					      x=x.replace(/^\s+|\s+$/g,"");
					      if (x==c_name)
					        {
					        return unescape(y);
					        }
					      }
					    return "";
					}
				});
				
				</script>

<div class="col-md-10 padding white right-p">
   <div class="content">
      <div class="main-hed">
         <a href="<?php echo site_url();?>/auth"><?php echo $this->lang->line('home');?></a> 
         <?php if(isset($title)) echo " >> Trip Sheet >> ".$title;?>
      </div>
	  
      <div class="col-md-12 padding-p-r">
         <div class="module">
		 <div class="module-head">
               <h3><?php echo $title;?></h3>
            </div>
			
			<div class="google-map-wrap" itemscope itemprop="hasMap" itemtype="https://schema.org/Map">
					<div id="google-map" class="google-map">
					</div><!-- #google-map -->
			</div>
			
         </div>
      </div>
      <!--/.module--> 
   </div>
   <!--/.content--> 
</div>

