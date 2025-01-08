<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KARAM | Permit QR</title>

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f4f4f4;
        }

        .header {
            width: 100%;
            background-color: #fff;
            padding: 10px 0;
            border-bottom: 4px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header img {
            width: 120px;
            height: 50px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            font-family: Georgia, serif;
            color: #333;
            margin: 0;
        }

        .details-table {
            width: 100%;
            /* margin-top: 30px; */
            border-spacing: 0;
            border-collapse: collapse;
            background-color: #fff;
            
        }

        .details-table td {
            padding: 10px;
            text-align: left;
            vertical-align: middle;
            
        }

        .details-table td:nth-child(2) {
            text-align: center;
        }

        .details-table th {
            background-color: #f1f1f1;
            padding: 10px;
            font-weight: bold;
            text-align: left;
            
        }

        .qr-code-container {
            margin-top: 30px;
            text-align: center;
            border: 2px solid #000;
            padding: 20px;
            background-color: #fff;
            width: 250px;
            height: 250px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: auto;
            margin-right: auto;
        }
        .qrcode {
            margin-top: 30px;
        }


        .qr-code {
            max-width: 100%;
            max-height: 100%;
        }

        .permit-no {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }


        .permit-no span {
            color: #000;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header Section -->
        <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
            <tr style="">
                <td border="0" style="width:50%;float:left;text-align:left;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                  PTW -  {{ $permit_no }}
                </td>
            </tr>
        </table>

        <table style="width:100%; margin-top:20px;" >
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                    Safety Permit
                </td>
            </tr>
        </table>
        <table class="details-table">
            <tr>
                <td><b>Date:</b></td>
                <td>{{ Displaydateformat($safetypermit->date) }}</td>
            </tr>
            <tr>
                <td><b>From Time:</b></td>
                <td>{{ $safetypermit->time_from }}</td>
            </tr>
            <tr>
                <td><b>To Time:</b></td>
                <td>{{ $safetypermit->time_to }}</td>
            </tr>
            <tr>
                <td><b>Unit:</b></td>
                <td>{{ getUnitname($safetypermit->unit_id) }}</td>
            </tr>
            <tr>
                <td><b>Exact Location of Job:</b></td>
                <td>{{ $safetypermit->exact_location_job }}</td>
            </tr>
            <tr>
                <td><b>Job Location & Area:</b></td>
                <td>{{ $safetypermit->job_location_area }}</td>
            </tr>
            <tr>
                <td><b>Created By:</b></td>
                <td>{{ getUsername($safetypermit->created_by) }}</td>
            </tr>
        </table>
        <div class="qr-code-container">
            <div class="qrcode">
                <img src="{{ $qrBase64 }}" alt="QR Code" class="qr-code">
            </div>
        </div>

    </div>

</body>

</html>
