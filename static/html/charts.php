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
</style>

<h1>Grow Records Nutrient Charts $SERVER_TYPE</h1>
<a href="index.html">Back to Index</a>
<p>Access all nutrient record charts here.</p>

<script>
// Convert names from database
var names =
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

    // Encode $names to json
    echo json_encode($names);

    // Close database link
    mysqli_close($link);
?>
;

var nutrient_schedule =
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

    // Encode nutrient data to json embedded in javascript
    echo json_encode($schedule);

    // Close database link
    mysqli_close($link);
?>
;

// Convert nutrient data from database to javascript object using php script
var nutrient_data = 
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

        // Add data point to plant
        array_push($data[$id], $row);
    }
    mysqli_free_result($result);

    // Encode nutrient data to json embedded in javascript
    echo json_encode($data);

    // Close database link
    mysqli_close($link);
?>
;

// Convert timestamps to javascript Date objects
for (let plant_id in nutrient_data) {
    for (let row of nutrient_data[plant_id]) {
        row[0] = new Date(Date.parse(row[0]));
    }
}

// Convert nutrient schedule to floats
for (let week_num in nutrient_schedule) {
    nutrient_schedule[week_num][2] = parseFloat(nutrient_schedule[week_num][2]);
    nutrient_schedule[week_num][3] = parseFloat(nutrient_schedule[week_num][3]);
    nutrient_schedule[week_num][4] = parseFloat(nutrient_schedule[week_num][4]);
    nutrient_schedule[week_num][5] = parseFloat(nutrient_schedule[week_num][5]);
}

// Log unformatted data
console.log(nutrient_data);

// Format data to be used in chart
let fmt_data = {};
for (let plant_id in nutrient_data) {
    if (!fmt_data.hasOwnProperty(plant_id)) {
        fmt_data[plant_id] = {};
        fmt_data[plant_id]['gallons'] = [];
        fmt_data[plant_id]['percent'] = [];
        fmt_data[plant_id]['ph_up'] = [];
        fmt_data[plant_id]['ph_down'] = [];
        fmt_data[plant_id]['floramicro'] = [];
        fmt_data[plant_id]['floragro'] = [];
        fmt_data[plant_id]['florabloom'] = [];
        fmt_data[plant_id]['calimagic'] = [];
    }

    for (let row of nutrient_data[plant_id]) {
        let gallons = parseInt(row[1]);
        let week = nutrient_schedule[parseInt(row[4])];
        let percent = parseInt(row[3]);
        fmt_data[plant_id]['gallons'].push({'x': row[0], 'y': gallons});
        fmt_data[plant_id]['percent'].push({'x': row[0], 'y': percent});
        fmt_data[plant_id]['ph_up'].push({'x': row[0], 'y': parseFloat(row[5]) / gallons});
        fmt_data[plant_id]['ph_down'].push({'x': row[0], 'y': parseFloat(row[6]) / gallons});
        percent *= 0.01;
        fmt_data[plant_id]['floramicro'].push({'x': row[0], 'y': week[2] * percent});
        fmt_data[plant_id]['floragro'].push({'x': row[0], 'y': week[3] * percent});
        fmt_data[plant_id]['florabloom'].push({'x': row[0], 'y': week[4] * percent});
        fmt_data[plant_id]['calimagic'].push({'x': row[0], 'y': row[week[7]] == '1' ? week[5] * percent : 0});
    }
}

// Sort data by timestamp
function sort_func(a, b) {
    return a['x'] - b['x'];
}
for (let plant_id in fmt_data) {
    fmt_data[plant_id]['gallons'].sort(sort_func);
    fmt_data[plant_id]['percent'].sort(sort_func);
    fmt_data[plant_id]['ph_up'].sort(sort_func);
    fmt_data[plant_id]['ph_down'].sort(sort_func);
    fmt_data[plant_id]['floramicro'].sort(sort_func);
    fmt_data[plant_id]['floragro'].sort(sort_func);
    fmt_data[plant_id]['florabloom'].sort(sort_func);
    fmt_data[plant_id]['calimagic'].sort(sort_func);
}

// Log formatted data
console.log(fmt_data);

var chart;
var data_labels = ['floramicro', 'floragro', 'florabloom', 'calimagic', 'gallons', 'percent', 'ph_up', 'ph_down'];

