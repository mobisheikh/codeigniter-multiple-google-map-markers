//----------------This is COntroller file

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends MY_Controller

{	
	function __construct()
	{
		parent::__construct();
	
	}
	public function getNewLocations()
	{
		if(isset($_POST['func']) && $_POST['func'] === 'getNewLocations')
		{
			$date = date('Y-m-d', time()); //'2016-04-08';//
			$today_day = date("l");//'Friday';//
			$get_vehicles_locations = $this->db->query("select vu.username as driver_name,vv.name as vehicle_name,vv.model as vehicle_model,vbcl.id, 
			vbcl.lat, vbcl.long, vbcl.vehicle_id,vbcl.current_vehicle_location,vbcl.date_time from vbs_vehicle_current_locations vbcl
			JOIN vbs_vehicle vv ON vbcl.vehicle_id=vv.id
			JOIN vbs_vehicle_driver vd ON vv.id=vd.vehicle_id
			JOIN vbs_users vu ON vd.driver_id=vu.id 
			WHERE vd.day_of_week = '$today_day'
			")->result();
			$locations = array();

                if($get_vehicles_locations){

					foreach($get_vehicles_locations as $row){
						//$getLatLong = $this->getLatLongForAddress($row->current_vehicle_location);
						//echo $getLatLong['lat'];
						/* Marker*/
						
						$content = '<div><span><strong>Vehicle Name:</strong> '.$row->vehicle_model.' '.$row->vehicle_name.'</span><span style="width: 45%;float: left;"><strong>Driver Name:</strong> '.$row->driver_name.'</span><br/><span><strong>Current Location:</strong> '.$row->current_vehicle_location.'</span><table width="500" border="1"><thead><tr><th style="text-align: center;" width="100">Client Name</th><th style="text-align: center;" width="150">Pickup Address</th><th style="text-align: center;" width="150">Dropoff Address</th><th style="text-align: center;" width="75">Job Status</th></tr></thead>'.$this->getVehilePickedJobs($row->vehicle_id).'</table></div>'; //'.$vehiclePickedJob.'
						$locations[] = array(
							'google_map' => array(
								'lat' => $row->lat,//$getLatLong['lat'],
								'lng' => $row->long,//$getLatLong['long'],
							),
							'location_address' => $row->current_vehicle_location,
							'location_name'    => $row->vehicle_model.' '.$row->vehicle_name.' ['.$row->current_vehicle_location.']',
							'vehicle_id' => $row->vehicle_id,
							'vehicle_name' => $row->vehicle_model.' '.$row->vehicle_name,
							'driver_name' => $row->driver_name,
							'content' => $content,
						);
						
					}				
				
				} else {
					$locations = '';
				}

				echo json_encode($locations);
		}
	}
	
	
}
