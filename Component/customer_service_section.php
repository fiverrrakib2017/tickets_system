<div class="col-12 mb-3">
    <label class="form-label">Services</label>
    <div id="servicesContainer">
        <div class="row mb-2 service-row align-items-center">
            <div class="col-md-3">
                <select name="service_id[]" class="form-select" required>
                    <option value="">--- Select Service ---</option>
                    <?php
                    $services = $con->query('SELECT * FROM customer_service');
                    while ($row = $services->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="limit[]" class="form-control limit-input" placeholder="Limit" required>
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
    </div>

    <!-- Total & Options -->
    <div class="d-flex justify-content-between align-items-center mt-2 p-2 border-top">
        <div>
            <strong>Total Limit:</strong> <span id="servicesTotal">0</span> MBPS
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio"
                name="service_type" id="nttn"
                value="NTTN" required>
            <label class="form-check-label" for="nttn">NTTN</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio"
                name="service_type" id="overhead"
                value="Overhead">
            <label class="form-check-label" for="overhead">Overhead</label>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const servicesContainer = document.getElementById('servicesContainer');
        const totalEl = document.getElementById('servicesTotal');

        function updateTotal() {
            let total = 0;
            servicesContainer.querySelectorAll('input[name="limit[]"]').forEach(function(input) {
                let val = parseInt(input.value);
                if (!isNaN(val)) total += val;
            });
            totalEl.textContent = total.toLocaleString();
        }

        /*--------Update total on input-----*/ 
        servicesContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('limit-input')) {
                updateTotal();
            }
        });

        /*-------Add/Remove service rows------*/ 
        servicesContainer.addEventListener('click', function(e) {
            if (e.target.closest('.addServiceBtn')) {
                let row = e.target.closest('.service-row');
                let newRow = row.cloneNode(true);

                /*---- Clear new row values------*/
                newRow.querySelector('select').value = '';
                newRow.querySelector('.limit-input').value = '';

                
                /*------- Change add button to remove-----*/ 
                const btn = newRow.querySelector('.addServiceBtn');
                btn.classList.remove('btn-success', 'addServiceBtn');
                btn.classList.add('btn-danger', 'removeServiceBtn');
                btn.innerHTML = '<i class="fas fa-trash"></i> Remove';

                servicesContainer.appendChild(newRow);
            } else if (e.target.closest('.removeServiceBtn')) {
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
