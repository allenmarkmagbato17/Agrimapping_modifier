<?php
include_once "inc/conn.php";
if (!isset($_SESSION['user_id'])) echo "<script>window.location.href ='login.php'</script>";
$user = $_SESSION['user_id'];

define("TITLE", "FARMER's LAND");

include_once "inc/head.php";

$error = array();
$error_two = array();
$result = array();
$farmer = $conn->query("SELECT farmer_id, farmer_name FROM farmers")->fetch_all();

$bar = $conn->query("SELECT * FROM barangays WHERE mun_id=1113")->fetch_all();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save']) && $_POST['save'] == "save") {
        $fname = $_POST['land_name'];
        $farmer_id = $_POST['farmer_id'];
        $bar_id = $_POST['bar_id'];
        $find = $conn->query("SELECT * FROM lands WHERE land_name LIKE '%$fname%'")->fetch_assoc();

        if (empty($find)) {
            $conn->query("INSERT INTO `lands` (`land_name`, `farmer_id`, `bar_id`) VALUES ('$fname', '$farmer_id', '$bar_id');");
            if ($conn->affected_rows > 0) {
                array_push($error, array("msg" => "Farmer's Land Successfully added", "type" => "success"));
            } else {
                array_push($error, array("msg" => "Failed to add lands try again.", "type" => "warning"));
            }
            unset($_POST);
        } else {
            array_push($error, array("msg" => "Farmer name already Exists...", "type" => "warning"));
        }
    } elseif (isset($_POST['save']) && $_POST['save'] == "edit") {
        $id = $_POST["land_id"];
        $fname = $_POST['land_name'];
        $farmer_id = $_POST['farmer_id'];
        $bar_id = $_POST['bar_id'];
        
        $conn->query("UPDATE lands SET land_name='$fname', farmer_id='$farmer_id', bar_id='$bar_id' WHERE land_id='$id'");
        if ($conn->affected_rows > 0) {
            array_push($error_two, array("msg" => "Land Successfully edited", "type" => "success"));
        } else {
            array_push($error_two, array("msg" => "Failed to edit lands try again.", "type" => "warning"));
        }
    } elseif (isset($_POST['save']) && $_POST['save'] == "remove") {

        $id = $_POST["land_id"];
        $conn->query("DELETE FROM lands WHERE land_id='$id'");
        if ($conn->affected_rows > 0) {
            array_push($error_two, array("msg" => "Farmer's Successfully remove", "type" => "success"));
        } else {
            array_push($error_two, array("msg" => "Failed to remove lands try again.", "type" => "warning"));
        }
    }

    if (isset($_POST['land_ids'])) {
        $id = $_POST['land_ids'];
        $f = $conn->query("SELECT * FROM land_test WHERE land_id='$id'");
        $land_texture = $_POST['land_texture'];
        $sand = $_POST['sand'];
        $silt = $_POST['silt'];
        $clay = $_POST['clay'];
        if ($f->num_rows == 0) {
            // $conn->query("INSERT INTO `land_test` (`land_test_id`, `land_moisture`, `land_id`, `land_humidity`, `land_texture`) VALUES (NULL, '$land_moisture', '$id', '$land_humidity', '$land_texture')");
        } else {
            $conn->query("UPDATE land_test SET land_texture='$land_texture', sand='$sand', silt='$silt', clay='$clay' WHERE land_id='$id' ");
        }
    }

    if (isset($_POST['search'])) {
        $search = $_POST['search'];
        $result = $conn->query("SELECT * FROM lands LEFT JOIN farmers ON farmers.farmer_id =  lands.farmer_id LEFT JOIN barangays as b ON b.bar_id=lands.bar_id WHERE concat(farmer_name, bar_name, land_name) LIKE '%$search%'");
    } else {
        $result = $conn->query("SELECT * FROM lands LEFT JOIN farmers ON farmers.farmer_id =  lands.farmer_id LEFT JOIN barangays as b ON b.bar_id=lands.bar_id");
    }
} else {
    $result = $conn->query("SELECT * FROM lands LEFT JOIN farmers ON farmers.farmer_id =  lands.farmer_id LEFT JOIN barangays as b ON b.bar_id=lands.bar_id");
}

?>

