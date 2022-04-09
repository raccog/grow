<?php
// Connect to database
$link = mysqli_connect('localhost', 'grow', 'helloworld', 'grow_test')
or die('Could not connect: ' . mysql_error());

// Query nutrient schedule and store in $schedule
$query = 'select * from nutrient_schedule';
$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
$schedule = array();
while ($row = mysqli_fetch_row($result)) {
    $schedule[$row[0]] = $row;
    unset($schedule[$row[0]][0]);
}
mysqli_free_result($result);

// Close database link
mysqli_close($link);
?>