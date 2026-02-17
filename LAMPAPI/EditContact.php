<?php
    require_once("IO.php");

    $inData = getRequestInfo();

	$userId = $inData["userId"]; # REFERS TO USER IN USER TABLE
	$contactID = $inData["contactId"]; # REFERS TO AUTO INCREMENTING ID

	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$phone = $inData["phone"];
    $notes = $inData["notes"];

	// check for userID and contactID, if missing return with error
	if(empty($userId) || empty($contactID)) {
		returnWithError("User ID and Contact ID are required.");
	}

	// ensure user must provide all required fields to update contact
	if(empty($firstName)) {
		returnWithError("First name is required.");
	}

	// establish database connection
    $conn = createConnection();
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// update contacts database, set new values where userId and contactId matches
		$stmt = $conn->prepare("UPDATE Contacts SET FirstName=?, LastName=?, Email=?, Phone=?, Notes=? WHERE UserId=? AND ID=?");
		$stmt->bind_param("sssssii", $firstName, $lastName, $email, $phone, $notes, $userId, $contactID);


		// if statement execution fails, return with error
		if(!$stmt->execute()) {
			$err = $stmt->error;
			$stmt->close();
			$conn->close();
			returnWithError($err);
			exit();
		}

		// if no rows were affected, return with error
		if($stmt->affected_rows <= 0) {
			$stmt->close();
			$conn->close();
			returnWithError("Contact not found. No changes made.");
			exit();
		}

		$stmt->close();
		$conn->close();
		returnWithError("");
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

    function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>