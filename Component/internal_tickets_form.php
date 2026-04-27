 

 
 
 <div class="col-md-6 mb-3 d-none">
     <label class="form-label">Ticket id</label>
     <input type="text" name="ticket_id" class="form-control"
         value="<?= isset($ticket['id']) ? htmlspecialchars($ticket['id']) : '' ?>" >
 </div>

<div class="col-md-6 mb-3 ">
    <label class="form-label">Category</label>
    <select name="category_id" id="category_id" class="form-select" required>
        <option value="">---select---</option>
        <?php
        $pop = $con->query('SELECT * FROM ticket_categories');
        while ($row = $pop->fetch_assoc()) {
            $selected = $row['id'] == $ticket['category_id'] ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected >{$row['name']}</option>";
        }
        ?>
    </select>
</div>
<div class="col-md-6 mb-3 ">
    <label class="form-label">Sub Category</label>
    <select name="sub_category_id" id="sub_category_id" class="form-select" required>
        <option value="">---select---</option>
        <?php
        $pop = $con->query('SELECT * FROM ticket_subcategories');
        while ($row = $pop->fetch_assoc()) {
            $selected = $row['id'] == $ticket['subcategory_id'] ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected >{$row['name']}</option>";
        }
        ?>
    </select>
</div>

<div class="col-md-6 mb-3 d-none" id="show_pop_branch_div">
    <label class="form-label">POP / Area</label>
    <select name="pop_branch" class="form-select" >
        <option value="0">---select---</option>
        <?php
        $pop = $con->query('SELECT * FROM pop_branch');
        while ($row = $pop->fetch_assoc()) {
            $selected = $row['id'] == $ticket['pop_id'] ? 'selected' : '';
            echo "<option value='{$row['id']}' $selected >{$row['name']}</option>";
        }
        ?>
    </select>
</div>

<div class="col-md-6 mb-3 d-none" id="show_pop_branch_ip_div">
    <label class="form-label">Router/Switch IP</label>
    <input type="text" id="show_pop_branch_ip" class="form-control" value="">
</div>
<div class="col-md-6 mb-3 d-none" id="effective_customer_div">
    <label class="form-label">Effective Customer</label>
    <select id="customer_id" name="customer_id[]" class="form-control" multiple> </select>
</div>

<div class="col-md-6 mb-3 ">
    <label class="form-label">Severity</label>
    <select id="ticket_priority" name="ticket_severity" class="form-select" required>
        <option value="">--- Select ---</option>

        <option value="critical" <?= (isset($ticket['severity']) && $ticket['severity'] === 'critical') ? 'selected' : ''; ?>>
            Critical
        </option>

        <option value="high" <?= (isset($ticket['severity']) && $ticket['severity'] === 'high') ? 'selected' : ''; ?>>
            High
        </option>

        <option value="medium" <?= (isset($ticket['severity']) && $ticket['severity'] === 'medium') ? 'selected' : ''; ?> >
            Medium
        </option>
        <option value="low" <?= (isset($ticket['severity']) && $ticket['severity'] === 'low') ? 'selected' : ''; ?> >
            Low
        </option>

       
    </select>
</div>



<div class="col-md-6 mb-3">
    <label  class="form-label">Subject</label>
    <input  type="text" name="customer_subject" class="form-control" value="<?= isset($ticket['subject']) ? $ticket['subject'] : '' ?>" placeholder="Enter Your Subject">
</div>

<div class="col-md-6 mb-3">
    <label  class="form-label">Description</label>
    <textarea  type="text" name="customer_description" class="form-control" placeholder="Enter Description"><?= isset($ticket['description']) ? $ticket['description'] : '' ?></textarea>
</div>

<div class="col-md-6 mb-3">
    <label  class="form-label">Attachments</label>
    <input type="file" name="customer_attachments" class="form-control" value="<?= isset($ticket['attachments']) ? $ticket['attachments'] : '' ?>">
</div>


<script src="http://103.112.206.139/assets/libs/jquery/jquery.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
       
       $(document).on('change', 'select[name="pop_branch"]', function () {
        let pop_id = $(this).val();

        if (pop_id === '') {
            $('#show_pop_branch_ip').val('');
            $('#customer_id').html('');
            $('#effective_customer_div').addClass('d-none');
            return;
        }

        /*-------------Load Customers by POP------------*/
        $.ajax({
            url: 'http://103.112.206.139/include/customer_server.php?get_customer=true',
            type: 'GET',
            data: { pop_id: pop_id },
            dataType: 'json',
            success: function (response) {

                let options = '';

                if (response.success && response.data.length > 0) {

                    $.each(response.data, function (index, customer) {
                        options += `
                            <option value="${customer.id}">
                                ${customer.customer_name}
                            </option>
                        `;
                    });

                    $('#customer_id').html(options);
                    $('#effective_customer_div').removeClass('d-none');

                } else {
                    $('#customer_id').html('');
                    $('#effective_customer_div').addClass('d-none');
                }
            }
        });


        /*------------Load Router IP------------*/
        $.ajax({
            url: 'http://103.112.206.139/include/pop_branch_server.php?get_pop_branch_ping_ip=true',
            type: 'GET',
            data: { pop_id: pop_id },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#show_pop_branch_ip_div').removeClass('d-none');
                    $('#show_pop_branch_ip').val(response.router_ip);
                } else {
                    $('#show_pop_branch_ip').val('');
                }
            }
        });

    });
        $(document).on('change', '#category_id', function () {
            let category_id = $(this).val();

            if (category_id == '') {
                $('#sub_category_id').html('<option value="">---select---</option>');
                return;
            }
            if(category_id =='2'){
                $('#show_pop_branch_div').removeClass('d-none');
            }else{
                $('#show_pop_branch_div').addClass('d-none');
            }

            $.ajax({
                url: 'http://103.112.206.139/include/category_server.php?get_subcategory_data=true',
                type: 'GET',
                data: { id: category_id },
                dataType: 'json',
                success: function (response) {

                    let options = '<option value="">---select---</option>';

                    if (response.success && response.data) {
                        options += `<option value="${response.data.id}">
                                        ${response.data.name}
                                    </option>`;
                    }

                    $('#sub_category_id').html(options);
                }
            });
        });
    });
    
</script>