<script>
    
    function soilAnalyzer(sand, silt, clay) {
        // let total = sand + silt + clay;
        // sand = (sand / total) * 100;
        // silt = (silt / total) * 100;
        // clay = (clay / total) * 100;
        let s = "";
        if ((clay > 40 && clay <= 100) && (silt >= 0 && silt < 60) && (sand >= 0 && sand <= 55)) {
            s = "Clay";
        } else if ((clay >= 35 && clay <= 55) && (silt >= 0 && silt <= 20) && (sand >= 35 && sand <= 75)) {
            s = "Sandy Clay";
        } else if ((clay >= 40 && clay <= 60) && (silt >= 40 && silt <= 60) && (sand >= 0 && sand <= 20)) {
            s = "Silty Clay";
        } else if ((clay >= 28 && clay <= 40) && (silt >= 15 && silt <= 52) && (sand >= 20 && sand <= 45)) {
            s = "Clay Loam";
        } else if ((clay >= 28 && clay <= 40) && (silt >= 40 && silt <= 73) && (sand >= 0 && sand <= 20)) {
            s = "Silty Clay Loam";
        } else if ((clay >= 20 && clay <= 35) && (silt >= 0 && silt <= 28) && (sand >= 45 && sand <= 80)) {
            s = "Sandy Clay Loam"; //ok
        } else if ((clay >= 5 && clay <= 28) && (silt >= 28 && silt <= 50) && (sand >= 23 && sand <= 53)) {
            s = "Loam  "; //ok
        } else if ((clay >= 0 && clay <= 28) && (silt >= 73 && silt <= 87) && (sand >= 0 && sand <= 50)) {
            s = "Silt Loam";  //ok
        } else if ((clay >= 0 && clay <= 14) && (silt >= 80 && silt <= 100) && (sand >= 0 && sand <= 20)) {
            s = "Silt"; //ok
        } else if ((clay >= 0 && clay <= 20) && (silt >= 0 && silt <= 50) && (sand >= 45 && sand <= 85)) {
            s = "Sandy Loam"; //ok
        } else if ((clay >= 0 && clay <= 15) && (silt >= 0 && silt <= 30) && (sand >= 70 && sand <= 90)) {
            s = "Loamy Sand";
        } else if ((clay >= 0 && clay <= 10) && (silt >= 0 && silt <= 10) && (sand >= 90 && sand <= 100)) {
            s = "Sand";
        }else{
            s = "";
        }
        return s;
    }


    function AnalyzeSoil(e) {
        let clay = $("input[name=clay]").val()
        let sand = $("input[name=sand]").val()
        let silt = $("input[name=silt]").val()
        let soil = soilAnalyzer(sand, silt, clay);
        $("input[name=land_texture]").val(soil);
    }

    function calculateAverageMoisture() {
        const moisture1 = parseFloat($("input[name='land_moisture_1']").val()) || 0;
        const moisture2 = parseFloat($("input[name='land_moisture_2']").val()) || 0;
        const moisture3 = parseFloat($("input[name='land_moisture_3']").val()) || 0;
        const moisture4 = parseFloat($("input[name='land_moisture_4']").val()) || 0;
        const moisture5 = parseFloat($("input[name='land_moisture_5']").val()) || 0;

        const averageMoisture = (moisture1 + moisture2 + moisture3 + moisture4 + moisture5) / 5;
        return averageMoisture.toFixed(2);
    }
</script>

