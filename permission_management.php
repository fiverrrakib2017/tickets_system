<?php
date_default_timezone_set('Asia/Dhaka');
/**----------Enable error reporting--------------**/ 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*-------- Database Connection------*/
if (file_exists('include/db_connect.php')) {
    include_once 'include/db_connect.php';
}
/*---------Security Token-------*/ 
if (file_exists('include/security_token.php')) {
    include_once 'include/security_token.php';
}
/*-------User Rights--------*/ 
if (file_exists('include/users_right.php')) {
    include_once  'include/users_right.php';
}
/*--------POP Security---------*/ 
if (file_exists('include/pop_security.php')) {
    include_once 'include/pop_security.php';
}
// checkPermission('permission_management');
// -------------------------------------------------------------------
// 1. Get Role Details & Assigned Permissions
// -------------------------------------------------------------------
$role_id = isset($_GET['role_id']) ? intval($_GET['role_id']) : 0;

if ($role_id === 0) {
    die("<div class='alert alert-danger m-4'>Error: Invalid Role ID!</div>");
}

// Get Role Info
$roleStmt = $con->prepare("SELECT `name` FROM roles WHERE id = ?");
$roleStmt->bind_param("i", $role_id);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();

if ($roleResult->num_rows === 0) {
    die("<div class='alert alert-danger m-4'>Error: Role Not Found!</div>");
}
$roleData = $roleResult->fetch_assoc();
$role_name = $roleData['name'];
$roleStmt->close();

// Get Already Assigned Permission IDs for this Role
$assigned_permissions = [];
$assignedStmt = $con->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
$assignedStmt->bind_param("i", $role_id);
$assignedStmt->execute();
$assignedResult = $assignedStmt->get_result();

while ($row = $assignedResult->fetch_assoc()) {
    $assigned_permissions[] = $row['permission_id'];
}
$assignedStmt->close();

// Get All Permissions Grouped by Module
$modules = [];
$total_permissions_count = 0;
$permQuery = $con->query("SELECT id, name, module, slug FROM permissions ORDER BY module ASC, name ASC");

while ($perm = $permQuery->fetch_assoc()) {
    $modules[$perm['module']][] = $perm;
    $total_permissions_count++;
}
?>

<!doctype html>
<html lang="en">

<?php require 'Head.php';?>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $page_title = 'Permission Management'; include 'Header.php'; ?>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <?php include 'Sidebar_menu.php'; ?>
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
       <style>
