
<?php

	require_once('IO.php');

	$inData = getRequestInfo();
	
	$connection = createConnection();
	if( $connection->connect_error )
	{
		returnWithError( $connection->connect_error );
	}
	else
	{
		$statement = $connection->prepare("SELECT ID,firstName,lastName, Password FROM Users WHERE Login=?");
		$statement->bind_param("s", $inData["login"]);
		$statement->execute();
		$result = $statement->get_result();

		if( $row = $result->fetch_assoc()  )
		{
			if (password_verify($inData["password"], $row['Password'])) 
			{
                returnWithInfo($row['firstName'], $row['lastName'], $row['ID']);
            } 
			else 
			{
                returnWithError("Invalid Password");
            }		
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
