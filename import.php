<?php
session_start();

// Database connection 
require_once 'config.php';

    // Read  the JSON file
    $json_data = file_get_contents(JSON_FILE_PATH);

 if ($json_data === false) {
        throw new Exception("Error reading JSON file: " . JSON_FILE_PATH);
    }

    $data = json_decode($json_data, true);

 if ($data === null) {
        throw new Exception("Error decoding JSON data.");
    }

    $chunkSize = 1000;
    
    // Insert JSON data in chunks
    $chunks = array_chunk($data, $chunkSize);
   // Insert jason data to db
 foreach ($chunks as $chunk) {
        $pdo->beginTransaction();
foreach ($chunk as $item) {
    //if (isset($item['version'])) {
    $employee_name = $item['employee_name'];
    $employee_mail = $item['employee_mail'];
    $event_id = $item['event_id'];
    $event_name = $item['event_name'];
    $booking_date = $item['event_date'];
    $booking_price = $item['participation_fee'];
    $participation_id = $item['participation_id'];
    $version = $item['version'];


    // Insert or update employee and event in the db.
    // last insert id  will help to  retrieve the employee_id for booking table.
   
       $stmt = $pdo->prepare("
    INSERT INTO employees (employee_name, employee_mail)
    VALUES (:employee_name, :employee_mail)
    ON DUPLICATE KEY UPDATE employee_id = LAST_INSERT_ID(employee_id),
    employee_name = :employee_name

");

    $stmt->bindParam(':employee_name', $employee_name);
    $stmt->bindParam(':employee_mail', $employee_mail);
    $stmt->execute();

    $employee_id = $pdo->lastInsertId(); // Get the employee_id
    

  $stmt = $pdo->prepare("
    INSERT INTO events (event_id, event_name)
    VALUES (:event_id, :event_name)
    ON DUPLICATE KEY UPDATE event_name = VALUES(event_name)
");

    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':event_name', $event_name);
    $stmt->execute();

    

    // Insert or update booking details
   $stmt = $pdo->prepare("
    INSERT INTO bookings (employee_id, event_id, booking_date, booking_price, participation_id, version)
    VALUES (:employee_id, :event_id, :booking_date, :booking_price, :participation_id, :version)
    ON DUPLICATE KEY UPDATE
    booking_price = :booking_price,
    version = :version,
    employee_id = $employee_id,
    event_id = $event_id,
    booking_date =:booking_date

");


    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':participation_id', $participation_id);
    $stmt->bindParam(':booking_date', $booking_date);
    $stmt->bindParam(':booking_price', $booking_price);
    $stmt->bindParam(':version', $version);

    
    $stmt->execute();
}
 
        $pdo->commit();
    }

$_SESSION['status'] ='Data imported successfully.';

header('location:index1.php');

?>
