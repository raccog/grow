<?php
// Connect to database
$link = mysqli_connect('localhost', 'grow', 'helloworld', '$DATABASE')
or die('Could not connect: ' . mysql_error());

// Query nutrient schedule and store in $schedule
$query = 'select * from nutrient_schedule';
$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
$schedule = array();
while ($row = mysqli_fetch_row($result)) {
    $row[2] = (float)$row[2];
    $row[3] = (float)$row[3];
    $row[4] = (float)$row[4];
    $row[5] = (float)$row[5];
    $schedule[$row[0]] = $row;
    unset($schedule[$row[0]][0]);
}
mysqli_free_result($result);

// Close database link
mysqli_close($link);
?>