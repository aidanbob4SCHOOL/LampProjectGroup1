<?php
    require_once('IO.php');

    $inData = getRequestInfo();

    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $login = $inData["login"];
    $password = $inData["password"];

    // Only allow messages that contain alphanumeric characters or hyphen and underscore
    $constraintsRegex = "/^[a-zA-Z0-9-_]+$/";

    $firstNameLen = strlen($firstName);
    if ($firstNameLen < 2 || $firstNameLen > 30) {
        returnWithError("First name must be between 2 and 30 characters.");
    }
    if (!preg_match($constraintsRegex, $firstName)) {
        returnWithError("First name contains invalid characters (only letters, numbers, hyphens, and underscores are allowed).");
    }

    $lastNameLen = strlen($lastName);
    if ($lastNameLen < 2 || $lastNameLen > 30) {
        returnWithError("Last name must be between 2 and 30 characters.");
    }
    if (!preg_match($constraintsRegex, $lastName)) {
        returnWithError("Last name contains invalid characters (only letters, numbers, hyphens, and underscores are allowed).");
    }

    $loginLen = strlen($login);
    if ($loginLen < 2 || $loginLen > 30) {
        returnWithError("Login must be between 2 and 30 characters.");
    }
    if (!preg_match($constraintsRegex, $login)) {
        returnWithError("Login contains invalid characters (only letters, numbers, hyphens, and underscores are allowed).");
    }

    // Placeholder until we decide on the actual password requirements
    if (strlen($password) < 8) {
        returnWithError("Password must be at least 8 characters.");
    }


    //Hash the password before it touches the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $connection = createConnection();
    if ($connection->connect_error) {
        returnWithError($connection->connect_error);
    } else {
        //Insert the user with the hashed password
        $statement = $connection->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
        $statement->bind_param("ssss", $firstName, $lastName, $login, $hashedPassword);
        
        if ($statement->execute()) {
            returnSuccess();
        } else {
            returnWithError($statement->error);
        }
        
        $statement->close();
        $connection->close();
    }

    function returnWithError($error ) {
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
