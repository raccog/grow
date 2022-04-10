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

function clamp_values() {
    if (percent_ele.value > 200) {
        percent_ele.value = 200;
    }
    if (gallons_ele.value > 15) {
        gallons_ele.value = 15;
    }
}

function submit_record() {
    clamp_values();
    console.log("Submit record not yet implemented...");
}

function recalculate_nutrients() {
    clamp_values();

    let week_id = week_ele.value;
    let week = nutrient_schedule[week_id];
    let percent = percent_ele.value;
    let gallons = gallons_ele.value;
    let is_calimagic = is_calimagic_ele.checked;
    
    micro_ele.innerHTML = (week[2] * gallons * percent * 0.01).toString() + "mL";
    gro_ele.innerHTML = (week[3] * gallons * percent * 0.01).toString() + "mL";
    bloom_ele.innerHTML = (week[4] * gallons * percent * 0.01).toString() + "mL";
    hydro_ele.innerHTML = (gallons * 2).toString() + "mL";
    calimagic_ele.innerHTML = (is_calimagic ? (week[5] * gallons * percent * 0.01) : 0).toString() + "mL";
}
recalculate_nutrients();