<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';
include 'functions.php';
if (!isset($_SESSION)) {
    session_start();
}
/*--------Top Customers By Tickets--------(incomming index.php file)--------------*/
// if (isset($_GET['top_customer_by_tickets']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
//     $sql = "SELECT c.id as customer_id,c.username, c.mobile, c.address, COUNT(t.id) as total_tickets
//         FROM ticket t
//         JOIN customers c ON t.customer_id = c.id
//         GROUP BY t.customer_id
//         ORDER BY total_tickets DESC
//         LIMIT 10";
//         $result = $con->query($sql);

//     echo "<table class='table table-bordered table-striped'>
//             <tr>
//                 <th>#</th>
//                 <th>Customer Name</th>
//                 <th>Phone</th>
//                 <th>Address</th>
//                 <th>Total Tickets</th>
//             </tr>";

//     $i=1;
//     while($row = $result->fetch_assoc()){
//         echo "<tr>
//                 <td>".$i++."</td>
//                 <td><a href='profile.php?clid=".$row['customer_id']."'>".$row['username']."</a></td>
//                 <td>".$row['mobile']."</td>
//                 <td>".$row['address']."</td>
//                 <td><b style='color:green'>".$row['total_tickets']."</b></td>
//             </tr>";
//     }
//     echo "</table>";
//         exit;
// }
/*--------Top Area By Tickets--------(incomming index.php file)--------------*/
// if (isset($_GET['top_area_by_tickets']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
//     $sql = "SELECT 
//                 a.id AS area_id, 
//                 a.name, 
//                 COUNT(t.id) AS total_tickets
//             FROM ticket t
//             JOIN area_list a ON t.area_id = a.id
//             GROUP BY t.area_id
//             ORDER BY total_tickets DESC
//             LIMIT 10;
//             ";
//         $result = $con->query($sql);

//     echo "<table class='table table-bordered table-striped'>
//             <tr>
//                 <th>#</th>
//                 <th>Area Name</th>
//                 <th>Total Tickets</th>
//             </tr>";

