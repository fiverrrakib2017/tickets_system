<ul class="list-group" id="vasList">
                                        
    <?php
        $result = $con->query("SELECT * FROM `value_added_service` ORDER BY id DESC");

        /*---- Check for results-----*/
        if($result && $result->num_rows > 0):
            while($service = $result->fetch_assoc()):
        ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="me-2"><?php echo ($service['icon']); ?></span>
                        <?php echo ($service['service_name']); ?>
                    </div>
                    <div>
                        <a href="<?php echo ($service['service_link']); ?>" target="_blank" class="btn btn-sm btn-success">Open</a>
                        <?php if(!$customer['id']):?>

                        <button class="btn btn-sm btn-primary ms-1 " name="edit_button" data-id="<?php echo $service['id']; ?>"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger ms-1 "  name="delete_button" data-id="<?php echo $service['id']; ?>"><i class="fas fa-trash"></i></button>

                        <?php endif; ?>
                    </div>
                </li>
        <?php
            endwhile;
        endif;
    ?>

</ul>