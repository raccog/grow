<?php
// Connect to database
$link = mysqli_connect('localhost', 'grow', 'helloworld', 'grow_test')
or die('Could not connect: ' . mysql_error());

// Query names and store in $names
$query = 'select * from names';
$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
$names = array();
while ($row = mysqli_fetch_row($result)) {
    $names[$row[0]] = $row[1];
}
mysqli_free_result($result);

// Close database link
mysqli_close($link);
?>