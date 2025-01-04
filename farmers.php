<?php
include_once "inc/conn.php";
if (!isset($_SESSION['user_id'])) echo "<script>window.location.href ='login.php'</script>";
$user = $_SESSION['user_id'];

define("TITLE", "MAPPING");


include_once "inc/head.php";

$error = array();
$error_two = array();
$result = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save']) && $_POST['save'] == "save") {
        $fname = $_POST['farmer_name'];
        $faddress = $_POST['farmer_address'];
        $contact_no = $_POST['contact_no'];
        $email = $_POST['email'];

        $find = $conn->query("SELECT * FROM farmers WHERE farmer_name LIKE '%$fname%'")->fetch_assoc();
        
        if (empty($find)) {
            $conn->query("INSERT INTO `farmers` (`farmer_name`, `farmer_address`, `user_id`, `contact_no`, `email`) VALUES ('$fname', '$faddress', '$user', '$contact_no', '$email');");

            if ($conn->affected_rows > 0) {
                array_push($error, array("msg" => "Farmer's Successfully added", "type" => "success"));
            } else {
                array_push($error, array("msg" => "Failed to add farmers try again.", "type" => "warning"));
            }
            unset($_POST);
        } else {
            array_push($error, array("msg" => "Farmer name already Exists...", "type" => "warning"));
        }
        
    } elseif (isset($_POST['save']) && $_POST['save'] == "edit") {
        $id = $_POST["farmer_id"];
        $fname = $_POST['farmer_name'];
        $faddress = $_POST['farmer_address'];
        $contact_no = $_POST['contact_no'];
        $email = $_POST['email'];
        $conn->query("UPDATE farmers SET farmer_name='$fname', farmer_address='$faddress', contact_no='$contact_no', email='$email' WHERE farmer_id='$id'");
        if ($conn->affected_rows > 0) {
            array_push($error_two, array("msg" => "Farmer's Successfully edited", "type" => "success"));
        } else {
            array_push($error_two, array("msg" => "Failed to edit farmers try again.", "type" => "warning"));
        }
    } elseif (isset($_POST['save']) && $_POST['save'] == "remove") {

        $id = $_POST["farmer_id"];
        $conn->query("DELETE FROM farmers WHERE farmer_id='$id'");
        if ($conn->affected_rows > 0) {
            array_push($error_two, array("msg" => "Farmer's Successfully remove", "type" => "success"));
        } else {
            array_push($error_two, array("msg" => "Failed to remove farmers try again.", "type" => "warning"));
        }
    } 
    
    if (isset($_POST['search'])) {
        $search = $_POST['search'];
        $result = $conn->query("
            SELECT 
                f.*,
                GROUP_CONCAT(DISTINCT l.land_id) as land_ids,
                GROUP_CONCAT(l.land_name) as land_names,
                GROUP_CONCAT(lt.land_tag_coord) as coordinates
            FROM farmers f
            LEFT JOIN lands l ON f.farmer_id = l.farmer_id
            LEFT JOIN land_tags lt ON l.land_id = lt.land_id
            WHERE CONCAT(f.farmer_name, f.farmer_address) LIKE '%$search%'
            GROUP BY f.farmer_id
        ");
    } else {
        $result = $conn->query("
            SELECT 
                f.*,
                GROUP_CONCAT(DISTINCT l.land_id) as land_ids,
                GROUP_CONCAT(l.land_name) as land_names,
                GROUP_CONCAT(lt.land_tag_coord) as coordinates
            FROM farmers f
            LEFT JOIN lands l ON f.farmer_id = l.farmer_id
            LEFT JOIN land_tags lt ON l.land_id = lt.land_id
            GROUP BY f.farmer_id
        ");
    }

} else {
    $result = $conn->query("
        SELECT 
            f.*,
            GROUP_CONCAT(DISTINCT l.land_id) as land_ids,
            GROUP_CONCAT(l.land_name) as land_names,
            GROUP_CONCAT(lt.land_tag_coord) as coordinates
        FROM farmers f
        LEFT JOIN lands l ON f.farmer_id = l.farmer_id
        LEFT JOIN land_tags lt ON l.land_id = lt.land_id
        GROUP BY f.farmer_id
    ");
}



?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container mt-2">
    <div class="row my-2">
        <div class="col-md-5 ">
            <form action="" class="" method="post">
                <div class="form-group ">
                    <input type="text" name="search" value="<?php echo $_POST['search'] ?? '' ?>" class="form-control px-2" placeholder="Search for Farmers...">
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
                Add New Farmer</button>
        </div>
    </div>
    <div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Farmer List</span>
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="highlight bordered responsive-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Farmer Name</th>
                                <th>Address</th>
                                <th>Contact No.</th>
                                <th>Email</th>
                                <th>Coordinates</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($result)): ?>
                                <?php foreach ($result as $row => $row_data): ?>
                                    <tr>
                                        <td><?php echo $row + 1; ?></td>
                                        <td><?php echo $row_data['farmer_name']; ?></td>
                                        <td><?php echo $row_data['farmer_address']; ?></td>
                                        <td><?php echo $row_data['contact_no']; ?></td>
                                        <td><?php echo $row_data['email']; ?></td>
                                        <td>
                                            <?php if (!empty($row_data['coordinates'])): ?>
                                                <button 
                                                    onclick="showCoordinates(
                                                        '<?php echo htmlspecialchars(json_encode($row_data['land_names'])); ?>', 
                                                        '<?php echo htmlspecialchars(json_encode($row_data['coordinates'])); ?>', 
                                                        '<?php echo htmlspecialchars(json_encode($row_data['land_ids'])); ?>'
                                                    )" 
                                                    class="btn-small">
                                                    <i class="material-icons left">location_on</i>View Coordinates
                                                </button>
                                            <?php else: ?>
                                                <span class="grey-text">
                                                    <i class="material-icons tiny">info</i> No coordinates
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a 
                                                onclick="edit(
                                                    `<?php echo $row_data['farmer_id'] ?>`, 
                                                    `<?php echo $row_data['farmer_name'] ?>`, 
                                                    `<?php echo $row_data['farmer_address'] ?>`,
                                                    `<?php echo $row_data['contact_no'] ?>`,
                                                    `<?php echo $row_data['email'] ?>`
                                                )"
                                                class="btn-flat teal-text text-darken-2">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <a 
                                                onclick="remove(
                                                    `<?php echo $row_data['farmer_id'] ?>`, 
                                                    `<?php echo $row_data['farmer_name'] ?>`, 
                                                    `<?php echo $row_data['farmer_address'] ?>`,
                                                    `<?php echo $row_data['contact_no'] ?>`,
                                                    `<?php echo $row_data['email'] ?>`
                                                )"
                                                class="btn-flat red-text text-darken-2">
                                                <i class="material-icons">delete</i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="center-align">No data found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Materialize Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the table if needed
    var elems = document.querySelectorAll('.pagination');
    var instances = M.Pagination.init(elems);
});
</script>

