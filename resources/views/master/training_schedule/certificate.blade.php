<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Certificate</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            padding: 30px;
            background-image: url('{{ url('public/assets/images/Certificate-design_1.jpg') }}');
            background-size: cover;
            text-align: center;
        }

        .certificate-header {
            margin-top: 30px;
        }

        .certificate-header img {
            max-width: 150px;
        }

        .certificate-body {
            margin: 40px 0;
        }

        .certificate-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            margin: 20px 0;
        }

        .certificate-footer {
            margin-top: 40px;
        }

        .footer-image {
            width: 15%;
            margin: auto;
            display: block;
        }

        .footer-note {
            font-size: 18px;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <!-- Header Section -->
        <div class="certificate-header">
            <img src="{{ url('public/assets/images/logo-dark.png') }}" alt="Logo">
            <h1>Certificate of Training</h1>
        </div>

        <!-- Body Section -->
        <div class="certificate-body">
            <p>This is to certify that</p>
            <h3 style="font-size: 20px; font-weight: bold;">{{ $userAttendPass->emp_name ?? '---' }}</h3>
            <p>has successfully completed the training on</p>
            <h3 style="font-size: 20px; font-weight: bold;">{{ $training_details->topic_name ?? '---' }}</h3>
            <p>
                conducted from
                <strong>{{ Displaydateformat($training_details->from_date) ?? '---' }}</strong>
                to
                <strong>{{ Displaydateformat($training_details->to_date) ?? '---' }}</strong>
            </p>
            <p>at <strong>{{ $training_details->name_of_the_conference_hall ?? '---' }}</strong></p>

            <!-- Mark and Result Section -->
            <div style="margin-top: 20px;">
                <p>Marks Obtained:
                    <strong>{{ $userAttendPass->mark ?? '---' }} /
                        100</strong>
                </p>
                <p>Result:
                    <strong style="color: {{ $userAttendPass->assessment == 1 ? 'green' : 'red' }};">
                        {{ $userAttendPass->assessment == 1 ? 'Pass' : ($userAttendPass->assessment == 2 ? 'Fail' : '---') }}

                    </strong>
                </p>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="certificate-footer">
            <img src="{{ url('public/assets/images/shapes.png') }}" alt="Shapes" class="footer-image">
            <p class="footer-note">
                This is a system-generated certificate and does not require a signature.
            </p>
        </div>
    </div>
</body>


</html>