//     $i=1;
//     while($row = $result->fetch_assoc()){
//         echo "<tr>
//                 <td>".$i++."</td>
//                 <td><a href='view_area.php?id=".$row['area_id']."'>".$row['name']."</a></td>
//                 <td><b style='color:green'>".$row['total_tickets']."</b></td>
//             </tr>";
//     }
//     echo "</table>";
//         exit;
// }
if (isset($_GET['get_tickets_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    require 'datatable.php';

    $table = 'ticket';
    $primaryKey = 'id';

    $columns = [
        ['db' => 'id', 'dt' => 0],
        [
            'db' => 'ticket_type',
            'dt' => 1,
            'formatter' => function ($d, $row) {
                if ($d === 'Complete') {
                    return '<a href="tickets_profile.php?id=' . $row['id'] . '"><span class="badge bg-success">Completed</span></a>';
                } elseif ($d === 'Active') {
                    return '<a href="tickets_profile.php?id=' . $row['id'] . '"><span class="badge bg-danger">Active</span></a>';
                } elseif ($d === 'Close') {
                    return '<a href="tickets_profile.php?id=' . $row['id'] . '"><span class="badge bg-success">Close</span></a>';
                } else {
                    return '<a href="tickets_profile.php?id=' . $row['id'] . '">' . $d . '</a>';
                }
            },
        ],
        [
            'db' => 'startdate',
            'dt' => 2,
            'formatter' => function ($d, $row) {
                return timeAgo($d);
            },
        ],
        [
            'db' => 'priority',
            'dt' => 3,
            'formatter' => function ($d, $row) {
                $priority = $row['priority'];
                $priorityLabel = '';
                $badgeClass = '';

                switch ($priority) {
                    case 1:
                        $priorityLabel = 'Low';
                        $badgeClass = 'badge bg-secondary'; // Gray
                        break;
                    case 2:
                        $priorityLabel = 'Normal';
                        $badgeClass = 'badge bg-info'; // Light Blue
                        break;
                    case 3:
                        $priorityLabel = 'Standard';
                        $badgeClass = 'badge bg-primary'; // Blue
                        break;
                    case 4:
                        $priorityLabel = 'Medium';
                        $badgeClass = 'badge bg-warning text-dark'; // Yellow
                        break;
                    case 5:
                        $priorityLabel = 'High';
                        $badgeClass = 'badge bg-danger'; // Red
                        break;
                    case 6:
                        $priorityLabel = 'Very High';
                        $badgeClass = 'badge bg-dark'; // Black
                        break;
                    default:
                        $priorityLabel = 'Unknown';
                        $badgeClass = 'badge bg-light text-dark'; // Light
                        break;
                }

                return "<span class='{$badgeClass}'>{$priorityLabel}</span>";
            },
        ],

        [
            'db' => 'customer_id',
            'dt' => 4,
            'formatter' => function ($d, $row) use ($con) {
                /*Fetch customer details*/
                $customerQuery = $con->query("SELECT * FROM customers WHERE id=$d");
                if ($customer = $customerQuery->fetch_assoc()) {
                    $username = $customer['username'];
                    $fullname = $customer['fullname'];
                    $onlineCheck = $con->query("SELECT * FROM radacct WHERE radacct.acctstoptime IS NULL AND username='$username'");
                    $statusIcon = $onlineCheck->num_rows == 1 ? '<abbr title="Online"><img src="images/icon/online.png" height="10" width="10"/></abbr>' : '<abbr title="Offline"><img src="images/icon/offline.png" height="10" width="10"/></abbr>';
                    return $statusIcon . ' <a href="profile.php?clid=' . $customer['id'] . '" >' . $fullname . '</a><br>(' . $username . ')';
                }
                return 'Unknown Customer';
            },
        ],
        [
            'db' => 'customer_id',
            'dt' => 5,
            'formatter' => function ($d, $row) use ($con) {
                /*Fetch customer details*/
                $customerQuery = $con->query("SELECT * FROM ticket WHERE customer_id=$d");
                if ($customer = $customerQuery->fetch_assoc()) {
                    $total_ticket = $customerQuery->num_rows;
                    return $total_ticket;
                }
                return '0';
            },
        ],
        [
            'db' => 'customer_id',
            'dt' => 6,
            'formatter' => function ($d, $row) use ($con) {
                /*Fetch customer details*/
                $customerQuery = $con->query("SELECT * FROM customers WHERE id=$d");
                if ($customer = $customerQuery->fetch_assoc()) {
                    return $customer['mobile'];
                }
                return 'Unknown Customer';
            },
        ],
        
        [
            'db' => 'complain_type',
            'dt' => 7,
            'formatter' => function ($d, $row) use ($con) {
                /* Fetch complain type*/
                $complainQuery = $con->query("SELECT * FROM ticket_topic WHERE id='$d'");
                if ($complain = $complainQuery->fetch_assoc()) {
                    return $complain['topic_name'];
                }
                return 'Unknown Topic';
            },
        ],
        [
            'db' => 'customer_id',
            'dt' => 8,
            'formatter' => function ($d, $row) use ($con) {
                $get_area = 'N/A';
                $areaQuery = $con->query("SELECT area FROM customers WHERE id=$d LIMIT 1");
                if ($areaQuery && $areaRow = $areaQuery->fetch_array()) {
                    $area_id = $areaRow['area'];
                    $areaNameQuery = $con->query("SELECT name FROM area_list WHERE id=$area_id LIMIT 1");
                    if ($areaNameQuery && $areaRow = $areaNameQuery->fetch_array()) {
                        $get_area = $areaRow['name'];
                    }
                }
                return $get_area;
            },
        ],
        
        [
            'db' => 'asignto',
            'dt' => 9,
            'formatter' => function ($d, $row) use ($con) {
                /*Fetch assigned group*/
                $groupQuery = $con->prepare('SELECT group_name FROM working_group WHERE id = ?');
                $groupQuery->bind_param('i', $d);
                $groupQuery->execute();
                $groupResult = $groupQuery->get_result();
                if ($group = $groupResult->fetch_assoc()) {
                    return htmlspecialchars($group['group_name']);
                }
                return 'No assigned group';
            },
        ],
        [
            'db' => 'enddate',
            'dt' => 10,
            'formatter' => function ($d, $row) {
                $startdate = $row['startdate'];
                $enddate = $row['enddate'];
                if (!empty($enddate)) {
                    return acctual_work($startdate, $enddate);
                } else {
                    return 'Work Processing..';
                }
            },
        ],
        [
            'db' => 'enddate',
            'dt' => 11,
            'formatter' => function ($d, $row) {
                $enddate = $row['enddate'];
                $now_time = date('Y-m-d H:i:s');
                if (!empty($enddate)) {
                    return acctual_work($enddate, $now_time);
                } else {
                    return 'Not Completed..';
                }
            },
        ],
        [
            'db' => 'parcent',
            'dt' => 12,
        ],
        [
            'db' => 'notes',
            'dt' => 13,
        ],
        [
            'db' => 'id',
            'dt' => 14,
            'formatter' => function ($d, $row) {
    //             return '
    // <button type="button" name="settings_button" data-id=' .
    //                 $row['id'] .
    //                 ' class="btn-sm btn btn-danger"> <i class="fas fa-cog"></i></button>
    // <a class="btn-sm btn btn-success" href="tickets_profile.php?id=' .
    //                 $row['id'] .
    //                 '"><i class="fas fa-eye"></i></a>';
                return ' <a class="btn-sm btn btn-success" href="tickets_profile.php?id=' .
                    $row['id'] .'"><i class="fas fa-eye"></i></a>';
            },
        ],
    ];
    $condition = '1=1';
   
    if (!empty($_SESSION['user_pop'])) {
        if ($_SESSION['user_pop'] == 1) {
            $condition .= '';
        } else {
            $condition .= " AND pop_id = '" . mysqli_real_escape_string($con, $_SESSION['user_pop']) . "'";
        }
    }
   
    /* Check if 'area_id' is provided in the GET request*/
    if (isset($_GET['area_id']) && !empty($_GET['area_id'])) {
        $condition .= " AND area_id = '" . mysqli_real_escape_string($con, $_GET['area_id']) . "'";
    }
    

    /* Check if 'pop_id' is provided in the GET request*/
    if (isset($_GET['pop_id']) && !empty($_GET['pop_id'])) {
        $condition .= " AND pop_id = '" . mysqli_real_escape_string($con, $_GET['pop_id']) . "'";
    }
    /*Check Ticket Status*/
    if (!empty($_GET['status'])) {
        $condition .= " AND ticket_type = '" . mysqli_real_escape_string($con, $_GET['status']) . "'";
    }
    /*Check Ticket Assign Filter*/
    // Assign Filter
    if (isset($_GET['assign_id']) && $_GET['assign_id'] !== '' && $_GET['assign_id'] !== null) {
        $assign_id = intval($_GET['assign_id']);
        if ($assign_id > 0) {
            $condition .= " AND `asignto` = $assign_id";
        }
    }
    /* Filter by date  */
    if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to = $_GET['to_date'];
        $condition .= " AND DATE(`to_date`) BETWEEN '" . $from . "' AND '" . $to . "'";
    } elseif (!empty($_GET['from_date'])) {
        $from = $_GET['from_date'];
        $condition .= " AND DATE(`startdate`) >= '" . $from . "'";
    } elseif (!empty($_GET['to_date'])) {
        $to = $_GET['to_date'];
        $condition .= " AND DATE(`enddate`) <= '" . $to . "'";
    }
    /*Filter Ticket Topic Data*/
    if (isset($_GET['topic_id']) && $_GET['topic_id'] !== '' && $_GET['topic_id'] !== null) {
        $topic_id = intval($_GET['topic_id']);
        if ($topic_id > 0) {
            $condition .= " AND `complain_type` = $topic_id";
        }
    }
    
    /* Output JSON for DataTables to handle*/
    echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, $condition));
}
if(isset($_GET['get_customer_tickets']) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $customer_id = intval($_GET['customer_id']); 
    $tickets = [];
    if($get_customer_ticket=$con->query("SELECT * FROM ticket WHERE customer_id=$customer_id")){
        while($rows=$get_customer_ticket->fetch_array()){

            $priority_map = [
                1 => '<span class="badge bg-danger">Low</span>',
                2 => '<span class="badge bg-warning text-dark">Normal</span>',
                3 => '<span class="badge bg-primary">Standard</span>',
                4 => '<span class="badge bg-info">Medium</span>',
                5 => '<span class="badge bg-success">High</span>',
                6 => '<span class="badge bg-dark">Very High</span>'
            ];
          
            $priority = isset($priority_map[$rows['priority']]) ? $priority_map[$rows['priority']] : 'Unknown';
            $status='';
            if($rows['ticket_type']=='Active'){
                $status='<span class="badge bg-danger">Active</span>';
            }else if($rows['ticket_type']=='Closed'){
                $status='<span class="badge bg-success">Closed</span>';
            }else if($rows['ticket_type']=='Complete'){
                $status='<span class="badge bg-success">Complete</span>';
            }
            $tickets[] = [
                'id' => $rows['id'],
                'complain_type' => $con->query("SELECT topic_name as name FROM ticket_topic WHERE id = $rows[complain_type]")->fetch_array()['name'],
                'priority' => $priority,
                'parcent' => $rows['parcent'],
                'acctual_work' =>acctual_work($rows['startdate'], $rows['enddate']),
                'status' => $status,
            ];
        }
    }
    echo json_encode(['success' => true, 'data' => $tickets]);
    exit;
}
if (isset($_POST['get_area']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerId = $_POST['customer_id'];

    /* Get area_id from customers table*/
    $allCstmr = $con->query("SELECT `area` FROM customers WHERE id = $customerId");

    if ($allCstmr && ($rows = $allCstmr->fetch_array())) {
        $area_id = $rows['area'];

        /* Check if there is any data in working_group with this area_id*/
        $all_working_area = $con->query("SELECT id, group_name FROM working_group WHERE FIND_IN_SET('$area_id', area_id)");

        if ($all_working_area && $all_working_area->num_rows > 0) {
            /* If area_id matches, show only those records*/
            while ($rowsss = $all_working_area->fetch_array()) {
                echo '<option value="' . $rowsss['id'] . '">' . $rowsss['group_name'] . '</option>';
            }
        } else {
            /* If no matching area_id, show all records from working_group*/
            $all_working_area = $con->query('SELECT id, group_name FROM working_group');
            while ($rowsss = $all_working_area->fetch_array()) {
                echo '<option value="' . $rowsss['id'] . '">' . $rowsss['group_name'] . '</option>';
            }
        }
    } else {
        /* If no area_id found, show all records from working_group*/
        $all_working_area = $con->query('SELECT id, group_name FROM working_group');
        while ($rowsss = $all_working_area->fetch_array()) {
            echo '<option value="' . $rowsss['id'] . '">' . $rowsss['group_name'] . '</option>';
        }
    }
}

if (isset($_GET['get_single_ticket']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];

        $stmt = $con->prepare('SELECT * FROM ticket WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'message' => 'success',
                'data' => $row,
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Not Found',
            ]);
        }
    }
}
if (isset($_GET['get_working_group']) && $_GET['get_working_group'] == 'true') {
    $stmt2 = $con->prepare('SELECT * FROM working_group');
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $data = [];
    while ($row = $result2->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'name' => $row['group_name'],
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $data,
    ]);
}

