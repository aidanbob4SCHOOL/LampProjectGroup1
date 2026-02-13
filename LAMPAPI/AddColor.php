<?php
	$inData = getRequestInfo();
	
	$color = $inData["color"];
	$userId = $inData["userId"];

	$connection = createConnection();
	if ($connection->connect_error)
	{
		returnWithError( $connection->connect_error );
	} 
	else
	{
		$statement = $connection->prepare("INSERT into Colors (UserId,Name) VALUES(?,?)");
		$statement->bind_param("ss", $userId, $color);
		$statement->execute();
		$statement->close();
		$connection->close();
		returnWithError("");
	}

	function returnWithError( $error )
	{
		$returnValue = '{"error":"' . $error . '"}';
		sendResultInfoAsJson( $returnValue );
	}
	
?>