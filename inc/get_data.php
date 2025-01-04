<?php
require_once("conn.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $data = $conn->query("SELECT * FROM land_test WHERE land_id='$id'");
    $rows = [];
    while ($row = $data->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode(["count" => $data->num_rows, "data" => $rows]);
}

if (isset($_GET["turnTest"])) {
    $id = $_GET["turnTest"];
    $rs = $conn->query("SELECT * FROM lands WHERE land_id='$id' AND sts='1'");
    $conn->query("UPDATE lands SET sts=0");
    if ($rs->num_rows > 0) {
        $conn->query("UPDATE lands SET sts=0 WHERE land_id='$id'");
    } else {
        $conn->query("UPDATE lands SET sts=1 WHERE land_id='$id'");
    }

    $sensor_data = $conn->query("SELECT * FROM land_test WHERE id='$id' ORDER BY timestamp DESC LIMIT 1");
    if ($sensor_data->num_rows > 0) {
        $sensor = $sensor_data->fetch_assoc();
        echo json_encode([
            "humidity" => $sensor['humidity'],
            "temperature" => $sensor['temperature'],
            "moisture1" => $sensor['moisture1'],
            "moisture2" => $sensor['moisture2'],
            "moisture3" => $sensor['moisture3'],
            "moisture4" => $sensor['moisture4'],
            "moisture5" => $sensor['moisture5']
        ]);
    } else {
        echo json_encode([]);
    }
}

if (isset($_GET['data'])) {
    $sensor_val_one = $_GET['s_one'];
    $sensor_val_two = $_GET['s_two'];
    $sensor_val_three = $_GET['s_three'];
    $sensor_val_four = $_GET['s_four'];
    $sensor_val_five = $_GET['s_five'];

    $hum = $_GET['hum'];
    $temp = $_GET['temp'];

    $turn_on = $conn->query("SELECT * FROM lands WHERE sts=1");
    if ($turn_on->num_rows == 1) {
        $data = $turn_on->fetch_assoc();
        $id = $data['land_id'];

        // Sensor 1
        $sensor_one = $conn->query("SELECT * FROM land_test WHERE land_id='$id' AND sensor_num=1");
        if ($sensor_one->num_rows > 0) {
            // update
            $conn->query("UPDATE land_test SET land_moisture='$sensor_val_one', land_humidity='$hum', land_temp='$temp' WHERE sensor_num=1 AND land_id='$id'");
        } else {
            // insert
            $conn->query("INSERT INTO `land_test` (`land_test_id`, `land_moisture`, `land_id`, `land_humidity`, `land_texture`, `land_temp`, `sensor_num`) VALUES (NULL, '$sensor_val_one', '$id', '$hum', NULL, '$temp', '1')");
        }

        $sensor_two = $conn->query("SELECT * FROM land_test WHERE land_id='$id' AND sensor_num=2");
        if ($sensor_two->num_rows > 0) {
            // update
            $conn->query("UPDATE land_test SET land_moisture='$sensor_val_two', land_humidity='$hum', land_temp='$temp' WHERE sensor_num=2 AND land_id='$id'");
        } else {
            // insert
            $conn->query("INSERT INTO `land_test` (`land_test_id`, `land_moisture`, `land_id`, `land_humidity`, `land_texture`, `land_temp`, `sensor_num`) VALUES (NULL, '$sensor_val_two', '$id', '$hum', NULL, '$temp', '2')");
        }

        $sensor_three = $conn->query("SELECT * FROM land_test WHERE land_id='$id' AND sensor_num=3");
        if ($sensor_three->num_rows > 0) {
            // update
            $conn->query("UPDATE land_test SET land_moisture='$sensor_val_three', land_humidity='$hum', land_temp='$temp' WHERE sensor_num=3 AND land_id='$id'");
        } else {
            // insert
            $conn->query("INSERT INTO `land_test` (`land_test_id`, `land_moisture`, `land_id`, `land_humidity`, `land_texture`, `land_temp`, `sensor_num`) VALUES (NULL, '$sensor_val_three', '$id', '$hum', NULL, '$temp', '3')");
        }

        $sensor_four = $conn->query("SELECT * FROM land_test WHERE land_id='$id' AND sensor_num=4");
        if ($sensor_four->num_rows > 0) {
            // update
            $conn->query("UPDATE land_test SET land_moisture='$sensor_val_four', land_humidity='$hum', land_temp='$temp' WHERE sensor_num=4 AND land_id='$id'");
        } else {
            // insert
            $conn->query("INSERT INTO `land_test` (`land_test_id`, `land_moisture`, `land_id`, `land_humidity`, `land_texture`, `land_temp`, `sensor_num`) VALUES (NULL, '$sensor_val_four', '$id', '$hum', NULL, '$temp', '4')");
        }

        $sensor_five = $conn->query("SELECT * FROM land_test WHERE land_id='$id' AND sensor_num=5");
        if ($sensor_five->num_rows > 0) {
            // update
            $conn->query("UPDATE land_test SET land_moisture='$sensor_val_five', land_humidity='$hum', land_temp='$temp' WHERE sensor_num=5 AND land_id='$id'");
        } else {
            // insert
            $conn->query("INSERT INTO `land_test` (`land_test_id`, `land_moisture`, `land_id`, `land_humidity`, `land_texture`, `land_temp`, `sensor_num`) VALUES (NULL, '$sensor_val_five', '$id', '$hum', NULL, '$temp', '5')");
        }
    }

    print_r($_SERVER['REQUEST_URI']);
}

if (isset($_GET['recommend'])) {
    $id = $_GET['recommend'];
    $land = $conn->query("SELECT * FROM land_test WHERE land_id='$id' LIMIT 1")->fetch_assoc();
    if (empty($land)) die();
    
    $mois = $land['land_moisture'];
    $hum = $land['land_humidity'];
    $text = $land['land_texture'];
    $temp = $land['land_temp'];
    
    $suggestions = "";
    
    $riceCondition = (($mois >= 40 && $mois <= 60) + ($hum >= 85 && $hum <= 90) + ($text == 'loam' || $text == 'clay loam')) >= 2;
    $sweetCornCondition = (($mois >= 70 && $mois <= 85) + ($hum >= 50 && $hum <= 70) + ($text == 'loamy')) >= 2;
    $whiteLagkitanCondition = (($mois >= 45 && $mois <= 80) + ($hum >= 60 && $hum <= 80) + ($text == 'loam' || $text == 'sandy loam')) >= 2;
    $visayanWhiteCondition = (($mois >= 60 && $mois <= 70) + ($hum >= 60 && $hum <= 80) + ($text == 'loamy' || $text == 'sandy loam')) >= 2;
    $purpleCornCondition = (($mois >= 45 && $mois <= 55) + ($hum >= 40 && $hum <= 60) + ($text == 'loamy')) >= 2;
    $wildVioletCondition = (($mois >= 40 && $mois <= 50) + ($hum >= 50 && $hum <= 70) + ($text == 'loamy')) >= 2;
    $yellowCornCondition = (($mois >= 60 && $mois <= 70) + ($hum >= 50 && $hum <= 70) + ($text == 'loamy')) >= 2;
    $coconutTallCondition = (($mois >= 60 && $mois <= 65) + ($hum >= 60 && $hum <= 65) + ($text == 'sandy loam' || $text == 'loamy')) >= 2;
    $coconutHybridCondition = (($mois >= 65 && $mois <= 70) + ($hum >= 65 && $hum <= 70) + ($text == 'sandy loam' || $text == 'loamy')) >= 2;
    $coconutDwarfCondition = (($mois >= 70 && $mois <= 75) + ($hum >= 70 && $hum <= 75) + ($text == 'sandy loam' || $text == 'loamy')) >= 2;
    $riceNSICRc222Condition = (($mois >= 60 && $mois <= 70) + ($hum >= 80 && $hum <= 90) + ($text == 'loam' || $text == 'clay loam')) >= 2;
    $riceSL8HCondition = (($mois >= 60 && $mois <= 70) + ($hum >= 80 && $hum <= 90) + ($text == 'loam' || $text == 'clay loam')) >= 2;    
    $riceNSICRc160Condition = (($mois >= 80 && $mois <= 85) + ($hum >= 80 && $hum <= 85) + ($text == 'loam' || $text == 'clay loam')) >= 2;
    $riceMestiso20Condition = (($mois >= 86 && $mois <= 90) + ($hum >= 86 && $hum <= 90) + ($text == 'loam')) >= 2;
    $riceNSICRc480Condition = (($mois >= 91 && $mois <= 95) + ($hum >= 91 && $hum <= 95) + ($text == 'loamy' || $text == 'clay loam')) >= 2;

    function generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, $moistureRange, $humidityRange) {
        $suggestions = "";
        $moistureColor = $moistureCompatible ? 'green' : 'red';
        $suggestions .= "• Soil Moisture Content: <span style='color: $moistureColor;'>" . $mois . "%</span> ";
        if ($moistureCompatible) {
            $suggestions .= "<span style='color: green;'>✓</span>";
        } else {
            $suggestions .= "<span style='color: red; cursor: pointer;'>⚠</span> ";
            if ($mois < $moistureRange[0]) {
                $suggestions .= "(Too low - Increase irrigation frequency)";
            } else if ($mois > $moistureRange[1]) {
                $suggestions .= "(Too high - Reduce irrigation, improve drainage)";
            }
        }
        $suggestions .= "<br>";
        
        $humidityColor = $humidityCompatible ? 'green' : 'red';
        $suggestions .= "• Humidity: <span style='color: $humidityColor;'>" . $hum . "%</span> ";
        if ($humidityCompatible) {
            $suggestions .= "<span style='color: green; background-color: lightgreen;'>✓</span>";
        } else {
            $suggestions .= "<span style='color: red; cursor: pointer;'>⚠</span> ";
            if ($hum < $humidityRange[0]) {
                $suggestions .= "(Too low - Consider using mulch or irrigation to increase humidity)";
            } else if ($hum > $humidityRange[1]) {
                $suggestions .= "(Too high - Improve air circulation, consider spacing plants further apart)";
            } 
        }
        $suggestions .= "<br><br>";
        
        if (!$moistureCompatible || !$humidityCompatible) {
            $suggestions .= "<div style='background-color:#fefefe; margin:15px auto; padding:20px; border:1px solid #888; width:80%;'>";
            $suggestions .= "<strong>Tips to optimize conditions:</strong><br>";
            if (!$moistureCompatible) {
                $suggestions .= "• For optimal soil moisture (" . $moistureRange[0] . "-" . $moistureRange[1] . "%):<br>";
                $suggestions .= "  - Use mulching to retain moisture<br>";
                $suggestions .= "  - Install proper drainage system<br>";
                $suggestions .= "  - Monitor irrigation schedule regularly<br>";
            }
            if (!$humidityCompatible) {
                $suggestions .= "• For optimal humidity (" . $humidityRange[0] . "-" . $humidityRange[1] . "%):<br>";
                $suggestions .= "  - Consider using overhead sprinklers in dry conditions<br>";
                $suggestions .= "  - Implement proper plant spacing<br>";
                $suggestions .= "  - Use row covers if needed<br>";
            }
            $suggestions .= "<br>";
            $suggestions .= "</div>";
        }
        return $suggestions;
    }

    if ($riceCondition || $sweetCornCondition || $whiteLagkitanCondition || $visayanWhiteCondition || $purpleCornCondition || $wildVioletCondition || $yellowCornCondition || $coconutTallCondition || $coconutHybridCondition || $coconutDwarfCondition || $riceNSICRc222Condition || $riceSL8HCondition || $riceNSICRc160Condition || $riceMestiso20Condition || $riceNSICRc480Condition) {
        if ($riceCondition) {
            $suggestions .= "<strong>RICE:</strong><br>
                Moisture level for rice germination and seedling growth: 40-60%<br>
                Humidity: 85-90%<br><br>
        
                <strong>Land Preparation:</strong><br>
                • Plow and harrow the field twice to eliminate weeds and stubbles. Make sure that the field is well leveled during the leveling to facilitate ease in irrigation and for easy control of weeds and golden apple snail.<br>
                <strong>Sowing:</strong><br>
                • Soak the seeds in clean water for 24-36 hours. Do not oversoak the seeds as it will affect germination. If possible, replace the water every 6 hours to avoid pathogenic microbes build-up. Put the seeds in a box or empty drum for incubation of another 24 hours until the roots start to grow.<br>
                • Seed inoculants such as Bio-N can be used prior to sowing. Bio-N is a microbial inoculant containing nitrogen-fixing bacteria that enhance seedling growth.<br>
                • Prepare 400 sq.m. seedbed for every 40 kilos of seeds. Make 20 plots of 1m x 20m each to accommodate the 40 kilos of seeds to be sown. Incorporate 10 bags of Carbonized Rice Hull (CRH) in the seedbed prior to sowing to aid in the easy pulling of seedlings.<br>
                <strong>Transplanting:</strong><br>
                • Rice can be transplanted at 10-15 days after sowing at 25x25 centimeter spacing. This method provides enough space for the plant to maximize its tillering potential. For fields infested with golden apple snails, 21-25 day-old seedlings are recommended.<br>
                
                <strong>Weed Management:</strong><br>
                • Weeding should be done 2 weeks after transplanting. Weeding at this stage allows air to penetrate the soil. If possible, use a rotary weeder for faster and easier weeding.<br>
                <strong>Pest and Disease Management:</strong><br>
                • Use panyawan/makabuhay extract for various pests. Extract at a 1:1:2 ratio (Panyawan: Molasses: Water) and use 3 tablespoons of the solution per liter of water.<br>
                • Use chili extract for soft-bodied and sucking insect pests. Extract 20-25 regular-size chili fruits for every 16 liters of water.<br>
                • Use garlic, onion, and ginger extracts to control fungal diseases.<br>
                • Use compost tea and vermi tea to control foliar diseases and strengthen plant immunity.<br>
                • Spray lactic acid bacteria serum (LABS) to increase the number of beneficial microorganisms that suppress pathogenic microbes.<br>
                • Manually collect larvae and pupae to help control insect pest populations.<br>
                • Gradually remove disease-infected plant parts to reduce the incidence of disease outbreaks.<br>
                • Regularly remove weeds to prevent nutrient, moisture, and sunlight competition. Weeds may also serve as alternate hosts for insect pests and sources of disease inocula.<br><br>";
        }
        
        if ($sweetCornCondition) {
            $moistureCompatible = ($mois >= 70 && $mois <= 85);
            $humidityCompatible = ($hum >= 50 && $hum <= 70);
            $suggestions .= "<strong>SWEET CORN:</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [70, 85], [50, 70]);
            $suggestions .= "<strong>Planting Techniques:</strong><br>
                • Soil Preparation: Prepare the soil by tilling it to a depth of about 6–8 inches to break up compacted layers.<br>
                • Row Spacing: Plant sweet corn in rows 30–36 inches apart.<br>
                • Planting Depth: Seeds should be planted 1–2 inches deep.<br>
                • Thinning: If seedlings emerge too close together, thin them.<br>
                • Soil Texture: Loamy soil is ideal.<br>
                • Soil Moisture Content: 70%–85%<br>
                • Humidity: 50%-70%<br><br>";
        }

        if ($whiteLagkitanCondition) {
            $moistureCompatible = ($mois >= 60 && $mois <= 80);
            $humidityCompatible = ($hum >= 60 && $hum <= 80);
            
            $suggestions .= "<strong>WHITE LAGKITAN CORN:</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [60, 80], [60, 80]);
            
            $suggestions .= "<strong>Planting Techniques:</strong><br>
                • Land Preparation: Plow and harrow the field to create a fine seedbed. This ensures proper root development and seed establishment.<br>
                • Planting Depth: Plant seeds 3-5 cm deep.<br>
                • Planting Spacing: Maintain a row spacing of about 75-90 cm with a plant spacing of 25-30 cm within the row.<br>
                • Planting Time: The ideal time to plant is during the rainy season to ensure ample moisture.<br>
                • Soil Texture: Loam or sandy loam soils are ideal.<br>
                • Soil Moisture Content: 60-80%<br>
                • Humidity: 60% to 80%<br><br>";
        }

        if ($visayanWhiteCondition) {
            $moistureCompatible = ($mois >= 60 && $mois <= 70);
            $humidityCompatible = ($hum >= 60 && $hum <= 80);
            $suggestions .= "<strong>VISAYAN WHITE CORN:</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [60, 70], [60, 80]);
            $suggestions .= "<strong>Planting Techniques:</strong><br>
                • Land Preparation: Tilling: Plow the field 2-3 times to break the soil and ensure proper aeration.<br>
                • Spacing: Use a spacing of about 25–30 cm between plants and 75–90 cm between rows.<br>
                • Seeding: Plant the seeds at a depth of 4–5 cm.<br>
                • Soil Texture: Loamy soils (soil types include loam and sandy loam).<br>
                • Soil Moisture Content: 60–70%<br>
                • Humidity: 60%–80%<br><br>";
        }

        if ($purpleCornCondition) {
            $moistureCompatible = ($mois >= 45 && $mois <= 55);
            $humidityCompatible = ($hum >= 40 && $hum <= 60);
            $suggestions .= "<strong>PURPLE CORN:</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [45, 55], [40, 60]);
            $suggestions .= "<strong>Planting Techniques:</strong><br>
                • Soil Preparation: Prior to planting, the soil should be tilled to a depth of about 6-8 inches (15-20 cm).<br>
                • Planting Depth: Plant seeds about 1-1.5 inches (2.5-4 cm) deep.<br>
                • Planting Spacing: Space seeds 8-12 inches (20-30 cm) apart in rows, with row spacing of 24-36 inches (60-90 cm).<br>
                • Soil Texture: Loamy soils (60% sand, 30% silt, and 10% clay).<br>
                • Soil Moisture Content: 60-80%<br>
                • Humidity: 40–60%<br><br>";
        }

        if ($wildVioletCondition) {
            $moistureCompatible = ($mois >= 40 && $mois <= 50);
            $humidityCompatible = ($hum >= 50 && $hum <= 70);
            $suggestions .= "<strong>WILD VIOLET CORN:</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [40, 50], [50, 70]);
            $suggestions .= "<strong>Planting Techniques:</strong><br>
                • Soil Preparation: Tillage: Prepare the soil by tilling to a depth of about 4-6 inches.<br>
                • Planting Depth: Plant the seeds 1-2 inches deep into well-prepared soil.<br>
                • Planting Spacing: Space the seeds 8-12 inches apart in rows, with rows about 30-36 inches apart.<br>
                • Soil Texture: Loamy soil.<br>
                • Soil Moisture: 60-70%<br>
                • Humidity: 50–70%<br><br>";
        }

        if ($yellowCornCondition) {
            $moistureCompatible = ($mois >= 60 && $mois <= 70);
            $humidityCompatible = ($hum >= 50 && $hum <= 70);
            $suggestions .= "<strong>YELLOW CORN:</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [60, 70], [50, 70]);
            $suggestions .= "<strong>Planting Techniques:</strong><br>
                • Land Preparation: Plow the field 2-3 times to ensure the soil is well-aerated and loose.<br>
                • Planting Spacing: Sow seeds at a spacing of 25-30 cm apart within rows and 70-90 cm between rows.<br>
                • Planting Depth: Plant seeds at a depth of 4-6 cm.<br>
                • Soil Texture: Loamy soil (40% sand, 40% silt, and 20% clay).<br>
                • Soil Moisture Content: 60-70%<br>
                • Humidity: 50-70%<br><br>";
        }
        
        if ($coconutTallCondition) {
            $moistureCompatible = ($mois >= 60 && $mois <= 80);
            $humidityCompatible = ($hum >= 70 && $hum <= 80);
            $suggestions .= "<strong>COCONUT (Tall):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [60, 80], [70, 80]);
            $suggestions .= "<strong>Planting Techniques for Tall Coconut:</strong><br>
                • Site Selection: Choose a well-drained area with sufficient sunlight exposure. Avoid waterlogged areas to prevent root rot.<br>
                • Seed Selection and Preparation: Use healthy and mature nuts from high-yielding mother palms (approximately 11–12 months old). Allow the nuts to germinate for 3–5 months before transplanting.<br>
                • Land Preparation: Clear the land of weeds and debris. Create planting holes that are 50 cm x 50 cm x 50 cm and spaced 7–10 meters apart.<br>
                • Planting Process: Fill the hole with a mixture of topsoil and organic compost. Position the seedling upright and ensure the base of the nut is level with the ground. Firmly pack the soil around the roots and water immediately after planting.<br>
                • Maintenance: Regularly irrigate during dry months, particularly in the first three years. Mulch around the base to retain moisture and suppress weeds.<br>
                • Soil Texture: Sandy loam or loamy soils<br>
                • Soil Moisture: 60-80%<br>
                • Humidity: 70-80%<br><br>";
        }
        if ($coconutHybridCondition) {
            $moistureCompatible = ($mois >= 65 && $mois <= 85);
            $humidityCompatible = ($hum >= 70 && $hum <= 85);
            $suggestions .= "<strong>COCONUT (Hybrid):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [65, 85], [70, 85]);
            $suggestions .= "<strong>Planting Techniques for Hybrid Coconut:</strong><br>
                • Site Selection: Ensure the site has good drainage, is free of waterlogging, and receives plenty of sunlight. Ideal elevation is up to 600 meters above sea level.<br>
                • Seedling Selection and Preparation: Select seedlings from reputable sources like the Philippine Coconut Authority (PCA). Choose 5–8-month-old seedlings with 6–8 healthy leaves and a strong, well-developed root system.<br>
                • Land Preparation: Clear the land of weeds and debris. Dig planting holes of 60 cm x 60 cm x 60 cm, with a spacing of 8–10 meters depending on cropping systems.<br>
                • Planting Process: Fill the hole with topsoil mixed with organic manure or compost. Plant the seedling with the nut base just above the ground level. Ensure the soil is firmly packed around the roots, leaving no air pockets.<br>
                • Watering: Water the seedlings immediately after planting and maintain regular watering during dry months.<br>
                • Soil Texture: Sandy loam or loamy soils<br>
                • Soil Moisture: 65–85%<br>
                • Humidity: 70–85%<br><br>";
        }
        if ($coconutDwarfCondition) {
            $moistureCompatible = ($mois >= 60 && $mois <= 80);
            $humidityCompatible = ($hum >= 70 && $hum <= 85);
            $suggestions .= "<strong>COCONUT (Dwarf):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [60, 80], [70, 85]);
            $suggestions .= "<strong>Planting Techniques for Dwarf Coconut:</strong><br>
                • Site Selection: Select well-drained, fertile land with abundant sunlight. Avoid low-lying areas prone to waterlogging.<br>
                • Seedling Selection and Preparation: Choose seedlings from high-yielding mother palms, preferably 4–6 months old. Ensure seedlings have 5–7 healthy leaves and a robust root system.<br>
                • Land Preparation: Clear the land of weeds and debris. Prepare planting holes of 45 cm x 45 cm x 45 cm, spaced 6–8 meters apart due to the smaller canopy size of dwarf varieties.<br>
                • Planting Process: Fill the planting hole with a mix of topsoil and organic compost. Place the seedling upright with the nut just above ground level. Firmly pack the soil around the roots to eliminate air pockets. Water immediately after planting.<br>
                • Watering: Regular irrigation is necessary during the first 2–3 years, especially in dry months.<br>
                • Soil Texture: Sandy loam or loamy soils<br>
                • Soil Moisture: 60-80%<br>
                • Humidity: 70-85%<br><br>";
        }
        
        if ($riceNSICRc222Condition) {
            $moistureCompatible = ($mois >= 60 && $mois <= 70);
            $humidityCompatible = ($hum >= 80 && $hum <= 90);
            $suggestions .= "<strong>NSIC Rc222 (Tubigan 18):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [60, 70], [80, 90]);
            $suggestions .= "<strong>Seed Preparation:</strong><br>
                Soak the seeds in water for 24 to 48 hours before sowing to improve germination rates. After soaking, allow the seeds to sprout for 24 to 36 hours.<br>
                <strong>Field Preparation:</strong><br>
                The field should be plowed and harrowed to create a well-prepared seedbed. For direct seeding, the soil must be flooded to a depth of about 5–10 cm.<br>
                <strong>Sowing (Direct Seeding):</strong><br>
                Seeds are broadcast in a puddled field. Typically, 80-100 kg of seeds are needed for a hectare.<br>
                <strong>Sowing (Transplanting):</strong><br>
                Transplant seedlings at 20-30 days after sowing with a spacing of 20-30 cm between rows and 20-25 cm between plants.<br>
                <strong>Water Management:</strong><br>
                Keep the soil flooded during most of the growing period, maintaining a water depth of 5–10 cm during the vegetative stage and reducing it during the reproductive stage.<br>
                <strong>Soil Texture:</strong><br>
                Loam or Clay Loam<br>
                <strong>Soil Moisture Content:</strong><br>
                Maintain soil moisture at 5–10 cm of standing water for most of the crop’s growth cycle.<br>
                <strong>Humidity:</strong><br>
                80–90%<br><br>";
        }
        if ($riceSL8HCondition) {
            $moistureCompatible = ($mois >= 60 && $mois <= 70);
            $humidityCompatible = ($hum >= 80 && $hum <= 90);
            $suggestions .= "<strong>SL-8H (Hybrid Rice):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [60, 70], [80, 90]);
            $suggestions .= "<strong>Seedbed Preparation:</strong><br>
                Start by preparing a well-tilled seedbed to ensure a fine, loose soil structure.<br>
                <strong>Seed Rate:</strong><br>
                Use around 30-40 kg of hybrid rice seed per hectare.<br>
                <strong>Sowing Method (Direct Seeding):</strong><br>
                For direct seeding, pre-germinated seeds are sown in puddled or non-puddled fields. The seeds should be sown at a depth of about 2-3 cm.<br>
                <strong>Sowing Method (Transplanting):</strong><br>
                For transplanting, seedling nurseries are prepared and 20-30 days old seedlings are transplanted at a spacing of 20-25 cm x 20-25 cm between rows and plants.<br>
                <strong>Water Management:</strong><br>
                For transplanting, fields should be flooded before transplanting and maintained with shallow water (about 5-7 cm) throughout the early growth stages. For direct seeding, it is essential to maintain consistent moisture levels for optimal seed germination.<br>
                <strong>Soil Texture:</strong><br>
                Loam to clay loam and clay soil with good water retention capacity<br>
                <strong>Soil Moisture Content:</strong><br>
                Water should be kept shallow at around 5-10 cm during the vegetative and reproductive stages.<br>
                <strong>Humidity:</strong><br>
                80–90%<br><br>";
        }
        if ($riceNSICRc160Condition) {
            $moistureCompatible = ($mois >= 80 && $mois <= 85);
            $humidityCompatible = ($hum >= 80 && $hum <= 85);
            $suggestions .= "<strong>NSIC Rc160 (Tubigan 14):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [80, 85], [80, 85]);
            $suggestions .= "<strong>Seed Preparation:</strong><br>
                Soak the seeds in water for 24-48 hours to enhance germination. Drain and allow them to sprout.<br>
                <strong>Land Preparation:</strong><br>
                The land should be well-prepared with thorough plowing and harrowing to achieve a fine seedbed. The field should be leveled to avoid water stagnation or uneven water distribution.<br>
                <strong>Sowing Method (Direct Seeding):</strong><br>
                Sowing in flooded fields, placing seeds at a rate of 80-100 kg/ha.<br>
                <strong>Sowing Method (Transplanting):</strong><br>
                Transplant young seedlings (25-30 days old) at a spacing of 20-25 cm between rows and 15-20 cm between plants.<br>
                <strong>Water Management:</strong><br>
                Maintain a shallow flood of 5-10 cm throughout the growing season. Drain the field 2-3 weeks before harvest to allow the soil to dry.<br>
                <strong>Soil Texture:</strong><br>
                Loam, clay-loam, and silty soils<br>
                <strong>Soil Moisture Content:</strong><br>
                Consistent water availability is crucial, with the field maintaining 5-10 cm of water depth.<br>
                <strong>Humidity:</strong><br>
                75-90%<br><br>";
        }
        if ($riceMestiso20Condition) {
            $moistureCompatible = ($mois >= 86 && $mois <= 90);
            $humidityCompatible = ($hum >= 86 && $hum <= 90);
            $suggestions .= "<strong>Mestiso 20 (M20):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [86, 90], [86, 90]);
            $suggestions .= "<strong>Land Preparation:</strong><br>
                Plowing: The land should be plowed to a depth of 10-15 cm. This helps break up the soil and improves water infiltration.<br>
                Leveling: After plowing, level the field to prevent water logging or uneven water distribution during irrigation.<br>
                Flooding: Flood the field with water, maintaining a shallow depth (3-5 cm) for the seedbed preparation.<br>
                <strong>Sowing:</strong><br>
                Direct Seeding: Sow the seeds either by broadcasting or in rows, using 100-120 kg of seeds per hectare.<br>
                Transplanting: If transplanting, seedlings should be 25-30 days old with 2-3 leaves. Transplant 20-25 cm apart with a spacing of 20 cm between rows.<br>
                <strong>Water Management:</strong><br>
                Keep the field flooded to maintain 3-5 cm of standing water during most of the growing season.<br>
                <strong>Soil Texture:</strong><br>
                Ideal soil texture for Mestiso 20 is loam, It can also tolerate clay loam, provided the drainage is adequate to avoid waterlogging.<br>
                <strong>Soil Moisture:</strong><br>
                20-30%<br>
                <strong>Humidity:</strong><br>
                70-80%<br><br>";
        }
        if ($riceNSICRc480Condition) {
            $moistureCompatible = ($mois >= 91 && $mois <= 95);
            $humidityCompatible = ($hum >= 91 && $hum <= 95);
            $suggestions .= "<strong>NSIC Rc480 (Submarino 1):</strong><br>";
            $suggestions .= generateSuggestions($mois, $moistureCompatible, $hum, $humidityCompatible, [91, 95], [91, 95]);
            $suggestions .= "<strong>Land Preparation:</strong><br>
                The field should be well-leveled, and any weeds or debris should be removed. A fine seedbed should be prepared by plowing and harrowing.<br>
                <strong>Seeding Method:</strong><br>
                Direct seeding or transplanting can be done depending on local practices. For direct seeding, the recommended seed rate is 90-120 kg/ha, and for transplanting, 20-25 kg/ha is suggested.<br>
                <strong>Water Management:</strong><br>
                NSIC Rc480 requires controlled irrigation, keeping the field flooded during the vegetative and reproductive stages, with periodic drainage for aeration.<br>
                <strong>Soil Texture:</strong><br>
                Loamy to clay loam<br>
                <strong>Soil Moisture Content:</strong><br>
                70-80%<br>
                <strong>Humidity:</strong><br>
                60-80%<br><br>";
        }
        
        echo $suggestions;
        
    } else {
        // General recommendations if no specific conditions are met
        echo "<strong>GENERAL RECOMMENDATIONS:</strong><br>
            Current conditions (Moisture: {$mois}%, Humidity: {$hum}%) are not optimal.<br><br>

            <strong>SOIL HEALTH AND FERTILITY:</strong><br>
            
            <strong>RICE:</strong><br>
            • Use organic matter such as compost and biochar to enrich soil structure, improve water retention, and increase nutrient content. Regular soil testing can help in determining the nutrient requirements of the rice crop.<br><br>

            <strong>CORN:</strong><br>
            • Apply organic fertilizers or well-rotted manure to maintain soil health. Regular application of nitrogen-based fertilizers during key growth stages can improve yield.<br><br>

            <strong>COCONUT:</strong><br>
            • Improve soil drainage and fertility through the addition of organic amendments such as compost and mulch. Coconut palms thrive in well-drained, fertile soils with a pH between 5.5 and 6.5.<br><br>

            <strong>WATER MANAGEMENT:</strong><br>

            <strong>RICE:</strong><br>
            �� Ensure proper irrigation to maintain optimal soil moisture (40-60%) for rice germination and seedling growth. Employ sustainable irrigation techniques like drip or furrow irrigation to minimize water wastage.<br><br>

            <strong>CORN:</strong><br>
            • Implement efficient irrigation strategies, particularly during the critical stages of corn growth (leaf formation, flowering, and early kernel development). Consider rainwater harvesting to optimize water usage.<br><br>

            <strong>COCONUT:</strong><br>
            • Manage water flow around the coconut palms by incorporating proper drainage systems. In areas with high rainfall, use trenching or mounding techniques to prevent waterlogging and root rot.<br><br>

            <strong>PEST AND DISEASE CONTROL:</strong><br>

            <strong>RICE:</strong><br>
            • Implement an Integrated Pest Management (IPM) system, utilizing natural pest control methods such as beneficial insects (e.g., ladybugs, spiders), biopesticides (panyawan or chili extracts), and manual pest collection to minimize chemical pesticide usage.<br><br>

            <strong>CORN:</strong><br>
            • Regularly monitor pest populations and implement pest control measures like using organic insecticides or promoting natural predators. Utilize crop rotation to reduce pest build-up.<br><br>

            <strong>COCONUT:</strong><br>
            • Use IPM practices to control pests such as the coconut rhinoceros beetle. This can include cultural practices like proper sanitation, pruning, and using biological control agents (e.g., natural predators) to keep pests at bay.<br><br>

            <strong>WEED MANAGEMENT:</strong><br>

            <strong>RICE:</strong><br>
            • Utilize pre-planting weed management techniques, including plowing and harrowing to control weeds before sowing. Post-transplant, implement manual weeding or use eco-friendly herbicides if necessary.<br><br>

            <strong>CORN:</strong><br>
            • Regularly monitor for weeds and implement shallow cultivation to avoid root damage. Use mulching techniques to suppress weed growth without harming the plants.<br><br>

            <strong>COCONUT:</strong><br>
            • Use a combination of mechanical and manual weeding, especially around the base of the palms. Additionally, practice mulching with organic materials like coconut husks or leaves to prevent weed growth while improving soil moisture retention.<br><br>

            <strong>SOIL CONSERVATION:</strong><br>

            <strong>RICE:</strong><br>
            • Use practices like contour farming or the creation of bunds (small embankments) to prevent soil erosion and retain water in the rice paddies.<br><br>

            <strong>CORN:</strong><br>
            • Implement soil conservation techniques such as terracing on sloped land to prevent soil erosion. This can be done using natural barriers or planted vegetation to stabilize the soil.<br><br>

            <strong>COCONUT:</strong><br>
            • Employ soil conservation methods such as minimum tillage, contour planting, and the use of cover crops to prevent erosion and maintain soil structure. Regular mulching can also reduce soil compaction and increase organic matter.<br><br>

            <strong>INTERCROPPING AND CROP ROTATION:</strong><br>

            <strong>RICE:</strong><br>
            • Consider intercropping rice with legumes or vegetables that can fix nitrogen in the soil, improving its fertility for the next planting season.<br><br>

            <strong>CORN:</strong><br>
            • Rotate corn with other crops like beans or peas to maintain soil fertility and break pest and disease cycles. Corn can also be intercropped with fast-growing crops like legumes or vegetables to maximize land use.<br><br>

            <strong>COCONUT:</strong><br>
            • Intercrop with shade-loving crops such as coffee or cacao, or with other fruit trees like bananas, to make the most out of the space between coconut palms while reducing soil erosion.<br><br>

            <strong>CLIMATE AND WEATHER MONITORING:</strong><br>

            <strong>RICE:</strong><br>
            • Adapt planting schedules based on seasonal weather patterns. Use weather forecasting tools to anticipate flooding or drought conditions and adjust irrigation accordingly.<br><br>

            <strong>CORN:</strong><br>
            • Monitor weather conditions closely, particularly during the germination and pollination stages. Adjust planting and irrigation schedules to suit weather patterns.<br><br>

            <strong>COCONUT:</strong><br>
            • Regularly monitor climatic conditions, especially temperature and rainfall, to ensure the palms are not exposed to extreme weather. Use weather data to adjust management practices such as irrigation or pest control.<br><br>

            <strong>POST-HARVEST HANDLING:</strong><br>

            <strong>RICE:</strong><br>
            • Implement proper drying and storage techniques to prevent post-harvest losses. Ensure harvested rice is stored in well-ventilated, dry conditions to avoid mold and insect infestations.<br><br>

            <strong>CORN:</strong><br>
            • Harvest corn when kernels reach physiological maturity (black layer stage) to ensure proper seed quality. Store harvested corn in moisture-controlled conditions to prevent spoilage.<br><br>

            <strong>COCONUT:</strong><br>
            • Improve post-harvest techniques by using proper drying and processing methods (e.g., copra drying) to maximize the value of coconut products.<br><br>

            <strong>FARMER EDUCATION AND TRAINING:</strong><br>
            • All three crops would benefit from regular farmer education programs that provide up-to-date techniques on crop care, pest management, and sustainable practices. Training farmers in modern technologies, sustainable farming methods, and market opportunities can lead to higher productivity and reduced environmental impact.";
    }
}
