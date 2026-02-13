<?php

function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson( $obj )
{
    header('Content-type: application/json');
    echo $obj;
}

function createConnection() {
    return new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
}

?>