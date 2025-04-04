<?php
/**
 * Database connection and query functions
 */

/**
 * Get database connection
 * 
 * @return mysqli Database connection
 */
function getDbConnection() {
    // Database configuration
    $db_host = 'localhost';
    $db_user = 'root';         // Change to your database username
    $db_pass = 'root';             // Change to your database password
    $db_name = 'virtual_kitchen';    // Change to your database name
    
    // Create connection
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

/**
 * Execute a query and return all results
 * 
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @param string $types Types of parameters (i: integer, d: double, s: string, b: blob)
 * @return array Query results
 */
function executeQuery($sql, $params = [], $types = '') {
    $conn = getDbConnection();
    $results = [];
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameters if any
    if (!empty($params)) {
        // If types not provided, assume all strings
        if (empty($types)) {
            $types = str_repeat('s', count($params));
        }
        
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute statement
    $stmt->execute();
    
    // Get results
    $result = $stmt->get_result();
    
    // Fetch all results
    if ($result) {
        $results = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    return $results;
}

/**
 * Get a single record from database
 * 
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @param string $types Types of parameters
 * @return array|null Single record or null if not found
 */
function getRecord($sql, $params = [], $types = '') {
    $results = executeQuery($sql, $params, $types);
    
    return !empty($results) ? $results[0] : null;
}

/**
 * Insert a record into database
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|bool Inserted ID or false on failure
 */
function insertRecord($table, $data) {
    $conn = getDbConnection();
    
    // Build query
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $types = '';
    $values = [];
    
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } elseif (is_string($value)) {
            $types .= 's';
        } else {
            $types .= 'b';
        }
        
        $values[] = $value;
    }
    
    $stmt->bind_param($types, ...$values);
    
    // Execute statement
    $result = $stmt->execute();
    
    // Get inserted ID
    $inserted_id = $result ? $conn->insert_id : false;
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    return $inserted_id;
}

/**
 * Update a record in database
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @param string $where Where clause
 * @param array $params Parameters for where clause
 * @param string $types Types of parameters for where clause
 * @return bool True on success, false on failure
 */
function updateRecord($table, $data, $where, $params = [], $types = '') {
    $conn = getDbConnection();
    
    // Build query
    $set = [];
    foreach ($data as $column => $value) {
        $set[] = "$column = ?";
    }
    
    $set_clause = implode(', ', $set);
    
    $sql = "UPDATE $table SET $set_clause WHERE $where";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $all_params = [];
    $all_types = '';
    
    // Add data parameters
    foreach ($data as $value) {
        if (is_int($value)) {
            $all_types .= 'i';
        } elseif (is_float($value)) {
            $all_types .= 'd';
        } elseif (is_string($value)) {
            $all_types .= 's';
        } else {
            $all_types .= 'b';
        }
        
        $all_params[] = $value;
    }
    
    // Add where parameters
    $all_types .= $types;
    $all_params = array_merge($all_params, $params);
    
    $stmt->bind_param($all_types, ...$all_params);
    
    // Execute statement
    $result = $stmt->execute();
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    return $result;
}

/**
 * Delete a record from database
 * 
 * @param string $table Table name
 * @param string $where Where clause
 * @param array $params Parameters for where clause
 * @param string $types Types of parameters
 * @return bool True on success, false on failure
 */
function deleteRecord($table, $where, $params = [], $types = '') {
    $conn = getDbConnection();
    
    // Build query
    $sql = "DELETE FROM $table WHERE $where";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameters if any
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute statement
    $result = $stmt->execute();
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    return $result;
}
?>