


<?php
date_default_timezone_set('Asia/Dhaka');
include 'include/security_token.php';
include 'include/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!doctype html>
<html lang="en">

<?php

require 'Head.php';

?>
<style>
/* ===============================
   CHAT FINAL STABLE DESIGN
================================ */

body {
  background:#eef1f7;
}

/* WRAPPER */
.chat-wrapper {
  height: calc(100vh - 40px);
  max-width: 1200px;
  margin: 20px auto;
  background: #f4f6fb;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  box-shadow: 0 10px 40px rgba(0,0,0,.08);
}

/* LEFT USERS */
.chat-users {
  width: 300px;
  background: #fff;
  border-right: 1px solid #e5e9f2;
  display: flex;
  flex-direction: column;
}

.chat-users-header {
  padding: 16px;
  font-weight: 600;
  border-bottom: 1px solid #e5e9f2;
}

.chat-user {
  padding: 12px 16px;
  display: flex;
  gap: 10px;
  align-items: center;
  cursor: pointer;
}

.chat-user:hover,
.chat-user.active {
  background: #f4f6fb;
}

.chat-user img {
  width: 42px;
  height: 42px;
  border-radius: 50%;
}

.chat-user h6 {
  margin: 0;
  font-size: 14px;
}

/* RIGHT CHAT */
.chat-box {
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* HEADER */
.chat-box-header {
  height: 70px;
  background: #fff;
  border-bottom: 1px solid #e5e9f2;
  padding: 12px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.chat-box-header img {
  width: 46px;
  height: 46px;
  border-radius: 50%;
}

/* BODY */
.chat-box-body {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: #f4f6fb;
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: relative;
}

/* MESSAGE ROW */
.msg-row {
  display: flex;
  gap: 10px;
  align-items: flex-end;
  max-width: 100%;
}

.msg-row.sent {
  justify-content: flex-end;
}

/* AVATAR */
.avatar-xs {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  flex-shrink: 0;
}

/* MESSAGE */
.chat-message {
  max-width: 70%;
  padding: 12px 14px;
  border-radius: 14px;
  font-size: 14px;
  line-height: 1.45;
  word-break: break-word;
}

.chat-message.received {
  background: #fff;
  border: 1px solid #e5e9f2;
  border-bottom-left-radius: 4px;
}

.msg-row.sent .chat-message {
  background: #0d6efd;
  color: #fff;
  border-bottom-right-radius: 4px;
}

.meta {
  font-size: 11px;
  opacity: .6;
  margin-top: 4px;
  display: block;
}

/* FOOTER */
.chat-box-footer {
  height: 70px;
  background: #fff;
  border-top: 1px solid #e5e9f2;
  padding: 12px;
  display: flex;
  gap: 10px;
}

/* SCROLL BUTTON - BOTTOM CENTER */
.scroll-bottom {
  position: absolute;
  left: 50%;
  bottom: 10px;
  transform: translateX(-50%);
  background: green;
  color: #fff;
  padding: 8px 16px;
  border-radius: 999px;
  font-size: 13px;
  display: none;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(0,0,0,.25);
  z-index: 10;
}


.call-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.call-btn i {
  font-size: 18px;
}


</style>
<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Inbox';
        include 'Header.php'; ?>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <?php include 'Sidebar_menu.php'; ?>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="chat-wrapper">

                    <!-- LEFT -->
                    <div class="chat-users">
                        <div class="chat-users-header">Messages</div>

                        <?php
                        $_active_customer_id = null;

                        /*-------------Others Customers-------------*/
                        if (
                            isset($_GET['is_message'], $_GET['customer_id']) &&
                            $_GET['is_message'] == true
                        ) {
                            $_active_customer_id = (int) $_GET['customer_id'];

                            $sql = "
                                SELECT 
                                    c.id,
                                    c.customer_name,
                                    c.profile_image,
                                    lc.message
                                FROM customers c
                                LEFT JOIN live_chats lc 
                                    ON lc.customer_id = c.id
                                WHERE c.id = $_active_customer_id
                                ORDER BY lc.id DESC
                                LIMIT 1
                            ";

                            $result = $con->query($sql);
                            if ($result && $row = $result->fetch_assoc()):
                        ?>

                            <div class="chat-user active">
                                <img src="<?= !empty($row['profile_image']) 
                                    ? 'profileImages/'.$row['profile_image'] 
                                    : 'assets/images/avatar.png'; ?>">

                                <div>
                                    <h6><?= htmlspecialchars($row['customer_name']) ?></h6>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($row['message'] ?? 'No messages yet') ?>
                                    </small>
                                </div>
                            </div>

                        <?php
                            endif;
                        }
                        ?>

                        <?php
                        /*-------------Others Customers-------------*/
                        $sql = "
                            SELECT 
                                c.id,
                                c.customer_name,
                                c.profile_image,
                                lc.message
                            FROM live_chats lc
                            JOIN customers c ON c.id = lc.customer_id
                            JOIN (
                                SELECT customer_id, MAX(id) last_id
                                FROM live_chats
                                GROUP BY customer_id
                            ) x ON x.last_id = lc.id
                        ";

                        if ($_active_customer_id !== null) {
                            $sql .= " WHERE c.id != $_active_customer_id ";
                        }

                        $sql .= " ORDER BY lc.id DESC";

                        $result = $con->query($sql);

                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>

                            <a href="chat_inbox.php?is_message=true&customer_id=<?= $row['id'] ?>"
                            class="chat-user">

                                <img src="<?= !empty($row['profile_image']) 
                                    ? 'profileImages/'.$row['profile_image'] 
                                    : 'assets/images/avatar.png'; ?>">

                                <div>
                                    <h6><?= htmlspecialchars($row['customer_name']) ?></h6>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($row['message']) ?>
                                    </small>
                                </div>
                            </a>

                        <?php
                            endwhile;
                        else:
                        ?>

                        <div class="chat-user text-muted">
                            No conversations found
                        </div>

                    <?php endif; ?>

                    </div>


                        <!-- RIGHT -->
                        <div class="chat-box">

                            <!-- HEADER -->
                            <?php
                            $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

                                $customer = null;
                                if ($customer_id > 0) {
                                    $res = $con->query("SELECT customer_name, profile_image FROM customers WHERE id = $customer_id");
                                    $customer = $res->fetch_assoc();
                                }
                                ?>

                                <div class="chat-box-header d-flex align-items-center">

                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= !empty($customer['profile_image']) 
                                            ? 'profileImages/'.$customer['profile_image'] 
                                            : 'assets/images/avatar.png' ?>">
                                        <div>
                                            <strong><?= htmlspecialchars($customer['customer_name'] ?? 'Select Customer') ?></strong><br>
                                            <small class="text-success">Online</small>
                                        </div>
                                    </div>
                                    <!-- Call Icons -->
                                    <div class="ms-auto d-flex align-items-center gap-2">
                                        <button class="btn btn-light btn-sm call-btn" title="Audio Call">
                                        <i class="mdi mdi-phone"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm call-btn" title="Video Call">
                                        <i class="mdi mdi-video"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm call-btn" title="More">
                                        <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                    </div>

                                </div>



                            <!-- BODY -->
                            <div class="chat-box-body" id="chatBody">

                            <?php
                            if ($customer_id > 0) {
                                $sql = "
                                    SELECT sender, message, created_at
                                    FROM live_chats
                                    WHERE customer_id = $customer_id
                                    ORDER BY id ASC
                                ";
                                $msgs = $con->query($sql);

                                while ($msg = $msgs->fetch_assoc()):
                                    $isAdmin = $msg['sender'] === 'admin';
                            ?>

                                <div class="msg-row <?= $isAdmin ? 'sent' : '' ?>">
                                    <?php if (!$isAdmin): ?>
                                        <img class="avatar-xs" src="assets/images/avatar.png">
                                    <?php endif; ?>

                                    <div class="chat-message <?= $isAdmin ? '' : 'received' ?>">
                                        <?= htmlspecialchars($msg['message']) ?>
                                        <span class="meta">
                                            <?= date('h:i A', strtotime($msg['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>

                            <?php endwhile; } ?>

                            </div>


                            <!-- FOOTER -->
                            <div class="chat-box-footer">
                                <input id="msgInput" type="text" class="form-control" placeholder="Type message...">
                                <button id="sendBtn" class="btn btn-primary">
                                    <i class="mdi mdi-send"></i>
                                </button>
                            </div>


                        </div>
                    </div>
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php include 'Footer.php'; ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>

    <script type="text/javascript">
        document.getElementById('sendBtn').addEventListener('click', sendMessage);
        document.getElementById('msgInput').addEventListener('keypress', function(e){
            if(e.key === 'Enter'){
                sendMessage();
            }
        });

        function sendMessage(){
            let msgInput = document.getElementById('msgInput');
            let message = msgInput.value.trim();
            let customerId = <?= (int)$customer_id ?>;

            if(message === '' || customerId === 0){
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "include/send_message_server.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function(){
                if(this.status === 200){
                    let res = JSON.parse(this.responseText);

                    if(res.status === 'success'){
                        let chatBody = document.getElementById('chatBody');

                        let html = `
                        <div class="msg-row sent">
                            <div class="chat-message">
                                ${res.message}
                                <span class="meta">${res.time}</span>
                            </div>
                        </div>
                        `;

                        chatBody.insertAdjacentHTML('beforeend', html);
                        chatBody.scrollTop = chatBody.scrollHeight;
                        msgInput.value = '';
                    }
                }
            };

            xhr.send(
                "customer_id=" + customerId +
                "&message=" + encodeURIComponent(message)
            );
        }
    </script>

</body>

</html>
