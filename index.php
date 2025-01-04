<?php
include_once "inc/conn.php";
if (!isset($_SESSION['user_id'])) echo "<script>window.location.href ='login.php'</script>";

$farmer_count = $conn->query("SELECT COUNT(*) as total FROM farmers")->fetch_assoc();
$crop_count = $conn->query("SELECT COUNT(*) as total FROM crops")->fetch_assoc();
$lands_count = $conn->query("SELECT COUNT(*) as total FROM lands")->fetch_assoc();
$tags_count = $conn->query("SELECT DISTINCT land_id FROM land_tags");

// define constant variable para sa title sa website
define("TITLE", "MAPPING");

// gitawag ang file nga conn.php para e include sa index

// gitawag ang file nga head.php para e include sa index ug ma apil ang css ug javascript nga gi link sa website
include_once "inc/head.php";

$bar = $conn->query("SELECT * FROM barangays WHERE mun_id=1113")->fetch_all();
$res = array();
$res_one = array();
if (isset($_GET['barangay'])) {
    $bar_id = $_GET['barangay'];
    $res = $conn->query("SELECT * FROM lands as l LEFT JOIN land_tags as lt ON l.land_id=lt.land_id WHERE bar_id='$bar_id'");

    $res_one = $conn->query("SELECT DISTINCT(lt.land_id) FROM `land_tags` as lt LEFT JOIN lands as l ON lt.land_id=l.land_id LEFT JOIN barangays as b ON l.bar_id=b.bar_id LEFT JOIN farmers as f ON f.farmer_id=l.farmer_id WHERE l.bar_id='$bar_id'");
}

$barangays = $conn->query("SELECT bar_name FROM barangays WHERE mun_id=1113")->fetch_all(MYSQLI_ASSOC);

// Prepare the query
$query = "
    SELECT 
        b.bar_name, 
        SUM(ST_Area(ST_GeomFromText(CONCAT('POLYGON((', lt.land_tag_coord, '))')))) as total_land_mass,
        GROUP_CONCAT(DISTINCT ltst.land_texture) as soil_texture
    FROM 
        barangays b
    LEFT JOIN 
        lands l ON b.bar_id = l.bar_id
    LEFT JOIN 
        land_tags lt ON l.land_id = lt.land_id
    LEFT JOIN
        land_test ltst ON l.land_id = ltst.land_id
    WHERE 
        b.mun_id = 1113
    GROUP BY 
        b.bar_id
";

$result = $conn->query($query);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

$data = $result->fetch_all(MYSQLI_ASSOC);