<div class="container mt-2">
    <div class="row my-2">
        <div class="col-md-5 ">
            <form action="" class="" method="post">
                <div class="form-group ">
                    <input type="text" name="search" value="<?php echo $_POST['search'] ?? '' ?>" class="form-control px-2" placeholder="Search for lands...">
                </div>

            </form>
        </div>
        <div class="col-md-5">
            <?php
            if (count($error_two)): foreach ($error_two as $msg): ?>
                    <div class="alert alert-<?= $msg['type'] ?> alert-dismissible fade show" role="alert"><?= $msg['msg'] ?></div>
            <?php endforeach;
            endif; ?>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-light btn-block" data-toggle="modal" data-target="#exampleModal" onclick="save()">
                <img src="images/farm.svg" style="width: 20px;" alt="" srcset="">
                Add New Land</button>
        </div>
    </div>
    <div class="row">
    <div class="col s12">
        <div class="card hoverable">
            <div class="card-content">
                <h5 class="center-align">Farm List</h5>
                <!-- Scrollable Table Wrapper -->
                <div class="responsive-table" style="max-height: 500px; overflow-y: auto;">
                    <table class="striped highlight centered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Farm Name</th>
                                <th>Farmer Name</th>
                                <th>Barangay</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($result)): ?>
                                <?php foreach ($result as $row => $row_data): ?>
                                    <tr class="hoverable">
                                        <td><?php echo $row + 1; ?></td>
                                        <td width="30%">
                                            <?php echo $row_data['land_name']; ?>
                                            <span 
                                                class="new badge <?= $row_data['sts'] ? 'green' : 'red' ?>" 
                                                data-badge-caption="<?= $row_data['sts'] ? 'Test On' : 'Test Off' ?>" 
                                                style="cursor:pointer;" 
                                                onclick="turnTest(`<?= $row_data['land_id'] ?>`)">
                                            </span>
                                        </td>
                                        <td><?php echo $row_data['farmer_name']; ?></td>
                                        <td><?php echo $row_data['bar_name']; ?></td>
                                        <td>
                                            <a 
                                                href="#" 
                                                class="waves-effect waves-light tooltipped" 
                                                data-position="top" 
                                                data-tooltip="Land Tagging" 
                                                onclick="LandTest(`<?= $row_data['land_id'] ?>`, `<?= ($row_data['farmer_name'] . ' : ' . $row_data['land_name']) ?>`)">
                                                <i class="material-icons">data_usage</i>
                                            </a>
                                            <a 
                                                href="tagging.php?land_id=<?php echo $row_data['land_id']; ?>" 
                                                target="_blank" 
                                                class="waves-effect waves-light tooltipped" 
                                                data-position="top" 
                                                data-tooltip="Tag Location">
                                                <i class="material-icons">location_on</i>
                                            </a>
                                            <a 
                                                href="#" 
                                                class="waves-effect waves-light tooltipped" 
                                                data-position="top" 
                                                data-tooltip="Edit Information" 
                                                onclick="edit(`<?php echo $row_data['land_id'] ?>`, `<?php echo $row_data['farmer_id'] ?>`, `<?php echo $row_data['land_name'] ?>`, `<?php echo $row_data['bar_id'] ?>`)">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <a 
                                                href="#" 
                                                class="waves-effect waves-light tooltipped red-text" 
                                                data-position="top" 
                                                data-tooltip="Remove Farm" 
                                                onclick="remove(`<?php echo $row_data['land_id'] ?>`, `<?php echo $row_data['farmer_id'] ?>`, `<?php echo $row_data['land_name'] ?>`, `<?php echo $row_data['bar_id'] ?>`)">
                                                <i class="material-icons">delete</i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="center-align">No Found Data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Farmer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <?php
                    if (count($error)):
                        foreach ($error as $msg):
                    ?>
                            <div class="alert alert-<?= $msg['type'] ?> alert-dismissible fade show" role="alert"><?= $msg['msg'] ?></div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                    <input type="hidden" name="land_id">
                    <div class="form-group">
                        <label for="">Farm name</label>
                        <input type="text" class="form-control" placeholder="Enter Farm Name" name="land_name" value="<?php echo $_POST['land_name'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Farmer</label>
                        <select id="barangay" name="farmer_id" class="form-control form-control-sm">
                            <?php foreach ($farmer as $m => $k): ?>
                                <option value="<?php echo $k[0] ?>"><?php echo $k[1] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Barangay</label>
                        <select id="barangay" name="bar_id" class="form-control form-control-sm">
                            <?php foreach ($bar as $m => $k): ?>
                                <option value="<?php echo $k[0] ?>"><?php echo $k[1] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    <input type="submit" name="save" value="save" class="btn btn-dark">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="landTestModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><span id="names"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <form action="" method="post">
                            <input type="hidden" name="land_ids">
                            <div class="form-group">
                                <label for="">Land Humidity</label>
                                <input type="text" class="form-control" name="land_humidity" placeholder=" Humidity Content" readonly>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Moisture (Sensor 1)</label>
                                        <input type="text" class="form-control" name="land_moisture_1" placeholder=" Moisture " readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Land Moisture (Sensor 2)</label>
                                        <input type="text" class="form-control" name="land_moisture_2" placeholder=" Moisture " readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Land Moisture (Sensor 3)</label>
                                        <input type="text" class="form-control" name="land_moisture_3" placeholder=" Moisture " readonly>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Land Moisture (Sensor 4)</label>
                                        <input type="text" class="form-control" name="land_moisture_4" placeholder=" Moisture " readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Land Moisture (Sensor 5)</label>
                                <input type="text" class="form-control" name="land_moisture_4" placeholder=" Moisture " readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Land Temperature</label>
                                <input type="text" class="form-control" name="land_temperature" placeholder=" Temperature " readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Land Texture</label>
                                <input type="text" class="form-control" name="land_texture" placeholder=" Land Texture " readonly>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="">Sand</label>
                                        <input type="number" class="form-control" onchange="AnalyzeSoil(this)"  onkeyup="AnalyzeSoil(this)" value="0" name="sand" placeholder="Sand ">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="">Silt</label>
                                        <input type="number" class="form-control" onchange="AnalyzeSoil(this)" onkeyup="AnalyzeSoil(this)" value="0" name="silt" placeholder=" Silt ">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="">Clay</label>
                                        <input type="number" class="form-control" onchange="AnalyzeSoil(this)" onkeyup="AnalyzeSoil(this)" value="0" name="clay" placeholder=" Clay ">
                                    </div>
                                </div>
                            </div>
                        
                    </div>
                    <div class="col-md-8">
                        <h4>Recommendation</h4>
                        <div class="row">
                            <div class="col-6">
                                Description
                                <div class="card p-3" style="max-height: 200px; overflow-y: auto;">
                                <span style="color:red;"><strong>SOIL TYPE:</strong></span>
    <span class="ml-2" id="slt">NO SOIL TYPE</span>
       <br>
       <span style="color:red;"><strong>CHARACTERISTICS:</strong></span>
    <span class="ml-2" id="cha"></span>
    <span style="color:red;"><strong>PROPERTIES:</strong></span>

    <span class="ml-2" id="pro"></span>
    <span></span><br>
    <span style="color:red;"><strong>PLANT SUGGESTIONS:</strong></span>

    <div class="ml-4" id="rec">No Suggestion</div>