if (isset($_GET['add_ticket_settings']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $tickId = $_POST['ticket_id'];
    $type = $_POST['ticket_type'];
    $progress = $_POST['progress'];
    $comment = $_POST['comment'];
    $assigned = $_POST['assigned'];

    $stmt1 = $con->prepare('INSERT INTO ticket_details (tcktid, status, datetm, comments, parcent, asignto) VALUES (?, ?, NOW(), ?, ?, ?)');
    $stmt1->bind_param('issss', $tickId, $type, $comment, $progress, $assigned);

    if ($stmt1->execute()) {
        /*Update Tickets*/
        $stmt2 = $con->prepare('UPDATE ticket SET notes=?, ticket_type = ?, asignto=?, parcent = ?, enddate = NOW() WHERE id = ?');
        $stmt2->bind_param('ssisi', $comment, $type, $assigned, $progress, $tickId);

        if ($stmt2->execute()) {
            echo json_encode(['success' => true, 'message' => 'Ticket settings updated successfully.']);

            /* Validate and Sanitize Input */
            $tick_Id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0; 

            /* Use Prepared Statement */
            $stmt = $con->prepare('SELECT * FROM ticket WHERE id = ?');
            $stmt->bind_param('i', $tick_Id);
            $stmt->execute();
            $get_ticket_info = $stmt->get_result();
            $stmt->close();

            if ($get_ticket_info && $get_ticket_info->num_rows > 0) {
                $row = $get_ticket_info->fetch_assoc();
                $customer_id = $row['customer_id'];
                $ticket_type = $row['ticket_type'];
                $complain_type = $row['complain_type'];
                $assigned = $row['asignto'];

                /* Fetch customer details  */
                $stmt = $con->prepare('SELECT username, mobile FROM customers WHERE id = ?');
                $stmt->bind_param('i', $customer_id);
                $stmt->execute();
                $customer_info = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $customer_name = $customer_info['username'] ?? 'Unknown';
                $customer_mobile = $customer_info['mobile'] ?? 'Unknown';

                /* Fetch complain type */
                $stmt = $con->prepare('SELECT topic_name FROM ticket_topic WHERE id = ?');
                $stmt->bind_param('i', $complain_type);
                $stmt->execute();
                $complain_type_info = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $complain_type_name = $complain_type_info['topic_name'] ?? 'Unknown';

                /* Fetch assigned group name */
                $stmt = $con->prepare('SELECT group_name FROM working_group WHERE id = ?');
                $stmt->bind_param('i', $assigned);
                $stmt->execute();
                $assigned_info = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $assigned_name = $assigned_info['group_name'] ?? 'Unknown';

                /* Construct WhatsApp Message */
                $whatsapp_message = "-----Ticket Info-----\nTicket ID: $tick_Id\nCustomer Name: $customer_name\nCustomer Mobile: $customer_mobile\nTicket Type: $ticket_type\nComplain Type: $complain_type_name\nAssigned To: $assigned_name";
                /* Send WhatsApp Message */
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://api.wassenger.com/v1/messages',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode([
                        'group' => '120363386291315088@g.us',
                        'message' => $whatsapp_message,
                    ]),
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Token: 33fe4de0b9752b595c2a8e5c22c8adf9ee81a7e413989a0700d0b61b985c8e2c96d50bbdbed32d7e'],
                ]);

                $response = curl_exec($curl);

                curl_close($curl);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update ticket.' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert ticket details.']);
    }

    /*Close the statement*/
    $stmt1->close();
    $stmt2->close();
}

