<?php
$host     = "localhost";
$user     = "admin";
$password = 'src@54321';
$database = "ticket_system";

$date     = date('Ymd-His');
$filename = $database . '_' . $date . '.sql';

// ১. মেমোরি এবং এক্সিকিউশন টাইম লিমিট বাড়ানো (বড় ডাটাবেজের জন্য)
ini_set('memory_limit', '512M');
set_time_limit(0);

// ২. ব্রাউজারে ব্যাকআপ ফাইল সরাসরি ডাউনলোড করানোর হেডার
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// ৩. --opt অপশন নিশ্চিত করে যে Structure + Data দুটিই আসবে
// পাসওয়ার্ডের স্পেশাল ক্যারেক্টার এড়াতে esapeshellarg ব্যবহার করা হয়েছে
$escapedPassword = escapeshellarg($password);

$cmd = "mysqldump --opt -h {$host} -u {$user} -p{$escapedPassword} {$database}";

// ৪. কমান্ড চালিয়ে আউটপুট ব্রাউজারে পাঠানো
passthru($cmd);
exit;
?>