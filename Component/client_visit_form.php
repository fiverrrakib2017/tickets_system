<!-- Section Header -->
<div class="position-relative text-center border-top my-4">
    <span class="px-2 text-muted bg-white font-weight-bold position-relative" style="top: -12px;">
        Client Visit Details
    </span>
</div>

<input type="hidden" name="id" value="<?= htmlspecialchars($visit['id'] ?? '') ?>">

<div class="row">
    <!-- Assign User -->
    <div class="col-md-6 mb-3">
        <label for="assign_user_id" class="form-label">
            Assigned User <span class="text-danger">*</span>
        </label>
        <select id="assign_user_id" name="assign_user_id" class="form-select select2" required>
            <option value="">Select User</option>
            <?php
            $session_uid = $_SESSION['uid'] ?? 0;
            
            $selected_user_id = $visit['assign_user_id'] ?? $session_uid;

            $user_query = $con->query("SELECT id, fullname FROM users ORDER BY fullname ASC");
            if ($user_query && $user_query->num_rows > 0) {
                while ($user = $user_query->fetch_assoc()) {
                    $selected = ($selected_user_id == $user['id']) ? 'selected' : '';
                    echo '<option value="' . $user['id'] . '" ' . $selected . '>' . htmlspecialchars($user['fullname']) . '</option>';
                }
            }
            ?>
        </select>
    </div>

    <!-- Title -->
    <div class="col-md-6 mb-3">
        <label for="title" class="form-label">
            Title <span class="text-danger">*</span>
        </label>
        <input 
            type="text" 
            id="title" 
            name="title" 
            class="form-control" 
            placeholder="Enter Visit Title"
            value="<?= htmlspecialchars($visit['title'] ?? '') ?>" 
            required
        >
    </div>

    <!-- Description -->
    <div class="col-md-6 mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea
            id="description"
            name="description"
            class="form-control auto-resize"
            placeholder="Enter Visit Description / Details"
            rows="3"
        ><?= htmlspecialchars($visit['description'] ?? '') ?></textarea>
    </div>

    <!-- Create Date -->
    <div class="col-md-6 mb-3">
        <label for="create_date" class="form-label">
            Create Date <span class="text-danger">*</span>
        </label>
        <?php 
            $currentDate = isset($visit['create_date']) 
                ? date('Y-m-d', strtotime($visit['create_date'])) 
                : date('Y-m-d');
        ?>
        <input 
            type="date" 
            id="create_date" 
            name="create_date" 
            class="form-control" 
            value="<?= $currentDate ?>" 
            required
        >
    </div>

    <!-- Status -->
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select select2">
            <option value="Active" <?= ($visit['status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
            <option value="Pending" <?= ($visit['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Complete" <?= ($visit['status'] ?? '') === 'Complete' ? 'selected' : '' ?>>Complete</option>
        </select>
    </div>
</div>