if (isset($_GET['get_all_customer']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    /* Check if session is started*/
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    /* Set default condition */
    $condition = 'WHERE pop = ?';

    /* Set default pop_id value*/
    $pop_id = 0;

    if (isset($_SESSION['user_pop']) && !empty($_SESSION['user_pop'])) {
        $pop_id = $_SESSION['user_pop'];
    }

    if ($pop_id == 0) {
        $condition = '';
    }
    /*Prepare the SQL query */
    $stmt = $con->prepare("SELECT id, username, fullname, mobile FROM customers $condition");
    if (!empty($condition)) {
        $stmt->bind_param('i', $pop_id);
    }

    /* Execute the query*/
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $customers = [];

        /*Fetch results*/
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }

        /* Return JSON response*/
        echo json_encode(['success' => true, 'data' => $customers]);
    } else {
        /*If there's an error with the query*/
        echo json_encode(['success' => false, 'message' => 'Error retrieving customer data']);
    }

    /* Close the statement*/
    $stmt->close();
    exit();
}
if (isset($_POST['get_complain_type']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $con->query('SELECT id,topic_name FROM ticket_topic WHERE user_type=1');
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}
if (isset($_GET['add_ticket_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    /* Sanitize input values */
    $customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
    $ticket_for = isset($_POST['ticket_for']) ? trim($_POST['ticket_for']) : '';
    $complain_type = isset($_POST['ticket_complain_type']) ? trim($_POST['ticket_complain_type']) : '';
    $assigned = isset($_POST['assigned']) ? trim($_POST['assigned']) : '';
    $send_message = isset($_POST['send_message']) ? trim($_POST['send_message']) : '0';
    $note = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $priority = isset($_POST['ticket_priority']) ? trim($_POST['ticket_priority']) : '';

    if (empty($customer_id)) $errors['customer_id'] = 'Customer ID is required.';
    if (empty($ticket_for)) $errors['ticket_for'] = 'Ticket For is required.';
    if (empty($complain_type)) $errors['ticket_complain_type'] = 'Complain Type is required.';
    if (empty($assigned)) $errors['assigned'] = 'Assigned field is required.';
    if (empty($priority)) $errors['ticket_priority'] = 'Priority is required.';

    $stmt = $con->prepare('SELECT ticket_type FROM ticket WHERE customer_id = ?');
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $allComplete = true;
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['ticket_type'] !== 'Complete') {
                $allComplete = false;
                break;
            }
        }
    }

    if (!$allComplete) {
        $errors['customer_ticket'] = 'You already have a ticket.';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit();
    }

    $customerPopId = null;
    $customerAreaId = null;
    $mobile_number = null;

    if ($allCstmr = $con->query("SELECT pop, area, mobile FROM customers WHERE id=$customer_id")) {
        $customerData = $allCstmr->fetch_assoc();
        $customerPopId = $customerData['pop'];
        $customerAreaId = $customerData['area'];
        $mobile_number = $customerData['mobile'];
    }
    if ($send_message == 1 && !empty($mobile_number)) {
        $message = 'Your Complain is Created. Sorry for inconvenience, as soon as possible connection will be recovered.';
        send_message($mobile_number, $message);
    }
    $create_date = date('Y-m-d H:i:s');
    $stmt = $con->prepare("INSERT INTO ticket (`customer_id`, `ticket_type`, `asignto`, `ticketfor`, `pop_id`,`area_id`, `complain_type`, `startdate`, `enddate`, `user_type`, `notes`, `parcent`, `priority`, `create_date`) VALUES (?, 'Active', ?, ?, ?, ?, ?, NOW(), NULL, 1, ?, '0%', ?,'$create_date')");
    $stmt->bind_param('iisssssi', $customer_id, $assigned, $ticket_for, $customerPopId, $customerAreaId, $complain_type, $note, $priority);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket Added Successfully!',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error: ' . $stmt->error,
        ]);
    }

    $stmt->close();
    exit; 
}
/******** Ticket Update ******************/
if (isset($_GET['update_ticket_data']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $ticket_type = isset($_POST['ticket_type']) ? trim($_POST['ticket_type']) : '';
    $assigned = isset($_POST['assigned']) ? trim($_POST['assigned']) : '';
    $ticket_for = isset($_POST['ticket_for']) ? trim($_POST['ticket_for']) : '';
    $complain_type = isset($_POST['complain_type']) ? trim($_POST['complain_type']) : '';
    $from_date= isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
    $end_date= isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
    $user_type= isset($_POST['user_type']) ? trim($_POST['user_type']) : '1';
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    $errors = [];
    if (empty($id)) $errors['id'] = 'Ticket ID is required.';
    if (empty($ticket_type)) $errors['ticket_type'] = 'Ticket Type is required.';
    if (empty($assigned)) $errors['assigned'] = 'Assigned field is required.';
    if (empty($ticket_for)) $errors['ticket_for'] = 'Ticket For is required.';
    if (empty($complain_type)) $errors['complain_type'] = 'Complain Type is required.';
    if (empty($from_date)) $errors['from_date'] = 'From Date is required.';
    // if (!empty($end_date) && strtotime($end_date) < strtotime($from_date)) {
    //     $errors['end_date'] = 'End Date cannot be earlier than From Date.';
    // }
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors,
        ]);
        exit();
    }
    // echo 'okkkk'; exit; 
    $stmt = $con->prepare("UPDATE ticket SET ticket_type=?, asignto=?, ticketfor=?, complain_type=?, startdate=?, enddate=?, notes=?, user_type=? WHERE id=?");
    $stmt->bind_param('sisssssii', $ticket_type, $assigned, $ticket_for, $complain_type, $from_date, $end_date, $note, $user_type, $id);    
    $result = $stmt->execute();
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket updated successfully.',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Error: ' . $stmt->error,
        ]);
    }
    $stmt->close();
    exit();
}


