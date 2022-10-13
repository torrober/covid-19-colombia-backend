<?php
include('connection.php');
include('headers.php');
$deptID = $_GET['deptID'];
$content = array();
$response = array("response" => "ok");
$error = array("error" => "null");
$i = 0;
if (intval($deptID)) {
    $query = mysqli_query($con, "SELECT * FROM `divipola_codes_town` WHERE `codigo_dept` = " . $deptID) or die('[{response: "failed", content: [], error: "error connecting with the database"}]');
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
