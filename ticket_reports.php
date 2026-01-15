<?php
include 'include/security_token.php';
include 'include/db_connect.php';
?>

<!doctype html>
<html lang="en">


<?php 

$extra_css = '<style>
         .report-header {
            text-align: center;
            padding: 15px;
        }
        .report-header img {
            height: 80px;
            width: 80px;
            margin-bottom: 10px;
        }
        .report-header h2 {
            font-weight: 100;
            margin-bottom: 5px;
        }
        .report-header p {
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
        }
    </style>';
require 'Head.php';


?>
<body data-sidebar="dark">




    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php $page_title = 'Ticket Reports';
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
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="d-flex align-items-end flex-wrap">
                                    <div class="mr-md-3 mr-xl-5">


                                        <div class="d-flex">
                                            <i class="mdi mdi-home text-muted hover-cursor"></i>
                                            <p class="text-muted mb-0 hover-cursor">&nbsp;/&nbsp;<a href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor">Ticket Reports</p>
                                        </div>


                                    </div>
                                    <br>


                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="row">
                        <div class="col-md-12 ">
                            <div class="card">
                            <div class="card-body  shadow">
                                <form class="row g-3 align-items-end" id="search_box">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="" class="form-label">Start Date: <span class="text-danger">*</span></label>
                                             <input class="start_date form-control" type="date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="" class="form-label">End Date: <span class="text-danger">*</span></label>
                                             <input class="end_date form-control" type="date">
                                        </div>
                                    </div>
                                    
               


                                    <div class="col-md-3 d-grid">
                                        <div class="form-group">
                                            <button type="button" name="search_btn" class="btn btn-success">
                                                <i class="fas fa-search me-1"></i> Search Now
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                                <div class="card-body " id="print_area">

                                    <!-- <div id="printHeader" class="report-header">
                                        <span>From Date :</span><br>
                                        <span>To Date   :</span><br>
                                        <span>Assign To : </span><br>
                                        <span>Completed : </span><br>
                                        <span>Active    : </span>
                                    </div>
                                    <div class="table-responsive responsive-table">
                                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No.</th> 
                                                    <th>Status</th> 
                                                    <th>Created</th>
                                                    <th>Customer Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Issues</th>
                                                    <th>Pop/Area</th>                                                   
                                                    <th>Assigned </th>
                                                    <th>Ticket For</th>
                                                    <th>Acctual Work</th>
                                                </tr>
                                            </thead>
                                            <tbody id="_data">
                                                <tr id="no-data">
                                                    <td colspan="10" class="text-center">No data available</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> -->

                                </div>
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
        $(document).ready(function() {
            $("#assign_id").select2({
                placeholder: "Select Assign",
                allowClear: false
            });
              /***Load Customer **/
            $("button[name='search_btn']").click(function() {
                var button = $(this); 

                button.html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`);
                button.attr('disabled', true);
                var start_date = $(".start_date").val();
                var end_date = $(".end_date").val();
                // var assign_id = $("#assign_id").val();

                /*Validation Input and select box*/
                if(start_date == ''|| end_date == '' ) {
                    toastr.error('Please fill all required fields.');
                    button.html('<i class="fas fa-search me-1"></i> Search Now');
                    button.attr('disabled', false);
                    return false;
                }
                
                // if(assign_id == '---Select---') {
                //     toastr.error('Please select a valid assign.');
                //     button.html('<i class="fas fa-search me-1"></i> Search Now');
                //     button.attr('disabled', false);
                //     return false;
                // }


                if ( $.fn.DataTable.isDataTable("#datatable1") ) {
                    $("#datatable1").DataTable().destroy();
                }
                $.ajax({
                    url: 'include/tickets_server.php?get_tickets_report_data=true',
                    type: 'POST',
                    dataType: 'json',
                    data: {start_date: start_date, end_date:end_date},
                    success: function(response) {
                        if(response.success==true){
                            
                            $("#print_area").removeClass('d-none');
                            $("#print_area").html(response.html);
                            $("#datatable1").DataTable({
                                "paging": true,
                                "searching": true,
                                "ordering": true,
                                "info": true
                            });
                            
                        }
                        
                        if(response.success==false) {
                            toastr.error(response.message);
                            $("#_data").html('<tr id="no-data"><td colspan="10" class="text-center">No data available</td></tr>');
                        }
                    },
                    complete: function() {
                        button.html('<i class="fas fa-search me-1"></i> Search Now');
                        button.attr('disabled', false);
                    }
                });
            });
        });

    </script>


</body>

</html>
