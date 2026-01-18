 <div class="col-12 bg-white p-0">
     <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
         <p class="mb-0 text-muted">
             <span class="fw-bold">
                 <input id="customer_ip_input" class="form-control" placeholder="Enter IP Address"
                     value="<?= isset($customer['ping_ip']) ? htmlspecialchars($customer['ping_ip']) : '' ?>">
             </span>
         </p>

         <span class="fw-semibold text-dark">
             <button type="button" id="save_customer_ping_ip_btn" class="btn btn-primary btn-sm">Save</button>
         </span>

         <script>
             document.addEventListener('DOMContentLoaded', function() {
                 const btn = document.getElementById('save_customer_ping_ip_btn');
                 const input = document.getElementById('customer_ip_input');

                 btn.addEventListener('click', function() {
                     const ip_value = input.value.trim();
                     const customerId = <?= (int) $customer['id'] ?>;

                     if (!customerId) {
                         alert('Invalid customer.');
                         return;
                     }

                     fetch('include/customer_server.php?update_customer_ping_ip=true', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json'
                             },
                             body: JSON.stringify({
                                 customer_id: customerId,
                                 customer_ping_ip: ip_value
                             })
                         })
                         .then(res => res.json())
                         .then(data => {
                             if (data.success) {
                                 toastr.success('Customer IP updated successfully!');
                             } else {
                                 toastr.error('Error: ' + data.message);
                             }
                         })
                         .catch(err => {
                             toastr.error('Something went wrong.');
                         });
                 });
             });
         </script>


     </div>
 </div>
