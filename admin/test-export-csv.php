<?php

$list = array (
    array('aaa', 'bbb', 'ccc', 'dddd'),
    array('123', '456', '789'),
    array('"aaa"', '"bbb"')
);

header( 'Content-Type: text/csv' );
header( 'Content-Disposition: attachment;filename=myfile.csv');
$fp = fopen('php://output', 'w');

foreach ($list as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
?>
