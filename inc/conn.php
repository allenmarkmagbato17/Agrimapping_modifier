<?php
session_start();
// error_reporting(error_level: E_ALL);
$conn = mysqli_connect("localhost","root","","mylocal-farmers2");


if(!$conn) die("Failed to connect to the database... \n check the connection settings..."); 