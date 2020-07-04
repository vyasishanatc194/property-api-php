<?php
//including the constant variables & database connection file
include_once("config.php");
//getting id of the data from url
$item_id = (isset($_GET['id'])) ? $_GET['id'] : 0;
$query = "SELECT * FROM properties WHERE `uuid` = '$item_id';";
$item = fetchAssociativeArray($query);
if ($item && isset($item['uuid'])) {
	//deleting all files of the data 
	deleteDirectory("uploads/" . $TABLE_PROPERTY . "/" . $item['uuid']);
	//deleting the row from table
	$result = sqlQuery("DELETE FROM $TABLE_PROPERTY WHERE uuid='$item_id'");
	if ($result) {
		$_SESSION['message_success'] = "Record deleted!";
	} else {
		$_SESSION['message_error'] = "Something went wrong!";
	}
} else {
	$_SESSION['message_warning'] = "Data not found!";
}
//redirecting to the display page (index.php in our case)
header("Location:index.php");