/******** Add Ticket topic  Script ******************/
if (isset($_GET['add_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic_name = trim($_POST['topic_name']);

    /* Validate Topic Name  name */
    __validate_input($topic_name, 'Topic Name');
    /* Insert into  table */
    $result = $con->query("INSERT INTO ticket_topic(topic_name) VALUES('$topic_name')");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Pool!',
        ]);
        exit();
    }
}
/******** Update ticket topic  Script ******************/
if (isset($_GET['update_ticket_topic__data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $topic_name = trim($_POST['topic_name']);
    $id = trim($_POST['id']);
    /* Validate Topic Name  name */
    __validate_input($topic_name, 'Topic Name');
    /* Check if topic name already exists */
    $check_topic_name = $con->query("SELECT * FROM ticket_topic WHERE topic_name='$topic_name' AND id != '$id'");
    if ($check_topic_name->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Topic Name Already exists!',
        ]);
        exit();
    }
    /* Update the topic in the database */
    $result = $con->query("UPDATE ticket_topic SET topic_name='$topic_name' WHERE id='$id'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Topic!',
        ]);
        exit();
    }
}

if (isset($_GET['get_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM ticket_topic WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM ticket_topic");
    }

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => isset($_GET['id']) ? ($data[0] ?? []) : $data,
    ]);
    exit();
}
/*Delete ticket topic Script*/
if (isset($_GET['delete_ticket_topic_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM ticket_topic WHERE id='$id'");
    $con->close();
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete!',
        ]);
        exit();
    }
}

