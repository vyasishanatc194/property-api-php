<?php
//form validation for rules required , minlength , maxlength & number , filesize
function formValidation($post, $rules)
{
    $result = [];
    foreach ($rules as $key => $value) {
        $error = [];
        if (isset($value["required"]) && (!isset($post[$key]) || $post[$key] == "")) {
            $error["required"] = true;
        }
        if (isset($value["minlength"]) && isset($post[$key]) && $post[$key] && empty($error)) {
            if (strlen($post[$key]) < $value['minlength']) {
                $error["minlength"] = true;
            }
        }
        if (isset($value["maxlength"]) && isset($post[$key]) && $post[$key] && empty($error)) {
            if (strlen($post[$key]) > $value['maxlength']) {
                $error["maxlength"] = true;
            }
        }
        if (isset($value["number"]) && isset($post[$key]) && $post[$key] && empty($error)) {
            if (!is_numeric($post[$key])) {
                $error["number"] = true;
            }
        }
        if (isset($value["filesize"]) && file_exists($_FILES[$key]['tmp_name'])) {
            if ($_FILES[$key]["size"] > $value['filesize']) {
                $error["filesize"] = true;
            }
        }
        if (!empty($error)) {
            $result[$key] = $error;
        }
    }
    return $result;
}
//Upload image with resize
function uploadImage($post, $path)
{
    $target_dir = "uploads/" . $path . "/";
    $thumb_dir = "uploads/" . $path . "/thumb/";
    //Create item specific directory if not exist
    file_exists($target_dir) or mkdir($target_dir, 0777, true);
    file_exists($thumb_dir) or mkdir($thumb_dir, 0777, true);

    $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $real_name = basename($_FILES["image"]["name"]);
    $name = uniqid() . "." . $ext;
    $target_file = $target_dir . $name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        //create thumb image of size 300x200
        img_resize($target_file, $thumb_dir . '/' . $name, 300, 200, $ext);
        return $target_file;
    }
    return "";
}
//delete directory with image or subfolder inside
function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
//find thumb image from url (Same name different folder)
function getThumbImageUrl($imageUrl)
{
    $arrayofdir = explode("/", $imageUrl);
    array_splice($arrayofdir, count($arrayofdir) - 1, 0, ['thumb']);

    $thumb_path = implode("/", $arrayofdir);
    if (file_exists($thumb_path)) {
        return $thumb_path;
    }
    //return main url if thumb image not exist
    return $imageUrl;
}
//define property image load from api data or localy uploaded
function getPropertyImageUrls($item, $isDefault = 0)
{
    //default image url
    $default_image = "assets/images/no-image.png";
    $result = ["image_thumb_url" => "", "image_full_url" => ""];
    //if there is localy uploaded file, load it first or file url from api
    if (isset($item['image_local']) && $item['image_local'] != "") {
        $result = ["image_thumb_url" => getThumbImageUrl($item['image_local']), "image_full_url" => $item['image_local']];
    } else if (isset($item['image_full']) && $item['image_full'] != "") {
        $result = ["image_thumb_url" => $item['image_thumbnail'], "image_full_url" => $item['image_full']];
    }
    if ($result['image_thumb_url'] == "" && $isDefault) {
        $result['image_thumb_url'] = $default_image;
    }
    if ($result['image_full_url'] == "" && $isDefault) {
        $result['image_full_url'] = $default_image;
    }
    return $result;
}
//image resize function
function img_resize($target, $newcopy, $w, $h, $ext)
{
    list($w_orig, $h_orig) = getimagesize($target);
    $scale_ratio = $w_orig / $h_orig;
    if (($w / $h) > $scale_ratio) {
        $w = $h * $scale_ratio;
    } else {
        $h = $w / $scale_ratio;
    }
    $img = "";
    $ext = strtolower($ext);
    if ($ext == "gif") {
        $img = imagecreatefromgif($target);
    } else if ($ext == "png") {
        $img = imagecreatefrompng($target);
    } else {
        $img = imagecreatefromjpeg($target);
    }
    $tci = imagecreatetruecolor($w, $h);
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    imagejpeg($tci, $newcopy, 80);
}
function apiCall($url, $method, $content = NULL)
{
    try {
        $ch = curl_init($url); // your URL to send array data
        if ($content !== NULL) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        }
        switch (strtolower($method)) {

            case "get":
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;

            case "post":
                curl_setopt($ch, CURLOPT_POST, true);
                break;

            case "put":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;

            case "delete":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, []);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        //to enable ssl or not (in ourcase no need)
        if (0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        $result = curl_exec($ch);
        if (!curl_errno($ch)) {
            return json_decode($result, true);
        } else {
            return [];
        }
    } catch (\Exception $e) {
        echo ($e->getMessage());
    }
}
//Create default 1 entry for property_type
function setDefaultPropertyType()
{
    $property_type_array = ["id" => 1, "title" => "Flat", "description" => "Default value", "created_at" => date("Y-m-d H:i:s"), "updated_at" => date("Y-m-d H:i:s")];
    if (createOrUpdateQuery($property_type_array, "property_type", "id", "create")) {
        return [$property_type_array];
    }
    return [];
}

