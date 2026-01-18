
 <form action="#" class="row g-2 align-items-center">

    <div class="col-md-7 d-flex align-items-center gap-2">
        <i class="fas fa-globe text-muted"></i>
        <label class="small text-muted mb-0">IP Address</label>
    </div>

    <div class="col-md-8">
        <input type="text"
            id="ping_ip"
            class="form-control"
            placeholder="e.g. 192.168.1.1"
            value="<?= htmlspecialchars($customer['ping_ip']); ?>">
    </div>

    <div class="col-md-4 text-end">
        <button type="button"
                id="save_ping_ip_btn"
                class="btn btn-sm btn-outline-primary px-3">
            <i class="fas fa-save"></i> Save
        </button>
    </div>

</form>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('save_ping_ip_btn');
        const input = document.getElementById('ping_ip');

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