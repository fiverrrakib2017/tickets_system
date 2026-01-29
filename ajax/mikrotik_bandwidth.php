<?php

$interface = $_GET['interface'];

/* Demo data */
echo json_encode([
    'time' => [
        date('H:i:s', time()-20),
        date('H:i:s', time()-15),
        date('H:i:s', time()-10),
        date('H:i:s', time()-5),
        date('H:i:s')
    ],
    'rx' => [10000000,12000000,15000000,13000000,16000000],
    'tx' => [4000000,6000000,5000000,7000000,6500000]
]);
