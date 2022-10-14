<?php
error_reporting(0);
include('connection.php');
include('headers.php');
$deptID = $_GET['deptID'];
$content = array();
$response = array("response" => "ok");
$error = array("error" => "null");
$i = 0;
function getDBError($db_error) {
    $response_fail_db = array("response" => "failed", "error" => "Error with the database: ".$db_error, "content" => []);
    return json_encode($response_fail_db, JSON_PRETTY_PRINT);
}
if (intval($deptID)) {
    $squery = sprintf("SELECT * FROM `divipola_codes_town` WHERE `codigo_dept` = '%s'",
    mysqli_real_escape_string($con, $deptID));
    $query = mysqli_query($con,$squery) or die(getDBError(mysqli_connect_error()));
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
