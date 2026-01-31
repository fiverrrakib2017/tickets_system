<?php
$interface = $_GET['interface'] ?? '';
$file      = $_GET['file'] ?? '';

if (!$interface || !$file) {
    http_response_code(400);
    exit('Invalid request');
}

$graph_server = '103.112.204.48';
$graph_port   = 8082;

// Allow only expected files (security)
$allowed = ['daily.gif', 'weekly.gif', 'monthly.gif', 'yearly.gif'];
if (!in_array($file, $allowed)) {
    http_response_code(403);
    exit('Not allowed');
}

$image_url = "http://$graph_server:$graph_port/graphs/iface/$interface/$file";

$image = @file_get_contents($image_url);

if ($image === false) {
    http_response_code(404);
    exit('Graph not found');
}

header('Content-Type: image/gif');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo $image;
exit;
