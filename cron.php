<?php
//including the constant variables & database connection file
include_once("config.php");

$url = $API_BASE_URL . "/properties?api_key=" . $API_KEY . "&page%5Bsize%5D=" . $API_PER_PAGE;
$is_complete = 0;
$counter = 0;
//Define all fillable field of table property_type
$property_type_fillable = ['id', 'title', 'description', 'created_at', 'updated_at'];
//Define all fillable field of table properties
$property_fillable = ["uuid", "property_type_id", "county", "country", "town", "description", "address", "image_full", "image_thumbnail", "image_local", "latitude", "longitude", "num_bedrooms", "num_bathrooms", "price", "type", "created_from", "postcode", 'created_at', 'updated_at'];

//To get all paginated data 
do {
	$api_data = apiCall($url, 'get', NULL);
	if (isset($api_data['data']) && count($api_data['data'])) {
		foreach ($api_data['data'] as $_data) {
			$property_type = $_data['property_type'];
			//Create or update property_type record
			if (isset($property_type['id'])) {
				$property_type['updated_at'] = date("Y-m-d H:i:s");
				$property_type_data = array_intersect_key($property_type, array_flip($property_type_fillable));
				createOrUpdateQuery($property_type_data, $TABLE_PROPERTY_TYPE, "id");
			}
			$_data['created_from'] = "live";
			$_data['image_local'] = "";
			$_data['updated_at'] = date("Y-m-d H:i:s");
			$_data = array_intersect_key($_data, array_flip($property_fillable));
			//Delete locally uploaded images
			deleteDirectory("uploads/" . $TABLE_PROPERTY . "/" . $_data['uuid']);
			//Create or update property record 
			if (createOrUpdateQuery($_data, $TABLE_PROPERTY, "uuid")) {
				$counter = $counter + 1;
			}
		}
		// To break loop after number of page
		if ($api_data['current_page'] > $API_MAX_CALL) {
			$is_complete = 1;
		}
		// To break loop after end of all page
		if ($api_data['current_page'] >= $api_data['last_page']) {
			$is_complete = 1;
		}
		// To break loop after last page
		if ($api_data['next_page_url']) {
			$url = $api_data['next_page_url'];
		} else {
			$is_complete = 1;
		}
	} else {
		$is_complete = 1;
	}
} while ($is_complete != 1);

echo $counter . " rows affected!";
