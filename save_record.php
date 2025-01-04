<?php
include_once "inc/conn.php"; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database connection
    include 'db_connection.php';

    $land_id = $_POST['land_id'];
    $land_texture = $_POST['land_texture'];
    $land_moisture = $_POST['land_moisture'];
    $land_humidity = $_POST['land_humidity'];
    $land_temperature = $_POST['land_temperature'];
    $sand = $_POST['sand'];
    $silt = $_POST['silt'];
    $clay = $_POST['clay'];

    // Fetch `land_moisture` from the `land_test` table for the given `land_id`
    $moisture_query = "SELECT land_moisture FROM land_test WHERE land_id = ?";
    $moisture_stmt = $conn->prepare($moisture_query);
    $moisture_stmt->bind_param("i", $land_id);

    if ($moisture_stmt->execute()) {
        $result = $moisture_stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Append the fetched moisture data
            $land_moisture .= ", " . $row['land_moisture']; // Combining the existing and fetched moisture
        }
    }
    $moisture_stmt->close();

    // Prepare the SQL query to insert the record into `land_records`
    $sql = "INSERT INTO land_records (land_id, land_texture, land_moisture, land_humidity, land_temperature, sand, silt, clay) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $land_id, $land_texture, $land_moisture, $land_humidity, $land_temperature, $sand, $silt, $clay);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Record saved successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error saving record!"]);
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- View Records -->
<div class="card">
    <div class="card-content">
        <span class="card-title">View Land Records</span>
        <form method="GET" action="save_record.php">
            <div class="input-field">
                <input id="land_id_view" name="land_id" type="number" required>
                <label for="land_id_view">Enter Land ID</label>
            </div>
            <button type="submit" class="btn waves-effect waves-light teal darken-3">View Records</button>
        </form>
    </div>
    <?php if (isset($land_id_view) && !empty($land_records)): ?>
        <div class="card-content">
            <h5>Records for Land ID: <?php echo htmlspecialchars($land_id_view); ?></h5>
            <table class="highlight responsive-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Texture</th>
                    <th>Moisture</th>
                    <th>Humidity</th>
                    <th>Temperature</th>
                    <th>Sand</th>
                    <th>Silt</th>
                    <th>Clay</th>
                    <th>Actions</th> <!-- Added Actions Column -->
                </tr>
                </thead>
                <tbody>
                <?php foreach ($land_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['land_id']); ?></td>
                        <td><?php echo htmlspecialchars($record['land_texture']); ?></td>
                        <td><?php echo htmlspecialchars($record['land_moisture']); ?></td>
                        <td><?php echo htmlspecialchars($record['land_humidity']); ?></td>
                        <td><?php echo htmlspecialchars($record['land_temperature']); ?></td>
                        <td><?php echo htmlspecialchars($record['sand']); ?></td>
                        <td><?php echo htmlspecialchars($record['silt']); ?></td>
                        <td><?php echo htmlspecialchars($record['clay']); ?></td>
                        <td>
                            <!-- Action Buttons -->
                            <a href="edit_record.php?id=<?php echo $record['land_id']; ?>" 
                               class="btn-small waves-effect waves-light amber darken-2">
                               <i class="material-icons">edit</i>
                            </a>
                            <a href="delete_record.php?id=<?php echo $record['land_id']; ?>" 
                               class="btn-small waves-effect waves-light red" 
                               onclick="return confirm('Are you sure you want to delete this record?');">
                               <i class="material-icons">delete</i>
                            </a>
                            <a href="download_record.php?id=<?php echo $record['land_id']; ?>" 
                               class="btn-small waves-effect waves-light green">
                               <i class="material-icons">download</i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($land_id_view)): ?>
        <div class="card-content">
            <p class="red-text">No records found for Land ID: <?php echo htmlspecialchars($land_id_view); ?></p>
        </div>
    <?php endif; ?>
</div>

