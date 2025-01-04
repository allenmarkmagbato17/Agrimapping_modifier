<?php
$conn = mysqli_connect("localhost", "root", "", "mylocal-farmers");

if (!$conn) {
    die("Failed to connect to the database...");
}

$sql = "SELECT * FROM sensor_data ORDER BY timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Sensor Data</h1>
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>Moisture 1</th>
                    <th>Moisture 2</th>
                    <th>Moisture 3</th>
                    <th>Moisture 4</th>
                    <th>Moisture 5</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['temperature']; ?></td>
                        <td><?php echo $row['humidity']; ?></td>
                        <td><?php echo $row['moisture1']; ?></td>
                        <td><?php echo $row['moisture2']; ?></td>
                        <td><?php echo $row['moisture3']; ?></td>
                        <td><?php echo $row['moisture4']; ?></td>
                        <td><?php echo $row['moisture5']; ?></td>
                        <td><?php echo $row['timestamp']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?> 