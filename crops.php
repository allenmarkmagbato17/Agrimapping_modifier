<?php
include_once "inc/conn.php";
if (!isset($_SESSION['user_id'])) echo "<script>window.location.href ='login.php'</script>";
$user = $_SESSION['user_id'];

define("TITLE", "CROPS");

include_once "inc/head.php";

$error = array();
$error_two = array();
$result = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save']) && $_POST['save'] == "save") {
        $crop_name = $_POST['crop_name'];
        $f_hum = $_POST['f_hum'];
        $t_hum = $_POST['t_hum'];
        $f_mois = $_POST['f_mois'];
        $t_mois = $_POST['t_mois'];
        $f_temp = $_POST['f_temp'];
        $t_temp = $_POST['t_temp'];
        $find = $conn->query("SELECT * FROM crops WHERE crop_name LIKE '%$crop_name%'")->fetch_assoc();

        if (empty($find)) {
            $conn->query("INSERT INTO `crops` (`crop_name`, `f_hum`, `t_hum`, `f_mois`, `t_mois`, `f_temp`, `t_temp`) VALUES ('$crop_name', '$f_hum', '$t_hum', '$f_mois', '$t_mois', '$f_temp', '$t_temp');");

            if ($conn->affected_rows > 0) {
                array_push($error, array("msg" => "Crops Successfully added", "type" => "success"));
            } else {
                array_push($error, array("msg" => "Failed to add crops try again.", "type" => "warning"));
            }
            unset($_POST);
        } else {
            array_push($error, array("msg" => "Crop name already Exists...", "type" => "warning"));
        }
    } elseif (isset($_POST['save']) && $_POST['save'] == "edit") {
        $id = $_POST["crop_id"];
        $crop_name = $_POST['crop_name'];
        $f_hum = $_POST['f_hum'];
        $t_hum = $_POST['t_hum'];
        $f_mois = $_POST['f_mois'];
        $t_mois = $_POST['t_mois'];
        $f_temp = $_POST['f_temp'];
        $t_temp = $_POST['t_temp'];
        $conn->query("UPDATE crops SET crop_name='$crop_name',f_hum='$f_hum', t_hum='$t_hum', f_mois='$f_mois', t_mois='$t_mois', f_temp='$f_temp', t_temp='$t_temp'  WHERE crop_id='$id'");
        if ($conn->affected_rows > 0) {
            array_push($error_two, array("msg" => "Crops Successfully edited", "type" => "success"));
        } else {
            array_push($error_two, array("msg" => "Failed to edit crops try again.", "type" => "warning"));
        }
    } elseif (isset($_POST['save']) && $_POST['save'] == "remove") {

        $id = $_POST["crop_id"];
        $conn->query("DELETE FROM crops WHERE crop_id='$id'");
        if ($conn->affected_rows > 0) {
            array_push($error_two, array("msg" => "Crops Successfully remove", "type" => "success"));
        } else {
            array_push($error_two, array("msg" => "Failed to remove crops try again.", "type" => "warning"));
        }
    }

    if (isset($_POST['search'])) {
        $search = $_POST['search'];
        $result = $conn->query("SELECT * FROM crops WHERE concat(crop_name) LIKE '%$search%'");
    } else {
        $result = $conn->query("SELECT * FROM crops");
    }
} else {
    $result = $conn->query("SELECT * FROM crops");
}



?>

<div class="container mt-2">
    <div class="row my-2">
        <div class="col-md-5 ">
            <form action="" class="" method="post">
                <div class="form-group ">
                    <input type="text" name="search" value="<?php echo $_POST['search'] ?? 0 ?>" class="form-control px-2" placeholder="Search for crops...">
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
        <!-- Simple Button Trigger -->
<!-- Button with Image -->
<!-- Green Button with Image -->
<a class="btn green modal-trigger" href="#cropModal" style="display: inline-flex; align-items: center; padding: 0 12px;">
    <img src="images/add.png" alt="Crop Icon" style="width: 20px; height: 20px; margin-right: 8px;">
    Crop