/* Module Permisison Custom Styles */
.module-accordion .card {
    border: 1px solid #e9ebec;
    border-radius: 6px;
    transition: all 0.2s ease;
}
.module-accordion .card-header {
    background-color: #ffffff;
    border-bottom: 3px dotted #bec0c1;
    padding: 0.75rem 1.25rem;
}
.perm-item {
    border: 1px solid #e3e6ef;
    border-radius: 6px;
    padding: 10px 14px;
    background-color: #fff;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
/* Active (Checked) State - Green */
.perm-item.active-perm {
    background-color: #f0fdf4;
    border-color: #bbf7d0;
}
.perm-item.active-perm .perm-status-icon {
    color: #16a34a;
}
.perm-item.active-perm .perm-title {
    color: #15803d;
}

/* Inactive (Unchecked) State - Red Cross */
.perm-item.inactive-perm {
    background-color: #fef2f2;
    border-color: #fecaca;
}
.perm-item.inactive-perm .perm-status-icon {
    color: #dc2626;
}
.perm-item.inactive-perm .perm-title {
    color: #991b1b;
}

.perm-item .custom-control-input {
    display: none; /* Default check mark hidden for custom UI */
}
</style>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Role Info Header Banner -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card bg-primary text-white mb-0 shadow-sm">
                        <div class="card-body d-flex align-items-center justify-content-between flex-wrap py-3">
                            <div>
                                <h4 class="text-white mb-1"><i class="mdi mdi-shield-check mr-2"></i>Role: <?php echo htmlspecialchars($role_name); ?></h4>
                                <p class="text-white-50 mb-0 font-size-13">Configure modular access control and functional permissions for this role.</p>
                            </div>
                            <div class="mt-2 mt-sm-0 d-flex align-items-center">
                                <span class="badge bg-light font-size-13 mr-3 p-2 text-dark shadow-sm" id="selectedCounter">
                                    Selected: <strong class="text-primary" id="counterValue"><?php echo count($assigned_permissions); ?></strong> / <?php echo $total_permissions_count; ?>
                                </span>
                                <a href="role_management.php" class="btn btn-danger btn-sm waves-effect waves-light">
                                    <i class="mdi mdi-arrow-left mr-1"></i> Back to Roles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Select All / Clear Bar -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-body p-3 shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="custom-control custom-checkbox custom-checkbox-outline custom-checkbox-primary">
                                <input type="checkbox" class="custom-control-input" id="checkAllGlobal">
                                <label class="custom-control-label font-weight-bold cursor-pointer text-dark" for="checkAllGlobal">
                                    Select All Modules & Permissions
                                </label>
                            </div>
                            <div>
                                <button type="button" id="expandAllBtn" class="btn btn-sm btn-soft-info mr-1 waves-effect">
                                    <i class="mdi mdi-arrow-expand-vertical mr-1"></i> Expand / Collapse All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Form -->
            <form id="permissionForm">
                <input type="hidden" name="role_id" value="<?php echo $role_id; ?>">
                <input type="hidden" name="update_permissions" value="1">

                <div class="row module-accordion">
                    <div class="col-12">
                        <?php if (!empty($modules)): ?>
                            <?php foreach ($modules as $module_name => $permissions): ?>
                                <?php 
                                    $module_hash = md5($module_name);
                                    $total_mod_perms = count($permissions);
                                    $checked_mod_perms = 0;
                                    foreach ($permissions as $p) {
                                        if (in_array($p['id'], $assigned_permissions)) $checked_mod_perms++;
                                    }
                                    $all_mod_checked = ($total_mod_perms > 0 && $total_mod_perms === $checked_mod_perms) ? 'checked' : '';
                                ?>
                                
                                <div class="card mb-3 shadow-sm border">
                                    <!-- Module Header -->
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center cursor-pointer" data-toggle="collapse" data-target="#collapse_<?php echo $module_hash; ?>">
                                            <i class="mdi mdi-folder-lock-open font-size-18 text-primary" style="margin-right: 9px;"></i>
                                            <h5 class="card-title font-size-15 mb-0 text-dark font-weight-semibold">
                                                <?php echo htmlspecialchars($module_name); ?>
                                                <span class="badge bg-danger font-size-11 ml-2 px-2">
                                                    <span class="mod-checked-count"><?php echo $checked_mod_perms; ?></span> / <?php echo $total_mod_perms; ?>
                                                </span>
                                            </h5>
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <div class="custom-control custom-switch custom-switch-md mr-3">
                                                <input type="checkbox" class="custom-control-input select-all-module" id="mod_switch_<?php echo $module_hash; ?>" <?php echo $all_mod_checked; ?>>
                                                <label class="custom-control-label font-size-12 text-muted cursor-pointer" for="mod_switch_<?php echo $module_hash; ?>">Select Module</label>
                                            </div>
                                            <a role="button" data-toggle="collapse" href="#collapse_<?php echo $module_hash; ?>" class="text-dark">
                                                <i class="mdi mdi-chevron-down font-size-20"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Module Items Grid -->
                                    <div id="collapse_<?php echo $module_hash; ?>" class="collapse show">
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <?php foreach ($permissions as $perm): ?>
                                                    <?php 
                                                        $isChecked = in_array($perm['id'], $assigned_permissions); 
                                                    ?>
                                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
                                                        <label for="perm_<?php echo $perm['id']; ?>" class="perm-item <?php echo $isChecked ? 'active-perm' : 'inactive-perm'; ?> mb-0 w-100">
                                                            <div class="d-flex align-items-center">
                                                                <input type="checkbox" 
                                                                       name="permissions[]" 
                                                                       value="<?php echo $perm['id']; ?>" 
                                                                       class="custom-control-input perm-checkbox" 
                                                                       id="perm_<?php echo $perm['id']; ?>" 
                                                                       <?php echo $isChecked ? 'checked' : ''; ?>>
                                                                <span class="perm-title font-size-13"><?php echo htmlspecialchars($perm['name']); ?></span>
                                                            </div>
                                                            <span class="perm-status-icon">
                                                                <i class="mdi <?php echo $isChecked ? 'mdi-check-circle font-size-18' : 'mdi-close-circle font-size-18'; ?>"></i>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning text-center p-4 shadow-sm">
                                <i class="mdi mdi-alert-outline font-size-24 d-block mb-2"></i>
                                No permissions found in database. Please sync file via CLI: <code>php artisan app:sync_permission</code>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sticky Bottom Action Footer Bar -->
                <div class="row my-4">
                    <div class="col-12">
                        <div class="card border-top shadow-sm">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted font-size-13">
                                    <i class="mdi mdi-information-outline text-info mr-1"></i> Changes will be applied immediately upon saving.
                                </span>
                                <div>
                                    <a href="role_management.php" class="btn btn-secondary waves-effect mr-2">Cancel</a>
                                    <button type="submit" id="savePermissionBtn" class="btn btn-success px-4">
                                        <i class="mdi mdi-content-save mr-1"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <?php include 'Footer.php'; ?>
</div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <div class="rightbar-overlay"></div>
    <?php include 'script.php'; ?>

    <!-- Admira Custom JS Handling -->
    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on('change', '.perm-checkbox', function() {
                    updatePermUI($(this));
                    updateModuleCounters($(this).closest('.card'));
                    updateGlobalCounter();
                });

                function updatePermUI($checkbox) {
                    var $label = $checkbox.closest('.perm-item');
                    var $icon = $label.find('.perm-status-icon i');

                    if ($checkbox.is(':checked')) {
                        $label.removeClass('inactive-perm').addClass('active-perm');
                        $icon.removeClass('mdi-close-circle').addClass('mdi-check-circle');
                    } else {
                        $label.removeClass('active-perm').addClass('inactive-perm');
                        $icon.removeClass('mdi-check-circle').addClass('mdi-close-circle');
                    }
                }

                $(document).on('change', '.select-all-module', function() {
                    var isChecked = $(this).is(':checked');
                    var $card = $(this).closest('.card');
                    
                    $card.find('.perm-checkbox').prop('checked', isChecked).each(function() {
                        updatePermUI($(this));
                    });
                    
                    updateModuleCounters($card);
                    updateGlobalCounter();
                });

                $('#checkAllGlobal').on('change', function() {
                    var isChecked = $(this).is(':checked');
                    $('.perm-checkbox, .select-all-module').prop('checked', isChecked);
                    
                    $('.perm-checkbox').each(function() {
                        updatePermUI($(this));
                    });

                    $('.card').each(function() {
                        updateModuleCounters($(this));
                    });
                    
                    updateGlobalCounter();
                });

                function updateModuleCounters($card) {
                    var total = $card.find('.perm-checkbox').length;
                    var checked = $card.find('.perm-checkbox:checked').length;
                    
                    $card.find('.mod-checked-count').text(checked);
                    $card.find('.select-all-module').prop('checked', total > 0 && total === checked);
                }

                function updateGlobalCounter() {
                    var totalChecked = $('.perm-checkbox:checked').length;
                    $('#selectedCounter strong, #counterValue').text(totalChecked);
                }

                $('#expandAllBtn').on('click', function() {
                    $('.module-accordion .collapse').collapse('toggle');
                });
            $('#permissionForm').on('submit', function(e) {
                e.preventDefault();

                var $btn = $('#savePermissionBtn');
                var originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="mdi mdi-spin mdi-loading mr-1"></i> Saving...');

                $.ajax({
                    url: 'include/role_server.php?update_role_permissions=true',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (response.success) {
                            toastr.success(response.message)
                        } else {
                            toastr.error(response.message)
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                        toastr.error('Something went wrong on the server! Please try again.', 'Server Error!');
                    }
                });
            });

        });
    </script>
</body>
</html>