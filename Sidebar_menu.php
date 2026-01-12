 <div id="sidebar-menu">
     <!-- Left Menu Start -->
     <ul class="metismenu list-unstyled" id="side-menu">
            

            <li>
             <a href="index.php" class="waves-effect">
                 <i class="mdi mdi-view-dashboard"></i>
                 <span> Dashboard </span>
             </a>
            </li>
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="mdi mdi-account-check"></i>
                    <span>Customer </span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="customers.php">Customer List</a></li>                   
                    <li><a href="customer_type.php">Customer Type</a></li>                   
                    <li><a href="services.php">Customer Services</a></li>                   
                    <li><a href="pop_branch.php">POP Branch</a></li>                   
                    
                </ul>
            </li>

         <?php if (isset($_SESSION['details']['role']) && $_SESSION['details']['role'] == 'Super Admin' || $_SESSION['details']['role']=='Staff' || $_SESSION['details']['role']=='Supports'): ?>
         <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="fas fa-ticket-alt"></i><span>Tickets</span></a>
             <ul class="sub-menu" aria-expanded="false">
                 <li><a href="allTickets.php"> List All Tickets </a></li>
                 <li><a href="ticket_reports.php">Tickets Report</a></li>
                 <li><a href="ticketsTopic.php"> Ticket Topics </a></li>
                 <li><a href="pending_issues.php"> Pending Issues </a></li>
                 <!-- <li><a href="member_group.php"> Member Group </a></li> -->
                 <li><a href="assign_members.php"> Assigned Members </a></li>
             </ul>
         </li>
         <?php endif; ?>
     </ul>
 </div>
