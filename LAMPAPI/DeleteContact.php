<?php
    require_once("IO.php");

    $inData = getRequestInfo();

	$token = $inData["token"];
	$contactID = $inData["contactId"];

	// check for userID and contactID, if missing return with error
	// add additional check for name, phone number , email ?
	// if(empty($userId) || empty($contactID)) {
	//	returnWithError("No Contact to delete.");
	//}

	// establish database connection
    $conn = createConnection();
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        $userId = getUserIdFromToken($conn, $token);
        if (!$userId) {
            returnWithError( "Your session has expired, please log in again." );
        }

		// deletes contact from database, set new values where userId and contactId matches
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE UserId=? AND ID=?");
		$stmt->bind_param("ii", $userId, $contactID);


		// if statement execution fails, return with error
		if(!$stmt->execute()) {
			$err = $stmt->error;
			$stmt->close();
			$conn->close();
			returnWithError($err);
			exit();
		}

		// if no rows were affected, return with error
		if ($stmt->affected_rows <= 0) {
			returnWithError("Contact not found. No changes made.");
			exit();
		}
			
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
        exit();
	}
?>