<!-- Modal -->
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
                    <input type="hidden" name="farmer_id">
                    <div class="form-group">
                        <label for="">Full name</label>
                        <input type="text" class="form-control" name="farmer_name" value="<?php echo $_POST['farmer_name'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="">Address</label>
                        <input type="text" class="form-control" name="farmer_address" value="<?php echo $_POST['farmer_address'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="">Contact No.</label>
                        <input type="text" class="form-control" name="contact_no" value="<?php echo $_POST['contact_no'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $_POST['email'] ?? '' ?>">
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

<!-- Coordinates Modal -->
<div id="coordinatesModal" class="modal">
    <div class="modal-content">
        <h4>Land Coordinates</h4>
        <div class="divider"></div>
        <div class="coordinates-table-container">
            <table class="striped highlight">
                <thead>
                    <tr>
                        <th>Land Name</th>
                        <th>Coordinates</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="coordinatesTableBody">
                    <!-- Data will be inserted here dynamically -->
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close btn-flat">Close</a>
    </div>
</div>

<script>
// Initialize modal
document.addEventListener('DOMContentLoaded', function() {
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);
});

function showCoordinates(landNames, coordinates, landIds) {
    // Parse the JSON strings
    const names = JSON.parse(landNames);
    const coords = JSON.parse(coordinates);
    const ids = JSON.parse(landIds);
    
    // Get the table body
    const tableBody = document.getElementById('coordinatesTableBody');
    tableBody.innerHTML = '';
    
    // Create table rows
    names.split(',').forEach((name, index) => {
        const tr = document.createElement('tr');
        
        // Land name cell
        const nameCell = document.createElement('td');
        nameCell.innerHTML = `<i class="material-icons tiny">agriculture</i> ${name}`;
        
        // Coordinates cell
        const coordCell = document.createElement('td');
        const coord = coords.split(',')[index];
        coordCell.innerHTML = `<span class="coord-text"><i class="material-icons tiny">location_on</i> ${coord}</span>`;
        
        // Action cell
        const actionCell = document.createElement('td');
        actionCell.innerHTML = `
            <a href="tagging.php?land_id=${ids.split(',')[index]}" 
               target="_blank" 
               class="btn-small">
                <i class="material-icons left">map</i>View Map
            </a>`;
        
        // Append cells to row
        tr.appendChild(nameCell);
        tr.appendChild(coordCell);
        tr.appendChild(actionCell);
        
        // Append row to table body
        tableBody.appendChild(tr);
    });
    
    // Open the modal
    var modal = M.Modal.getInstance(document.getElementById('coordinatesModal'));
    modal.open();
}
</script>

