
<?php

	$inData = getRequestInfo();
	
	$id = 0;
	$firstName = "";
	$lastName = "";

	$connection = createConnection();
	if( $connection->connect_error )
	{
		returnWithError( $connection->connect_error );
	}
	else
	{
		$statement = $connection->prepare("SELECT ID,firstName,lastName FROM Users WHERE Login=? AND Password =?");
		$statement->bind_param("ss", $inData["login"], $inData["password"]);
		$statement->execute();
		$result = $statement->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			returnWithInfo( $row['firstName'], $row['lastName'], $row['ID'] );
		}
		else
		{
			returnWithError("No Records Found");
		}

		$statement->close();
		$connection->close();
	}

	function returnWithError($error )
	{
		$returnValue = '{"id":0,"firstName":"","lastName":"","error":"' . $error . '"}';
		sendResultInfoAsJson( $returnValue );
	}
	
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$returnValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $returnValue );
	}
	
?>
