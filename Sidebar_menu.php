<div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">

        <!-- Dashboard -->
        <li>
            <a href="index.php" class="waves-effect">
                <i class="mdi mdi-view-dashboard-outline"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Customers -->
        <li class="menu-title">Customer Management</li>

        <li>
            <a href="customers.php" class="waves-effect">
                <i class="mdi mdi-account-multiple-outline"></i>
                <span>Customers</span>
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
            <a href="allTickets.php" class="waves-effect">
                <i class="fas fa-ticket-alt"></i>
                <span>All Tickets</span>
            </a>
        </li>

        <li>
            <a href="pending_issues.php" class="waves-effect">
                <i class="mdi mdi-timer-sand"></i>
                <span>Pending Issues</span>
            </a>
        </li>

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

    </ul>
</div>
