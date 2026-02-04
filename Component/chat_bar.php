<div class="dropdown d-inline-block">
<button type="button"
    class="btn header-item noti-icon waves-effect position-relative"
    data-bs-toggle="dropdown"
    aria-haspopup="true"
    aria-expanded="false">

    <i class="mdi mdi-message-text-outline"></i>

    <!-- Badge -->
    <span class="badge bg-danger rounded-pill noti-dot">
        <?php 
        $result = $con->query("
            SELECT COUNT(id) AS total 
            FROM live_chats 
            WHERE sender = 'customer' 
            AND is_seen = 0
        ");
        
        $row = $result->fetch_assoc();
        echo $unreadCount = $row['total'];
        
        function timeAgo($datetime){
            $time = strtotime($datetime);
            $diff = time() - $time;
        
            if($diff < 60) return $diff.'s';
            if($diff < 3600) return floor($diff/60).'m';
            if($diff < 86400) return floor($diff/3600).'h';
        
            return floor($diff/86400).'d';
        }
        
        
        ?>
    </span>
</button>


    <!-- Dropdown -->
<div class="dropdown-menu dropdown-menu-end chat-dropdown">
    <h6 class="dropdown-header">New Messages</h6>

    <?php
    $sql = "
       SELECT 
            lc.customer_id,
            lc.message,
            lc.created_at,
            c.customer_name AS fullname,
            c.profile_image AS photo
        FROM live_chats lc
        JOIN customers c ON c.id = lc.customer_id
        JOIN (
            SELECT customer_id, MAX(id) AS last_id
            FROM live_chats
            WHERE sender = 'customer'
            AND is_seen = 0
            GROUP BY customer_id
        ) x ON x.last_id = lc.id
        ORDER BY lc.created_at DESC
        LIMIT 5;
    ";

    $result = $con->query($sql);

    if($result->num_rows > 0):
        while($row = $result->fetch_assoc()):
    ?>

        <a href="chat_inbox.php?user=<?= $row['customer_id'] ?>"
           class="dropdown-item chat-item">

            <img src="http://103.112.206.139/assets/images/avatar.png"
                class="chat-avatar" alt="prodile image">

            <div class="chat-info">
                <div class="chat-name">
                    <?= htmlspecialchars($row['fullname']) ?>
                </div>
                <div class="chat-text">
                    <?= htmlspecialchars($row['message']) ?>
                </div>
            </div>

            <div class="chat-time">
                <?= timeAgo($row['created_at']); ?>
            </div>
        </a>

    <?php
        endwhile;
    else:
    ?>

        <div class="dropdown-item text-center text-muted">
            No new messages
        </div>

    <?php endif; ?>

    <div class="dropdown-divider"></div>

    <a href="chat_list.php" class="dropdown-item text-center text-primary">
        View All Messages
    </a>
</div>

</div>

<style>
     .noti-icon {
        position: relative;
    }

    .noti-icon i {
        font-size: 22px;
        color: #495057;
    }

    .noti-dot {
        position: absolute;
        top: 10px;
        right: 8px;
        font-size: 11px;
        padding: 2px 6px;
    }


    .chat-dropdown{
        width:320px;
        padding:0;
    }

    .chat-item{
        display:flex;
        align-items:center;
        gap:10px;
        padding:10px;
    }

    .chat-item:hover{
        background:#f8f9fa;
    }

    .chat-avatar{
        width:40px;
        height:40px;
        border-radius:50%;
        object-fit:cover;
    }

    .chat-info{
        flex:1;
        overflow:hidden;
    }

    .chat-name{
        font-weight:600;
        font-size:14px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .chat-text{
        font-size:13px;
        color:#6c757d;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .chat-time{
        font-size:12px;
        color:#adb5bd;
    }

</style>