<?php
    require_once('IO.php');

    $inData = getRequestInfo();

    $first = $inData["firstName"];
    $last = $inData["lastName"];
    $number = $inData["phoneNumber"];
    $email = $inData["email"];
    $note = $inData["notes"];
    $token = $inData["token"];

    if (strlen($first) == 0) {
        returnWithError("First name is required");
    }

    $connection = createConnection();
    if ($connection->connect_error)
    {
        returnWithError( $connection->connect_error );
    }
    else
    {
        $userId = getUserIdFromToken($connection, $token);
        if (!$userId) {
            returnWithError( "Your session has expired, please log in again." );
        }

        $statement = $connection->prepare("INSERT into Contacts (FirstName, LastName, Phone, Email, Notes, UserID) VALUES(?, ?, ?, ?, ?, ?)");
        $statement->bind_param("ssssss", $first, $last, $number, $email, $note, $userId);
        $statement->execute();
        $statement->close();
        $connection->close();
        returnSuccess();
    }

    function returnWithError( $error )
    {
        $returnValue = '{"error":"' . $error . '"}';
        sendResultInfoAsJson( $returnValue );
        exit();
    }

    function returnSuccess() {
        $returnValue = '{"error": ""}';
        sendResultInfoAsJson($returnValue);
        exit();
    }

?>