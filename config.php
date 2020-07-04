<?php
session_start();

//Define general config variables
$API_KEY = '3NLTTNlXsi6rBWl7nYGluOdkl2htFHug';
$API_MAX_CALL = 2;
$API_PER_PAGE = 10;
$DATA_LISTING_PER_PAGE = 10;
$MAX_INPUT_FILE_SIZE = 5000000;
$API_BASE_URL = 'http://trialapi.craig.mtcdevserver.com/api';

//Define table name
$TABLE_PROPERTY = "properties";
$TABLE_PROPERTY_TYPE = "property_type";

//Define config variable for database connection
$databaseHost = 'localhost';
$databaseName = 'property_system';
$databaseUsername = 'root';
$databasePassword = '';

//Define mysql connection
$MYSQLI = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

//including the function file
include_once('functions.php');
