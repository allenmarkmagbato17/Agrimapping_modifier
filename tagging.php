<?php
include_once "inc/conn.php";
if (!isset($_SESSION['user_id'])) echo "<script>window.location.href ='login.php'</script>";

// define constant variable para sa title sa website
define("TITLE", value: "MAPPING");

// gitawag ang file nga conn.php para e include sa index

// gitawag ang file nga head.php para e include sa index ug ma apil ang css ug javascript nga gi link sa website
include_once "inc/head.php";

$bar = $conn->query("SELECT * FROM lands as l LEFT JOIN farmers as f ON l.farmer_id=f.farmer_id");
$coords = [];
$coords_one = [];
if (!isset($_GET['land_id'])) {
    echo "<script>window.close()</script>";
} else {
    $id = $_GET['land_id'];
    if (isset($_POST['manual_coor']) && !empty($_POST['manual_coor'])) {
        $coor = "[" . $_POST['manual_coor'] . "]";
        $conn->query("INSERT INTO `land_tags` (`land_tag_id`, `land_tag_coord`, `land_id`) VALUES (NULL, '$coor', '$id')");
    } elseif (isset($_POST['coor']) && !empty($_POST['coor'])) {
        $coor = "[" . $_POST['coor'] . "]";
        $conn->query("INSERT INTO `land_tags` (`land_tag_id`, `land_tag_coord`, `land_id`) VALUES (NULL, '$coor', '$id')");
    } elseif (isset($_POST['coor']) && empty($_POST['coor'])) {
        echo '<script>alert(`Failed to save coordinates required`)</script>';
    }
    $coords = $conn->query("SELECT * FROM `land_tags` WHERE land_id='$id'");
    $coords_one = $conn->query("SELECT * FROM `land_tags` as lt LEFT JOIN lands as l ON lt.land_id=l.land_id LEFT JOIN barangays as b ON l.bar_id=b.bar_id LEFT JOIN farmers as f ON f.farmer_id=l.farmer_id WHERE lt.land_id='$id'")->fetch_assoc();
}



?>



<style>
   #map {
    width: 100%;
    height: 100vh;  /* Full viewport height */
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #f4f4f4;
    object-fit: cover; /* Ensures the map fills the area without distortion */
}


.floating-panel {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 999;
    background: rgba(169, 167, 167, 0.7); /* Increased transparency */
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0px 1px 5px rgba(40, 13, 13, 0.2);
    font-size: 0.9em;
    backdrop-filter: blur(5px); /* Adds a subtle blur effect for a polished look */
}



    .transparent-input {
        background-color: rgba(5, 2, 2, 0.8);
        border: none;
        font-size: 0.9em;
    }

    .btn-small img {
        margin-right: 5px;
        vertical-align: middle;
        width: 16px;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(66, 65, 65, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.3);
        width: 90%;
        max-width: 600px;
    }
    #coor + label {
    color: #000; /* Black color for the label text */
    font-weight: Arial; 
}

</style>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col s12">
            <div id="map"></div>
            <div class="floating-panel">
                <!-- Coordinates Display -->
                <form action="" method="post">
                    <div class="row">
                        <div class="input-field col s12">
                            <input readonly type="text" name="coor" id="coor" required class="transparent-input">
                            <label for="coor">Coordanation</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s4">
                            <button type="submit" class="btn btn-small blue waves-effect waves-light">
                                <img src="images/save.svg" alt="Save Icon"> Save
                            </button>
                        </div>
                        <div class="col s4">
                            <button type="button" onclick="getLocation()" class="btn btn-small green waves-effect waves-light">
                                <img src="images/location.svg" alt="Location Icon"> Get Location
                            </button>
                        </div>
                        <div class="col s4">
                            <button type="button" onclick="showModal()" class="btn btn-flat deep-orange lighten-1 white-text">
    <i class="material-icons left">place</i> Input
