<?php
function to_js($var_name, $value) {
    echo "<script>\n";
    echo "var $var_name = \n";
    echo json_encode($value);
    echo ";\n";
    echo "</script>\n";
}
?>