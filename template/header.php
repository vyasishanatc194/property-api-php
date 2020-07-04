<!DOCTYPE html>
<html lang="en">

<head>
  <title>Property</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
  <style>
    .error {
      color: red;
    }
  </style>
</head>

<body>
  <!-- success or fail notification from session -->
  <?php if (isset($_SESSION['message'])) { ?>
    <div class="alert alert-info"> <?php echo $_SESSION['message']; ?> </div>
  <?php
    unset($_SESSION['message']);
  } else if (isset($_SESSION['message_success'])) { ?>
    <div class="alert alert-success"> <?php echo $_SESSION['message_success']; ?> </div>
  <?php
    unset($_SESSION['message_success']);
  } else if (isset($_SESSION['message_error'])) { ?>
    <div class="alert alert-warning"> <?php echo $_SESSION['message_error']; ?> </div>
  <?php
    unset($_SESSION['message_error']);
  } else if (isset($_SESSION['message_warning'])) { ?>
    <div class="alert alert-danger"> <?php echo $_SESSION['message_warning']; ?> </div>
  <?php
    unset($_SESSION['message_warning']);
  } ?>