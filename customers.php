<?php
include 'include/security_token.php';
include 'include/db_connect.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

?>

<!doctype html>
<html lang="en">
<?php 
$extra_css  = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
require 'Head.php';

?>

<body data-sidebar="dark">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Customers';
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
                                            <p class="text-primary mb-0 hover-cursor">&nbsp;/&nbsp;<a href="index.php">Dashboard</a>&nbsp;/&nbsp;
                                            </p>
                                            <p class="text-primary mb-0 hover-cursor">Customers</p>
                                        </div>
                                    </div>
                                    <br>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                               <div class="card-header customer_card_header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background-color: white;">
                                <!-- Add Customer Button -->
                                <button data-bs-toggle="modal" data-bs-target="#addCustomerModal" type="button" class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i> Add New Customer
                                </button>
                            </div>

                                <div class="card-body">
                                    <div class="table-responsive ">
                                        <table id="customers_table" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Mobile No.</th>
                                                    <th>Area/Location</th>
                                                     <th>Create Date</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="customer-list">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class=" flex-wrap justify-content-between align-items-center">


                                    <button type="button" class="btn btn-success mb-2" name="export_to_excel">
                                        <img src="https://img.icons8.com/?size=100&id=117561&format=png&color=000000"
                                            class="img-fluid icon-img" style="height: 20px !important;">
                                        
                                    </button>

                                    <button type="button" onclick="printSelectedRows()" class="btn btn-danger mb-2">
                                        <i class="fas fa-print"></i>&nbsp;
                                    </button>
                                    <button type="button" class="btn btn-danger mb-2" name="customer_delete_btn">
                                        <i class="fas fa-trash"></i>&nbsp; 
                                    </button>

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

    <!-- Modal for Send Message -->
     <?php include 'modal/message_modal.php'; ?>
   
    <!------------------ Modal Customer Tickets ------------------>
    <?php require 'modal/tickets_modal.php'; ?>
    <?php require 'modal/customer_modal.php'; ?>
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>
    <script src="js/tickets.js"></script>
    <script src="js/Ajax.js"></script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <script type="text/javascript">
        var table;
        $(document).ready(function() {
            var checkedBoxes = {};
            table = $('#customers_table').DataTable({
                "searching": true,
                "paging": true,
                "info": true,
                "order": [
                    [0, "desc"]
                ],
                "lengthChange": true,
                "processing": true,
                "serverSide": true,
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                }],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                select: {
                    style: 'os',
                    selector: 'td.select-checkbox'
                },
                "zeroRecords": "No matching records found",
                "ajax": {
                    url: "include/customer_server_new.php?get_customers_data=true",
                    type: 'GET',
                    cache: true,
                    data: function(d) {

                    },
                    beforeSend: function() {
                        $(".dataTables_empty").html(
                            '<img src="assets/images/loading.gif" style="background-color: transparent"/>'
                            );
                    },

                },
                "drawCallback": function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                    /* Restore checked state*/
                    $('.customer-checkbox').each(function() {
                        var id = $(this).val();
                        if (checkedBoxes[id]) {
                            $(this).prop('checked', true);
                        }
                    });
                },
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        titleAttr: 'Export to Excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        titleAttr: 'Print',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],
            });
            table.buttons().container().appendTo($('#export_buttonscc'));
            /* Check/Uncheck All Checkboxes*/
            $(document).on('change', '#checkedAll', function() {
                var isChecked = $(this).is(':checked');
                $('.customer-checkbox').prop('checked', isChecked);
                $('.customer-checkbox').each(function() {
                    var id = $(this).val();
                    checkedBoxes[id] = isChecked;
                });
            });

            /* Handle Individual Checkbox Change*/
            $(document).on('cilck', '.customer-checkbox', function() {
                var id = $(this).val();
                checkedBoxes[id] = $(this).is(':checked');

                var allChecked = $('.customer-checkbox:checked').length === $('.customer-checkbox').length;
                $('#checkedAll').prop('checked', allChecked);
            });
        });

        /* POP filter change event*/
        $(document).on('change', '.pop_filter', function() {

            var pop_filter_result = $('.pop_filter').val() == null ? '' : $('.pop_filter').val();

            // table.columns(9).search(pop_filter_result).draw();
            table.ajax.reload(null, false);

        });
        $(document).on('click', 'button[name="export_to_excel"]', function () {
            const headers = [
                'ID', 'Name', 'Status', 'Package', 'Amount', 'Create Date', 'Expired Date',
                'Username', 'Mobile no.', 'POP/Branch', 'Area/Location', 'Liabilities', 
                'Total Usages'
            ];

            let csvRows = [];
            csvRows.push(headers.join(",")); 

            let nameIndex = null;

            $('#customers_table thead th').each(function (idx) {
                const txt = $(this).text().trim().toLowerCase();
                if (txt.includes('name')) {
                    nameIndex = idx;
                    return false;
                }
            });

            if (nameIndex === null) {
                nameIndex = 1;
            }

            $('#customers_table tbody tr').each(function () {
                let $tds = $(this).find('td');
                if ($tds.length < 2) return;

                let rowCells = [];
                let extractedStatus = ""; 

                for (let i = 1; i < $tds.length - 1; i++) {
                    let cell = $tds.eq(i).text().trim();

                    if (i === nameIndex) {
                        let originalText = cell;

                        let statusMatch = originalText.match(/\((.*?)\)/);
                        if (statusMatch) {
                            extractedStatus = statusMatch[1].trim();
                        }

                        cell = cell.replace(/\(.*?\)/g, '').trim();
                        cell = cell.replace(/\s{2,}/g, ' ').trim();

                        rowCells.push('"' + cell.replace(/"/g, '""') + '"');

                        rowCells.push('"' + extractedStatus.replace(/"/g, '""') + '"');
                    } 
                    else {
                        rowCells.push('"' + cell.replace(/"/g, '""') + '"');
                    }
                }

                csvRows.push(rowCells.join(","));
            });

            const csvString = '\uFEFF' + csvRows.join("\r\n");

            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'customers.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });







        function printTable() {
            var divToPrint = document.getElementById('customers_table');
            var newWin = window.open('', '_blank');
            newWin.document.write('<html><head><title>Customer</title>');
            newWin.document.write('<style>');
            newWin.document.write('table { width: 100%; border-collapse: collapse; }');
            newWin.document.write('table, th, td { border: 1px solid black; padding: 10px; text-align: left; }');
            newWin.document.write('a { text-decoration: none; color: black; }');
            newWin.document.write('</style></head><body>');
            newWin.document.write(divToPrint.outerHTML);
            newWin.document.write('</body></html>');
            newWin.document.close();
            newWin.focus();
            newWin.print();
            newWin.close();
        }

        function printSelectedRows() {
            let selectedContent = `
                <table class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Expired Date</th>
                            <th>Expired Date</th>
                            <th>User Name</th>
                            <th>Mobile no.</th>
                            <th>POP/Branch</th>
                            <th>Area/Location</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            let hasSelectedRows = false;

            $('#customers_table tbody tr').each(function() {
                let checkbox = $(this).find('input[type="checkbox"]');
                if (checkbox.is(':checked')) {
                    hasSelectedRows = true;
                    selectedContent += "<tr>";
                    $(this).find('td').each(function() {
                        selectedContent += `<td>${$(this).text().trim()}</td>`;
                    });
                    selectedContent += "</tr>";
                }
            });

            if (!hasSelectedRows) {
                toastr.error("Please select at least one row to print.");
                return;
            }

            selectedContent += `
                    </tbody>
                </table>
            `;


            let newWin = window.open('', '_blank');
            newWin.document.write('<html><head><title>Customer Data</title>');
            newWin.document.write('<style>');
            newWin.document.write('table { width: 100%; border-collapse: collapse; }');
            newWin.document.write('table, th, td { border: 1px solid black; padding: 10px; text-align: left; }');
            newWin.document.write('</style></head><body>');
            newWin.document.write(selectedContent);
            newWin.document.write('</body></html>');
            newWin.document.close();
            newWin.focus();
            newWin.print();
            newWin.close();
        }


        /************************** Add ticket Modal Script **************************/
        ticket_modal();
        loadCustomers();
        ticket_assign();
        ticket_complain_type();
       
       /************************** Customer Delete Section **************************/
        $(document).on('click', 'button[name="customer_delete_btn"]', function(e) {
            e.preventDefault();

            var $button = $(this);

            var customers = [];
            $(".checkSingle:checked").each(function() {
                customers.push($(this).val());
            });

            if (customers.length === 0) {
                toastr.error("Please select at least one customer");
                return;
            }

            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with deletion
                    $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                    $.ajax({
                        url: "",
                        method: 'POST',
                        data: {
                            action: 'customer_delete',
                            customers: JSON.stringify(customers)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#customers_table').DataTable().ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            toastr.error("Request failed.");
                        },
                        complete: function() {
                            $button.prop('disabled', false).html('<i class="fas fa-trash"></i>&nbsp;');
                        }
                    });
                }
            });
        });


    </script>
</body>

</html>
