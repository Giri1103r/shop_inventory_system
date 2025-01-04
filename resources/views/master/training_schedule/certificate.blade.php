<?php
$logo = '<img src="' . url('public/assets/images/logo-dark.png') . '" style="width:85%;">';
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .badge {
            padding: 1px 9px 2px;
            font-size: 12.025px;
            font-weight: bold;
            white-space: nowrap;
            color: #ffffff;
            background-color: #999999;
            border-radius: 9px;
        }
        @page {
            size: auto;
            odd-header-name: html_myHeader1;
            even-header-name: html_myHeader2;
            odd-footer-name: html_myFooter1;
            even-footer-name: html_myFooter2;
        }
        body {
            font-size: 13px;
            margin: 0;
            font-family: 'Open Sans', sans-serif;
        }
        table {
            border-collapse: collapse;
        }
        .table {
            width: 100%;
        }
        .pm-certificate-container {
            position: relative;
            height: 100%;
            padding: 30px;
            color: #333;
        }
        .pm-certificate-title h1 {
            font-size: 65px;
            letter-spacing: 3px;
            margin: 0;
        }
        .pm-certificate-title h4 {
            font-size: 25px;
            letter-spacing: 3px;
        }
        .pm-certificate-header, .pm-certificate-body, .pm-certificate-footer {
            text-align: center;
        }
        .pm-certificate-footer img {
            width: 15%;
        }
        .certificate-details {
            font-size: 20px;
            margin: 10px 0;
        }
        #document-container {
            background-image: url('public/assets/images/Certificate-design_1.jpg');
            background-size: cover;
            padding: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php foreach ($training_Details as $data): ?>
    <div id="document-container">
        <div class="pm-certificate-container">
            <div class="pm-certificate-title">
                <div><?php echo $logo; ?></div>
                <h1>CERTIFICATE</h1>
                <h4>OF TRAINING ON</h4>
                <p><?php echo $data->training_category_name; ?></p>
            </div>
            <div class="pm-certificate-body">
                <p class="certificate-details">
                    This certificate is proudly presented to:
                    <strong><?php echo $data->select_trainee_name; ?></strong>
                </p>
                <p class="certificate-details">
                    For successfully completing training on:
                    <strong><?php echo $data->training_topics_name; ?></strong>
                </p>
                <p class="certificate-details">
                    Training Period:
                    <strong><?php echo date('d-m-Y', strtotime($data->start_date)) . ' to ' . date('d-m-Y', strtotime($data->end_date)); ?></strong>
                </p>
                <p class="certificate-details">
                    Certificate Issue Date:
                    <strong><?php echo date('d-m-Y', strtotime($data->issue_date ?? '')); ?></strong>
                </p>
                <p class="certificate-details">
                    Certificate Expiry Date:
                    <strong><?php echo date('d-m-Y', strtotime($data->expiry_date ?? '')); ?></strong>
                </p>
            </div>
            <div class="pm-certificate-footer">
                <img src="public/assets/images/shapes.png" alt="Certificate Footer Design">
                <p>
                    This is a system-generated certificate and does not require a signature.
                    <br>
                    The certificate is valid for two years from the date of issuance.
                </p>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</body>
</html>
