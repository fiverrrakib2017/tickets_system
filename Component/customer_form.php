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
 <div class="col-md-6 mb-3">
     <label class="form-label">Login Username</label>
     <input type="username" name="customer_username" class="form-control" placeholder="Enter Username"
         value="<?= isset($customer['username']) ? htmlspecialchars($customer['username']) : '' ?>"required>
 </div>
 <div class="col-md-6 mb-3">
     <label class="form-label">Login Password</label>
     <input type="password" name="customer_password" class="form-control" placeholder="Enter Password"
         value="<?= isset($customer['password']) ? htmlspecialchars($customer['password']) : '' ?>" required>
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
 <div class="col-md-6 mb-3">
     <label class="form-label">Customer Type</label>
     <select name="service_customer_type" class="form-select" required>
        <option value="">---Select---</option>
         <option value="1" <?php if (isset($customer['service_customer_type']) && $customer['service_customer_type'] == 1) {
             echo 'selected';
         } ?>>Bandwidth</option>
         <option value="2" <?php if (isset($customer['service_customer_type']) && $customer['service_customer_type'] == 2) {
             echo 'selected';
         } ?>>Mac Reseller</option>
     </select>
 </div>

    <!--------------- Mac Reseller Customer Count ---------------->
    <div class="col-12 mb-3 d-none" id="mac_reseller_box">
        <label class="form-label">Services</label>

        <div id="mac_reseller_container">
            <?php
            $rows = [];

            if(isset($customer_id) && $customer_id > 0){
                $res = $con->query("SELECT * FROM mac_reseller_customer_inv WHERE customer_id = $customer_id");
                while($row = $res->fetch_assoc()){
                    $rows[] = $row;
                }
            }

            /*---------No Data Empty Row---------*/
            if(count($rows) === 0){
                $rows[] = [
                    'package_count' => '',
                    'total_customer' => ''
                ];
            }

            foreach($rows as $service):
            ?>
            <div class="row mb-2 align-items-center reseller-row">
                <div class="col-md-3">
                    <input type="text"
                        class="form-control"
                        name="service_mac_reseller_package[]"
                        placeholder="Customer Package"
                        value="<?php echo htmlspecialchars($service['package_count']); ?>">
                </div>

                <div class="col-md-3">
                    <input type="text" class="form-control" value="MBPS" readonly>
                </div>

                <div class="col-md-3">
                    <input type="number"
                        name="service_mac_reseller_customer_count[]"
                        class="form-control mac_reseller_customer_count_input"
                        placeholder="Customer Count"
                        value="<?php echo htmlspecialchars($service['total_customer']); ?>"
                        min="0">
                </div>

                <div class="col-md-3">
                    <button type="button" class="btn-sm btn btn-success add_reseller_column">
                        <i class="mdi mdi-plus"></i>
                    </button>

                    <?php if(count($rows) > 1): ?>
                    <button type="button" class="btn-sm btn btn-danger remove_reseller_column">
                        <i class="mdi mdi-minus"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <!-- Total & Service Type -->
    <div class="d-flex justify-content-between align-items-center mt-2 p-2 border-top">

        <div>
            <strong class="text-muted text-success">Total Customers:</strong>
            <span id="mac_reseller_total">0</span> 
        </div>

    </div>

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