// Add this query near the top with other queries
$all_farms = $conn->query("
    SELECT 
        l.land_id,
        l.land_name,
        f.farmer_name,
        b.bar_name,
        MIN(lt.land_tag_coord) as marker_coord, -- Only get one coordinate for marker
        GROUP_CONCAT(lt.land_tag_coord) as polygon_coords
    FROM lands l
    LEFT JOIN farmers f ON l.farmer_id = f.farmer_id
    LEFT JOIN barangays b ON l.bar_id = b.bar_id
    LEFT JOIN land_tags lt ON l.land_id = lt.land_id
    GROUP BY l.land_id
");

// Add these queries near the top with other queries
$corn_area_query = "
    SELECT SUM(ST_Area(ST_GeomFromText(CONCAT('POLYGON((', lt.land_tag_coord, '))')))) as total_area
    FROM lands l
    JOIN land_tags lt ON l.land_id = lt.land_id 
    JOIN land_test ltest ON l.land_id = ltest.land_id
    WHERE (
        -- Match the exact conditions from get_data.php suggestions
        (ltest.land_moisture BETWEEN 70 AND 85 AND 
         ltest.land_humidity BETWEEN 50 AND 70 AND 
         ltest.land_texture = 'loamy') OR -- Sweet Corn
        (ltest.land_moisture BETWEEN 45 AND 80 AND 
         ltest.land_humidity BETWEEN 60 AND 80 AND 
         ltest.land_texture IN ('loam', 'sandy loam')) OR -- White Lagkitan
        (ltest.land_moisture BETWEEN 60 AND 70 AND 
         ltest.land_humidity BETWEEN 60 AND 80 AND 
         ltest.land_texture IN ('loamy', 'sandy loam')) -- Visayan White
    )";

$rice_area_query = "
    SELECT SUM(ST_Area(ST_GeomFromText(CONCAT('POLYGON((', lt.land_tag_coord, '))')))) as total_area
    FROM lands l
    JOIN land_tags lt ON l.land_id = lt.land_id 
    JOIN land_test ltest ON l.land_id = ltest.land_id
    WHERE (
        -- Match the exact conditions from get_data.php suggestions
        (ltest.land_moisture BETWEEN 40 AND 60 AND 
         ltest.land_humidity BETWEEN 85 AND 90 AND 
         ltest.land_texture IN ('loam', 'clay loam')) OR -- General Rice
        (ltest.land_moisture BETWEEN 60 AND 70 AND 
         ltest.land_humidity BETWEEN 80 AND 90 AND 
         ltest.land_texture IN ('loam', 'clay loam')) -- NSIC varieties
    )";

$coconut_area_query = "
    SELECT SUM(ST_Area(ST_GeomFromText(CONCAT('POLYGON((', lt.land_tag_coord, '))')))) as total_area
    FROM lands l
    JOIN land_tags lt ON l.land_id = lt.land_id 
    JOIN land_test ltest ON l.land_id = ltest.land_id
    WHERE (
        -- Match the exact conditions from get_data.php suggestions
        (ltest.land_moisture BETWEEN 60 AND 85 AND 
         ltest.land_humidity BETWEEN 70 AND 85 AND 
         ltest.land_texture IN ('sandy loam', 'loamy'))
    )";

$corn_area = $conn->query($corn_area_query)->fetch_assoc();
$rice_area = $conn->query($rice_area_query)->fetch_assoc();
$coconut_area = $conn->query($coconut_area_query)->fetch_assoc();

?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css">
<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="https://unpkg.com/leaflet-geometryutil@0.9.3/src/leaflet.geometryutil.js"></script>
<style>
    .area-tooltip {
        background: rgba(0, 0, 0, 0.8);
        border: none;
        border-radius: 4px;
        color: white;
        font-weight: bold;
        padding: 4px 8px;
    }

    .leaflet-interactive {
        pointer-events: auto !important;
    }

    .custom-div-icon {
        background: transparent;
        border: none;
    }

    .custom-div-icon div {
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }

    .custom-div-icon div:hover {
        transform: scale(1.2);
    }

    /* Add legend to the map */
    .legend {
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 1px 5px rgba(0,0,0,0.4);
        line-height: 25px;
        margin: 10px;
    }

    .legend i {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: 2px solid white;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* Add these styles to your existing CSS */
    #map {
        width: 100%;
        height: 100%;
        border-radius: 4px;
        cursor: grab;
    }

    #map.leaflet-dragging {
        cursor: grabbing;
    }

    .leaflet-control-zoom {
        cursor: pointer;
    }

    .leaflet-control-zoom a {
        cursor: pointer !important;
    }

    .leaflet-popup {
        cursor: auto;
    }

    .leaflet-popup-close-button {
        cursor: pointer !important;
    }

    .farm-popup .btn-small {
        cursor: pointer !important;
    }

    /* Improve marker visibility */
    .leaflet-marker-icon {
        filter: drop-shadow(0 2px 2px rgba(0,0,0,0.3));
        transition: transform 0.2s ease;
    }

    .leaflet-marker-icon:hover {
        transform: scale(1.1);
        z-index: 1000 !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://pixinvent.com/stack-responsive-bootstrap-4-admin-template/app-assets/css/colors.min.css">
<link rel="stylesheet" type="text/css" href="https://pixinvent.com/stack-responsive-bootstrap-4-admin-template/app-assets/css/bootstrap-extended.min.css">
<link rel="stylesheet" type="text/css" href="https://pixinvent.com/stack-responsive-bootstrap-4-admin-template/app-assets/fonts/simple-line-icons/style.min.css">

<div class="container-fluid mt-2">
    <div class="container mb-3">
        <div class="grey lighten-4 z-depth-1 p-3">
            <section id="statistics">
                <div class="row mb-2">
                    <div class="col s12">
                        <h5 class="uppercase">Statistics</h5>
                        <small class="grey-text">Overview of Data</small>
                    </div>
                </div>

                <div class="row">
                    <!-- Total Farmers -->
                    <div class="col xl3 s12 m6">
                        <div class="card hoverable small">
                            <div class="card-content">
                                <div class="row valign-wrapper no-mb">
                                    <div class="col s3">
                                        <i class="material-icons medium blue-text">person</i>
                                    </div>
                                    <div class="col s9">
                                        <h4 class="blue-text m-0"><?= $farmer_count['total'] ?></h4>
                                        <small>Total Farmers</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Crops -->
                    <div class="col xl3 s12 m6">
                        <div class="card hoverable small">
                            <div class="card-content">
                                <div class="row valign-wrapper no-mb">
                                    <div class="col s3">
                                        <i class="material-icons medium orange-text">local_florist</i>
                                    </div>
                                    <div class="col s9">
                                        <h4 class="orange-text m-0"><?= $crop_count['total'] ?></h4>
                                        <small>Total Crops</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Tagged Land -->
                    <div class="col xl3 s12 m6">
                        <div class="card hoverable small">
                            <div class="card-content">
                                <div class="row valign-wrapper no-mb">
                                    <div class="col s3">
                                        <i class="material-icons medium green-text">label</i>
                                    </div>
                                    <div class="col s9">
                                        <h4 class="green-text m-0"><?= $tags_count->num_rows ?></h4>
                                        <small>Total Tagged Land</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Lands -->
                    <div class="col xl3 s12 m6">
                        <div class="card hoverable small">
                            <div class="card-content">
                                <div class="row valign-wrapper no-mb">
                                    <div class="col s3">
                                        <i class="material-icons medium red-text">terrain</i>
                                    </div>
                                    <div class="col s9">
                                        <h4 class="red-text m-0"><?= $lands_count['total'] ?></h4>
                                        <small>Total Lands</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
/* Add these styles to make statistics more compact */
.card.small {
    height: 85px;
    margin: 0.5rem 0;
}

.card.small .card-content {
    padding: 12px;
}

.card .material-icons.medium {
    font-size: 2.2rem;
}

.no-mb {
    margin-bottom: 0 !important;
}

.m-0 {
    margin: 0 !important;
}

h4.m-0 {
    font-size: 1.8rem;
    line-height: 1.2;
}

.uppercase {
    text-transform: uppercase;
    font-size: 1.2rem;
    margin: 0;
    font-weight: 500;
}

.container.mb-3 {
    margin-bottom: 1rem !important;
}

.p-3 {
    padding: 1rem !important;
}

small {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* Responsive adjustments */
@media only screen and (max-width: 600px) {
    .card.small {
        height: 70px;
    }

    .card .material-icons.medium {
        font-size: 1.8rem;
    }

    h4.m-0 {
        font-size: 1.5rem;
    }
}
</style>

<div class="row">
    <div class="col s12">
        <div class="card hoverable">
            <div class="card-content" style="display: flex; align-items: flex-start;">
                <!-- Find Farm on Map Form -->
                <div style="width: 30%; padding-right: 15px; border-right: 2px solid #ddd;">
                    <h5 class="card-title center-align" style="font-weight: 600; font-size: 1.2em;">Find Farm on Map</h5>
                    <div class="input-field">
                        <input type="text" id="farmSearch" class="form-control" placeholder="Search farms...">
                    </div>
                    <div class="input-field">
                        <select id="barangay" name="barangay" class="browser-default">
                            <option value="" disabled selected>Select Barangay</option>
                            <?php foreach ($bar as $m => $k): ?>
                                <option value="<?php echo $k[0] ?>"><?php echo $k[1] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="center-align">
                        <button type="button" id="findfarms" class="waves-effect waves-light btn-small">
                            <i class="material-icons left">search</i>Find Farms
                        </button>
                    </div>
                </div>
                
                <!-- Map Container -->
                <div style="width: 70%; height: 70vh;">
                    <div id="map" style="width: 100%; height: 100%; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var style = document.createElement('style');
style.textContent = `
    .leaflet-container {
        cursor: default !important;
    }
    
    .leaflet-interactive {
        cursor: pointer !important;
        pointer-events: auto !important;
        stroke: #ff4444 !important;
        stroke-width: 2px !important;
    }
    
    .leaflet-grab {
        cursor: grab !important;
    }
    
    .leaflet-dragging .leaflet-grab {
        cursor: grabbing !important;
    }
    
    .leaflet-marker-icon {
        cursor: pointer !important;
    }
    
    .area-tooltip {
        background: rgba(0, 0, 0, 0.8);
        border: none;
        border-radius: 4px;
        color: white;
        font-weight: bold;
        padding: 4px 8px;
        pointer-events: none;
    }
    
    .leaflet-popup-content {
        cursor: default;
    }
`;
document.head.appendChild(style);

var map = L.map('map', {
    center: [8.0061, 125.9505],
    zoom: 13,
    zoomControl: true,
    dragging: true,
    scrollWheelZoom: true,
    doubleClickZoom: true,
    boxZoom: true
}).setView([8.0061, 125.9505], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

var farmGroup = L.featureGroup().addTo(map);

<?php
// Modify the query to get polygon coordinates
$all_farms = $conn->query("
    SELECT 
        l.land_id,
        l.land_name,
        f.farmer_name,
        b.bar_name,
        MIN(lt.land_tag_coord) as marker_coord, -- Only get one coordinate for marker
        GROUP_CONCAT(lt.land_tag_coord) as polygon_coords
    FROM lands l
    LEFT JOIN farmers f ON l.farmer_id = f.farmer_id
    LEFT JOIN barangays b ON l.bar_id = b.bar_id
    LEFT JOIN land_tags lt ON l.land_id = lt.land_id
    GROUP BY l.land_id
");

while ($farm = $all_farms->fetch_assoc()) {
    $marker_coord = trim($farm['marker_coord'], '[]');
    
    echo "
    //Define colored marker icons
    var yellowMarker = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-yellow.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    
    var greenMarker = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
    
    var brownMarker = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    var defaultMarker = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Create marker with default icon
    var marker = L.marker([$marker_coord], {
        icon: defaultMarker
    }).bindPopup(`
        <div class='farm-popup'>
            <h6>${farm['land_name']}</h6>
            <p><i class='material-icons tiny'>person</i> ${farm['farmer_name']}</p>
            <p><i class='material-icons tiny'>location_on</i> ${farm['bar_name']}</p>
            <div class='popup-actions'>
                <a href='javascript:void(0);' 
                   onclick='quickViewLandDetails(${farm['land_id']})' 
                   class='btn-small waves-effect waves-light green'>
                    <i class='material-icons left'>visibility</i>View Details
                </a>
            </div>
        </div>
    `);

    // Get suggestions and set marker color
    $.ajax({
        url: 'inc/get_data.php?recommend=${farm['land_id']}',
        type: 'GET',
        async: false,
        success: function(suggestions) {
            suggestions = suggestions.toLowerCase();
            if(suggestions.includes('corn') || 
               suggestions.includes('white lagkitan') || 
               suggestions.includes('visayan white') || 
               suggestions.includes('sweet corn')) {
                marker.setIcon(yellowMarker);
            }
            else if(suggestions.includes('rice') || 
                    suggestions.includes('nsic rc')) {
                marker.setIcon(greenMarker);
            }
            else if(suggestions.includes('coconut')) {
                marker.setIcon(brownMarker);
            }
        }
    });

    farmGroup.addLayer(marker);

    // Create polygon from all coordinates
    var polygonCoords = [];
    var coords = '${farm['polygon_coords']}'.split('],[');
    coords.forEach(function(coord) {
        coord = coord.replace('[', '').replace(']', '');
        var [lat, lng] = coord.split(',');
        polygonCoords.push([parseFloat(lat), parseFloat(lng)]);
    });

    if (polygonCoords.length > 0) {
        var polygon = L.polygon(polygonCoords, {
            color: 'red',
            fillColor: 'red',
            weight: 3,
            opacity: 0.8,
            fillOpacity: 0.2,
            smoothFactor: 1,
            interactive: true
        }).addTo(map);

        // Calculate area
        var area = L.GeometryUtil.geodesicArea(polygon.getLatLngs()[0]);
        var areaHectares = (area / 10000).toFixed(2);
        var areaSquareMeters = area.toFixed(0);

        // Improve tooltip behavior
        var tooltip = polygon.bindTooltip(
            areaHectares + ' ha<br>' + 
            areaSquareMeters + ' m²',
            {
                permanent: false,
                direction: 'center',
                className: 'area-tooltip',
                sticky: true,
                opacity: 1
            }
        );

        // Enhanced hover interactions
        polygon.on('mouseover', function(e) {
            if (map.getZoom() >= 16) {
                e.target.setStyle({
                    fillOpacity: 0.4,
                    weight: 4
                });
                e.target.openTooltip();
            }
        });

        polygon.on('mouseout', function(e) {
            e.target.setStyle({
                fillOpacity: 0.2,
                weight: 3
            });
            e.target.closeTooltip();
        });

        // Add click interaction
        polygon.on('click', function(e) {
            map.fitBounds(e.target.getBounds(), {
                padding: [50, 50],
                maxZoom: 18
            });
        });
    }
    ";
}
?>

// Fit the map to show all farms
map.fitBounds(farmGroup.getBounds());

// Add search functionality
document.getElementById('farmSearch').addEventListener('input', function(e) {
    var searchText = e.target.value.toLowerCase();
    farmGroup.eachLayer(function(layer) {
        if (layer instanceof L.Marker) {
            var popup = layer.getPopup();
            var content = popup.getContent().toLowerCase();
            if (content.includes(searchText)) {
                layer.setOpacity(1);
                if (layer._poly) layer._poly.setStyle({opacity: 1, fillOpacity: 0.3});
            } else {
                layer.setOpacity(0.2);
                if (layer._poly) layer._poly.setStyle({opacity: 0.2, fillOpacity: 0.1});
            }
        }
    });
});

// Create an object to store barangay coordinates
var barangayCoordinates = {
    <?php
    // Add coordinates for each barangay
    foreach ($bar as $barangay) {
        $barangay_id = $barangay[0];
        // Get the center coordinates for this barangay from land_tags
        $center_query = "SELECT AVG(SUBSTRING_INDEX(land_tag_coord, ',', 1)) as lat, 
                               AVG(SUBSTRING_INDEX(land_tag_coord, ',', -1)) as lng 
                        FROM land_tags lt 
                        JOIN lands l ON lt.land_id = l.land_id 
                        WHERE l.bar_id = '$barangay_id'";
        $center_result = $conn->query($center_query);
        $center = $center_result->fetch_assoc();
        
        if ($center['lat'] && $center['lng']) {
            echo "'{$barangay[1]}': [{$center['lat']}, {$center['lng']}],";
        }
    }
    ?>
};

// Modify the barangay change event listener
document.getElementById('barangay').addEventListener('change', function(e) {
    var selectedBarangay = this.options[this.selectedIndex].text;
    var selectedBarangayLower = selectedBarangay.toLowerCase();
    var farmersInBarangay = [];
    
    // Filter the farms and collect coordinates
    farmGroup.eachLayer(function(layer) {
        if (layer instanceof L.Marker) {
            var popup = layer.getPopup();
            var content = popup.getContent().toLowerCase();
            if (selectedBarangayLower === '' || content.includes(selectedBarangayLower)) {
                layer.setOpacity(1);
                if (layer._poly) layer._poly.setStyle({opacity: 1, fillOpacity: 0.3});
                // Store the layer for zooming
                farmersInBarangay.push(layer);
            } else {
                layer.setOpacity(0.2);
                if (layer._poly) layer._poly.setStyle({opacity: 0.2, fillOpacity: 0.1});
            }
        }
    });

    // Create a feature group for the filtered farmers
    var filteredFarmGroup = L.featureGroup(farmersInBarangay);
    
    // If farmers are found in the selected barangay, zoom to them
    if (farmersInBarangay.length > 0) {
        map.fitBounds(filteredFarmGroup.getBounds(), {
            padding: [50, 50], // Add padding around the bounds
            maxZoom: 16, // Limit maximum zoom level
            duration: 1.5 // Animation duration in seconds
        });
    }
});

// Add the same functionality to the Find Farms button
document.getElementById('findfarms').addEventListener('click', function() {
    var selectedBarangay = document.getElementById('barangay').options[
        document.getElementById('barangay').selectedIndex
    ].text;
    var selectedBarangayLower = selectedBarangay.toLowerCase();
    var farmersInBarangay = [];
    
    farmGroup.eachLayer(function(layer) {
        if (layer instanceof L.Marker) {
            var popup = layer.getPopup();
            var content = popup.getContent().toLowerCase();
            if (content.includes(selectedBarangayLower)) {
                farmersInBarangay.push(layer);
            }
        }
    });

    var filteredFarmGroup = L.featureGroup(farmersInBarangay);
    
    if (farmersInBarangay.length > 0) {
        map.fitBounds(filteredFarmGroup.getBounds(), {
            padding: [50, 50],
            maxZoom: 16,
            duration: 1.5
        });
    }
});

// Add this function before the marker creation
function viewAllLandDetails(landId) {
    $.ajax({
        url: `inc/get_data.php?id=${landId}`,
        type: 'GET',
        success: function(response) {
            const data = JSON.parse(response);
            if (!data.data || !data.data.length) {
                M.toast({ html: 'No data available for this land', classes: 'red' });
                return;
            }
            
            // Calculate average moisture from all sensors
            const moistureValues = [
                parseFloat(data.data[0].land_moisture || 0),
                parseFloat(data.data[1]?.land_moisture || 0),
                parseFloat(data.data[2]?.land_moisture || 0),
                parseFloat(data.data[3]?.land_moisture || 0),
                parseFloat(data.data[4]?.land_moisture || 0)
            ].filter(val => !isNaN(val));
            
            const avgMoisture = moistureValues.length ? 
                moistureValues.reduce((a, b) => a + b) / moistureValues.length : 0;

            // Prepare the content for the modal
            const modalContent = `
                <div class="land-details-container">
                    <div class="row">
                        <div class="col s12">
                            <h5>Land Details</h5>
                            <table class="striped">
                                <tbody>
                                    <tr>
                                        <td><strong>Average Moisture:</strong></td>
                                        <td>${avgMoisture.toFixed(2)}%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Temperature:</strong></td>
                                        <td>${data.data[0].land_temp || 'N/A'}°C</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Humidity:</strong></td>
                                        <td>${data.data[0].land_humidity || 'N/A'}%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Soil Type:</strong></td>
                                        <td>${data.data[0].land_texture || 'N/A'}</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="soil-composition mt-3">
                                <h6>Soil Composition</h6>
                                <p>Sand: ${data.data[0].sand || '0'}%</p>
                                <p>Silt: ${data.data[0].silt || '0'}%</p>
                                <p>Clay: ${data.data[0].clay || '0'}%</p>
                            </div>

                            <div class="recommendations mt-3">
                                <h6>Recommendations</h6>
                                <div id="landRecommendations"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Initialize modal if not already initialized
            var elem = document.getElementById('landDetailsModal');
            var instance = M.Modal.getInstance(elem);
            if (!instance) {
                instance = M.Modal.init(elem);
            }

            // Update modal content and open it
            document.getElementById('landDetailsContent').innerHTML = modalContent;
            instance.open();

            // Load recommendations
            $.ajax({
                url: `inc/get_data.php?recommend=${landId}`,
                type: 'GET',
                success: function(recommendations) {
                    document.getElementById('landRecommendations').innerHTML = recommendations;
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            M.toast({ html: 'Error loading land details', classes: 'red' });
        }
    });
}

// Initialize all modals when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals, {
        dismissible: true,
        opacity: 0.5,
        inDuration: 250,
        outDuration: 200,
        startingTop: '10%',
        endingTop: '15%'
    });
});

// Add this function
function quickViewLandDetails(landId) {
    // Initialize and open modal
    var modal = M.Modal.getInstance(document.getElementById('quickViewModal'));
    modal.open();
    
    // Fetch land details
    $.ajax({
        url: `inc/get_data.php?id=${landId}`,
        type: 'GET',
        success: function(response) {
            const data = JSON.parse(response);
            if (!data.data || !data.data.length) {
                M.toast({ html: 'No data available', classes: 'red' });
                return;
            }
            
            // Calculate average moisture
            const moistureValues = [
                parseFloat(data.data[0].land_moisture || 0),
                parseFloat(data.data[1]?.land_moisture || 0),
                parseFloat(data.data[2]?.land_moisture || 0),
                parseFloat(data.data[3]?.land_moisture || 0),
                parseFloat(data.data[4]?.land_moisture || 0)
            ].filter(val => !isNaN(val));
            
            const avgMoisture = moistureValues.length ? 
                (moistureValues.reduce((a, b) => a + b) / moistureValues.length).toFixed(2) : 'N/A';

            // Update modal content
            document.getElementById('modalMoisture').textContent = `${avgMoisture}%`;
            document.getElementById('modalTexture').textContent = data.data[0].land_texture || 'N/A';
            document.getElementById('modalHumidity').textContent = `${data.data[0].land_humidity || 'N/A'}%`;
            document.getElementById('modalTemperature').textContent = `${data.data[0].land_temp || 'N/A'}°C`;

            // Fetch and display suggestions
            $.ajax({
                url: `inc/get_data.php?recommend=${landId}`,
                type: 'GET',
                success: function(suggestions) {
                    document.getElementById('modalSuggestions').innerHTML = suggestions;
                },
                error: function() {
                    document.getElementById('modalSuggestions').innerHTML = 'Failed to load suggestions';
                }
            });
        },
        error: function() {
            M.toast({ html: 'Error loading land details', classes: 'red' });
        }
    });
}

// Add this after map initialization
var legend = L.control({position: 'bottomright'});

legend.onAdd = function (map) {
    var div = L.DomUtil.create('div', 'legend');
    div.innerHTML = 
        '<h6 style="margin: 0 0 5px 0;">Crop Suggestions</h6>' +
        '<i style="background: #ffc107"></i> Corn<br>' +
        '<i style="background: #4CAF50"></i> Rice<br>' +
        '<i style="background: #795548"></i> Coconut<br>' +
        '<i style="background: #2196F3"></i> No suggestion';
    return div;
};

legend.addTo(map);
</script>

<style>
.farm-popup {
    padding: 10px;
    pointer-events: auto;
    z-index: 1000;
}

.farm-popup h6 {
    margin: 0 0 10px 0;
    font-weight: bold;
}

.farm-popup p {
    margin: 5px 0;
    display: flex;
    align-items: center;
}

.farm-popup .material-icons.tiny {
    margin-right: 5px;
}

.farm-popup .btn-small {
    margin-top: 10px;
}

#farmSearch {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

#farmSearch:focus {
    outline: none;
    border-color: #26a69a;
    box-shadow: 0 1px 0 0 #26a69a;
}

.input-field {
    margin-bottom: 20px;
}

.popup-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.popup-actions .btn-small {
    flex: 1;
    text-align: center;
}

.farm-popup {
    min-width: 200px;
}
</style>

<!-- Add this modal structure right after your map div -->
<div id="landDetailsModal" class="modal">
    <div class="modal-content">
        <div id="landDetailsContent"></div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn">Close</a>
    </div>
</div>

<!-- Add this right before the closing body tag -->
<div id="quickViewModal" class="modal">
    <div class="modal-content">
        <h5>Land Details</h5>
        <div class="row">
            <div class="col s12">
                <div class="sensor-readings">
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td><strong>Soil Moisture:</strong></td>
                                <td id="modalMoisture">Loading...</td>
                            </tr>
                            <tr>
                                <td><strong>Land Texture:</strong></td>
                                <td id="modalTexture">Loading...</td>
                            </tr>
                            <tr>
                                <td><strong>Humidity:</strong></td>
                                <td id="modalHumidity">Loading...</td>
                            </tr>
                            <tr>
                                <td><strong>Temperature:</strong></td>
                                <td id="modalTemperature">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="suggestions-section">
                    <h6>Plant Suggestions</h6>
                    <div id="modalSuggestions">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn">Close</a>
    </div>
</div>

<style>
#quickViewModal {
    max-width: 500px;
    border-radius: 8px;
}

#quickViewModal .modal-content {
    padding: 20px;
}

#quickViewModal h5 {
    margin-top: 0;
    color: #26a69a;
    margin-bottom: 20px;
}

#quickViewModal .sensor-readings {
    margin-bottom: 20px;
}

#quickViewModal table {
    margin-bottom: 20px;
}

#quickViewModal .suggestions-section {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    margin-top: 20px;
}

#quickViewModal h6 {
    color: #26a69a;
    margin-top: 0;
    margin-bottom: 10px;
}

#quickViewModal .modal-footer {
    padding: 10px 20px;
}

#modalSuggestions {
    max-height: 200px;
    overflow-y: auto;
}
</style>

    <?php
    // gitawag ang file nga foot.php para e include sa index ug ma apil ang css ug javascript nga gi link sa website
    include_once "inc/foot.php";

    ?>