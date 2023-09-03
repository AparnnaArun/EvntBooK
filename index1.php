<?php
session_start();
require_once 'config.php';

         // Handle filter conditions (customize as needed)
        $conditions = [];
        $params = [];

        if (!empty($_GET['employee_name'])) {
            $conditions[] = 'e.employee_name LIKE :employee_name';
            $params[':employee_name'] = '%' . $_GET['employee_name'] . '%';
        }

        if (!empty($_GET['event_name'])) {
            $conditions[] = 'ev.event_name LIKE :event_name';
            $params[':event_name'] = '%' . $_GET['event_name'] . '%';
        }

        if (!empty($_GET['booking_date'])) {
            $conditions[] = 'b.booking_date = :booking_date';
            $params[':booking_date'] = $_GET['booking_date'];
        }

        // Join 3 tables
        $sql = "SELECT e.employee_name, ev.event_name, b.booking_date, b.booking_price 
                FROM bookings b JOIN employees e ON b.employee_id = e.employee_id
                JOIN events ev ON b.event_id = ev.event_id";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // Execute the query and display results
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $totalPrice = 0;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Booking System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

</head>
<body>
    <div class="container">
    <h1>Booking System</h1>

    <div class="row">

 <form id="filter-form" method="GET">
  <div class="row">
    <div class="col-md-3">
      <input type="text" class="form-control" placeholder="Employee Name" id="employee_name" name="employee_name">
      
    </div>
    <div class="col-md-3">
      <input type="text" class="form-control" placeholder="Event Name" id="event_name" name="event_name">
         
    </div>
    <div class="col-md-2">
      <input type="text" class="form-control datepicker" placeholder="Date" id="booking_date" name="booking_date" >
         
    </div>
    <div class="col-md-2">
        <button type="submit" value="" class="btn btn-primary">Filter</button>
         
    </div>
      <div class="col-md-2">
        <a href="import.php"  class="btn btn-primary">Read JSON File</a>
         
    </div>
  </div>
</form>
</div>

<div class="row mt-3 mb-3">
    <div class="col-md-12 mt-3">
        <?php
     
if(isset($_SESSION['status'])){
  echo htmlspecialchars_decode(htmlentities($_SESSION['status']));
unset ($_SESSION['status']);
}

 if(isset($_SESSION['failed'])){
  echo htmlspecialchars_decode(htmlentities($_SESSION['failed']));
unset ($_SESSION['failed']);
}
 ?>

</div>
</div>

   <div class="row mt-3">
    <table  class='table table-striped'>
       <thead>
    <tr>
        <th scope='col'>#</th>
          <th scope='col'>Employee</th>
      <th scope='col'>Event</th>
      <th scope='col'>Date</th>
      <th scope='col'>Fee</th>
    </tr>
  </thead>
<tbody>
    <?php
    $serialNumber = 1; 
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
   echo "<td>" . $serialNumber . "</td>";
    echo "<td>" . htmlentities($row['employee_name']) . "</td>";
    echo "<td>" . htmlentities($row['event_name']) . "</td>";
    echo "<td>" . htmlentities($row['booking_date']) . "</td>";
    echo "<td>" . htmlentities($row['booking_price']) . "</td>";
            echo "</tr>";
             $serialNumber++;
             $totalPrice += $row['booking_price'];
        }
        ?>
     
        <tr>
            <td colspan="4"><b>Total Price</b></td>
          <td><b><?php echo htmlentities($totalPrice); ?></b></td>

        </tr>
    </tbody>
    </table>

</div>
</div>
</body>
   <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script type="text/javascript">
   

            // JQuery datepicker
      $( function() {
    $( ".datepicker" ).datepicker(
        { dateFormat: 'yy-mm-dd' });
  } );
  </script>
</html>