/******** Add Ticket Assign  Script ******************/
if (isset($_GET['add_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $assign_name = trim($_POST['name']);

    /* Validate Assign Name  name */
    __validate_input($assign_name, 'Assign Name');
    /* Insert into  table */
    $result = $con->query("INSERT INTO ticket_assign(name) VALUES('$assign_name')");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add Pool!',
        ]);
        exit();
    }
}
/******** Update ticket assign  Script ******************/
if (isset($_GET['update_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $assign_name = trim($_POST['name']);
    $id = trim($_POST['id']);
    /* Validate Assign Name  name */
    __validate_input($assign_name, 'Assign Name');
    /* Check if assign name already exists */
    $check_assign_name = $con->query("SELECT * FROM ticket_assign WHERE name='$assign_name' AND id != '$id'");
    if ($check_assign_name->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Assign Name Already exists!',
        ]);
        exit();
    }
    /* Update the assign in the database */
    $result = $con->query("UPDATE ticket_assign SET name='$assign_name' WHERE id='$id'");
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Updated successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update Topic!',
        ]);
        exit();
    }
}

if (isset($_GET['get_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = isset($_GET['id']) ? trim($_GET['id']) : '';
    $data = [];
    if (isset($id) && is_numeric($id)) {
        $result = $con->query("SELECT * FROM ticket_assign WHERE id='$id'");
    }else{
        $result = $con->query("SELECT * FROM ticket_assign");
    }

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => isset($_GET['id']) ? ($data[0] ?? []) : $data,
    ]);
    exit();
}
/*Delete ticket assign Script*/
if (isset($_GET['delete_ticket_assign_data']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID is required!',
        ]);
        exit();
    }
    $result = $con->query("DELETE FROM ticket_assign WHERE id='$id'");
    $con->close();
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Deleted successfully!',
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete!',
        ]);
        exit();
    }
}






