<?php
//including the constant variables & database connection file
include_once("config.php");
//Define empty array for errors
$validation_errors = [];
//Submited form 
if (isset($_POST) && count($_POST)) {

	$input = $_POST;
	//Serverside validation rules
	$validation_rules = [
		"property_type_id" => ["required" => true],
		"country" => ["required" => true, "minlength" => 2, "maxlength" => 60],
		"postcode" => ["number" => true, "minlength" => 2, "maxlength" => 60],
		"price" => ["required" => true, "number" => true],
		"image" => ["filesize" => $MAX_INPUT_FILE_SIZE],
	];
	$validation_errors = formValidation($input, $validation_rules);
	//Check if conain any validation error or not
	if (!empty($validation_errors)) {
		$_SESSION['message_warning'] = "Input data is not in correct format";
	} else {
		//Define all fillable field of table properties
		$property_fillable = ["uuid", "property_type_id", "county", "country", "town", "description", "address", "image_full", "image_thumbnail", "image_local", "latitude", "longitude", "num_bedrooms", "num_bathrooms", "price", "type", "created_from", "postcode", 'created_at', 'updated_at'];
		$methodtype = "";
		//edit record
		if (isset($input['item_id']) && $input['item_id'] != "") {
			$methodtype = "updtae";
			$item_id = $input['item_id'];
			$query = "SELECT * FROM $TABLE_PROPERTY WHERE `uuid` = '$item_id';";
			$item = fetchAssociativeArray($query);
			//verify if record exists 
			if (!isset($item['uuid'])) {
				$_SESSION['message_warning'] = "No data found!";
				//redirecting to the display page (index.php in our case)
				header('Location: index.php');
				exit;
			}
			$input['uuid'] = $item['uuid'];
		} else {
			//create new record with auto generate uuid
			$methodtype = "create";
			$input['uuid'] = uniqid();
			$input['created_from'] = "local";
			$input['created_at'] = date("Y-m-d H:i:s");
		}
		$input['updated_at'] = date("Y-m-d H:i:s");
		//verify input image before upload
		if (file_exists($_FILES['image']['tmp_name']) && $_FILES["image"]["size"] < $MAX_INPUT_FILE_SIZE) {
			$imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
			if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
				$image_local = uploadImage($_POST, $TABLE_PROPERTY . "/" . $input['uuid']);
				if ($image_local != "") {
					$input['image_local'] = $image_local;
				}
			}
		}
		//get only table fillable fields from post data
		$input = array_intersect_key($input, array_flip($property_fillable));
		if (createOrUpdateQuery($input, $TABLE_PROPERTY, "uuid", $methodtype)) {
			if ($methodtype == "create") {
				$_SESSION['message_success'] = "New record created!";
			} else {
				$_SESSION['message_success'] = "Record updated!";
			}
		} else {
			$_SESSION['message_error'] = "Something went wrong!";
		}
		//redirecting to the display page (index.php in our case)
		header('Location: index.php');
		exit;
	}
}
//For edit record
if ((isset($_GET['id']))) {
	$item_id = (isset($_GET['id'])) ? $_GET['id'] : 0;
	$query = "SELECT * FROM $TABLE_PROPERTY WHERE `uuid` = '$item_id';";
	$item = fetchAssociativeArray($query);
	if (!$item || !isset($item['uuid'])) {
		$_SESSION['message_warning'] = "Data not found!";
		//redirecting to the display page (index.php in our case)
		header("Location:index.php");
	}
	$method = "edit";
} else {
	$item = [];
	$method = "create";
}
//get relational data property_type
$listquery = "SELECT * FROM property_type ORDER BY id";
$property_type_array = fetchDataArray($listquery);
if (!count($property_type_array)) {
	//create default value if no value
	$property_type_array = setDefaultPropertyType();
}
$no_of_arr = [1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6'];
?>

<?php include_once("template/header.php"); ?>
<div class="container">
	<div class="row ">
		<div class="col-md-12">
			<h2><?php echo ucfirst($method); ?> Property </h2>
		</div>
	</div>
	<a href="index.php">Back</a>
	<form method="POST" id="createorupdate" accept-charset="UTF-8" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">
		<input name="item_id" type="hidden" value="<?php echo (isset($item['uuid'])) ? $item['uuid'] : ""; ?>">
		<input name="method" type="hidden" value="POST">
		<div class="row ">
			<div class="col-md-6">
				<?php if (isset($item['uuid'])) : ?>
					<div class="form-group <?php echo (isset($validation_errors['uuid'])) ? 'has-error' : ''; ?>">
						<label for="uuid" class="">
							<span class="field_compulsory"></span>Uuid
						</label>
						<input class="form-control" disabled="disabled" name="uuid" type="text" value="<?php echo (isset($item['uuid'])) ? $item['uuid'] : ""; ?>">
					</div>
				<?php endif; ?>
				<div class="form-group <?php echo (isset($validation_errors['county'])) ? 'has-error' : ''; ?>">
					<label for="county" class="">
						<span class="field_compulsory">*</span>County
					</label>
					<input class="form-control" name="county" type="text" value="<?php echo (isset($item['county'])) ? $item['county'] : ""; ?>">
				</div>
				<div class="form-group <?php echo (isset($validation_errors['country'])) ? 'has-error' : ''; ?>">
					<label for="country" class="">
						<span class="field_compulsory">*</span>Country
					</label>
					<input class="form-control" name="country" type="text" value="<?php echo (isset($item['country'])) ? $item['country'] : ""; ?>">
				</div>
				<div class="form-group <?php echo (isset($validation_errors['postcode'])) ? 'has-error' : ''; ?>">
					<label for="postcode" class="">
						<span class="field_compulsory"></span>Postcode
					</label>
					<input class="form-control" name="postcode" type="text" value="<?php echo (isset($item['postcode'])) ? $item['postcode'] : ""; ?>">
				</div>
				<div class="form-group <?php echo (isset($validation_errors['description'])) ? 'has-error' : ''; ?>">
					<label for="description" class="">
						<span class="field_compulsory">*</span>Description
					</label>
					<textarea rows="2" class="form-control" autocomplete="off" name="description" cols="50"><?php echo (isset($item['description'])) ? $item['description'] : ""; ?></textarea>
				</div>
				<div class="form-group <?php echo (isset($validation_errors['address'])) ? 'has-error' : ''; ?>">
					<label for="address" class="">
						<span class="field_compulsory">*</span>Address
					</label>
					<input class="form-control" name="address" type="text" value="<?php echo (isset($item['address'])) ? $item['address'] : ""; ?>">
				</div>
				<div class="form-group <?php echo (isset($validation_errors['town'])) ? 'has-error' : ''; ?>">
					<label for="town" class="">
						<span class="field_compulsory">*</span>Town
					</label>
					<input class="form-control" name="town" type="text" value="<?php echo (isset($item['town'])) ? $item['town'] : ""; ?>">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group <?php echo (isset($validation_errors['image'])) ? 'has-error' : ''; ?>">
					<label for="logo">Uploaded Image </label>
					<div class="">
						<div class="row">
							<?php $image_urls = getPropertyImageUrls($item, 0); ?>
							<?php if (isset($image_urls['image_full_url']) && $image_urls['image_full_url'] != "") { ?>
								<div class="col-sm-2 relative-container">
									<a href="<?php echo $image_urls['image_full_url']; ?>">
										<img src="<?php echo $image_urls['image_thumb_url']; ?>" height="75" />
									</a>
								</div>
							<?php } ?>
						</div>
						<input class="form-control" accept="image/*" name="image" type="file">
					</div>
				</div>
				<div class="form-group <?php echo (isset($validation_errors['num_bedrooms'])) ? 'has-error' : ''; ?>">
					<label for="num_bedrooms" class="">
						<span class="field_compulsory">*</span>Number of bedrooms
					</label>
					<select class="form-control" name="num_bedrooms">
						<?php foreach ($no_of_arr as $k => $val) : ?>
							<option value="<?php echo $k; ?>" <?php echo (isset($item['num_bedrooms']) && $item['num_bedrooms'] == $k) ? "selected" : ""; ?>>
								<?php echo $val; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group <?php echo (isset($validation_errors['num_bathrooms'])) ? 'has-error' : ''; ?>">
					<label for="num_bathrooms" class="">
						<span class="field_compulsory">*</span>Number of bathrooms
					</label>
					<select class="form-control" name="num_bathrooms">
						<?php foreach ($no_of_arr as $k => $val) : ?>
							<option value="<?php echo $k; ?>" <?php echo (isset($item['num_bathrooms']) && $item['num_bathrooms'] == $k) ? "selected" : ""; ?>>
								<?php echo $val; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group <?php echo (isset($validation_errors['price'])) ? 'has-error' : ''; ?>">
					<label for="price" class="">
						<span class="field_compulsory">*</span>Price
					</label>
					<input class="form-control" name="price" type="number" value="<?php echo (isset($item['price'])) ? $item['price'] : ""; ?>">
				</div>
				<div class="form-group <?php echo (isset($validation_errors['property_type_id'])) ? 'has-error' : ''; ?>">
					<label for="price" class="">
						<span class="field_compulsory">*</span>Property type
					</label>
					<select class="form-control" name="property_type_id">
						<?php foreach ($property_type_array as $_type) : ?>
							<option value="<?php echo $_type['id']; ?>" <?php echo (isset($item['property_type_id']) && $item['property_type_id'] == $_type['id']) ? "selected" : ""; ?>><?php echo $_type['title']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group  type-group <?php echo (isset($validation_errors['type'])) ? 'has-error' : ''; ?>">
					<label for="type" class=""><span class="field_compulsory">*</span>Type</label>
					<div class="form-control">
						<input id="rd2" name="type" type="radio" value="rent" <?php echo (isset($item['type']) && $item['type'] == 'rent') ? "checked" : ""; ?>> <label for="rd2">Rent</label>
						<input id="rd1" name="type" type="radio" value="sale" <?php echo (isset($item['type']) && $item['type'] == 'sale') ? "checked" : ""; ?>> <label for="rd1">Sale</label>
					</div>
					<div class="error-type-message"></div>
				</div>
			</div>
		</div>
		<div class="row ">
			<div class="col-md-12">
				<div class="form-group">
					<input class="btn btn-primary" type="submit" name="submit" value="<?php echo (isset($item['uuid'])) ? "Update" : "Create"; ?>">
					<input class="btn btn-light" type="reset" value="Reset">
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript" src="assets/js/validation.js"></script>
<?php include_once("template/footer.php"); ?>