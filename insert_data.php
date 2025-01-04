<?php
$conn = mysqli_connect("localhost", "root", "", "mylocal-farmers");

if (!$conn) {
    die("Failed to connect to the database...");
}

if (isset($_POST['temperature']) && isset($_POST['humidity']) && isset($_POST['moisture1']) && isset($_POST['moisture2']) && isset($_POST['moisture3']) && isset($_POST['moisture4']) && isset($_POST['moisture5'])) {
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $moisture1 = $_POST['moisture1'];
    $moisture2 = $_POST['moisture2'];
    $moisture3 = $_POST['moisture3'];
    $moisture4 = $_POST['moisture4'];
    $moisture5 = $_POST['moisture5'];

    $sql = "INSERT INTO sensor_data (temperature, humidity, moisture1, moisture2, moisture3, moisture4, moisture5) VALUES ('$temperature', '$humidity', '$moisture1', '$moisture2', '$moisture3', '$moisture4', '$moisture5')";
    if (mysqli_query($conn, $sql)) {
        echo "Data inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    echo "Missing data";
}

$conn->close();
?> 