</a>


    </div>
    <div class="col-md-12 table-responsive table-hover">
        <table class="table ">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Crops Name</th>
                    <th>Humidity</th>
                    <th>Temperature</th>
                    <th>Moisture</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($result)): ?>
                    <?php foreach ($result as $row => $row_data): ?>
                        <tr>
                            <td><?php echo $row + 1; ?></td>
                            <td width=""><?php echo $row_data['crop_name']; ?></td>
                            <td width=""><?php echo $row_data['f_hum']; ?>% - <?php echo $row_data['t_hum']; ?>%</td>
                            <td width=""><?php echo $row_data['f_temp']; ?>°C - <?php echo $row_data['t_temp']; ?>°C</td>
                            <td width=""><?php echo $row_data['f_mois']; ?>% - <?php echo $row_data['t_mois']; ?>%</td>
                            <td width="10%">
                                <a
                                    onclick="edit(`<?php echo $row_data['crop_id'] ?>`, `<?php echo $row_data['crop_name'] ?>`, `<?= $row_data['f_hum'] ?>`,`<?= $row_data['t_hum'] ?>`, `<?= $row_data['f_temp'] ?>`, `<?= $row_data['t_temp'] ?>`, `<?= $row_data['f_mois'] ?>`, `<?= $row_data['t_mois'] ?>`)">
                                    <img src="images/edit.svg" style="width: 20px;" alt="" srcset=""></a>
                                <a
                                    onclick="remove(`<?php echo $row_data['crop_id'] ?>`, `<?php echo $row_data['crop_name'] ?>`, `<?= $row_data['f_hum'] ?>`,`<?= $row_data['t_hum'] ?>`, `<?= $row_data['f_temp'] ?>`, `<?= $row_data['t_temp'] ?>`, `<?= $row_data['f_mois'] ?>`, `<?= $row_data['t_mois'] ?>`)">
                                    <img src="images/delete.svg" style="width: 20px;" alt="" srcset=""></a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                    <tr>
                        <td colspan="6">No Found Data</td>
                    </tr>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>


<!-- Modal -->
<!-- Modal Trigger -->

<!-- Modal Structure -->
<div id="cropModal" class="modal">
    <div class="modal-content">
        <h5 class="modal-title center-align">Add New Crop</h5>
        <form action="" method="post" onsubmit="verifySubmission()">
            <div class="crop-list-container">
                <?php
                if (count($error)):
                    foreach ($error as $msg):
                ?>
                        <div class="card-panel <?= $msg['type'] ?> lighten-4"><?= $msg['msg'] ?></div>
                <?php
                    endforeach;
                endif;
                ?>
                <input type="hidden" name="crop_id">
                <div class="input-field">
                    <input type="text" id="crop_name" name="crop_name" value="<?php echo $_POST['crop_name'] ?? '' ?>" required>
                    <label for="crop_name">Crop's Name</label>
                </div>
                <fieldset>
                    <legend>Humidity</legend>
                    <div class="row">
                        <div class="input-field col s6">
                            <input type="number" id="min_humidity" onchange="CheckHum()" name="f_hum" value="<?php echo $_POST['f_hum'] ?? 0 ?>" required>
                            <label for="min_humidity">Minimum</label>
                        </div>
                        <div class="input-field col s6">
                            <input type="number" id="max_humidity" onchange="CheckHum()" name="t_hum" value="<?php echo $_POST['t_hum'] ?? 100 ?>" required>
                            <label for="max_humidity">Maximum</label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Temperature</legend>
                    <div class="row">
                        <div class="input-field col s6">
                            <input type="number" id="min_temp" onchange="CheckTemp()" name="f_temp" value="<?php echo $_POST['f_temp'] ?? 0 ?>" required>
                            <label for="min_temp">Minimum</label>
                        </div>
                        <div class="input-field col s6">
                            <input type="number" id="max_temp" onchange="CheckTemp()" name="t_temp" value="<?php echo $_POST['t_temp'] ?? 100 ?>" required>
                            <label for="max_temp">Maximum</label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Moisture</legend>
                    <div class="row">
                        <div class="input-field col s6">
                            <input type="number" id="min_mois" onchange="CheckMois()" name="f_mois" value="<?php echo $_POST['f_mois'] ?? 0 ?>" required>
                            <label for="min_mois">Minimum</label>
                        </div>
                        <div class="input-field col s6">
                            <input type="number" id="max_mois" onchange="CheckMois()" name="t_mois" value="<?php echo $_POST['t_mois'] ?? 100 ?>" required>
                            <label for="max_mois">Maximum</label>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div id="mess" class="red-text center-align" style="display:none;">Minimum must not be higher than Maximum</div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-light btn-flat">Close</a>
                <button type="submit" name="save" class="btn waves-effect waves-light">Save</button>
            </div>
        </form>
    </div>
