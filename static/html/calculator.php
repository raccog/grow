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

</body>
</html>
