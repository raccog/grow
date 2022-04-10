<!DOCTYPE html>
<html>
<head>
<title>Grow Records Calculator $SERVER_TYPE</title>
<style>
    body {
        width: 35em;
        margin: 0 auto;
        font-family: Tahoma, Verdana, Arial, sans-serif;
    }
</style>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">  
</head>
<body>
<h1>Grow Records Calculator $SERVER_TYPE</h1>
<a href="index.html">Back to Index</a>
<p>Calculate and post nutrient data here.</p>

<?php include 'db_names.php';?>
<?php include 'db_nutrient_schedule.php';?>
<?php include 'db_nutrient_data.php';?>
<?php include 'db_to_javascript.php';?>

<?php 
to_js("names", $names);
to_js("nutrient_schedule", $schedule);
to_js("nutrient_data", $data);
?>

<script>
// Convert timestamps to javascript Date objects
for (let plant_id in nutrient_data) {
    for (let row of nutrient_data[plant_id]) {
        row[0] = new Date(Date.parse(row[0]));
    }
}
</script>

<div>
    <h3>Select Plant</h3>
<?php
// Get most recent id
$recent_id = 0;
foreach ($names as $id => $_) {
    if ($id > $recent_id) {
        $recent_id = $id;
    }
}

// Create plant selector button
$name = $names[$recent_id];
echo "<select id='plant_selector' value=\"($recent_id,'$name')\">\n";

// Create option to select each plant
foreach ($names as $id => $name) {
    echo "\t<option value='$id'";
    if ($recent_id == $id) {
        echo " selected";
    }
    echo ">$name</option>\n";
}
echo "</select>\n";
?>
<label for="replace1">Replace?</label>
<input id="replace1" type="checkbox">
<div>
    <button type="button" style="visibility: hidden;">Add Plant</button>
</div>

<div>
<h3>Enter Nutrient Data</h3>
<label for="week_selector">Week</label>
<?php
// Create week selector button
$week = $schedule[1][1];
echo "<select id='week_selector' value=\"(1, '$week')\" onchange='recalculate_nutrients()'>\n";

// Create option to select each week
foreach ($schedule as $id => $week) {
    echo "\t<option value='$id'";
    if ($id == 1) {
        echo "selected";
    }
    echo ">$week[1]</option>\n";
}
echo "</select>\n";
?>
</div>

<div>
<label for="percent">Percent</label>
<input id="percent" type="number" value="80" min="0" max="200" onchange="recalculate_nutrients()">
</div>

<div>
<label for="gallons">Gallons</label>
<input id="gallons" type="number" value="6" onchange="recalculate_nutrients()">
</div>

<div>
<label for="calimagic?">CaliMagic</label>
<input id="calimagic?" type="checkbox" onchange="recalculate_nutrients()">
</div>

<div>
<label for="ph_up">pH Up</label>
<input id="ph_up" type="number" value="0">
</div>

<div>
<label for="ph_down">pH Down</label>
<input id="ph_down" type="number" value="0">
</div>

<div>
    <button type="button" onclick="submit_record()">Submit Record</button>
</div>

<div>
<h3>Calculated Nutrient Amounts (in mL)</h3>
<table>
    <tbody>
        <tr>
            <td>FloraMicro</td>
            <td id="floramicro"></td>
        </tr>
        <tr>
            <td>FloraGro</td>
            <td id="floragro"></td>
        </tr>
        <tr>
            <td>FloraBloom</td>
            <td id="florabloom"></td>
        </tr>
        <tr>
            <td>HydroGuard</td>
            <td id="hydroguard"></td>
        </tr>
        <tr>
            <td>CaliMagic</td>
            <td id="calimagic"></td>
        </tr>
    </tbody>
</table>
</div>

<script src="js/calculator.js"></script>

</body>
</html>