<div class="col-12 mb-3 d-none" id="bandwidth_services_box">
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
                       >
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
    /*---------- MAC Reseller Section Script----------------*/  
    const _customer_type_selected = document.querySelector('select[name="service_customer_type"]');
    const bandwidthBox = document.getElementById('bandwidth_services_box');
    const macBox = document.getElementById('mac_reseller_box');
    
    function _toggle_customer_type_boxes() {
        const _select_type = _customer_type_selected.value;
         if (_select_type === '1') {
            // Bandwidth
            bandwidthBox.classList.remove('d-none');
            macBox.classList.add('d-none');
        }else if (_select_type === '2') {
            // Mac Reseller
            bandwidthBox.classList.add('d-none');
            macBox.classList.remove('d-none');
        }else {
            bandwidthBox.classList.add('d-none');
            macBox.classList.add('d-none');
        }
    }
    _toggle_customer_type_boxes();
    _customer_type_selected.addEventListener('change',_toggle_customer_type_boxes);

    /*--------- Add Reseller Column----------*/ 
    document.querySelectorAll('.add_reseller_column').forEach(button => {
        button.addEventListener('click', function () {
            const container = document.getElementById('mac_reseller_container');

            const div = document.createElement('div');
            div.className = 'row mb-2 align-items-center';

            div.innerHTML = `
                <div class="col-md-3">
                    <input type="text" class="form-control" name="service_mac_reseller_package[]" placeholder="Customer Package" >
                </div>
                 <div class="col-md-3">
                    <input type="text"
                        class="form-control"
                        value="MBPS"
                        readonly>
                </div>
                <div class="col-md-3">
                    <input type="number"
                           name="service_mac_reseller_customer_count[]"
                           class="form-control mac_reseller_customer_count_input"
                           value=""
                           placeholder="Customer Count"
                           min="0"
                           step="any">
                </div>
                <div class="col-md-3">
                  <button type="button" class="btn-sm btn btn-danger remove_reseller_column">
                        <i class="mdi mdi-minus"></i>
                    </button>
                </div>
            `;

            container.appendChild(div);
        });
    });
    /*---------Remove Reseller Column----------*/ 
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove_reseller_column')) {
            e.target.closest('.row').remove();
            _update_mac_reseller_total();
        }
    });
    /*------------Update Total Customers------------*/ 
    const mac_reseller_total_element = document.getElementById('mac_reseller_total');
    function _update_mac_reseller_total() {
        let total = 0;
        document.querySelectorAll('.mac_reseller_customer_count_input').forEach(input => {
            let v = parseInt(input.value);
            if (!isNaN(v)) total += v;
        });
        mac_reseller_total_element.textContent = total.toLocaleString();
    }
    _update_mac_reseller_total();
    document.getElementById('mac_reseller_container').addEventListener('input', function(e){
        if(e.target.classList.contains('mac_reseller_customer_count_input')){
            _update_mac_reseller_total();
        }
    });




    /*----------Service Section Script----------------*/       
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
    <input type="file"
           name="nid_file"
           id="nid_file"
           class="form-control"
           onchange="_preview_file_generate(this, 'nid_preview')">

    <div id="nid_preview" class="mt-2 d-none"></div>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Upload Service Agreement (PDF)</label>
    <input type="file"
           name="service_agreement_file"
           id="service_file"
           class="form-control"
           onchange="_preview_file_generate(this, 'service_preview')">

    <div id="service_preview" class="mt-2 d-none"></div>
</div>

<!---------------- Customer NID And Service Agreement Section End ------------------>
<script>
function _preview_file_generate(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (!input.files || !input.files[0]) {
        preview.classList.add('d-none');
        return;
    }

    const file = input.files[0];
    const fileType = file.type;
    let html = '';

    if (fileType.startsWith('image/')) {
        // Image preview
        const reader = new FileReader();
        reader.onload = function(e) {
            html = `
                <div class="border rounded p-2 d-flex justify-content-between align-items-center bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${e.target.result}" class="img-thumbnail" style="width:50px;height:50px;object-fit:cover;">
                        <span class="small">${file.name}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="_remove_file('${input.id}', '${previewId}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            preview.innerHTML = html;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);

    } else {
        /*----------------------Other files (PDF, DOC, etc.)----------------------*/
        let iconClass = 'fa-file';
        if (fileType === 'application/pdf') iconClass = 'fa-file-pdf text-danger';
        else if (fileType.includes('word')) iconClass = 'fa-file-word text-primary';
        else if (fileType.includes('excel')) iconClass = 'fa-file-excel text-success';
        else if (fileType.includes('zip')) iconClass = 'fa-file-zipper text-warning';

        html = `
            <div class="border rounded p-2 d-flex justify-content-between align-items-center bg-light">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas ${iconClass} fs-4"></i>
                    <span class="small">${file.name}</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger"
                        onclick="_remove_file('${input.id}', '${previewId}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        preview.innerHTML = html;
        preview.classList.remove('d-none');
    }
}

function _remove_file(inputId, previewId) {
    document.getElementById(inputId).value = '';
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    preview.classList.add('d-none');
}
</script>




