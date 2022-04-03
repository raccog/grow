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
    }
    td {
        text-align: right;
    }

    .tables {
        visibility: hidden;
    }
    #table1 {
        visibility: visible !important;
    }
</style>
<h1>Grow Records Nutrient Tables $SERVER_TYPE</h1>
<p>Access all nutrient record tables here.</p>
</head>
<body>

<?php
    $link = mysqli_connect('localhost', 'grow', 'helloworld', 'grow_test')
        or die('Could not connect: ' . mysql_error());

    // $query = 'select week_number, week_id from weeks';
    // $result

    $query = 'select * from nutrient_data';
    $result = mysqli_query($link, $query) or die('Query failed: ' . mysql_error());

    $last_id = 0;
    while ($line = mysqli_fetch_row($result)) {
        if ($last_id != $line[0]) {
            if ($last_id != 0) {
                echo "</table>\n";
            }
            $last_id = $line[0];
            echo "<div class='tables' id='table$last_id'>\n";
            echo "<h2>Table Number $last_id</h2>\n";
            echo "<table>\n\t<tr>\n\t\t<th>Timestamp</th>\n\t\t";
            echo "<th>Gallons</th>\n\t\t<th>Water Replaced</th>\n\t\t<th>Nutrient Strength (%)</th>";
            echo "\n\t\t<th>Week</th>\n\t\t<th>pH Up (mL)</th>\n\t\t\n\t\t\n\t</tr>\n";
            echo "</div>\n";
        }
        echo "\t<tr>\n";

        unset($line[0]);
        if ($line[3] == 1) {
            $line[3] = "True";
        } else {
            $line[3] = "False";
        }
        foreach ($line as $col_value) {
            echo "\t\t<td>$col_value</td>\n";
        }
        echo "\t</tr>\n";
    }

    mysqli_free_result($result);
    mysqli_close($link);
?>

</body>
</html>
