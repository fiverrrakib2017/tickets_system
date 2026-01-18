<?php
$isEdit = false;
$customerServiceMap = [];

if (!empty($customer_id) && (int)$customer_id > 0) {
    $isEdit = true;

    $q = $con->query("
        SELECT service_id, customer_limit
        FROM customer_invoice
        WHERE customer_id = $customer_id
    ");

    while ($r = $q->fetch_assoc()) {
        $customerServiceMap[$r['service_id']] = $r['customer_limit'];
    }
}
?>



<div class="col-12 mb-3">
    <label class="form-label">Services</label>

    <div id="servicesContainer">

        <?php
        $services = $con->query("SELECT id, name FROM customer_service");
        while ($service = $services->fetch_assoc()):
            $limitValue = $customerServiceMap[$service['id']] ?? '';
        ?>
            <div class="row mb-2 align-items-center">

                <!-- Service Name -->
                <div class="col-md-4">
                    <input type="hidden"
                           name="service_id[]"
                           value="<?= $service['id']; ?>">

                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars($service['name']); ?>"
                           readonly>
                </div>

                <!-- Limit -->
                <div class="col-md-4">
                    <input type="number"
                           name="limit[]"
                           class="form-control limit-input"
                           value="<?= htmlspecialchars($limitValue); ?>"
                           placeholder="Limit"
                           min="0"
                           step="any">
                </div>

                <!-- Unit -->
                <div class="col-md-4">
                    <input type="text"
                           class="form-control"
                           value="MBPS"
                           readonly>
                </div>

            </div>
        <?php endwhile; ?>

    </div>

    <!-- Total & Service Type -->
    <div class="d-flex justify-content-between align-items-center mt-2 p-2 border-top">

        <div>
            <strong>Total Limit:</strong>
            <span id="servicesTotal">0</span> MBPS
        </div>

        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="service_type"
                       value="NTTN"
                       <?= (($customer['service_type'] ?? '') === 'NTTN') ? 'checked' : ''; ?>
                       required>
                <label class="form-check-label">NTTN</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="service_type"
                       value="Overhead"
                       <?= (($customer['service_type'] ?? '') === 'Overhead') ? 'checked' : ''; ?>>
                <label class="form-check-label">Overhead</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="service_type"
                       value="Both"
                       <?= (($customer['service_type'] ?? '') === 'Both') ? 'checked' : ''; ?>>
                <label class="form-check-label">Both</label>
            </div>
        </div>

    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const servicesContainer = document.getElementById('servicesContainer');
    const totalEl = document.getElementById('servicesTotal');

    function updateTotal() {
        let total = 0;
        servicesContainer.querySelectorAll('.limit-input').forEach(input => {
            let v = parseInt(input.value);
            if (!isNaN(v)) total += v;
        });
        totalEl.textContent = total.toLocaleString();
    }

    updateTotal(); 

    servicesContainer.addEventListener('input', function(e){
        if(e.target.classList.contains('limit-input')){
            updateTotal();
        }
    });
});
</script>


<style>
    #servicesContainer .service-row {
        background: #f9f9f9;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 5px;
    }

    #servicesContainer .service-row select,
    #servicesContainer .service-row input {
        height: 38px;
    }
</style>
