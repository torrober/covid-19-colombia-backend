<?php
error_reporting(0);
include('connection.php');
include('headers.php');
$deptID = $_GET['deptID'];
$content = array();
$i = 0;
function getDBError($db_error) {
    $response_fail_db = array("response" => "failed", "error" => "Error connecting with the database: ".$db_error, "content" => []);
    return json_encode($response_fail_db, JSON_PRETTY_PRINT);
}
//Query Casos Reportados
if (intval($deptID)) {
    $squery = "
    SET @deptID = ".$deptID.";
    create temporary table Info(
    casos2020 int, casos2021 int, casos2022 int, muertos int, recuperados int, CasosRegistrados int, CasosMasculinos int, CasosFemeninos int
    );
    SET
    @casos2022 = (Select count(*) from cases where codigo_dept = @deptID AND fecha_registro LIKE '%2022%'), 
    @casos2021 = (Select count(*) from cases where codigo_dept = @deptID AND fecha_registro LIKE '%2021%'),
    @casos2020 = (Select count(*) from cases where codigo_dept = @deptID AND fecha_registro LIKE '%2020%'),
    @muertos = (Select count(*) from cases where codigo_dept = @deptID AND recuperado LIKE '%Fallecido%'), 
    @recuperados = (Select count(*) from cases where codigo_dept = @deptID AND recuperado LIKE '%Recuperado%'),
    @CasosRegistrados = (Select count(*) from cases where codigo_dept = @deptID),
    @CasosMasculinos = (Select count(*) from cases where codigo_dept = @deptID AND sexo = 'M'), 
    @CasosFemeninos = (Select count(*) from cases where codigo_dept = @deptID AND  sexo = 'F');

    insert into Info value(@casos2020,@casos2021,@casos2022,@muertos,@recuperados,@CasosRegistrados,@CasosMasculinos,@CasosFemeninos);

    Select * from Info
    ";
    //$squery = "SELECT * FROM `divipola_codes_town` WHERE `codigo_dept` = " . $deptID;
    $query = mysqli_query($con,$squery) or die(getDBError(mysqli_error($con)));
    while ($row = mysqli_fetch_array($query)) {
        array_push($content, array(
            "casos2020" => $row['casos2020'],
            "casos2021" => $row['casos2021'],
            "casos2022" => $row['casos2022'],
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
