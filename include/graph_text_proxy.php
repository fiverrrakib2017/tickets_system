<?php
$interface = $_GET['interface'] ?? '';
$period    = $_GET['period'] ?? 'day'; // day, week, month, year

if (!$interface) exit('Invalid');

$url = "http://103.112.204.48:8082/graphs/iface/$interface/";

$html = @file_get_contents($url);
if (!$html) exit('No data');

/*
 MRTG সাধারণত এই order এ থাকে:
 Daily → Weekly → Monthly → Yearly
*/

preg_match_all('/<p>(.*?)<\/p>/si', $html, $matches);

$map = [
    'day'   => 0,
    'week'  => 1,
    'month' => 2,
    'year'  => 3,
];

$index = $map[$period] ?? 0;

if (!isset($matches[1][$index])) {
    exit('No info');
}

echo '<div>';
echo strip_tags($matches[1][$index], '<b><br>');
echo '</div>';
