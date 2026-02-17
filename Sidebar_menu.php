<?php
/* Fetch Others Links from Database */
$others_links = [];
$result = $con->query("SELECT name, link FROM others_link ORDER BY id ASC");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $others_links[] = $row;
    }
}
?>


<div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">
       
        <!-- Dashboard -->
        <li>
            <a href="index.php" class="waves-effect">
                <i class="mdi mdi-view-dashboard-outline"></i>
                <span>Dashboard</span>
            </a>
        </li>
         <select name="menu_select_box" id="menu_select_box" class="form-select" style="width: 100%;"></select> 

        <!-- Customers -->
        <li class="menu-title">Customer Management</li>

        <li>
            <a href="customers.php" class="waves-effect">
                <i class="mdi mdi-account-multiple-outline"></i>
                <span>Customers</span>
            </a>
        </li>
        <li>
            <a href="mac_reseller_customer.php" class="waves-effect">
                <i class="mdi mdi-account-multiple-outline"></i>
                <span>Mac Reseller</span>
            </a>
        </li>
        <li>
            <a href="bandwidth_customer.php" class="waves-effect">
                <i class="mdi mdi-account-multiple-outline"></i>
                <span>Bandwidth Customer</span>
            </a>
        </li>
        
        <li>
            <a href="create_customer.php" class="waves-effect">
                <i class="mdi mdi-account-plus-outline"></i>
                <span>Create Customer</span>
            </a>
        </li>

        <li>
            <a href="customer_type.php" class="waves-effect">
               <i class="mdi mdi-account-details-outline"></i>

                <span>Customer Types</span>
            </a>
        </li>
        <li>
            <a href="customer_links.php" class="waves-effect">
               <i class="mdi mdi-account-details-outline"></i>

                <span>Customer Links</span>
            </a>
        </li>

        <li>
            <a href="services.php" class="waves-effect">
                <i class="mdi mdi-cog-outline"></i>
                <span>Customer Services</span>
            </a>
        </li>

        <li>
            <a href="pop_branch.php" class="waves-effect">
                <i class="mdi mdi-office-building-outline"></i>
                <span>POP Branch</span>
            </a>
        </li>

        <!-- Tickets -->
        <?php if (
            isset($_SESSION['details']['role']) &&
            (
                $_SESSION['details']['role'] == 'Super Admin' ||
                $_SESSION['details']['role'] == 'Staff' ||
                $_SESSION['details']['role'] == 'Supports'
            )
        ): ?>

        <li class="menu-title">Ticket Management</li>

        <li>
            <a href="tickets.php" class="waves-effect">
                <i class="fas fa-ticket-alt"></i>
                <span>All Tickets</span>
            </a>
        </li>

        <!-- <li>
            <a href="pending_issues.php" class="waves-effect">
                <i class="mdi mdi-timer-sand"></i>
                <span>Pending Issues</span>
            </a>
        </li> -->

        <li>
            <a href="assign_members.php" class="waves-effect">
                <i class="mdi mdi-account-group-outline"></i>
                <span>Assigned Members</span>
            </a>
        </li>

        <li>
            <a href="ticketsTopic.php" class="waves-effect">
                <i class="mdi mdi-tag-multiple-outline"></i>
                <span>Ticket Topics</span>
            </a>
        </li>

        <li>
            <a href="ticket_reports.php" class="waves-effect">
                <i class="mdi mdi-file-chart-outline"></i>
                <span>Ticket Reports</span>
            </a>
        </li>

        <?php endif; ?>

        
        <!-- User Management -->
        <?php if (isset($_SESSION['details']['role']) && $_SESSION['details']['role'] === 'Super Admin') { ?>
            <li>
                <a href="users.php" class="waves-effect">
                    <i class="mdi mdi-account-multiple-outline"></i>
                    <span>User Management</span>
                </a>
            </li>
        <?php } ?>
        <li>
            <a href="value_add_service.php" class="waves-effect">
                <i class="mdi mdi-account-multiple-outline"></i>
                <span>Value Added Service</span>
            </a>
        </li>
        <!-- Others Links -->
        <li>
            <a href="others_link.php" class="waves-effect">
                <i class="mdi mdi-account-multiple-outline"></i>
                <span>Others Link</span>
            </a>
        </li>

        <?php if (!empty($others_links)): ?>

        <li class="menu-title">Useful Links</li>

        <?php foreach ($others_links as $link): ?>
            <li>
                <a href="<?php echo htmlspecialchars($link['link']); ?>"
                target="_blank"
                class="waves-effect d-flex align-items-center">
                    <i class="mdi mdi-open-in-new me-2"></i>
                    <span><?php echo htmlspecialchars($link['name']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>

        <?php endif; ?>



    </ul>
</div>