</div>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#dataModal">
    View Details
</button>
                            </div>
                            <div class="col-6">
                                Area <br>
                                <img src="images/soil.png" style="width:100%;" alt="" srcset="">
                                <div class="card">

                                </div>
                            </div>
                        </div>
                        <div id="charts" style="overflow-y:hidden;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
   <!--<a href="javascript:void(0);" onclick="saveLandRecord()" class="waves-effect waves-light btn green">Save</a>
    //<a href="lands.php" class="modal-close waves-effect waves-light btn red">Close</a>
</div>-->
<div class="modal-footer">
    <!-- Close Button -->
<!-- Close Button -->
<button 
    type="button" 
    class="btn waves-effect waves-light red lighten-1 modal-close" 
    onclick="closeModalAndRedirect()">
    Close
</button>


    <button type="submit" name="save_test" class="btn waves-effect waves-light blue darken-2" onclick="showSaveWarning()">Save</button>
</div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Trigger -->


<!-- Modal Structure -->
<div id="dataModal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <!-- Modal Header -->
        <h4 class="center-align">Detailed Information</h4>
        
        <!-- Responsive Table -->
        <div class="row">
            <div class="col s12">
                <table class="striped centered highlight responsive-table">
                    <thead class="blue lighten-4">
                        <tr>
                            <th>Soil Type</th>
                            <th>Characteristics</th>
                            <th>Properties</th>
                            <th>Plant Suggestions</th>
                        </tr>
                    </thead>
                    <tbody id="modalTableBody">
                        <!-- Dynamic Data will populate here -->
                        <tr>
                            <td>Loamy</td>
                            <td>Retains moisture, drains well</td>
                            <td>Rich in nutrients</td>
                            <td>Tomatoes, Peppers, Corn</td>
                        </tr>
                        <tr>
                            <td>Sandy</td>
                            <td>Drains quickly</td>
                            <td>Poor in nutrients</td>
                            <td>Carrots, Potatoes, Peanuts</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Footer -->
    <div class="modal-footer">
        <a href="lands.php" class="modal-close waves-effect waves-light btn red">
            Close
        </a>
    </div>
</div>

<style>
    .modal {
        max-height: 80%;
        width: 80%;
    }
    .modal-content h4 {
        font-size: 2rem;
        font-weight: bold;
    }
    .modal-footer .btn {
        margin-right: 10px;
    }
    .striped th, .striped td {
        padding: 15px;
    }
