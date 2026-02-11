<!-- JAVASCRIPT -->
<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/metismenu/metisMenu.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script> 
<script src="assets/libs/node-waves/waves.min.js"></script> 
<script src="assets/libs/select2/js/select2.min.js"></script>
<script src="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>

<!-- Required datatable js -->
<script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

<!-- Buttons examples -->
<script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
<script src="assets/libs/jszip/jszip.min.js"></script>
<script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script> 

<!-- Responsive examples -->
<script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- Toastr -->
<script src="js/toastr/toastr.min.js"></script>
<script src="js/toastr/toastr.init.js"></script>

<!-- Datatable init js -->
<script src="assets/js/pages/datatables.init.js"></script>

<!-- App Js -->
<script src="assets/js/app.js"></script>

<!-- Peity chart -->
<script src="assets/libs/peity/jquery.peity.min.js"></script> 

<!-- C3 Chart -->
<script src="assets/libs/d3/d3.min.js"></script>
<script src="assets/libs/c3/c3.min.js"></script> 

<!-- jQuery Knob -->
 <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>

<!-- Dashboard init -->
<script src="assets/js/pages/dashboard.init.js"></script>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<!-- Fluid Meter -->
 <script src="assets/js/js-fluid-meter.js"></script>
<!-- Form Advanced Init -->
 <!-- <script src="assets/js/pages/form-advanced.init.js"></script>  -->

<!-- Plugin Js for Charts -->
<script src="assets/libs/chartist/chartist.min.js"></script>
<script src="assets/libs/chartist-plugin-tooltips/chartist-plugin-tooltip.min.js"></script>

 <!-- form wizard -->
 <script src="assets/libs/jquery-steps/build/jquery.steps.min.js"></script>

<!-- form wizard init -->
<script src="assets/js/pages/form-wizard.init.js"></script>
<!-- Counter-Up -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
<script>
    $(document).ready(function () {
        $('.counter-value').counterUp({
            delay: 10,
            time: 1000
        });
    });
</script>

 <script type="text/javascript">
$(document).ready(function () {

   $('#menu_select_box').select2({

    placeholder: 'Search customer...',
    minimumInputLength: 1,
    allowClear: false,

    ajax: {
        url: 'include/customer_server.php',
        dataType: 'json',
        delay: 300,
        data: function (params) {
            return {
                search_customer: params.term,
                ajax_select: true
            };
        },
        processResults: function (response) {

            if (!response.success || !Array.isArray(response.data)) {
                return { results: [] };
            }

            let results = response.data.map(function (customer) {

                let firstPhone = '';

                if (customer.phones && typeof customer.phones === 'string') {
                    firstPhone = customer.phones.split(',')[0].trim();
                }

                return {
                    id: customer.id,
                    name: customer.customer_name || '',
                    email: customer.customer_email || '',
                    phone: firstPhone,
                    ping_ip: customer.ping_ip || '',
                    status: customer.ping_ip_status || 'offline'
                };
            });

            return { results: results };
        },
        cache: true
    },

    templateResult: function (data) {

        if (!data.id) return data.text;

        let online_offline_badge = data.status === 'online'
            ? '<span class="badge bg-success ms-2">Online</span>'
            : '<span class="badge bg-danger ms-2">Offline</span>';
        let _ip_badge = data.ping_ip ? `<span class="badge bg-info ms-1">${data.ping_ip}</span>`: '';

        return `
            <div>
                <strong>[${data.id}] ${data.name}</strong> ${   
                online_offline_badge} ${_ip_badge}
                <br>
                <small>
                    ${data.email || ''} 
                    ${data.phone ? '(' + data.phone + ')' : ''}
                </small>
            </div>
        `;
    },

    templateSelection: function (data) {
        return data.name ? `[${data.id}] ${data.name}` : data.text;
    },

    escapeMarkup: function (markup) {
        return markup; // HTML render করতে দেবে
    }

});


    // Redirect on select
    $('#menu_select_box').on('select2:select', function (e) {
        let customerId = e.params.data.id;
        if (customerId) {
            window.location.href = 'customer_profile.php?clid=' + customerId;
        }
    });

});
</script>  


