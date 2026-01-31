 <form method="post" action="include/save_mikrotik_config.php" class="d-flex align-items-center gap-2">

     <input type="text" name="mikrotik_ip" class="form-control form-control-sm" placeholder="MikroTik IP"
         style="width:150px;" value="<?= isset($customer['ping_ip']) ? htmlspecialchars($customer['ping_ip']) : '' ?>" required>

     <input type="text" name="mikrotik_port" class="form-control form-control-sm" placeholder="Port"
         style="width:90px;" value="<?= isset($customer['port']) ? htmlspecialchars($customer['port']) : '' ?>" required>

     <button type="button" id="save_mikrotik_info" class="btn btn-sm btn-success">
         Save
     </button>
 </form>

 <script>
     document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('save_mikrotik_info');
        const mikrotik_ip = document.querySelector('input[name="mikrotik_ip"]');
        const mikrotik_port = document.querySelector('input[name="mikrotik_port"]');

        btn.addEventListener('click', function() {
            const linkValue = mikrotik_ip.value.trim();
            const portValue = mikrotik_port.value.trim();
            const customerId = <?= (int) $customer['id'] ?>;

            if (!customerId) {
                alert('Invalid customer.');
                return;
            }

            fetch('include/customer_server.php?update_mikrotik_info=true', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    customer_id: customerId,
                    mikrotik_ip: linkValue,
                    mikrotik_port: portValue
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    toastr.success('MikroTik info updated successfully!');
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
