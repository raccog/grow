// All elements
var plant_ele = document.getElementById('plant_selector');
var replace_ele = document.getElementById('replace1');
var week_ele = document.getElementById('week_selector');
var percent_ele = document.getElementById('percent');
var gallons_ele = document.getElementById('gallons');
var is_calimagic_ele = document.getElementById('calimagic?');
var micro_ele = document.getElementById('floramicro');
var gro_ele = document.getElementById('floragro');
var bloom_ele = document.getElementById('florabloom');
var hydro_ele = document.getElementById('hydroguard');
var calimagic_ele = document.getElementById('calimagic');
var ph_up_ele = document.getElementById('ph_up');
var ph_down_ele = document.getElementById('ph_down');
var status_ele = document.getElementById('status');

function clamp_values() {
    // Clamp percent
    if (percent_ele.value > 200) {
        percent_ele.value = 200;
    }
    // Clamp gallons
    if (gallons_ele.value > 15) {
        gallons_ele.value = 15;
    }
}

async function submit_record() {
    // Ensure values are clamped
    clamp_values();
    
    // Assert values are valid
    if (percent_ele.value > 200 || percent_ele.value < 0) {
        status_ele.innerHTML = 'Percent value is invalid';
        status_ele.style.color = 'red';
        return;
    }
    if (gallons_ele.value > 15 || gallons_ele.value <= 0) {
        status_ele.innerHTML = 'Gallons value is invalid';
        status_ele.style.color = 'red';
        return;
    }
    if (week_ele.value < 1 || week_ele.value > 11) {
        status_ele.innerHTML = 'Week value is invalid';
        status_ele.style.color = 'red';
        return;
    }

    // Create nutrient record to send
    let record = {
        "percent": parseFloat(percent_ele.value),
        "gallons": parseInt(gallons_ele.value),
        "week": parseInt(week_ele.value),
        "calimagic": is_calimagic_ele.checked,
        "ph_up": parseFloat(ph_up_ele.value),
        "ph_down": parseFloat(ph_down_ele.value),
        "plants": [[parseInt(plant_ele.value), replace_ele.checked]],
    };
    let response = await fetch('http://192.168.0.67:$API_PORT/nutrient_record.post', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify(record)
    });

    // Send record to database
    let result = await response.json();
    if ('status' in result && result['status'] === 'success') {
        status_ele.innerHTML = 'Successfully submitted record';
        status_ele.style.color = 'green';
    } else {
        status_ele.innerHTML = 'Failed to submitted record';
        status_ele.style.color = 'red';
    }
}

function recalculate_nutrients() {
    // Ensure values are clamped
    clamp_values();

    // Values
    let week_id = week_ele.value;
    let week = nutrient_schedule[week_id];
    let percent = percent_ele.value;
    let gallons = gallons_ele.value;
    let is_calimagic = is_calimagic_ele.checked;
    
    // Calulate nurient values
    micro_ele.innerHTML = (week[2] * gallons * percent * 0.01).toString() + "mL";
    gro_ele.innerHTML = (week[3] * gallons * percent * 0.01).toString() + "mL";
    bloom_ele.innerHTML = (week[4] * gallons * percent * 0.01).toString() + "mL";
    hydro_ele.innerHTML = (gallons * 2).toString() + "mL";
    calimagic_ele.innerHTML = (is_calimagic ? (week[5] * gallons * percent * 0.01) : 0).toString() + "mL";
}

// Calculate nutrients on page load
window.onload = function () {
    recalculate_nutrients();
}