<?php
    require_once('IO.php');

    $inData = getRequestInfo();

    $first = $inData["firstName"];
    $last = $inData["lastName"];
    $number = $inData["phoneNumber"];
    $email = $inData["email"];
    $userId = $inData["userId"];

    if (strlen($first) == 0 && strlen($last) == 0 && strlen($number) == 0 && strlen($email) == 0) {
        returnWithError("At least one field is required");
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
            returnWithError( "Attempting to add contact for user that does not exist." );
        }
        $verifyUserExists->close();

        $statement = $connection->prepare("INSERT into Contacts (FirstName, LastName, Phone, Email, UserID) VALUES(?, ?, ?, ?, ?)");
        $statement->bind_param("sssss", $first, $last, $number, $email, $userId);
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