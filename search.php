<?php
    $inData = getRequestInfo(); // Read JSON request body and decode to associative array.
    
    $searchResults = ""; // Initialize string that will hold comma-separated JSON result entries.
    $searchCount = 0; // Counter for how many results found.

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); // Create MySQLi connection (host, user, pass, db).
    if ($conn->connect_error) 
    {
        returnWithError( $conn->connect_error ); // If connection failed, return JSON error and stop.
    } 
    else
    {
        $stmt = $conn->prepare("select FirstName, LastName, Phone, Email from Contacts where (FirstName like ? or LastName like ? or Email like ? or Phone like ?) and UserID=?"); // Prepare parameterized SQL to avoid injection.
        $searchParam = "%" . $inData["search"] . "%"; // Build search pattern with wildcards for LIKE.
        $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam, $inData["userId"]); // Bind four string parameters: search pattern and userId.
        $stmt->execute(); // Execute the prepared statement.
        
        $result = $stmt->get_result(); // Get result set from executed statement.
        
        while($row = $result->fetch_assoc())
        {
            if( $searchCount > 0 )
            {
                $searchResults .= ","; // Add comma between entries after the first.
            }
            $searchCount++; // Increment results counter.
            $searchResults .= '{"firstName":"' . $row["FirstName"] . '","lastName":"' . $row["LastName"] . '","phone":"' . $row["Phone"] . '","email":"' . $row["Email"] . '"}'; // Append the contact fields as a quoted JSON string.
        }
        
        if( $searchCount == 0 )
        {
            returnWithError( "No Records Found" ); // If no rows, return JSON error.
        }
        else
        {
            returnWithInfo( $searchResults ); // Otherwise return JSON with results array.
        }
        
        $stmt->close(); // Close statement.
        $conn->close(); // Close DB connection.
    }

    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true); // Read raw request body and decode JSON to assoc array.
    }

    function sendResultInfoAsJson( $obj )
    {
        header('Content-type: application/json'); // Set response content type to JSON.
        echo $obj; // Output the JSON string.
    }
    
    function returnWithError( $err )
    {
        $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}'; // Build an error JSON payload (fields present for client compatibility).
        sendResultInfoAsJson( $retValue ); // Send the JSON response.
    }
    
    function returnWithInfo( $searchResults )
    {
        $retValue = '{"results":[' . $searchResults . '],"error":""}'; // Build success JSON with results array.
        sendResultInfoAsJson( $retValue ); // Send the JSON response.
    }
    
?>