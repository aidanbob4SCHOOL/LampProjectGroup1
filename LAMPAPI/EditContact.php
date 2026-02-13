// edit contacts

<?php
    $inData = getRequestInfo();

	$userId = $inData["userId"]; # REFERS TO USER IN USER TABLE
	$contactID = $inData["contactId"]; # REFERS TO AUTO INCREMENTING ID

	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$phone = $inData["phone"];

	// check for userID and contactID, if missing return with error
	//if(empty($userId) || empty($contactID)) {
	//	returnWithError("User ID and Contact ID are required.");
	//}

	// ensure user must provide all fields to update contact
	if(empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
		returnWithError("All fields are required.");
	}

	// establish database connection
    $conn = new mysqli("localhost", "apitest_user", "apitest_1234", "api_test_db");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// update contacts database, set new values where userId and contactId matches
		$stmt = $conn->prepare("UPDATE Contacts SET firstName=?, lastName=?, email=?, phone=? WHERE UserId=? AND ID=?");
		$stmt->bind_param("ssssii", $firstName, $lastName, $email, $phone, $userId, $contactID);


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

    function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>