if (isset($_POST['addTicketComment'])) {
    $tickId = $_POST['id'];
    $type = $_POST['type'];
    $progress = $_POST['progress'];
    $comment = $_POST['comment'];
    $assigned = $_POST['assigned'];
    $is_send_message =$_POST['is_send_message'];
    $customerMessage = $_POST['customerMessage'];


    /* Insert data into the database*/
    $sql = "INSERT INTO ticket_details (tcktid, status, datetm, comments, parcent, asignto) 
            VALUES ('$tickId', '$type', NOW(), '$comment', '$progress', '$assigned')";

    $con->query("UPDATE ticket SET parcent='$progress',asignto=$assigned WHERE id='$tickId'");

    if($progress == '100%' && $type == 'Complete'){
        $con->query("UPDATE ticket SET  ticket_type='Complete',parcent='100%',asignto=$assigned,enddate=NOW() WHERE id='$tickId' ");
    }

    /*-----------Send Customer Message --------------*/
    if($is_send_message == 'On' && !empty($customerMessage)){
        $customer_id = 0;
        if($get_customer_id=$con->query("SELECT customer_id FROM ticket WHERE id=$tickId")){
            if($get_customer_id->num_rows > 0){
                $customer_id = $get_customer_id->fetch_array()['customer_id'];
            }
        }
        $mobile_number = '';
        if($customer_id > 0){
            if($get_mobile=$con->query("SELECT mobile FROM customers WHERE id=$customer_id")){
                if($get_mobile->num_rows > 0){
                    $mobile_number = $get_mobile->fetch_array()['mobile'];
                }
            }
        }
        if(!empty($mobile_number)){
            $numbers = preg_split('/[ ,;]+/', $mobile_number);
            $firstNumber = isset($numbers[0]) ? trim($numbers[0]) : null;
            send_message($firstNumber, $customerMessage);
        }
    }
  
    if ($con->query($sql) === true) {
        echo 1;
        exit; 
    } else {
        echo 'Error: ' . $sql . '<br>' . $con->error;
    }

    exit; 
}



