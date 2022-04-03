<!DOCTYPE html>
<html>
<head>
<title>Grow Records Nutrient Tables $SERVER_TYPE</title>
<style>
    head {
        width: 35em;
        margin: 0 auto;
        font-family: Tahoma, Verdana, Arial, sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table, tr, th, td {
        border: 1px solid;
        padding-top: 1em;
        padding-bottom: 1em;
    }
    tr {
        padding-top: 25px;
        padding-bottom: 25px;
    }
    td {
        text-align: right;
    }
    th {
        word-wrap: break-word;
    }
    thead, tfoot {
        background-color: rgb(10, 200, 100);
        color: rgb(20, 20, 20);
    }

    .datarow:hover {
        background-color: #ddd;
    }
</style>

<h1>Grow Records Nutrient Tables $SERVER_TYPE</h1>
<a href="index.html">Back to Index</a>
<p>Access all nutrient record tables here.</p>

</head>
<body>

<div>
    <h3>Select Plant</h3>
<?php
    // Connect to database
    $link = mysqli_connect('localhost', 'grow', 'helloworld', 'grow_test')
        or die('Could not connect: ' . mysql_error());

    // Query names and store in $names
    $query = 'select * from names';
    $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
    $names = array();
    $new_id = 0;
    while ($row = mysqli_fetch_row($result)) {
        if ($row[0] > $new_id) {
            $new_id = $row[0];
        }
        $names[$new_id] = $row[1];
    }
    mysqli_free_result($result);

    // Create selector button
    $name = $names[$new_id];
    echo "<select id='plant_selector' value=\"($new_id,'$name')\" onchange='select_table(this.value)'>\n";

    // Create option to select each plant
    foreach ($names as $id => $name) {
        echo "\t<option value='$id'";
        if ($new_id == $id) {
            echo " selected";
        }
        echo ">$name</option>\n";
    }
?>
</select>
</div>

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

    // Query names and store in $names
    $query = 'select * from names';
    $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());
    $names = array();
    while ($row = mysqli_fetch_row($result)) {
        $names[$row[0]] = $row[1];
    }
    mysqli_free_result($result);

    // Query nutrient data
    $query = 'select * from nutrient_data';
    $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());

    // Create tables from nutrient data
    $last_id = 0;
    while ($line = mysqli_fetch_row($result)) {
        // Create table header
        if ($last_id != $line[0]) {
            if ($last_id != 0) {
                echo "\t</tbody>\n
                </table>\n
                </div>\n";
            }
            $last_id = $line[0];
            $name = $names[$last_id];
            echo "<div class='tables' id='table$last_id' style='visibility: hidden; display: none'>\n
            <h2>$name Nutrient Table</h2>\n
            <table>\n
                \t<thead>\n
                    \t\t<tr>\n
                        \t\t\t<th>Timestamp</th>\n
                        \t\t\t<th>Week</th>\n
                        \t\t\t<th>Water Replaced</th>\n
                        \t\t\t<th>Nutrient Strength (%)</th>\n
                        \t\t\t<th>Gallons</th>\n
                        \t\t\t<th>pH Up (mL)</th>\n
                        \t\t\t<th>pH Down (mL)</th>\n
                        \t\t\t<th>FloraMicro (mL)</th>\n
                        \t\t\t<th>FloraGro (mL)</th>\n
                        \t\t\t<th>FloraBloom (mL)</th>\n
                        \t\t\t<th>CaliMagic (mL)</th>\n
                    \t\t</tr>\n
                \t</thead>\n
                \t<tbody>\n";
        }
        echo "\t<tr class='datarow'>\n";

        // Convert row data
        unset($line[0]);
        if ($line[3] == 1) {
            $line[3] = "True";
        } else {
            $line[3] = "False";
        }
        $week = $schedule[$line[5]];
        $line[5] = $week[1];

        // Save calimagic flag
        $calimagic = $line[8];

        // Swap gallons with week
        $gallons = $line[2];
        $line[2] = $line[5];
        $line[5] = $gallons;

        // Calculate nutrient levels
        $percent = $line[4] * 0.01;
        $line[8] = $week[2] * $percent * $gallons;
        $line[9] = $week[3] * $percent * $gallons;
        $line[10] = $week[4] * $percent * $gallons;
        if ($calimagic == 0) {
            $line[11] = 0;
        } else {
            $line[11] = $week[5] * $percent * $gallons;
        }

        // Create table row
        foreach ($line as $col_value) {
            echo "\t\t<td>$col_value</td>\n";
        }
        echo "\t</tr>\n";
    }

    // Free data
    mysqli_free_result($result);
    mysqli_close($link);
?>

<script>
    var selected_table = document.getElementById('plant_selector').value;
    var new_table = document.getElementById('table' + selected_table.toString());
    new_table.style.visibility = 'visible';
    new_table.style.display = 'contents';

    function select_table(id) {
        for (let ele of document.getElementsByClassName('tables')) {
            ele.style.visibility = 'hidden';
            ele.style.display = 'none';
        }

        let table = document.getElementById('table' + id.toString());
        table.style.visibility = 'visible';
        table.style.display = 'contents';
    }
</script>

</body>
</html>