</style>
<?php

include_once "inc/foot.php";

?>

<?php
if (count($error)):
    echo "<script>$(`#exampleModal`).modal(`show`);</script>";
endif;
?>

<script>
   let char = {
    "Loam": {
        "Description": "A balanced mix of sand, silt, and clay, with less than 20% clay and roughly equal sand and silt proportions.",
        "Properties": "Loam may have a slight gritty feel but does not show a finger print and forms only short ribbons of from 0.25 inch to 0.50 inch in length, Loam will from a ball that can be handled without breaking. Sand imparts a gritty feel to soil due to the shape of the individual particles.Good water retention and drainage, easy to cultivate.",
        "Sand": "40%",
        "Silt": "40%",
        "Clay": "20%",
    },
    "Sandy_Loam": {
        "Description": "Soil with 50–85% sand, less than 20% clay, and significant silt content.",
        "Properties": "Sandy loam has a gritty feel, forms a ball that can be picked up with the fingers and handled with care without breaking. Drains quickly, low water retention, may require frequent watering.",
        "Sand": "60%",
        "Silt": "30%",
        "Clay": "10%",
    },
    "Clay_Loam": {
        "Description": "Soil with 27–40% clay and balanced proportions of sand and silt.",
        "Properties": "Clay loam is sticky when moist. Clay loam forms a thin ribbon of one (1) to two (2) inches in length and produces a slight sheen when rubbed with the thumb nail. Clay loam produces a nondistinct finger print.",
        "Sand": "30%",
        "Silt": "30%",
        "Clay": "40%",
    },
    "Silt_Loam": {
        "Description": "Soil with over 50% silt and less than 20% clay, with moderate sand content.",
        "Properties": "Smooth texture, good water retention, moderate drainage.",
        "Sand": "20%",
        "Silt": "60%",
        "Clay": "20%",
    },
    "Sandy_Clay_Loam": {
        "Description": "Higher clay and sand content with minimal silt.",
        "Properties": "Retains water well, but may require amendments to improve drainage.",
        "Sand": "50%",
        "Silt": "20%",
        "Clay": "30%",
    },
    "Silty_Clay_Loam": {
        "Description": "Higher silt and clay content with minimal sand.",
        "Properties": "Smooth texture, retains water well, but can become compacted.",
        "Sand": "20%",
        "Silt": "50%",
        "Clay": "30%",
    },
    "Clay": {
        "Description": "High clay content with minimal sand and silt.",
        "Properties": "Excellent water retention, poor drainage, often requires amendments.",
        "Sand": "10%",
        "Silt": "20%",
        "Clay": "70%",
    },
};
    

    function LandTest(id, name) {
        $.ajax({
            url: `inc/get_data.php?id=` + id,
            type: 'GET',
            success: function(i) {
                $("input[name=land_moisture]").val("");
                $("input[name=land_humidity]").val("");
                $("input[name=land_texture]").val("Sand");
                i = JSON.parse(i);

                if (i.count) {
                    $("input[name=land_moisture_1]").val(i.data[0].land_moisture + "%");
                    $("input[name=land_moisture_2]").val(i.data[1].land_moisture + "%");
                    $("input[name=land_moisture_3]").val(i.data[2].land_moisture + "%");
                    $("input[name=land_moisture_4]").val(i.data[3].land_moisture + "%");
                    $("input[name=land_moisture_5]").val(i.data[4].land_moisture + "%");
                    $("input[name=land_temperature]").val(i.data[0].land_temp + "°C");
                    $("input[name=land_humidity]").val(i.data[0].land_humidity + "%");

                    $("input[name=land_texture]").val(i.data[0].land_texture);
                    $("input[name=sand]").val(i.data[0].sand);
                    $("input[name=silt]").val(i.data[0].silt);
                    $("input[name=clay]").val(i.data[0].clay);


                    $("#slt").html(i.data[0].land_texture);
                    if (i.data[0].land_texture == "Loam") {
                        $("#cha").html("* " + char.Loam.Description);
                        $("#pro").html("* " + char.Loam.Properties);
                    } else if (i.data[0].land_texture == "Sandy Loam") {
                        $("#cha").html("* " + char.Sandy_Loam.Description);
                        $("#pro").html("* " + char.Sandy_Loam.Properties);
                    } else if (i.data[0].land_texture == "Clay Loam") {
                        $("#cha").html("* " + char.Clay_Loam.Description);
                        $("#pro").html("* " + char.Clay_Loam.Properties);
                    } else if (i.data[0].land_texture == "Silt Loam") {
                        $("#cha").html("* " + char.Silt_Loam.Description);
                        $("#pro").html("* " + char.Silt_Loam.Properties);
                    } else if (i.data[0].land_texture == "Sandy Clay Loam") {
                        $("#cha").html("* " + char.Sandy_Clay_Loam.Description);
                        $("#pro").html("* " + char.Sandy_Clay_Loam.Properties);
                    } else if (i.data[0].land_texture == "Silty Clay Loam") {
                        $("#cha").html("* " + char.Silty_Clay_Loam.Description);
                        $("#pro").html("* " + char.Silty_Clay_Loam.Properties);
                    } else if (i.data[0].land_texture == "Clay") {
                        $("#cha").html("* " + char.Clay.Description);
                        $("#pro").html("* " + char.Clay.Properties);
                    }
                }

                $("#names").html(name);
                $("input[name=land_ids]").val(id);
                $(`#landTestModal`).modal(`show`);
            }
        });

        $.ajax({
            url: `inc/get_data.php?recommend=` + id,
            type: 'GET',
            success: function(i) {
                $("#rec").html(i);
            }
        });
        $("#charts").html(`<iframe style="width:100%; height:40vh;overflow-y:hidden; border:none;" src="charts.php?land_id=${id}" title="W3Schools Free Online Web Tutorials"></iframe>`);


    }

    function save() {
        $("input[name=save]").val("save");
        $("input[name=land_id]").val();
        $("#exampleModalLabel").html("Add new Farmer");
    }

    function remove(id, farmer_id, land_name, bar_id) {
        $("input[name=save]").val("remove");
        $("input[name=land_id]").val(id);
        $("input[name=land_name]").val(land_name).prop("readonly", false);
        $("select[name=farmer_id]").val(farmer_id).prop("readonly", false);
        $("select[name=bar_id]").val(bar_id).prop("readonly", false);
        $("#exampleModalLabel").html("Confirm Remove farmer");
        $(`#exampleModal`).modal(`show`);
    }

    function edit(id, farmer_id, land_name, bar_id) {
        $("input[name=save]").val("edit");
        $("input[name=land_id]").val(id);
        $("input[name=land_name]").val(land_name).prop("readonly", false);
        $("select[name=farmer_id]").val(farmer_id).prop("readonly", false);
        $("select[name=bar_id]").val(bar_id).prop("readonly", false);
        $("#exampleModalLabel").html("Edit Information");
        $(`#exampleModal`).modal(`show`);
    }

    function turnTest(id) {
        $.ajax({
            type: 'GET',
            url: 'inc/get_data.php?turnTest=' + id,
            success: function(data) {
                window.location.href = '';
            }
        })
        
    }
    $('#dataModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    var soilType = $('#slt').text();
    var characteristics = $('#cha').text();
    var properties = $('#pro').text();
    var plantSuggestions = $('#rec').html();

    var tableRow = `
        <tr>
            <td>${soilType}</td>
            <td>${characteristics}</td>
            <td>${properties}</td>
            <td>${plantSuggestions}</td>
        </tr>
    `;

    modal.find('#modalTableBody').html(tableRow);
});

