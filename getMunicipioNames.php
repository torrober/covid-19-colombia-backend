<?php
error_reporting(0);
include('connection.php');
include('headers.php');
$deptID = $_GET['deptID'];
$content = array();
$response = array("response" => "ok");
$error = array("error" => "null");
$i = 0;
$response_fail_db = array("response" => "failed", "error" => "Error connecting with the database", "content" => $content);
if (intval($deptID)) {
    $query = mysqli_query($con, "SELECT * FROM `divipola_codes_town` WHERE `codigo_dept` = " . $deptID) or die(json_encode($response_fail_db, JSON_PRETTY_PRINT));
    while ($row = mysqli_fetch_array($query)) {
        array_push($content, array(
            "munName" => $row['nombre_municipio'],
            "munID" => $row['codigo_municipio']
        ));
        $i = $i+1;
    }
    if($i>0) {
        $response = array("response" => "ok", "error" => "null", "content" => $content);
    } else {
        $response = array("response" => "failed", "error" => "Non-existent department", "content" => $content);
    }
} else {
    $response = array("response" => "failed", "error" => "Invalid department Code", "content" => $content);
}
echo json_encode($response, JSON_PRETTY_PRINT);
