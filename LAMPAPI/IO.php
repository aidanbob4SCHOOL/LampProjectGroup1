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

function getUserIdFromToken($connection, $token) {
    $verifyUserExists = $connection->prepare("SELECT ID FROM Users WHERE Token = ?");
    $verifyUserExists->bind_param("s", $token);
    $verifyUserExists->execute();
    $result = $verifyUserExists->get_result();
    $userIdRow = $result->fetch_assoc();

    if ($userIdRow == null) {
        return false;
    }

    $userId = $userIdRow["ID"];
    $verifyUserExists->close();

    return $userId;
}

?>