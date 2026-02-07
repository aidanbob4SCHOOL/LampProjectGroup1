<?php
    require_once('IO.php');

    $inData = getRequestInfo();

    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $login = $inData["login"];
    $password = $inData["password"];

    //Hash the password before it touches the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $conn = createConnection();
    if ($conn->connect_error) {
        returnWithError($conn->connect_error);
    } else {
        //Insert the user with the hashed password
        $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $login, $hashedPassword);
        
        if ($stmt->execute()) {
            returnWithError(""); //Empty string means success
        } else {
            returnWithError($stmt->error);
        }
        
        $stmt->close();
        $conn->close();
    }

    function returnWithError( $err ) {
        $returnValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $returnValue );
    }
?>
