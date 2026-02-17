<?php
    require_once('IO.php');

    $inData = getRequestInfo();

    $first = $inData["firstName"];
    $last = $inData["lastName"];
    $number = $inData["phoneNumber"];
    $email = $inData["email"];
    $note = $inData["notes"];
    $userId = $inData["userId"];

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
        $verifyUserExists = $connection->prepare("SELECT ID FROM Users WHERE ID = ?");
        $verifyUserExists->bind_param("i", $userId);
        $verifyUserExists->execute();
        $verifyUserExists->store_result();
        if ($verifyUserExists->num_rows < 1) {
            returnWithError( "You are attempting to add a contact for a user that does not exist." );
        }
        $verifyUserExists->close();

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