// Callback to update chart data
function select_chart(plant_id) {
    let i = 0;
    for (let d of chart.options.data) {
        d['dataPoints'] = fmt_data[plant_id][data_labels[i]];
        ++i;
    }
    chart.options.title.text = "Nutrient Data for " + names[plant_id];
    chart.render();
}

// Create chart
window.onload = function () {

chart = new CanvasJS.Chart("chartContainer", {
    title: {
        text: "Nutrient Data"
    },
    axisX: {
        valueFormatString: "D MMM YYYY",
    },
    axisY: [{
        title: "Gallons",
        lineColor: "#C24642",
        tickColor: "#C24642",
        labelFontColor: "#C24642",
        titleFontColor: "#C24642",
        suffix: ' gal',
        gridThickness: 0
    },
    {
        title: 'Milliliters per Gallon (mL/gal)',
        lineColor: "#369EAD",
        tickColor: "#369EAD",
        labelFontColor: "#369EAD",
        titleFontColor: "#369EAD",
        includeZero: true,
        suffix: 'mL',
        gridThickness: 0
    }],
    axisY2: {
        title: "Percent",
        lineColor: "#7F6084",
        tickColor: "#7F6084",
        labelFontColor: "#7F6084",
        titleFontColor: "#7F6084",
        includeZero: true,
        suffix: "%",
        gridThickness: 0
    },
    toolTip: {
        shared: true
    },
    legend: {
        cursor: "pointer",
        verticalAlign: "top",
        horizontalAlign: "center",
        dockInsidePlotArea: true,
        itemclick: toogleDataSeries
    },
    data: [{
        type: "line",
        name: "FloraMicro",
        lineThickness: 12,
        showInLegend: true,
        color: 'black',
        markerSize: 25,
        axisYIndex: 1,
        yValueFormatString: "0.0mL/gal",
        dataPoints: fmt_data[2]['floramicro']
    },
    {
        type: "line",
        name: "FloraGro",
        showInLegend: true,
        lineDashType: 'dash',
        color: 'magenta',
        lineThickness: 8,
        markerSize: 16,
        axisYIndex: 1,
        yValueFormatString: "0.0mL/gal",
        dataPoints: fmt_data[2]['floragro']
    },
    {
        type: "line",
        name: "FloraBloom",
        showInLegend: true,
        lineThickness: 6,
        color: '#808000',
        lineDashType: 'shortDash',
        markerSize: 10,
        axisYIndex: 1,
        yValueFormatString: "0.0mL/gal",
        dataPoints: fmt_data[2]['florabloom']
    },
    {
        type: "line",
        name: "CaliMagic",
        showInLegend: true,
        lineThickness: 8,
        color: 'grey',
        markerSize: 16,
        axisYIndex: 1,
        yValueFormatString: "0.0mL/gal",
        dataPoints: fmt_data[2]['calimagic']
    },
    {
        type:"line",
        name: "Gallons",
        showInLegend: true,
        color: 'blue',
        lineThickness: 4,
        markerSize: 10,
        yValueFormatString: "0 gal",
        dataPoints: fmt_data[2]['gallons'],
    },
    {
        type: "line",
        name: "Percent",
        showInLegend: true,
        color: 'red',
        lineThickness: 4,
        markerSize: 10,
        dataPoints: fmt_data[2]['percent'],
        axisYType: "secondary",
        yValueFormatString: "#,##0.##\"%\"",
    },
    {
        type: "line",
        name: "pH Up",
        showInLegend: true,
        color: 'cyan',
        lineThickness: 4,
        markerSize: 10,
        axisYIndex: 1,
        yValueFormatString: "0.0mL/gal",
        dataPoints: fmt_data[2]['ph_up'],
    },
    {
        type: "line",
        name: "pH Down",
        showInLegend: true,
        color: 'orange',
        lineThickness: 4,
        markerSize: 10,
        axisYIndex: 1,
        yValueFormatString: "0.0mL/gal",
        dataPoints: fmt_data[2]['ph_down']
    },]
});
chart.render();

function toogleDataSeries(e){
    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        e.dataSeries.visible = false;
    } else{
        e.dataSeries.visible = true;
    }
    chart.render();
}

// Update chart to show selected plant
var plant_id = document.getElementById('plant_selector').value;
select_chart(plant_id);
console.log(chart);

}
</script>

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
    echo "<select id='plant_selector' value=\"($new_id,'$name')\" onchange='select_chart(this.value)'>\n";

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

<div id="chartContainer" style="height: 500; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

</body>
</html>