document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.modal');
    var instances = M.Modal.init(elems, {
        opacity: 0.5,
        inDuration: 300,
        outDuration: 200
    });
});
function saveLandRecord() {
    let land_id = $("input[name=land_ids]").val();
    let land_texture = $("input[name=land_texture]").val();
    let land_moisture = $("input[name=land_moisture]").val();
    let land_humidity = $("input[name=land_humidity]").val();
    let land_temperature = $("input[name=land_temperature]").val();
    let sand = $("input[name=sand]").val();
    let silt = $("input[name=silt]").val();
    let clay = $("input[name=clay]").val();

    $.ajax({
        url: "save_record.php",
        type: "POST",
        data: {
            land_id: land_id,
            land_texture: land_texture,
            land_moisture: land_moisture,
            land_humidity: land_humidity,
            land_temperature: land_temperature,
            sand: sand,
            silt: silt,
            clay: clay
        },
        success: function(response) {
            let res = JSON.parse(response);
            if (res.status === "success") {
                alert(res.message);
                $(`#landTestModal`).modal(`close`);
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert("An error occurred while saving the record.");
        }
    });
}
// Function to show a warning when 'Close' is clicked
// Function to show a notification after the modal is closed
// Function to show a toast notification and redirect to 'lands.php'
function closeModalAndRedirect() {
    // Show the toast notification
    M.toast({ 
        html: 'You have closed the modal.', 
        classes: 'red darken-1', 
        displayLength: 2000 // Shorter duration to allow redirection
    });

    // Delay redirection to let the toast display
    setTimeout(() => {
        window.location.href = 'lands.php';
    }, 100); // Matches the toast display length
}


// Function to show a success or warning when 'Save' is clicked
function showSaveWarning() {
    M.toast({ html: 'Data has been saved successfully.', classes: 'green darken-1' });
}

// Add this new function to handle viewing all land details
function viewAllLandDetails(landId) {
    $.ajax({
        url: `inc/get_data.php?id=${landId}`,
        type: 'GET',
        success: function(response) {
            const data = JSON.parse(response);
            
            // Calculate average moisture from all sensors
            const moistureValues = [
                parseFloat(data.data[0].land_moisture),
                parseFloat(data.data[1].land_moisture),
                parseFloat(data.data[2].land_moisture),
                parseFloat(data.data[3].land_moisture),
                parseFloat(data.data[4].land_moisture)
            ];
            const avgMoisture = moistureValues.reduce((a, b) => a + b) / moistureValues.length;

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
                                        <td>${data.data[0].land_temp}°C</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Humidity:</strong></td>
                                        <td>${data.data[0].land_humidity}%</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Soil Type:</strong></td>
                                        <td>${data.data[0].land_texture}</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="soil-composition mt-3">
                                <h6>Soil Composition</h6>
                                <p>Sand: ${data.data[0].sand}%</p>
                                <p>Silt: ${data.data[0].silt}%</p>
                                <p>Clay: ${data.data[0].clay}%</p>
                            </div>

                            <div class="recommendations mt-3">
                                <h6>Recommendations</h6>
                                <div id="landRecommendations"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Show the modal with the land details
            const modal = M.Modal.getInstance(document.getElementById('landDetailsModal'));
            document.getElementById('landDetailsContent').innerHTML = modalContent;
            modal.open();

            // Load recommendations separately
            $.ajax({
                url: `inc/get_data.php?recommend=${landId}`,
                type: 'GET',
                success: function(recommendations) {
                    document.getElementById('landRecommendations').innerHTML = recommendations;
                }
            });
        }
    });
}

// Add this HTML for the modal structure
</script>

<div id="suggestionsModal" class="modal">
    <div class="modal-content">
        <h4 class="center-align">All Plant Suggestions</h4>
        <div class="suggestions-content" style="max-height: 60vh; overflow-y: auto;">
            <table class="striped centered highlight responsive-table">
                <thead>
                    <tr>
                        <th>Soil Type</th>
                        <th>Characteristics</th>
                        <th>Properties</th>
                        <th>Plant Suggestions</th>
                    </tr>
                </thead>
                <tbody id="suggestionsTableBody">
                    <!-- Suggestions data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="modal-close waves-effect waves-light btn red">Close</a>
    </div>
</div>

<!-- Land Details Modal -->
<div class="modal fade custom-modal" id="landDetailsModal">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <div class="header-content">
                    <div class="farm-icon">
                        <i class="material-icons">landscape</i>
                    </div>
                    <div class="farm-info">
                        <h4 class="modal-title">Farm Details</h4>
                        <p class="farm-location">
                            <i class="material-icons tiny">location_on</i>
                            <span id="farmLocation">Barangay Location</span>
                        </p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column - Sensor Data -->
                    <div class="col-md-4">
                        <!-- Current Readings Card -->
                        <div class="info-card">
                            <div class="card-header">
                                <i class="material-icons">schedule</i>
                                <h5>Current Readings</h5>
                            </div>
                            <div class="readings-grid">
                                <div class="reading-box temperature">
                                    <i class="material-icons">thermostat</i>
                                    <span class="reading-value">27°C</span>
                                    <span class="reading-label">Temperature</span>
                                </div>
                                <div class="reading-box humidity">
                                    <i class="material-icons">water_drop</i>
                                    <span class="reading-value">65%</span>
                                    <span class="reading-label">Humidity</span>
                                </div>
                                <div class="reading-box moisture">
                                    <i class="material-icons">opacity</i>
                                    <span class="reading-value">45%</span>
                                    <span class="reading-label">Soil Moisture</span>
                                </div>
                            </div>
                        </div>

                        <!-- Soil Composition Card -->
                        <div class="info-card">
                            <div class="card-header">
                                <i class="material-icons">layers</i>
                                <h5>Soil Composition</h5>
                            </div>
                            <div class="composition-chart">
                                <div class="composition-bar">
                                    <div class="sand" style="width: 40%">Sand 40%</div>
                                    <div class="silt" style="width: 35%">Silt 35%</div>
                                    <div class="clay" style="width: 25%">Clay 25%</div>
                                </div>
                                <div class="soil-type">
                                    <span class="label">Soil Type:</span>
                                    <span class="value">Loamy Soil</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Analysis & Recommendations -->
                    <div class="col-md-8">
                        <!-- Soil Analysis Card -->
                        <div class="info-card">
                            <div class="card-header">
                                <i class="material-icons">analytics</i>
                                <h5>Soil Analysis</h5>
                            </div>
                            <div class="analysis-content">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="soil-characteristics">
                                            <h6>Characteristics</h6>
                                            <ul class="characteristic-list">
                                                <li>Good water retention</li>
                                                <li>High nutrient content</li>
                                                <li>Well-draining structure</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="soil-triangle">
                                            <img src="images/soil.png" alt="Soil Triangle" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recommendations Card -->
                     <!--   <div class="info-card">
                            <div class="card-header">
                                <i class="material-icons">eco</i>
                                <h5>Crop Recommendations</h5>
                            </div>
                            <div class="recommendations-grid">
                                <div class="crop-card">
                                    <img src="images/corn.jpg" alt="Corn">
                                    <h6>Corn</h6>
                                    <div class="compatibility high">
                                        High Compatibility
                                    </div>
                                </div>
                                <div class="crop-card">
                                    <img src="images/rice.jpg" alt="Rice">
                                    <h6>Rice</h6>
                                    <div class="compatibility medium">
                                        Medium Compatibility
                                    </div>
                                </div>
                                <div class="crop-card">
                                    <img src="images/vegetables.jpg" alt="Vegetables">
                                    <h6>Vegetables</h6>
                                    <div class="compatibility high">
                                        High Compatibility
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 -->

<style>
/* Modal Styles */
.custom-modal .modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.custom-modal .modal-header {
    background: linear-gradient(135deg, #1e88e5, #1565c0);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 20px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.farm-icon {
    background: rgba(255,255,255,0.2);
    padding: 10px;
    border-radius: 50%;
}

.farm-info h4 {
    margin: 0;
    font-size: 1.5rem;
}

.farm-location {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 5px 0 0 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Card Styles */
.info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    overflow: hidden;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.card-header h5 {
    margin: 0;
    font-size: 1.1rem;
    color: #2c3e50;
}

/* Readings Grid */
.readings-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 20px;
}

.reading-box {
    text-align: center;
    padding: 15px;
    border-radius: 10px;
    color: white;
}

.reading-box.temperature { background: linear-gradient(135deg, #ff6b6b, #ee5253); }
.reading-box.humidity { background: linear-gradient(135deg, #4facfe, #00f2fe); }
.reading-box.moisture { background: linear-gradient(135deg, #43e97b, #38f9d7); }

.reading-value {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 5px 0;
}

.reading-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Soil Composition */
.composition-chart {
    padding: 20px;
}

.composition-bar {
    display: flex;
    height: 30px;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 15px;
}

.composition-bar div {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.sand { background: #ffd700; }
.silt { background: #cd853f; }
.clay { background: #8b4513; }

/* Recommendations */
.recommendations-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 20px;
}

.crop-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.crop-card img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.crop-card h6 {
    margin: 10px;
    text-align: center;
}

.compatibility {
    text-align: center;
    padding: 5px;
    font-size: 0.8rem;
    color: white;
}

.compatibility.high { background: #2ecc71; }
.compatibility.medium { background: #f1c40f; }
.compatibility.low { background: #e74c3c; }

/* Responsive Design */
@media (max-width: 768px) {
    .readings-grid,
    .recommendations-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-dialog {
        margin: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);
});
</script>