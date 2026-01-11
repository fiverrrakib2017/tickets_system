<head>

    <meta charset="utf-8">

    <!-- <title>FAST-ISP-BILLING-SYSTEM</title> -->
    <?php 
    $settings = $con->query("SELECT * FROM app_settings LIMIT 1")->fetch_assoc();
    ?>
    <title>FAST-ISP-BILLING- <?=$settings['company_name']?? '';?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include 'style.php'; ?>
    
    <!-- Extra CSS per page -->
    <?php if (!empty($extra_css)) echo $extra_css; ?>
</head>