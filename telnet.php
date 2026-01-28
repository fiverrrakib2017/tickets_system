<?php
// security: IP validation
// if (!isset($_GET['ip']) || !filter_var($_GET['ip'], FILTER_VALIDATE_IP)) {
//     die('Invalid IP address');
// }

// $ip = $_GET['ip'];
// $username = $_GET['user'] ?? 'star';

// // random free port
// $port = rand(8000, 9000);

// // start ttyd
// $cmd = sprintf(
//     'nohup ttyd -i 0.0.0.0 -p %d --writable ssh %s > /dev/null 2>&1 &',
//     $port,
//     escapeshellarg("$username@$ip")
// );
// exec($cmd);

// // wait ttyd start
// sleep(1);

// // redirect to web terminal
// header("Location: http://103.112.206.139:$port");
// exit;

session_start();

$host = "127.0.0.1";
$port = 23;
$user = "youruser";
$pass = "yourpass";

if (!isset($_SESSION['fp'])) {

    $fp = fsockopen($host, $port, $errno, $errstr, 10);
    if (!$fp) die("Telnet connection failed");

    stream_set_timeout($fp, 2);

    // Read login prompt
    sleep(1);
    fread($fp, 1024);

    // Send username
    fwrite($fp, $user . "\n");
    sleep(1);
    fread($fp, 1024);

    // Send password
    fwrite($fp, $pass . "\n");
    sleep(1);
    fread($fp, 2048);

    $_SESSION['fp'] = $fp;
}
else {
    $fp = $_SESSION['fp'];
}

// Send command
$cmd = $_POST['cmd'] . "\n";
fwrite($fp, $cmd);

// Read response
$out = "";
$start = time();

while (true) {
    $data = fread($fp, 4096);
    if ($data) {
        $out .= $data;
        if (strpos($data, "$") !== false) break; // shell prompt
    }
    if (time() - $start > 2) break;
}

echo htmlspecialchars($out);
?>