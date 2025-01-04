<?php

include("../inc/conn.php");

if (isset($_GET['mun'])) {
    $mun = $_GET['mun'];
    $res = $conn->query("SELECT * FROM tbl_barangays WHERE mun_id='$mun'");
    $res = $res->fetch_all(MYSQLI_ASSOC);
    echo  json_encode($res);
}

if (isset($_GET['bar'])) {
    $bar_id = $_GET['bar'];
    $res = $conn->query("SELECT * FROM tbl_farms WHERE bar_id='$bar_id'");
    $res = $res->fetch_all(MYSQLI_ASSOC);
    echo  json_encode($res);
}
