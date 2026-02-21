
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
				$now = date('Y-m-d H:i:s');
				$token = uniqid("", true);
				$tokenWriter = $connection->prepare("UPDATE Users SET Token=?,TokenCreated=? WHERE ID=?");
				$tokenWriter->bind_param("sss", $token, $now, $row['ID']);
				$tokenWriter->execute();
				$tokenWriter->close();

                returnWithInfo($row['firstName'], $row['lastName'], $row['ID'], $token);
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
		$returnValue = '{"id":0,"firstName":"","lastName":"","token":"","error":"' . $error . '"}';
		sendResultInfoAsJson( $returnValue );
		exit();
	}
	
	function returnWithInfo( $firstName, $lastName, $id, $token)
	{
		$returnValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","token":"' . $token . '","error":""}';
		sendResultInfoAsJson( $returnValue );
	}
	
?>