</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Manual Input -->
<div id="manualInputModal" class="modal">
    <div class="modal-content">
        <h5>Enter Coordinates</h5>
        <div class="row">
            <div class="input-field col s6">
                <input type="text" id="manualLatitude" placeholder="Latitude">
                <label for="manualLatitude">Latitude</label>
            </div>
            <div class="input-field col s6">
                <input type="text" id="manualLongitude" placeholder="Longitude">
                <label for="manualLongitude">Longitude</label>
            </div>
        </div>
        <div class="row">
            <div class="col s6">
                <button type="button" onclick="saveCoordinates()" class="btn btn-small red waves-effect waves-light">Save</button>
            </div>
            <div class="col s6">
                <button type="button" onclick="closeModal()" class="btn btn-small grey waves-effect waves-light">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    function getLocation() {
        M.toast({ html: 'Fetching current location...', classes: 'blue' });
        document.getElementById('coor').value = '14.5995, 120.9842'; // Example coordinates
    }

    function showModal() {
        const modal = document.getElementById('manualInputModal');
        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('manualInputModal');
        modal.style.display = 'none';
    }

    function saveCoordinates() {
        const lat = document.getElementById('manualLatitude').value;
        const lng = document.getElementById('manualLongitude').value;
        if (!lat || !lng) {
            M.toast({ html: 'Please enter both Latitude and Longitude.', classes: 'red' });
            return;
        }
        document.getElementById('coor').value = `${lat}, ${lng}`;
        M.toast({ html: 'Coordinates saved.', classes: 'green' });
        closeModal();
    }
</script>



<?php

include_once "inc/foot.php";

?>

<script>
    <?php if (!empty($coords_one)): ?>
        var map = L.map('map').setView(<?= $coords_one['land_tag_coord'] ?>, 20);
    <?php else: ?>
        var map = L.map('map').setView([7.953010588730743, 123.5841060994726], 20);
    <?php endif; ?>

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {}).addTo(map);

    const x = document.getElementById("coor");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        document.getElementById('coor').value = lat + ", " + lng;
        document.getElementById('manual_coor').value = lat + ", " + lng;
        map.setView([lat, lng], 20);
        L.marker([lat, lng]).addTo(map)
            .bindPopup('Current Location: ' + lat + ', ' + lng)
            .openPopup();
    }
    
    let polygon = [];
    <?php if ($coords->num_rows): ?>
        <?php while ($c =  $coords->fetch_assoc()): ?>
            polygon.push(<?= $c['land_tag_coord'] ?>)
        <?php endwhile; ?>
    <?php endif; ?>

    L.polygon(polygon, {
        color: 'red',
        fillColor: 'red',
        // clickable: false
        weight: 3,
        opacity: 0.8,
        smoothFactor: 1
    }).addTo(map);


    <?php if (!empty($coords_one)): ?>

        L.marker(<?php echo $coords_one['land_tag_coord'] ?>).addTo(map)
            .bindPopup(`  
             <img src="images/farm.svg" style="width:15px;"/> <?php echo strtoupper($coords_one['land_name']) ?> <br> 
            <img src="images/location.svg" style="width:15px;"/> <?php echo $coords_one['bar_name'] ?><br> 
            <img src="images/farmer.svg" style="width:15px;"/> <?php echo $coords_one['farmer_name'] ?>
            
            `)
            .openPopup();

    <?php endif; ?>

    function updateMap() {
        var lat = parseFloat(document.getElementById('manualLatitude').value);
        var lng = parseFloat(document.getElementById('manualLongitude').value);

        if (!isNaN(lat) && !isNaN(lng)) {
            document.getElementById('coor').value = lat + ", " + lng;
            document.getElementById('manual_coor').value = lat + ", " + lng;
            map.setView([lat, lng], 20);
            L.marker([lat, lng]).addTo(map)
                .bindPopup('Custom Location: ' + lat + ', ' + lng)
                .openPopup();
        } else {
            alert('Please enter valid latitude and longitude values.');
        }
    }
</script>