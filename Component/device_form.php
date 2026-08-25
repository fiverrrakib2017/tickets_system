<div class="relative text-center border-top">
    <span class="px-2 text-muted bg-white font-weight-bold  position-relative" style="top: -12px;">
        Device  &amp; Details
    </span>
</div>

<div class="col-md-6 mb-3 d-none">
     <label class="form-label">Deivce id</label>
     <input type="text" name="device_id" class="form-control"
         value="<?= isset($device['id']) ? htmlspecialchars($device['id']) : '' ?>" >
 </div>
<div class="col-md-6 mb-3">
    <label for="name">Device Name <span class="text-danger">*</span></label>
    <input  type="text" id="name" name="name"  class="form-control"  placeholder="e.g. Core Router"  value="<?= isset($device['name']) ? htmlspecialchars($device['name']) : '' ?>"  required>
</div>
<div class="col-md-6 mb-3">
    <label for="ip_address">IP Address <span class="text-danger">*</span></label>
    <input type="text" id="ip_address" name="ip_address" class="form-control" placeholder="192.168.1.1" value="<?= isset($device['ip_address']) ? htmlspecialchars($device['ip_address']) : '' ?>" required>
</div>
<div class="col-md-6 mb-3">
    <label for="hostname">Hostname <span class="text-danger">*</span></label>
    <input type="text" id="hostname" name="hostname" class="form-control" placeholder="core-router-01" value="<?= isset($device['hostname']) ? htmlspecialchars($device['hostname']) : '' ?>" required>
</div>
<div class="col-md-6 mb-3">
    <label for="device_type">Device Type</label>
    <select id="device_type" name="device_type" class="form-select select2">
        <option value="router" <?= ($device['device_type'] ?? '') === 'router' ? 'selected' : '' ?>>Router</option>
        <option value="switch" <?= ($device['device_type'] ?? '') === 'switch' ? 'selected' : '' ?>>Switch</option>
        <option value="olt" <?= ($device['device_type'] ?? '') === 'olt' ? 'selected' : '' ?>>OLT</option>
        <option value="server" <?= ($device['device_type'] ?? '') === 'server' ? 'selected' : '' ?>>Server</option>
        <option value="firewall" <?= ($device['device_type'] ?? '') === 'firewall' ? 'selected' : '' ?>>Firewall</option>
        <option value="access_point" <?= ($device['device_type'] ?? '') === 'access_point' ? 'selected' : '' ?>>Access Point</option>
        <option value="other" <?= ($device['device_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
    </select>
</div>
<div class="col-md-6 mb-3">
    <label for="vendor">Vendor</label>
    <input  type="text" id="vendor" name="vendor" class="form-control" placeholder="MikroTik" value="<?= htmlspecialchars($device['vendor'] ?? '') ?>" >
</div>
<div class="col-md-6 mb-3">
   <label for="model">Model</label>
    <input  type="text" id="model" name="model"class="form-control" placeholder="CCR2004-16G-2S+"  value="<?= htmlspecialchars($device['model'] ?? '') ?>" >
</div>
<div class="col-md-6 mb-3">
    <label for="serial_number">Serial Number</label>
    <input  type="text" id="serial_number" name="serial_number"class="form-control" placeholder="e.g. HE12345678"  value="<?= htmlspecialchars($device['serial_number'] ?? '') ?>" >
</div>
<!-- Location Field -->
<div class="col-md-6 mb-3">
    <label for="location">Location</label>
    <input type="text" id="location" name="location" class="form-control" placeholder="Main POP / Data Center" value="<?= htmlspecialchars($device['location'] ?? '') ?>">
</div>
<div class="col-md-12 mb-3">
    <label for="description">Description</label>
    <textarea type="text" id="description" name="description" class="form-control"  placeholder="Write details about device usage or maintenance notes..." value="<?= htmlspecialchars($device['description'] ?? '') ?>"></textarea>
</div>


<div class="relative text-center border-top">
    <span class="px-2 text-muted bg-white font-weight-bold  position-relative" style="top: -12px;">
        MONITORING &amp; SNMP
    </span>
</div>

<div class="col-md-6 mb-3">
     <input type="checkbox" class="custom-control-input"  id="monitoring_enabled"  name="monitoring_enabled"  <?= isset($device['monitoring_enabled']) || !$_POST ? 'checked' : '' ?>  >
    <label class="custom-control-label font-weight-normal" for="monitoring_enabled">
        Enable Device Monitoring
    </label>
</div>

<div class="col-md-6 mb-3">
    <input 
        type="checkbox" 
        class="custom-control-input" 
        id="snmp_enabled" 
        name="snmp_enabled" 
        <?= isset($_POST['snmp_enabled']) || !$_POST ? 'checked' : '' ?>
    >
    <label class="custom-control-label font-weight-normal" for="snmp_enabled">
        Enable SNMP Protocol
    </label>
</div>
<div class="col-md-6 mb-3">
    <label for="snmp_version">SNMP Version</label>
    <select id="snmp_version" name="snmp_version" class="form-control">
        <option value="2c" <?= ($device['snmp_version'] ?? '2c') === '2c' ? 'selected' : '' ?>>SNMP v2c</option>
        <option value="1" <?= ($device['snmp_version'] ?? '') === '1' ? 'selected' : '' ?>>SNMP v1</option>
        <option value="3" <?= ($device['snmp_version'] ?? '') === '3' ? 'selected' : '' ?>>SNMP v3</option>
    </select>
</div>
<div class="col-md-6 mb-3">
    <label for="snmp_port">SNMP Port</label>
    <input 
        type="number" 
        id="snmp_port"
        name="snmp_port" 
        class="form-control" 
        value="<?= htmlspecialchars($device['snmp_port'] ?? '161') ?>" 
        min="1" 
        max="65535"
    >
</div>
<div class="col-md-6 mb-3">
    <label for="snmp_community">Community String</label>
    <div class="input-group">
        <input 
            type="password" 
            id="snmp_community"
            name="snmp_community" 
            class="form-control" 
            placeholder="e.g. public"
            value="<?= htmlspecialchars($_POST['snmp_community'] ?? '') ?>"
        >
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="toggle_community">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>
    <small class="form-text text-muted">Required for SNMP v1 and v2c authorization.</small>
</div>

