<div class="relative text-center border-top">
    <span class="px-2 text-muted bg-white font-weight-bold  position-relative" style="top: -12px;">
        Incident  &amp; Summary
    </span>
</div>

<div class="col-md-6 mb-3 d-none">
     <label class="form-label">ticket id</label>
     <input type="text" name="ticket_id" class="form-control"
         value="<?= isset($ticket['id']) ? htmlspecialchars($ticket['id']) : '' ?>" >
 </div>
<div class="col-md-6 mb-3">
    <label for="incident_summary">
        Incident Summary <span class="text-danger">*</span>
    </label>

    <textarea
        id="incident_summary"
        name="incident_summary"
        class="form-control auto-resize"
        placeholder="Enter Incident Summary"
        required
    ><?= isset($ticket['incident_summary']) ? htmlspecialchars($ticket['incident_summary']) : '' ?></textarea>
</div>

<div class="col-md-6 mb-3">
    <label for="device_type">Status</label>
    <select id="status" name="status" class="form-select select2">
        <option value="Active" <?= ($ticket['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
        <option value="Pending" <?= ($ticket['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Complete" <?= ($ticket['status'] ?? '') === 'Complete' ? 'selected' : '' ?>>Complete</option>
       
    </select>
</div>