//Sql query functions
function fetchDataArray($query)
{
    global $MYSQLI;
    return mysqli_fetch_all(mysqli_query($MYSQLI, $query), MYSQLI_ASSOC);
}
function fetchAssociativeArray($query)
{
    global $MYSQLI;
    return mysqli_fetch_assoc(mysqli_query($MYSQLI, $query));
}
function sqlQuery($query)
{
    global $MYSQLI;
    return mysqli_query($MYSQLI, $query);
}
function countQuery($query)
{
    global $MYSQLI;
    return mysqli_num_rows(mysqli_query($MYSQLI, $query));
}
//Function to insert or update --update if record already exist
function createOrUpdateQuery($array, $table_name, $pkfield, $querytype = "")
{
    global $MYSQLI;
    $count = 0;
    $fields = '';
    foreach ($array as $col => $val) {
        if ($count++ != 0) $fields .= ', ';
        $col = mysqli_real_escape_string($MYSQLI, $col);
        $val = mysqli_real_escape_string($MYSQLI, $val);
        $fields .= "`$col` = '$val'";
    }
    //inset query
    $query = "INSERT INTO $table_name SET $fields;";
    //if query type not define or update check uuid to define insert or update 
    if ($querytype != "create") {
        $uuid = $array[$pkfield];
        $numrows = countQuery("SELECT * FROM $table_name WHERE $pkfield = '$uuid'");
        if ($numrows > 0) {
            $query = "UPDATE $table_name SET $fields WHERE $pkfield = '$uuid';";
        }
    }
    if (sqlQuery($query)) {
        return 1;
    }
    return 0;
}
//Function to get properties data with search/filter and pagination
function getSearchAndFilteredData($requestdata)
{
    global $DATA_LISTING_PER_PAGE;
    global $TABLE_PROPERTY;
    global $TABLE_PROPERTY_TYPE;
    //get property data with search and filter option
    $perPage = $DATA_LISTING_PER_PAGE;
    $page = (isset($requestdata['page'])) ? (int) $requestdata['page'] : 1;
    $startAt = $DATA_LISTING_PER_PAGE * ($page - 1);

    //Search and filter condition
    $filterwhere = " where 1=1";
    if (isset($requestdata['filter_from']) && $requestdata['filter_from'] != "") {
        $filterwhere .= " AND ( $TABLE_PROPERTY.created_from = '" . $requestdata['filter_from'] . "')";
    }
    if (isset($requestdata['filter_type']) && $requestdata['filter_type'] != "") {
        $filterwhere .= " AND ( $TABLE_PROPERTY.type = '" . $requestdata['filter_type'] . "')";
    }
    if (isset($requestdata['search']) && $requestdata['search'] != "") {
        $search = $requestdata['search'];
        $filterwhere .= " AND ( $TABLE_PROPERTY.uuid like '%" . $search . "%' OR $TABLE_PROPERTY.county like '%" . $search . "%' OR $TABLE_PROPERTY.country like '%" . $search . "%' OR $TABLE_PROPERTY.address like '%" . $search . "%' OR $TABLE_PROPERTY.price like '%" . $search . "%' OR $TABLE_PROPERTY.postcode like '%" . $search . "%' OR $TABLE_PROPERTY_TYPE.title like '%" . $search . "%' )";
    }

    $queryfromwhere = " FROM $TABLE_PROPERTY INNER JOIN $TABLE_PROPERTY_TYPE ON $TABLE_PROPERTY.property_type_id=$TABLE_PROPERTY_TYPE.id" . $filterwhere;

    $listquery = "SELECT $TABLE_PROPERTY.*,$TABLE_PROPERTY_TYPE.title" . $queryfromwhere  . " ORDER BY `$TABLE_PROPERTY`.`updated_at` DESC LIMIT $startAt,$DATA_LISTING_PER_PAGE";
    $countquery = "SELECT COUNT(*) as total" . $queryfromwhere;

    $pagination = fetchAssociativeArray($countquery);
    return [
        "data" => fetchDataArray($listquery),
        "total_page" => ceil($pagination['total'] / $DATA_LISTING_PER_PAGE),
    ];
}
