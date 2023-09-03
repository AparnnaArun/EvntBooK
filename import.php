<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'booking';
$username = 'root';
$password = '';

try {
    // Connect to db
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read and process the JSON file
    $json_data = file_get_contents('C:/xampp/htdocs/booking/Code Challenge (Events).json');

    $data = json_decode($json_data, true);

   // Insert jason data to db

foreach ($data as $item) {
    if (isset($item['version'])) {
    $employee_name = $item['employee_name'];
    $employee_mail = $item['employee_mail'];
    $event_name = $item['event_name'];
    $booking_date = $item['event_date'];
    $booking_price = $item['participation_fee'];
    $participation_id = $item['participation_id'];
    $version = $item['version'];


    // Insert or update employee and event in the db
    $stmt = $pdo->prepare("
        INSERT INTO employees (employee_name,employee_mail)
        VALUES (:employee_name,:employee_mail)
        ON DUPLICATE KEY UPDATE employee_id = LAST_INSERT_ID(employee_id)
    ");
    $stmt->bindParam(':employee_name', $employee_name);
    $stmt->bindParam(':employee_mail', $employee_mail);
    $stmt->execute();

    $employee_id = $pdo->lastInsertId(); // Get the employee_id

    $stmt = $pdo->prepare("
        INSERT INTO events (event_name,event_date)
        VALUES (:event_name,:event_date)
        ON DUPLICATE KEY UPDATE event_id = LAST_INSERT_ID(event_id)
    ");
    $stmt->bindParam(':event_name', $event_name);
    $stmt->bindParam(':event_date', $booking_date);
    $stmt->execute();

    $event_id = $pdo->lastInsertId(); // Get the event_id

    // Insert booking
    $stmt = $pdo->prepare("
        INSERT INTO bookings (employee_id, event_id, booking_date, booking_price,participation_id,version)
        VALUES (:employee_id, :event_id, :booking_date, :booking_price,:participation_id,:version)
    ");

    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':participation_id', $participation_id);
    $stmt->bindParam(':booking_date', $booking_date);
    $stmt->bindParam(':booking_price', $booking_price);
 
     $stmt->bindParam(':version', $version);
 }else{
   $employee_name = $item['employee_name'];
    $employee_mail = $item['employee_mail'];
    $event_name = $item['event_name'];
    $booking_date = $item['event_date'];
    $booking_price = $item['participation_fee'];
    $participation_id = $item['participation_id'];
    


    // Insert or update employee and event in the db
    $stmt = $pdo->prepare("
        INSERT INTO employees (employee_name,employee_mail)
        VALUES (:employee_name,:employee_mail)
        ON DUPLICATE KEY UPDATE employee_id = LAST_INSERT_ID(employee_id)
    ");
    $stmt->bindParam(':employee_name', $employee_name);
    $stmt->bindParam(':employee_mail', $employee_mail);
    $stmt->execute();

    $employee_id = $pdo->lastInsertId(); // Get the employee_id

    $stmt = $pdo->prepare("
        INSERT INTO events (event_name,event_date)
        VALUES (:event_name,:event_date)
        ON DUPLICATE KEY UPDATE event_id = LAST_INSERT_ID(event_id)
    ");
    $stmt->bindParam(':event_name', $event_name);
    $stmt->bindParam(':event_date', $booking_date);
    $stmt->execute();

    $event_id = $pdo->lastInsertId(); // Get the event_id

    // Insert booking
    $stmt = $pdo->prepare("
        INSERT INTO bookings (employee_id, event_id, booking_date, booking_price,participation_id)
        VALUES (:employee_id, :event_id, :booking_date, :booking_price,:participation_id)
    ");

    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':participation_id', $participation_id);
    $stmt->bindParam(':booking_date', $booking_date);
    $stmt->bindParam(':booking_price', $booking_price);
  
 }
   
    
    $stmt->execute();
}

echo htmlentities("Data imported successfully.", ENT_QUOTES, 'UTF-8');

//header('location:index1.php');
} catch (PDOException $e) {
    echo "Error: " . htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8');

}

// Function to get or create records to db
function getOrCreate($pdo, $table, $column, $value, $insertStatement) {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $column = :value");
    $stmt->execute([':value' => $value]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $insertStatement->execute([':' . $column => $value]);
        return $pdo->lastInsertId();
    } else {
        return $result['id'];
    }
}
?>
