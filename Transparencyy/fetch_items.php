<?php
// fetch_items.php
require_once('function.php'); // Include necessary functions

if (isset($_GET['type_id'])) {
    $type_id = $_GET['type_id'];
    $items = getItemsByType($type_id); // Define this function in function.php

    // Output options for items
    foreach ($items as $item) {
        echo "<option value=\"{$item['item_id']}\">{$item['item_name']}</option>";
    }
}
?>