<?php
    require_once("IO.php");

    $inData = getRequestInfo(); // Read JSON request body and decode to associative array.
    
    $searchResults = ""; // Initialize string that will hold comma-separated JSON result entries.
    $searchCount = 0; // Counter for how many results found.

    $conn = createConnection();
    if ($conn->connect_error) 
    {
        returnWithError( $conn->connect_error ); // If connection failed, return JSON error and stop.
    } 
    else
    {
        $userId = getUserIdFromToken($conn, $inData["token"]);
        if (!$userId) {
            returnWithError( "Your session has expired, please log in again." );
        }

        $stmt = $conn->prepare("select ID, FirstName, LastName, Phone, Email, Notes from Contacts where (FirstName like ? or LastName like ? or Email like ? or Phone like ? or Notes like ? or CONCAT(FirstName, ' ', LastName) like ?) and UserID=?"); // Prepare parameterized SQL to avoid injection.
        $searchParam = "%" . $inData["search"] . "%"; // Build search pattern with wildcards for LIKE.
        $stmt->bind_param("sssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $inData["userId"]); // Bind string parameters: search pattern and userId.
        $stmt->execute(); // Execute the prepared statement.
        
        $result = $stmt->get_result(); // Get result set from executed statement.
        
        while($row = $result->fetch_assoc())
        {
            if( $searchCount > 0 )
            {
                $searchResults .= ","; // Add comma between entries after the first.
            }
            $searchCount++; // Increment results counter.
            $searchResults .= '{"id":' . json_encode($row["ID"]) . ',"firstName":' . json_encode($row["FirstName"]) . ',"lastName":' . json_encode($row["LastName"]) . ',"phone":' . json_encode($row["Phone"]) . ',"email":' . json_encode($row["Email"]) . ',"notes":' . json_encode($row["Notes"]) . '}'; // Append the contact fields as a quoted JSON string.
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
    
    function returnWithError( $err )
    {
        $retValue = '{"results":[],"error":"' . $err . '"}'; // Build an error JSON payload (fields present for client compatibility).
        sendResultInfoAsJson( $retValue ); // Send the JSON response.
        exit();
    }
    
    function returnWithInfo( $searchResults )
    {
        $retValue = '{"results":[' . $searchResults . '],"error":""}'; // Build success JSON with results array.
        sendResultInfoAsJson( $retValue ); // Send the JSON response.
    }
    
?>