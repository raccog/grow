<?php
// Connect to database
$link = mysqli_connect('localhost', 'grow', 'helloworld', 'grow_test')
or die('Could not connect: ' . mysql_error());

// Query nutrient data
$query = 'select * from nutrient_data';
$result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());

$data = array();
while ($line = mysqli_fetch_row($result)) {
    // Create array for plant if it does not exist
    $id = $line[0];
    if (!array_key_exists($id, $data)) {
        $data[$id] = array();
    }
    unset($line[0]);

    // Create indexed array from row data
    $row = array();
    foreach ($line as $column) {
        array_push($row, $column);
    }

    // Convert strings to numbers
    $row[1] = (int)$row[1];
    $row[3] = (float)$row[3];
    $row[5] = (float)$row[5];
    $row[6] = (float)$row[6];

    // Add data point to plant
    array_push($data[$id], $row);
}
mysqli_free_result($result);

// Sort nutrient records by timestamp
foreach ($data as $id => $_) {
    usort($data[$id], function ($a, $b) {
        $a_val = DateTime::createFromFormat('Y-m-d H:i:s', $a[0]);
        $b_val = DateTime::createFromFormat('Y-m-d H:i:s', $b[0]);

        if ($a_val > $b_val) return 1;
        if ($a_val < $b_val) return -1;
        return 0;
    });
}

// Close database link
mysqli_close($link);
?>




