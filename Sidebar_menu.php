<?php
/*------ Fetch Others Links from Database --------*/
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

        <!-- Customer Management  -->
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="mdi mdi-account-group-outline"></i>
                <span>Customers</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="customers.php">Customers</a></li>
                <li><a href="mac_reseller_customer.php">Mac Reseller</a></li>
                <li><a href="bandwidth_customer.php">Bandwidth Customer</a></li>
                <li><a href="create_customer.php">Create Customer</a></li>
                <li><a href="customer_type.php">Customer Types</a></li>
                <li><a href="customer_links.php">Customer Links</a></li>
                <li><a href="services.php">Customer Services</a></li>
            </ul>
        </li>

        <!-- Device Management (Dropdown) -->
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="fas fa-server"></i>
                <span>Device Management</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="device-dashboard.php">Device Dashboard</a></li>
                <li><a href="devices.php">All Devices</a></li>
                <li><a href="add-device.php">Add Device</a></li>
                <li><a href="device-groups.php">Device Groups</a></li>
            </ul>
        </li>

        <!-- Monitoring (Dropdown) -->
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="fas fa-chart-line"></i>
                <span>Monitoring</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="device-status.php">Device Status</a></li>
                <li><a href="interfaces.php">Interfaces</a></li>
                <li><a href="traffic.php">Traffic</a></li>
                <li><a href="device-resources.php">CPU & Memory</a></li>
                <li><a href="sensors.php">Sensors</a></li>
            </ul>
        </li>

        <!-- Alerts (Dropdown) -->
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="fas fa-bell"></i>
                <span>Alerts</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="active-alerts.php">Active Alerts</a></li>
                <li><a href="alert-history.php">Alert History</a></li>
                <li><a href="alert-rules.php">Alert Rules</a></li>
            </ul>
        </li>

        <!-- Discovery (Dropdown) -->
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="fas fa-search"></i>
                <span>Discovery</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="snmp-discovery.php">SNMP Discovery</a></li>
                <li><a href="auto-discovery.php">Auto Discovery</a></li>
                <li><a href="discovery-logs.php">Discovery Logs</a></li>
            </ul>
        </li>

        <!-- Device Settings (Dropdown) -->
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="fas fa-cog"></i>
                <span>Device Settings</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="snmp-settings.php">SNMP Settings</a></li>
                <li><a href="polling-settings.php">Polling Settings</a></li>
                <li><a href="device-types.php">Device Types</a></li>
            </ul>
        </li>

        <!-- POP Branch -->
        <li>
            <a href="pop_branch.php" class="waves-effect">
                <i class="mdi mdi-office-building-outline"></i>
                <span>POP Branch</span>
            </a>
        </li>

        <!-- Tickets Management (Dropdown with Role Check) -->
        <?php if (
            isset($_SESSION['details']['role']) &&
            (
                $_SESSION['details']['role'] == 'Super Admin' ||
                $_SESSION['details']['role'] == 'Staff' ||
                $_SESSION['details']['role'] == 'Supports'
            )
        ): ?>
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="fas fa-ticket-alt"></i>
                <span>Ticket Management</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                <li><a href="tickets.php">All Tickets</a></li>
                <li><a href="ticketsTopic.php">Ticket Topics</a></li>
                <li><a href="ticket_notes.php">NOC Note</a></li>
                <li><a href="internal_tickets.php">NOC & Backbone</a></li>
                <li><a href="upstream.php">Upstream</a></li>
                <li><a href="tickets_category.php">Tickets Category</a></li>
                <li><a href="tickets_sub_category.php">Tickets Sub Category</a></li>
                <li><a href="ticket_reports.php">Ticket Reports</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- System & Others -->
        <li class="menu-title">System Management</li>

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
                <i class="mdi mdi-briefcase-plus-outline"></i>
                <span>Value Added Service</span>
            </a>
        </li>
        
        <li>
            <a href="others_link.php" class="waves-effect">
                <i class="mdi mdi-link-variant"></i>
                <span>Others Link</span>
            </a>
        </li>

        <li>
            <a href="database_backup.php" class="waves-effect">
                <i class="mdi mdi-database-export-outline"></i>
                <span>Database Backup</span>
            </a>
        </li>

        <!-- Dynamic Useful Links -->
        <?php if (!empty($others_links)): ?>
        <li class="menu-title">Useful Links</li>
        <?php foreach ($others_links as $link): ?>
            <li>
                <a href="<?php echo htmlspecialchars($link['link']); ?>" target="_blank" class="waves-effect">
                    <i class="mdi mdi-open-in-new"></i>
                    <span><?php echo htmlspecialchars($link['name']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
        <?php endif; ?>

    </ul>
</div>