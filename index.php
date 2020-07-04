<?php
//including the constant variables & database connection file
include_once("config.php");
//Get properties data with search/filter and pagination
$properties = getSearchAndFilteredData($_GET);
$page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;
?>

<?php include_once("template/header.php"); ?>
<div class="container">
	<h2>Property Listing</h2>
	<a href="cron.php" target="_blank"> Fetch API Property (Cron)</a><br /><br />
	<form method="get">
		<div class="row">
			<div class="col-xl-3 col-sm-3">
				<div class="form-group">
					<select class="form-control filter" id="filter_from" name="filter_from">
						<option value="">Filter by Data</option>
						<option value="local">Local</option>
						<option value="live">Live</option>
					</select>
				</div>
			</div>
			<div class="col-xl-3 col-sm-3">
				<div class="form-group">
					<select class="form-control filter" id="filter_type" name="filter_type">
						<option value="">Filter by Type</option>
						<option value="rent">Rent</option>
						<option value="sale">Sale</option>
					</select>
				</div>
			</div>
			<div class="col-xl-3 col-sm-3">
				<div class="form-group">
					<input class="form-control" placeholder="Search" name="search" type="text">
				</div>
			</div>
			<div class="col-xl-3 col-sm-3">
				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Apply filter">
				</div>
			</div>
		</div>
	</form>
	<a class="pull-right" href="form-property.php">Add New Data</a><br /><br />
	<table class="table">
		<thead>
			<tr>
				<th>Uuid</th>
				<th>Image</th>
				<th>Type</th>
				<th>Sale/Rent</th>
				<th>Address</th>
				<th>Town</th>
				<th>Price</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($properties['data'] as $item) : ?>
				<tr>
					<td><?php echo $item['uuid']; ?></td>
					<td>
						<?php $image_urls = getPropertyImageUrls($item, 1); ?>
						<a href="<?php echo $image_urls['image_full_url']; ?>" target="_blank">
							<img src="<?php echo $image_urls['image_thumb_url']; ?>" height="75" width="75" />
						</a>
					</td>
					<td><?php echo $item['title']; ?></td>
					<td><?php echo ucfirst($item['type']); ?></td>
					<td><?php echo $item['address']; ?></td>
					<td><?php echo $item['town']; ?></td>
					<td><?php echo $item['price']; ?></td>
					<td>
						<a href="form-property.php?id=<?php echo $item['uuid']; ?>"><i class="fa fa-pencil-square-o"></i></a>
						<a href="delete.php?id=<?php echo $item['uuid']; ?>" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash-o"></i> </a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<ul class="pagination">
		<?php for ($i = 1; $i <= $properties['total_page']; $i++) : ?>
			<li class="<?php echo ($i == $page) ? "active" : ""; ?>">
				<a href="<?php echo ($i != $page) ? "index.php?page=" . $i : "#"; ?>">
					<?php echo $i; ?>
				</a>
			</li>
		<?php endfor; ?>
	</ul>
</div>