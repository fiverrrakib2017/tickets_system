<?php
session_start();
/*------------------ Base Setup ------------------*/
if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
}

include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';

/*------------------ Input ------------------*/
$customer_id = (int)($_GET['customer_id'] ?? 0);
$interface   = trim($_GET['interface'] ?? '');
$community   = 'starcomm';

if ($customer_id === 0 || $interface === '') {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

/*------------------ Get Router IP ------------------*/
$res = $con->query("
    SELECT ping_ip 
    FROM customers 
    WHERE id = $customer_id
");

if (!$res || $res->num_rows === 0) {
    echo json_encode(['error' => 'Customer not found']);
    exit;
}

$router_ip = $res->fetch_assoc()['ping_ip'];

/*------------------ Helper: Clean Interface ------------------*/
function cleanInterfaceName($str)
{
    // remove STRING:, quotes, hidden chars, lowercase
    $str = str_replace(['STRING:', '"'], '', $str);
    $str = preg_replace('/[^\w\-\.]/', '', $str);
    return strtolower(trim($str));
}

$interfaceClean = cleanInterfaceName($interface);


/*------------------ SNMP: Find ifIndex ------------------*/
$ifNameOid = '1.3.6.1.2.1.2.2.1.2';
$snmpNames = @snmpwalk($router_ip, $community, $ifNameOid);

$ifIndex = null;

if ($snmpNames) {
    foreach ($snmpNames as $index => $value) {

        $snmpIfName = cleanInterfaceName($value);
        $currentIfIndex = $index + 1;

        if ($snmpIfName === $interfaceClean) {
            $ifIndex = $currentIfIndex;
            break;
        }
    }
}

if (!$ifIndex) {
    echo json_encode([
        'error' => 'Interface not found',
        'interface' => $interface
    ]);
    exit;
}

/*------------------ RX / TX OIDs ------------------*/
$rxOid = "1.3.6.1.2.1.31.1.1.1.6.$ifIndex";
$txOid = "1.3.6.1.2.1.31.1.1.1.10.$ifIndex";

$rx = @snmpget($router_ip, $community, $rxOid);
$tx = @snmpget($router_ip, $community, $txOid);

/*------------------ Parse Values ------------------*/
$rxValue = $rx ? (int)preg_replace('/\D/', '', $rx) : 0;
$txValue = $tx ? (int)preg_replace('/\D/', '', $tx) : 0;

/*------------------ Previous Values ------------------*/
$prevRx   = $_SESSION['prev_rx']   ?? $rxValue;
$prevTx   = $_SESSION['prev_tx']   ?? $txValue;
$prevTime = $_SESSION['prev_time'] ?? time();

/* Counter reset protection */
if ($rxValue < $prevRx) $prevRx = $rxValue;
if ($txValue < $prevTx) $prevTx = $txValue;

$currentTime = time();
$timeDiff = max(1, $currentTime - $prevTime);

/*------------------ Calculate Bandwidth ------------------*/
$rxRate = (($rxValue - $prevRx) * 8) / $timeDiff; // bps
$txRate = (($txValue - $prevTx) * 8) / $timeDiff;

/*------------------ Store Current ------------------*/
$_SESSION['prev_rx']   = $rxValue;
$_SESSION['prev_tx']   = $txValue;
$_SESSION['prev_time'] = $currentTime;

/*------------------ JSON Output ------------------*/
echo json_encode([
    'time' => [date('H:i:s')],
    'rx'   => [round($rxRate / 1000000, 2)], // Mbps
    'tx'   => [round($txRate / 1000000, 2)]
]);