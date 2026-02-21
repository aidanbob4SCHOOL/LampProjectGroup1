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
    $verifyUserExists = $connection->prepare("SELECT ID, TokenCreated FROM Users WHERE Token = ?");
    $verifyUserExists->bind_param("s", $token);
    $verifyUserExists->execute();
    $result = $verifyUserExists->get_result();
    $userIdRow = $result->fetch_assoc();

    if ($userIdRow == null) {
        return false;
    }

    if ($userIdRow["TokenCreated"] == null) {
        return false;
    }

    try {
        $tokenCreated = new DateTime($userIdRow["TokenCreated"]);
        $now = new DateTime();
        $deltaSeconds = $now->getTimestamp() - $tokenCreated->getTimestamp();
        $twentyMinutesInSeconds = 60 * 20;

        if ($deltaSeconds > $twentyMinutesInSeconds) {
            return false;
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    $userId = $userIdRow["ID"];
    $verifyUserExists->close();

    return $userId;
}

?>