<style>
.btn-small {
    background-color: #26a69a;
    color: white;
    border: none;
    padding: 0 12px;
    height: 32px;
    line-height: 32px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-small:hover {
    background-color: #2bbbad;
}

.btn-small i {
    vertical-align: middle;
    margin-right: 4px;
}

.btn-flat {
    background: transparent;
    color: #26a69a;
    border: none;
    padding: 0 12px;
    cursor: pointer;
}

.btn-flat:hover {
    background: rgba(38, 166, 154, 0.1);
}

.coordinates-table-container {
    margin-top: 20px;
    max-height: 400px;
    overflow-y: auto;
}

.coord-text {
    display: flex;
    align-items: center;
    gap: 4px;
}

.coord-text .material-icons.tiny {
    font-size: 16px;
}

#coordinatesModal {
    width: 80%;
    max-height: 90%;
    border-radius: 8px;
}

#coordinatesModal .modal-content {
    padding: 24px;
}

#coordinatesModal h4 {
    margin: 0;
    margin-bottom: 16px;
    color: #26a69a;
}

#coordinatesModal table {
    margin-top: 16px;
}

#coordinatesModal .btn-small {
    margin: 4px;
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
    function save() {
        $("input[name=save]").val("save");
        $("input[name=farmer_id]").val();
        $("#exampleModalLabel").html("Add new Farmer");
    }

    function remove(id, name, address, contact, email) {
        $("input[name=save]").val("remove");
        $("input[name=farmer_id]").val(id);
        $("input[name=farmer_name]").val(name).prop("readonly", true);
        $("input[name=contact_no]").val(contact).prop("readonly", true);
        $("input[name=email]").val(email).prop("readonly", true);
        $("input[name=farmer_address]").val(address).prop("readonly", true);
        $("#exampleModalLabel").html("Confirm Remove farmer");
        $(`#exampleModal`).modal(`show`);
    }

    function edit(id, name, address, contact, email) {
        $("input[name=save]").val("edit");
        $("input[name=farmer_id]").val(id);
        $("input[name=farmer_name]").val(name).prop("readonly", false);
        $("input[name=farmer_address]").val(address).prop("readonly", false);
        
        $("input[name=contact_no]").val(contact).prop("readonly", false);
        $("input[name=email]").val(email).prop("readonly", false);
        $("#exampleModalLabel").html("Edit Information");
        $(`#exampleModal`).modal(`show`);
    }
    function remove(id, name, address, contact, email) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $("input[name=save]").val("remove");
            $("input[name=farmer_id]").val(id);
            $("input[name=farmer_name]").val(name).prop("readonly", true);
            $("input[name=contact_no]").val(contact).prop("readonly", true);
            $("input[name=email]").val(email).prop("readonly", true);
            $("input[name=farmer_address]").val(address).prop("readonly", true);
            $("#exampleModalLabel").html("Confirm Remove farmer");
            $(`#exampleModal`).modal(`show`);
        }
    });
}
</script>