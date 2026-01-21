
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
<?php
$status = $customer['ping_ip_status'] ?? 'unknown';
$isOnline = ($status === 'online');

$badgeClass = $isOnline ? 'bg-success' : 'bg-danger';
$iconClass  = $isOnline ? 'mdi-wifi' : 'mdi-wifi-off';
$statusText = strtoupper($status);
?>

<div id="ping_result" class="mt-3">
    <div class="">
        <div class="card-body p-3">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="mb-0 fw-semibold text-secondary">
                        <i class="mdi mdi-lan-connect me-1 text-primary"></i>
                        Ping Status
                    </h6>
                    <small class="text-muted">Real-time connectivity check</small>
                </div>

                <span class="badge <?= $badgeClass; ?> px-3 py-2 d-flex align-items-center gap-1">
                    <i class="mdi <?= $iconClass; ?>"></i>
                    <?= $statusText; ?>
                </span>
            </div>

            <!-- Packet Info -->
            <div class="row text-center g-2 mb-3">
                <div class="col-4">
                    <div class="p-2 bg-light rounded">
                        <div class="text-muted small">Sent</div>
                        <div class="fw-bold">
                            <?= htmlspecialchars($customer['ping_sent'] ?? 'N/A'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="p-2 bg-light rounded">
                        <div class="text-muted small">Received</div>
                        <div class="fw-bold text-success">
                            <?= htmlspecialchars($customer['ping_received'] ?? 'N/A'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="p-2 bg-light rounded">
                        <div class="text-muted small">Lost</div>
                        <div class="fw-bold text-danger">
                            <?= htmlspecialchars($customer['ping_lost'] ?? 'N/A'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latency -->
            <div class="row text-center g-2">
                <div class="col-4">
                    <div class="p-2 border rounded">
                        <div class="text-muted small">Min</div>
                        <div class="fw-semibold">
                            <?= htmlspecialchars($customer['ping_min_ms'] ?? 'N/A'); ?> ms
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="p-2 border rounded">
                        <div class="text-muted small">Max</div>
                        <div class="fw-semibold">
                            <?= htmlspecialchars($customer['ping_max_ms'] ?? 'N/A'); ?> ms
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="p-2 border rounded bg-soft-primary">
                        <div class="text-muted small">Avg</div>
                        <div class="fw-bold text-primary">
                            <?= htmlspecialchars($customer['ping_avg_ms'] ?? 'N/A'); ?> ms
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offline Duration -->
            <?php if (!$isOnline && !empty($customer['offline_since'])): ?>
                <div class="alert alert-danger mt-3 py-2 small text-center mb-0">
                    <i class="mdi mdi-alert-circle-outline me-1"></i>
                    <strong>Offline Since:</strong>
                    <?= htmlspecialchars($customer['offline_since']); ?>
                    <br>
                    <strong>Duration:</strong>
                    <?= gmdate("H:i:s", (int)$customer['offline_duration']); ?>
                </div>
            <?php endif; ?>

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