function acctual_work($startdate, $enddate)
{
    $startTimestamp = strtotime($startdate);
    $endTimestamp = strtotime($enddate);
    $time_difference = $endTimestamp - $startTimestamp;

    // Define time periods in seconds
    $units = [
        'year' => 365 * 24 * 60 * 60,
        'month' => 30 * 24 * 60 * 60,
        'week' => 7 * 24 * 60 * 60,
        'day' => 24 * 60 * 60,
        'hour' => 60 * 60,
        'minute' => 60,
        'second' => 1,
    ];

    // Determine the appropriate time period
    foreach ($units as $unit => $value) {
        if ($time_difference >= $value) {
            $count = floor($time_difference / $value);
            return $count . ' ' . $unit . ($count > 1 ? 's' : '') . ' ';
        }
    }

    return 'just now';
}


if (isset($_GET['get_single_ticket_at_client']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];

        $stmt = $con->prepare('SELECT * FROM client_tickets WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'message' => 'success',
                'data' => $row,
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Not Found',
            ]);
        }
    }
}

if (isset($_GET['add_client_ticket_settings']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $tickId = $_POST['ticket_id'];
    $type = $_POST['ticket_type'];
    $progress = $_POST['progress'];
    $comment = $_POST['comment'];
    //$assigned = $_POST["assigned"];

    $stmt2 = $con->prepare('UPDATE client_tickets SET notes=?, ticket_type = ?, parcent = ?, enddate = NOW() WHERE id = ?');
    $stmt2->bind_param('sssi', $comment, $type, $progress, $tickId);

    if ($stmt2->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket settings updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update ticket.' . $stmt->error]);
    }
    /*Close the statement*/
    $stmt2->close();
}


function __validate_input($value, $field)
{
    if (empty($value) || $value === '---Select---') {
        echo json_encode([
            'success' => false,
            'message' => '' . $field . ' is required!',
        ]);
        exit();
    }
}