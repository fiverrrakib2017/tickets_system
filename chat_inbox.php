


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

                    <div class="chat-user active">
                        <img src="https://i.pravatar.cc/100?img=12">
                        <div>
                        <h6>Rakib Mahmud</h6>
                        <small class="text-muted">Internet slow...</small>
                        </div>
                    </div>

                    <div class="chat-user">
                        <img src="http://103.146.16.154/profileImages/1752565976_494636897_10163477787563103_7852121941522030832_n.jpg">
                        <div>
                        <h6>Shafiul Bashar Sumon</h6>
                        <small class="text-muted">sir...Support lagbe...</small>
                        </div>
                    </div>
                    <div class="chat-user">
                        <img src="https://i.pravatar.cc/100?img=32">
                        <div>
                        <h6>Customer #102</h6>
                        <small class="text-muted">Support lagbe...</small>
                        </div>
                    </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="chat-box">

                    <!-- HEADER -->
                    <div class="chat-box-header">
                        <img src="https://i.pravatar.cc/100?img=12">
                        <div>
                        <strong>Rakib Mahmud</strong><br>
                        <small class="text-success">Online</small>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="chat-box-body" id="chatBody">

                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Hello bhai, internet slow
                            <span class="meta">10:15 AM</span>
                        </div>
                        </div>

                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>

                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>

                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>
                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>
                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>
                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>
                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>
                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>
                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>
                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>
                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>
                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>
                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>
                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>
                        <div class="msg-row sent">
                        <div class="chat-message">
                            Router restart korechen?
                            <span class="meta">10:16 AM</span>
                        </div>
                        </div>
                        <div class="msg-row">
                        <img class="avatar-xs" src="https://i.pravatar.cc/100?img=12">
                        <div class="chat-message received">
                            Ha korechi
                            <span class="meta">10:17 AM</span>
                        </div>
                        </div>

                        <div id="scrollBottom" class="scroll-bottom">
                        <i class="mdi mdi-arrow-down"></i> Latest
                        </div>

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
    <script type="text/javascript"></script>
    <script type="text/javascript">
        const chatBody = document.getElementById('chatBody');
        const scrollBtn = document.getElementById('scrollBottom');
        const sendBtn = document.getElementById('sendBtn');
        const msgInput = document.getElementById('msgInput');

        /* start at bottom */
        chatBody.scrollTop = chatBody.scrollHeight;

        chatBody.addEventListener('scroll', () => {
        const threshold = 50; 
        const distanceFromBottom =
            chatBody.scrollHeight - chatBody.scrollTop - chatBody.clientHeight;

        if (distanceFromBottom > threshold) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
        });


        /* button click */
        scrollBtn.onclick = () => {
        chatBody.scrollTop = chatBody.scrollHeight;
        scrollBtn.style.display = 'none';
        };

        /* send message */
        sendBtn.onclick = sendMessage;
        msgInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') sendMessage();
        });

        function sendMessage() {
        const text = msgInput.value.trim();
        if (!text) return;

        const row = document.createElement('div');
        row.className = 'msg-row sent';
        row.innerHTML = `
            <div class="chat-message">
            ${text}
            <span class="meta">now</span>
            </div>
        `;
        chatBody.insertBefore(row, scrollBtn);
        msgInput.value = '';
        chatBody.scrollTop = chatBody.scrollHeight;
        }

    </script>
</body>

</html>
