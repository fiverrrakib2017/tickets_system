<div class="dropdown d-inline-block">
<button type="button"
    class="btn header-item noti-icon waves-effect position-relative"
    data-bs-toggle="dropdown"
    aria-haspopup="true"
    aria-expanded="false">

    <i class="mdi mdi-message-text-outline"></i>

    <!-- Badge -->
    <span class="badge bg-danger rounded-pill noti-dot">
        1
    </span>
</button>


    <!-- Dropdown -->
    <div class="dropdown-menu dropdown-menu-end chat-dropdown">
        <h6 class="dropdown-header">New Messages</h6>

        <!-- Chat Item -->
        <a href="chat_inbox.php?user=123" class="dropdown-item chat-item">
            <img src="http://103.146.16.154/profileImages/1752565976_494636897_10163477787563103_7852121941522030832_n.jpg" class="chat-avatar" alt="">
            <div class="chat-info">
                <div class="chat-name">Shafiul Bashar Sumon</div>
                <div class="chat-text">Hello, support lagbe...</div>
            </div>
            <div class="chat-time">2m</div>
        </a>

        <!-- Chat Item -->
        <a href="chat_inbox.php?user=124" class="dropdown-item chat-item">
            <img src="http://103.146.16.154/profileImages/1756013173_r.jpg" class="chat-avatar" alt="">
            <div class="chat-info">
                <div class="chat-name">Zunayed Hasan</div>
                <div class="chat-text">নেট এতো স্লো কেনো </div>
            </div>
            <div class="chat-time">10m</div>
        </a>

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