</div>
<style>/* Scrollable container for crops */
.crop-list-container {
    max-height: 400px; /* Adjust based on modal size */
    overflow-y: auto;
    margin-bottom: 20px;
}

/* Smooth scrolling */
.crop-list-container::-webkit-scrollbar {
    width: 8px;
}
.crop-list-container::-webkit-scrollbar-thumb {
    background-color: #9e9e9e;
    border-radius: 4px;
}
.crop-list-container::-webkit-scrollbar-thumb:hover {
    background-color: #616161;
}
</style>



<?php
// gitawag ang file nga foot.php para e include sa index ug ma apil ang css ug javascript nga gi link sa website
include_once "inc/foot.php";

?>

<?php
if (count($error)):
    echo "<script>$(`#exampleModal`).modal(`show`);</script>";

endif;
?>

<script>
    
    function CheckHum(){
     let from = $("input[name=f_hum]").val();
     let to = $("input[name=t_hum]").val();
     if(parseInt(from) > parseInt(to)){
        $("input[name=save]").css("display", "none");
        $("#mess").css("display", "block");
    }else{
        $("input[name=save]").css("display", "block");
        $("#mess").css("display", "none");
     }
    }

    function CheckTemp(){
     let from = $("input[name=f_temp]").val();
     let to = $("input[name=t_temp]").val();
     if(parseInt(from) > parseInt(to)){
        $("input[name=save]").css("display", "none");
        $("#mess").css("display", "block");
     }else{
        $("input[name=save]").css("display", "block");
        $("#mess").css("display", "none");
     }
    }

    function CheckMois(){
     let from = $("input[name=f_mois]").val();
     let to = $("input[name=t_mois]").val();
     if(parseInt(from) > parseInt(to)){
        $("input[name=save]").css("display", "none");
        $("#mess").css("display", "block");
     }else{
        $("input[name=save]").css("display", "block");
        $("#mess").css("display", "none");
     }
    }

    function save() {
        $("input[name=save]").val("save");
        $("input[name=crop_id]").val();
        $("#exampleModalLabel").html("Add new Crop");
    }

    function remove(id, name, f_hum, t_hum, f_temp, t_temp, f_mois, t_mois) {
        $("input[name=save]").val("remove");
        $("input[name=crop_id]").val(id);
        $("input[name=crop_name]").val(name).prop("readonly", true);
        $("input[name=f_hum]").val(f_hum).prop("readonly", true);
        $("input[name=t_hum]").val(t_hum).prop("readonly", true);
        $("input[name=f_temp]").val(f_temp).prop("readonly", true);
        $("input[name=t_temp]").val(t_temp).prop("readonly", true);
        $("input[name=f_mois]").val(f_mois).prop("readonly", true);
        $("input[name=t_mois]").val(t_mois).prop("readonly", true);
        $("#exampleModalLabel").html("Confirm Remove Crop");
        $(`#exampleModal`).modal(`show`);
    }

    function edit(id, name, f_hum, t_hum, f_temp, t_temp, f_mois, t_mois) {
        $("input[name=save]").val("edit");
        $("input[name=crop_id]").val(id);
        $("input[name=crop_name]").val(name).prop("readonly", false);
        $("input[name=f_hum]").val(f_hum).prop("readonly", false);
        $("input[name=t_hum]").val(t_hum).prop("readonly", false);
        $("input[name=f_temp]").val(f_temp).prop("readonly", false);
        $("input[name=t_temp]").val(t_temp).prop("readonly", false);
        $("input[name=f_mois]").val(f_mois).prop("readonly", false);
        $("input[name=t_mois]").val(t_mois).prop("readonly", false);
        $("#exampleModalLabel").html("Edit Information");
        $(`#exampleModal`).modal(`show`);
    }
    // Initialize Materialize Modal
document.addEventListener('DOMContentLoaded', function () {
    var elems = document.querySelectorAll('.modal');
    M.Modal.init(elems);
});

</script>