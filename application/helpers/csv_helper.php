<?php 

// application/helpers/csv_helper.php

if (!function_exists('write_csv')) {
    function write_csv($filepath, $data) {
        $fp = fopen($filepath, 'w');
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}
