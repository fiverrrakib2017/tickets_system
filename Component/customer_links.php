 <div class="col-12 bg-white p-0">
     <div class="d-flex justify-content-between align-items-center py-3 px-3 border-bottom border-dotted">
         <p class="mb-0 text-muted">
             <span class="fw-bold">
                 <input id="customer_link_input" class="form-control" placeholder="Enter Links"
                     value="<?= isset($customer['customer_link']) ? htmlspecialchars($customer['customer_link']) : '' ?>">
             </span>
         </p>

         <span class="fw-semibold text-dark">
             <button type="button" id="saveCustomerLinkBtn" class="btn btn-primary btn-sm">Save</button>
         </span>

         <script>
             document.addEventListener('DOMContentLoaded', function() {
                 const btn = document.getElementById('saveCustomerLinkBtn');
                 const input = document.getElementById('customer_link_input');

                 btn.addEventListener('click', function() {
                     const linkValue = input.value.trim();
                     const customerId = <?= (int) $customer['id'] ?>;

                     if (!customerId) {
                         alert('Invalid customer.');
                         return;
                     }

                     fetch('include/customer_server.php?update_customer_link=true', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json'
                             },
                             body: JSON.stringify({
                                 customer_id: customerId,
                                 customer_link: linkValue
                             })
                         })
                         .then(res => res.json())
                         .then(data => {
                             if (data.success) {
                                 toastr.success('Customer link updated successfully!');
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
