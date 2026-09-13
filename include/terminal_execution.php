<?php
include 'include/security_token.php';
include 'include/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
    exit;
}

$command = isset($_POST['command']) ? trim($_POST['command']) : '';

if (empty($command)) {
    echo json_encode(['status' => 'error', 'message' => 'Empty Command']);
    exit;
}

/* ---------------------------------------------------------
   TESTING CREDENTIALS
--------------------------------------------------------- */
$ip   = '103.146.16.154';
$port = 22;
$user = 'star';
$pass = 'star@54321';
$type = 'ssh';

/* ---------------------------------------------------------
   SSH EXECUTION FUNCTION (UPDATED & FIXED)
--------------------------------------------------------- */
function execute_ssh($ip, $port, $user, $pass, $cmd) {
    // ১. Native SSH2 Extension চেষ্টা করা
    if (function_exists('ssh2_connect')) {
        $connection = @ssh2_connect($ip, $port);
        if (!$connection) {
            return "Error: Could not connect to SSH server at $ip:$port (Network/Firewall issue)";
        }

        if (!@ssh2_auth_password($connection, $user, $pass)) {
            return "Error: Authentication failed for user '$user'. Check password.";
        }

        $stream = @ssh2_exec($connection, $cmd);
        if (!$stream) {
            return "Error: Failed to execute command on target server.";
        }

        stream_set_blocking($stream, true);
        
        // Read Standard Output
        $stdout = stream_get_contents($stream);
        
        // Read Standard Error
        $stderr_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($stderr_stream, true);
        $stderr = stream_get_contents($stderr_stream);

        fclose($stream);

        $output = trim($stdout . "\n" . $stderr);
        return !empty($output) ? $output : "Command executed successfully (No Output).";
    } 
    // ২. Fallback: shell_exec + sshpass (Fix applied)
    else if (function_exists('shell_exec')) {
        // Check if sshpass is installed
        $sshpass_check = shell_exec("which sshpass 2>&1");
        if (empty($sshpass_check)) {
            return "Error: Neither PHP 'ssh2' extension nor 'sshpass' utility is installed on your Web Server.\nFix: Run 'sudo apt install php-ssh2' or 'sudo apt install sshpass' on your Web Server.";
        }

        // Properly escape arguments
        $cmd_escaped = escapeshellarg($cmd);
        $pass_escaped = escapeshellarg($pass);
        
        $full_cmd = "sshpass -p {$pass_escaped} ssh -o StrictHostKeyChecking=no -o ConnectTimeout=5 -p {$port} {$user}@{$ip} {$cmd_escaped} 2>&1";
        
        $output = shell_exec($full_cmd);
        return !empty($output) ? trim($output) : "Command executed, but returned empty response.";
    } 
    else {
        return "Error: Both 'ssh2' extension and 'shell_exec()' function are disabled in your php.ini.";
    }
}

/* ---------------------------------------------------------
   TELNET EXECUTION FUNCTION
--------------------------------------------------------- */
function execute_telnet($ip, $port, $user, $pass, $cmd) {
    $fp = @fsockopen($ip, $port, $errno, $errstr, 5);
    if (!$fp) return "Telnet Connection failed: $errstr ($errno)";

    stream_set_timeout($fp, 5);
    usleep(300000);
    fread($fp, 4096);

    fputs($fp, $user . "\n");
    usleep(300000);

    fputs($fp, $pass . "\n");
    usleep(500000);

    fputs($fp, $cmd . "\n");
    usleep(800000);

    $cmd_output = "";
    while ($buffer = fread($fp, 4096)) {
        $cmd_output .= $buffer;
        $info = stream_get_meta_data($fp);
        if ($info['timed_out']) break;
    }

    fputs($fp, "exit\n");
    fclose($fp);

    return $cmd_output;
}

// Execution Router
if ($type === 'ssh') {
    $response_output = execute_ssh($ip, $port, $user, $pass, $command);
} else {
    $response_output = execute_telnet($ip, $port, $user, $pass, $command);
}

// Clean Non-printable characters except newlines/tabs
$response_output = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $response_output);

echo json_encode([
    'status' => 'success',
    'output' => $response_output
]);
exit;