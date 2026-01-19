
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
<div id="ping_result" class="mt-3">

    <div class="card border shadow-sm">
        <div class="card-body p-3">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 text-muted">
                    <i class="mdi mdi-lan-connect me-1 text-primary"></i>
                    Ping Status
                </h6>

                <span class="badge bg-success px-3 py-1">
                    ONLINE
                </span>
            </div>

            <hr class="my-2">

            <!-- Packet Info -->
            <div class="row text-center mb-2">
                <div class="col-4">
                    <div class="text-muted small">Sent</div>
                    <div class="fw-bold">
                        <?= $ping['sent'] ?? 'N/A'; ?>
                    </div>
                </div>

                <div class="col-4">
                    <div class="text-muted small">Received</div>
                    <div class="fw-bold text-success">
                        <?= $ping['received'] ?? 'N/A'; ?>
                    </div>
                </div>

                <div class="col-4">
                    <div class="text-muted small">Lost</div>
                    <div class="fw-bold text-danger">
                        <?= $ping['lost'] ?? 'N/A'; ?>
                    </div>
                </div>
            </div>

            <!-- Latency -->
            <div class="row text-center">
                <div class="col-4">
                    <div class="text-muted small">Min (ms)</div>
                    <div class="fw-semibold">
                        <?= $ping['min'] ?? 'N/A'; ?>
                    </div>
                </div>

                <div class="col-4">
                    <div class="text-muted small">Max (ms)</div>
                    <div class="fw-semibold">
                        <?= $ping['max'] ?? 'N/A'; ?>
                    </div>
                </div>

                <div class="col-4">
                    <div class="text-muted small">Avg (ms)</div>
                    <div class="fw-semibold text-primary">
                        <?= $ping['avg'] ?? 'N/A'; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>



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