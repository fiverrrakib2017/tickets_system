<div class="col-12 mb-3">
    <label class="form-label">Services</label>

    <div id="servicesContainer">

        <?php
        /* customer_profile_edit.php */
        $customerServices = [];
        $q = $con->query("
            SELECT service_id, customer_limit 
            FROM customer_invoice 
            WHERE customer_id = $customer_id
        ");
        while($r = $q->fetch_assoc()){
            $customerServices[] = $r;
        }

        /* If edit mode & services exist */
        if (!empty($customerServices)) {
            foreach ($customerServices as $index => $cs) {
        ?>
        <div class="row mb-2 service-row align-items-center">
            <div class="col-md-3">
                <select name="service_id[]" class="form-select" required>
                    <option value="">--- Select Service ---</option>
                    <?php
                    $services = $con->query("SELECT * FROM customer_service");
                    while ($row = $services->fetch_assoc()) {
                        $selected = ($row['id'] == $cs['service_id']) ? 'selected' : '';
                        echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <input type="number" name="limit[]"
                       class="form-control limit-input"
                       value="<?php echo (int)$cs['customer_limit']; ?>"
                       required>
            </div>

            <div class="col-md-2">
                <input type="text" class="form-control" value="MBPS" disabled>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <?php if ($index == 0) { ?>
                    <button type="button" class="btn btn-success btn-sm addServiceBtn">
                        <i class="fas fa-plus"></i>
                    </button>
                <?php } else { ?>
                    <button type="button" class="btn btn-danger btn-sm removeServiceBtn">
                        <i class="fas fa-minus"></i>
                    </button>
                <?php } ?>
            </div>
        </div>
        <?php
            }
        } else {
        /* Add mode default row */
        ?>
        <div class="row mb-2 service-row align-items-center">
            <div class="col-md-3">
                <select name="service_id[]" class="form-select" required>
                    <option value="">--- Select Service ---</option>
                    <?php
                    $services = $con->query("SELECT * FROM customer_service");
                    while ($row = $services->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <input type="number" name="limit[]"
                       class="form-control limit-input"
                       placeholder="Limit" required>
            </div>

            <div class="col-md-2">
                <input type="text" class="form-control" value="MBPS" disabled>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="button" class="btn btn-success btn-sm addServiceBtn">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
        </div>
        <?php } ?>

    </div>

    <!-- Total & Service Type -->
    <div class="d-flex justify-content-between align-items-center mt-2 p-2 border-top">
        <div>
            <strong>Total Limit:</strong>
            <span id="servicesTotal">0</span> MBPS
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio"
                   name="service_type" value="NTTN"
                   <?php if(($customer['service_type'] ?? '')=='NTTN') echo 'checked'; ?>
                   required>
            <label class="form-check-label">NTTN</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio"
                   name="service_type" value="Overhead"
                   <?php if(($customer['service_type'] ?? '')=='Overhead') echo 'checked'; ?>>
            <label class="form-check-label">Overhead</label>
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

    updateTotal(); // EDIT MODE auto total

    servicesContainer.addEventListener('input', function(e){
        if(e.target.classList.contains('limit-input')){
            updateTotal();
        }
    });

    servicesContainer.addEventListener('click', function(e){
        if(e.target.closest('.addServiceBtn')){
            let row = e.target.closest('.service-row');
            let newRow = row.cloneNode(true);

            newRow.querySelector('select').value = '';
            newRow.querySelector('.limit-input').value = '';

            let btn = newRow.querySelector('button');
            btn.className = 'btn btn-danger btn-sm removeServiceBtn';
            btn.innerHTML = '<i class="fas fa-trash"></i> Remove';

            servicesContainer.appendChild(newRow);
        }

        if(e.target.closest('.removeServiceBtn')){
            e.target.closest('.service-row').remove();
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
