<?php
    $inData = getRequestInfo();

	$userId = $inData["userId"];
	$contactID = $inData["contactId"];

	// check for userID and contactID, if missing return with error
	// add additional check for name, phone number , email ?
	// if(empty($userId) || empty($contactID)) {
	//	returnWithError("No Contact to delete.");
	//}

	// establish database connection
    $conn = new mysqli("localhost", "apitest_user", "apitest_1234", "api_test_db");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        // insert "do you want to delete this contact" confirmation here ?

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

    function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

    function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>