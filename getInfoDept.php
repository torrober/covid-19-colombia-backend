<?php
error_reporting(0);
include('connection.php');
include('headers.php');
$deptID = $_GET['deptID'];
$content = [];
$data = [];
$i = 0;
function getDBError($db_error)
{
    $response_fail_db = array("response" => "failed", "error" => "Error connecting with the database: " . $db_error, "content" => []);
    return json_encode($response_fail_db, JSON_PRETTY_PRINT);
}
//Query Casos Reportados
if (intval($deptID)) {
    $squery = "SET @deptID = " . $deptID . ";";
    $squery .= "create temporary table Info(casos2020 int, casos2021 int, casos2022 int, muertos int, recuperados int, CasosRegistrados int, CasosMasculinos int, CasosFemeninos int);";
    $squery .= "SET @casos2022 = (Select count(*) from cases where codigo_dept = @deptID AND fecha_registro LIKE '%2022%'), @casos2021 = (Select count(*) from cases where codigo_dept = @deptID AND fecha_registro LIKE '%2021%'),@casos2020 = (Select count(*) from cases where codigo_dept = @deptID AND fecha_registro LIKE '%2020%'),@muertos = (Select count(*) from cases where codigo_dept = @deptID AND recuperado LIKE '%Fallecido%'),@recuperados = (Select count(*) from cases where codigo_dept = @deptID AND recuperado LIKE '%Recuperado%'),@CasosRegistrados = (Select count(*) from cases where codigo_dept = @deptID),@CasosMasculinos = (Select count(*) from cases where codigo_dept = @deptID AND sexo = 'M'), @CasosFemeninos = (Select count(*) from cases where codigo_dept = @deptID AND  sexo = 'F');";
    $squery .= "insert into Info value(@casos2020,@casos2021,@casos2022,@muertos,@recuperados,@CasosRegistrados,@CasosMasculinos,@CasosFemeninos);";
    $squery .= "Select * from Info";
    //$squery = "SELECT * FROM `divipola_codes_town` WHERE `codigo_dept` = " . $deptID;
    mysqli_multi_query($con, $squery);
    do {
        /* store the result set in PHP */
        if ($result = mysqli_store_result($con)) {
            while ($row = mysqli_fetch_row($result)) {
               $data = $row;
            }
        }
        $i = $i+1;
    } while (mysqli_next_result($con));
    if($i>0) {
        array_push($content, array(
            "casos2020" => $data[0],
            "casos2021" => $data[1],
            "casos2022" => $data[2],
            "muertos" => $data[3],
            "recuperados" => $data[4],
            "CasosRegistrados" => $data[5],
            "CasosMasculinos" => $data[6],
            "CasosFemeninos" => $data[7]
        ));
        $response = array("response" => "ok", "error" => "null", "content" => $content);
    } else {
        $response = array("response" => "failed", "error" => "Non-existent department", "content" => $content);
    }
} else {
    $response = array("response" => "failed", "error" => "Invalid department Code", "content" => $content);
}
echo json_encode($response, JSON_PRETTY_PRINT);
