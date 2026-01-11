<div class="modal fade bs-example-modal-lg" id="sendMessageModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><span class="mdi mdi-account-check mdi-18px"></span>
                    &nbsp;Send Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="selectedCustomerCount"></div>
                <form id="paymentForm" method="POST">

                    <div class="form-group mb-2">
                        <label>Message Template</label>
                        <select class="form-select" name="message_template">
                            <option>---Select---</option>
                            <?php
                            if ($allCstmr = $con->query('SELECT * FROM message_template')) {
                                while ($rows = $allCstmr->fetch_array()) {
                                    echo '<option value=' . $rows['id'] . '>' . $rows['template_name'] . '</option>';
                                }
                            }
                            
                            ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Message</label>
                        <textarea id="message" rows="5" placeholder="Enter Your Message" class="form-control"></textarea>
                    </div>
                    <div class="mb-2">
                        <label>Insert Shortcodes:</label><br>
                        <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                            data-code="{id}">{id}</button>
                        <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                            data-code="{mobile}">{mobile}</button>
                    </div>

                    <div class="modal-footer ">
                        <button data-bs-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                        <button type="button" name="send_message_btn" class="btn btn-success">Send
                            Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="./assets/libs/jquery/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".shortcode-btn").on("click", function() {
            let shortcode = $(this).data("code");
            let textarea = $("#sendMessageModal #message");

            let cursorPos = textarea.prop("selectionStart");
            let v = textarea.val();
            let textBefore = v.substring(0, cursorPos);
            let textAfter = v.substring(cursorPos, v.length);

            textarea.val(textBefore + shortcode + textAfter);
            textarea.focus();
            textarea[0].selectionStart = cursorPos + shortcode.length;
            textarea[0].selectionEnd = cursorPos + shortcode.length;
        });
        $('select[name="message_template"]').on('change', function() {
            var name = $(this).val();
            var currentMsgTemp = "0";
            $.ajax({
                type: 'POST',
                data: {
                    name: name,
                    currentMsgTemp: currentMsgTemp
                },
                url: 'include/message.php',
                success: function(response) {
                    $("#message").val(response);
                }
            });
        });
        $(document).on('click', '#send_message_btn', function(event) {
            event.preventDefault();
            var selectedCustomers = [];
            $(".checkSingle:checked").each(function() {
                selectedCustomers.push($(this).val());
            });
            var countText = "You have selected " + selectedCustomers.length + " customers.";
            $("#selectedCustomerCount").text(countText);
            $('#sendMessageModal').modal('show');

        });
        $(document).on('click', 'button[name="send_message_btn"]', function(e) {
            var button = $(this);
            button.html(
                `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`
                );
            button.attr('disabled', true);

            var selectedCustomers = [];
            $(".checkSingle:checked").each(function() {
                selectedCustomers.push($(this).val());
            });

            e.preventDefault();
            $.ajax({
                url: 'include/message_server.php?bulk_message=true',
                method: 'POST',
                dataType: 'json',
                data: {
                    /*sending the array of customer IDs*/
                    customer_ids: selectedCustomers,
                    message: $("#message").val(),
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#sendMessageModal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                    else if (response.success == false) {
                        if (response.errors) {
                            $.each(response.errors, function(key, error) {
                                toastr.error(error);
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("An error occurred: " + error);
                },
                complete: function() {
                    button.html('Send Message');
                    button.attr('disabled', false);
                }
            });
        });
    });
</script>
