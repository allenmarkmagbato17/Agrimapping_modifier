<?php
require_once "inc/conn.php";
if (!isset($_GET['land_id'])) {
    echo "<script>window.close()</script>";
}
$id = $_GET['land_id'];
$test = $conn->query("SELECT * FROM land_test WHERE land_id='$id'");

$temp = 0;
$hum = 0;
$mois1 = 0;
$mois2 = 0;
$mois3 = 0;
$mois4 = 0;
$mois5 = 0;

echo "<pre>";
while ($t = $test->fetch_assoc()) {
    // print_r($t);
    if ($t['sensor_num'] == 1) {
        $mois1 = $t['land_moisture'];
        $hum = $t['land_humidity'];
        $temp = $t['land_temp'];
    } else if ($t['sensor_num'] == 2) {
        $mois2 = $t['land_moisture'];
    } else if ($t['sensor_num'] == 3) {
        $mois3 = $t['land_moisture'];
    } else if ($t['sensor_num'] == 4) {
        $mois4 = $t['land_moisture'];
    } else if ($t['sensor_num'] == 5) {
        $mois5 = $t['land_moisture'];
    }
}
echo "</pre>";


$dataPoints1 = array(
    array("label" => "Temperature", "y" => $temp),
    array("label" => "Humidity", "y" => $hum),
    array("label" => "Moisture 1", "y" => $mois1),
    array("label" => "Moisture 2", "y" => $mois2),
    array("label" => "Moisture 3", "y" => $mois3),
    array("label" => "Moisture 4", "y" => $mois4),
    array("label" => "Moisture 5", "y" => $mois5)
);


?>
<!DOCTYPE HTML>
<html>

<head>
    <script>
        window.onload = function() {

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Sensor Captured Data"
                },
                axisY: {
                    includeZero: true
                },
                legend: {
                    cursor: "pointer",
                    verticalAlign: "top",
                    horizontalAlign: "top",
                    itemclick: toggleDataSeries
                },
                data: [{
                    type: "column",
                    name: "Sensor Data",
                    indexLabel: "{y}",
                    // yValueFormatString: "#0.##%",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

            function toggleDataSeries(e) {
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                chart.render();
            }

        }
    </script>
</head>

<body>
    <div id="chartContainer" style="height: 90vh; width: 100%; margin-bottom:20px;"></div>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>

</html>