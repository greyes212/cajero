<?php
header("Content-type:application/json");
require_once "cajeroController.php";
$con =new CajeroController();
$id=$_GET["id"];
$cantidad=$_GET["cantidad"];
$resultado=$con->retirarEfectivo($id,$cantidad);
echo json_encode($resultado);