 <div class="col-md-6 mb-3 d-none">
     <label class="form-label">Customer id</label>
     <input type="text" name="customer_id" class="form-control"
         value="<?= isset($customer['id']) ? htmlspecialchars($customer['id']) : '' ?>" >
 </div>
 <div class="col-md-6 mb-3">
     <label class="form-label">Customer Name</label>
     <input type="text" name="customer_name" class="form-control" placeholder="Enter Customer Name"
         value="<?= isset($customer['customer_name']) ? htmlspecialchars($customer['customer_name']) : '' ?>" required>
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Email</label>
     <input type="email" name="customer_email" class="form-control" placeholder="Enter Customer Email"
         value="<?= isset($customer['customer_email']) ? htmlspecialchars($customer['customer_email']) : '' ?>">
 </div>


<!---------- Phone Number Section Start--------- -->
<div class="col-md-6 mb-3">
    <label class="form-label">Phone Numbers</label>

    <div id="phoneWrapper">

        <?php
        $hasPhone = false;

        if (isset($customer['id'])) {
            $stmt = $con->prepare("SELECT phone_number FROM customer_phones WHERE customer_id = ?");
            $stmt->bind_param('i', $customer['id']);
            $stmt->execute();
            $phones = $stmt->get_result();

            if ($phones->num_rows > 0) {
                $hasPhone = true;
                while ($phone = $phones->fetch_assoc()) {
            ?>
                    <div class="input-group mb-2">
                        <input type="text"
                               name="customer_phones[]"
                               class="form-control"
                               placeholder="Enter Phone Number"
                               value="<?= htmlspecialchars($phone['phone_number']) ?>">

                        <button type="button" class="btn btn-danger removePhone">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
            <?php
                }
            }
        }
        ?>
        <div class="input-group mb-2">
            <input type="text"
                   name="customer_phones[]"
                   class="form-control"
                   placeholder="Enter Phone Number">

            <button type="button" class="btn btn-primary" id="addPhone">
                <i class="mdi mdi-plus"></i>
            </button>
        </div>

    </div>
</div>


<script>
document.getElementById('addPhone').addEventListener('click', function () {
    const wrapper = document.getElementById('phoneWrapper');

    const div = document.createElement('div');
    div.className = 'input-group mb-2';

    div.innerHTML = `
        <input type="text"
               name="customer_phones[]"
               class="form-control"
               placeholder="Enter Phone Number">
        <button type="button" class="btn btn-danger removePhone">
            <i class="mdi mdi-close"></i>
        </button>
    `;

    wrapper.appendChild(div);
});

/*-------Remove phone input------*/ 
document.addEventListener('click', function (e) {
    if (e.target.closest('.removePhone')) {
        e.target.closest('.input-group').remove();
    }
});
</script>
 <!---------- Phone Number Section End--------- -->

 <div class="col-md-6 mb-3">
     <label class="form-label">POP / Area</label>
     <select name="customer_pop_branch" class="form-select" required>
        <option value="">---Select---</option>
         <?php
         $pop = $con->query('SELECT * FROM pop_branch');
         while ($row = $pop->fetch_assoc()) {
             $selected = $row['id'] == $customer['pop_id'] ? 'selected' : '';
             echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
         }
         ?>
     </select>
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Connection Via</label>
        <select name="customer_type" class="form-select">
         <?php
         $types = $con->query('SELECT * FROM customer_type');
         while ($row = $types->fetch_assoc()) {
             $selected = $row['id'] == $customer['customer_type_id'] ? 'selected' : '';
             echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
         }
        ?>
     </select>
 </div>



 <div class="col-md-6 mb-3">
     <label class="form-label">VLAN</label>
     <input type="text" name="customer_vlan" class="form-control" placeholder="Enter VLAN"
         value="<?= isset($customer['customer_vlan']) ? htmlspecialchars($customer['customer_vlan']) : '' ?>">
 </div>

 
<!---------- Phone public ip Section Start--------- -->
<div class="col-md-6 mb-3">
   <label class="form-label">Public Address</label>

    <div id="public_ip_Wrapper">

        <?php
        $has_ip_address = false;

        if (isset($customer['id'])) {
            $stmt = $con->prepare("SELECT ip_address FROM customer_public_ip_address WHERE customer_id = ?");
            $stmt->bind_param('i', $customer['id']);
            $stmt->execute();
            $objects = $stmt->get_result();

            if ($objects->num_rows > 0) {
                $has_ip_address = true;
                while ($ip_address = $objects->fetch_assoc()) {
            ?>
                    <div class="input-group mb-2">
                        <input type="text"
                               name="customer_public_ip[]"
                               class="form-control"
                               placeholder="Enter Public IP Address"
                               value="<?= htmlspecialchars($ip_address['ip_address']) ?>">

                        <button type="button" class="btn btn-danger removeIP">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
            <?php
                }
            }
        }
        ?>
        <div class="input-group mb-2">
            <input type="text" 
                    name="customer_public_ip[]"
                    class="form-control"
                    placeholder="Enter Public IP Address">

            <button type="button" class="btn btn-primary" id="addIP">
                <i class="mdi mdi-plus"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('addIP').addEventListener('click', function () {
    const wrapper = document.getElementById('public_ip_Wrapper');

    const div = document.createElement('div');
    div.className = 'input-group mb-2';

    div.innerHTML = `
        <input type="text"
               name="customer_public_ip[]"
               class="form-control"
                placeholder="Enter Public IP Address">
        <button type="button" class="btn btn-danger removeIP">
            <i class="mdi mdi-close"></i>
        </button>
    `;

    wrapper.appendChild(div);
});

/*-------Remove IP input------*/ 
document.addEventListener('click', function (e) {
    if (e.target.closest('.removeIP')) {
        e.target.closest('.input-group').remove();
    }
});
</script>
 <div class="col-md-6 mb-3">
     <label class="form-label">Private Address</label>
     <input type="text" name="private_customer_ip" class="form-control" placeholder="Enter IP Address"
         value="<?= isset($customer['private_customer_ip']) ? htmlspecialchars($customer['private_customer_ip']) : '' ?>">
 </div>

 <div class="col-md-6 mb-3">
     <label class="form-label">Status</label>
     <select name="customer_status" class="form-select">
         <option value="1" <?php if (isset($customer['status']) && $customer['status'] == 1) {
             echo 'selected';
         } ?>>Active</option>
         <option value="0" <?php if (isset($customer['status']) && $customer['status'] == 0) {
             echo 'selected';
         } ?>>Inactive</option>
     </select>
 </div>

<!----------------Customer Service Section Start------------------------>
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
<!----------------Customer Service Section End------------------------>

<!---------------- Customer NID And Service Agreement Section Start ------------------>
<div class="col-md-6 mb-3">
    <label class="form-label">Upload NID (PDF)</label>
    <input type="file" name="nid_file"  class="form-control"  accept="application/pdf" >
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Upload Service Agreement (PDF)</label>
    <input type="file" name="service_agreement_file" class="form-control" accept="application/pdf">
</div>
<!---------------- Customer NID And Service Agreement Section End ------------------>




