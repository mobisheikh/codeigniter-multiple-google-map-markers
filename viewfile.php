
<script type='text/javascript' src='<?php echo base_url();?>/assets/system_design/maps/jquery-migrate.js'></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyALpgIN4lLtLTmIUkRSLSRlyVfltW2xHs8">
</script>
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
						$content = $location['content'];
						$map_lat = $location['google_map']['lat'];
						$map_lng = $location['google_map']['lng'];
						?>
						/* Set Bound Marker */
						var latlng = new google.maps.LatLng(<?php echo $map_lat; ?>, <?php echo $map_lng; ?>);
						bounds.push(latlng);
						var marker;

						// every 10 seconds
						//setInterval(updateMarker,5000);
						/* Add Marker */
						map.addMarker({
							lat: <?php echo $map_lat; ?>,
							lng: <?php echo $map_lng; ?>,
							title: '<?php echo $name; ?>',
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
					//map.setCenter(-37.814107,144.963280);
					//setInterval(reloadMap,9000);
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
						
					}, 10000); //10 seconds
					
					
					function resetMarkers(data)
					{
						map.removeMarkers();
						map.refresh(map);
						
						obj = JSON.parse(data);
						console.log(obj.length);
						
						$.each(obj, function(i, item) {
									map.addMarker({
												lat: item.google_map.lat, //-37.876964,//
												lng: item.google_map.lng,// 145.058542,//
												title: item.location_name,
												zoom: 8,
												infoWindow: {
												content: item.content//content//'<p>'+item.location_name+'</p>'newContent//
											}
	
										});
						});
						
						map.fitZoom();
								 
					}
				});
				
				</script>

<div class="google-map-wrap" itemscope itemprop="hasMap" itemtype="http://schema.org/Map">
	<div id="google-map" class="google-map">
	</div><!-- #google-map -->
</div>
			
