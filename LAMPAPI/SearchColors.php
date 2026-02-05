<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;

	$connection = createConnection();
	if ($connection->connect_error)
	{
		returnWithError( $connection->connect_error );
	} 
	else
	{
		$stmt = $connection->prepare("select Name from Colors where Name like ? and UserID=?");
		$colorName = "%" . $inData["search"] . "%";
		$stmt->bind_param("ss", $colorName, $inData["userId"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '"' . $row["Name"] . '"';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
		}
		
		$stmt->close();
		$connection->close();
	}
	
	function returnWithError($error )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $error . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>