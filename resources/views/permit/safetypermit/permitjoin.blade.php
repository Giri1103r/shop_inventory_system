<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KARAM | Permit QR </title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .container {
            /* background: url('{{ asset('public/assets/images/common/fleet.png') }}') no-repeat center center; */
            background-size: cover;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .qr-code {
            max-width: 300px;
            width: 100%;
            margin-top: 320px;
            margin-bottom: 10px;
        }

        .text {
            color: rgb(7, 7, 7);
            font-size: 24px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="permit-details">
            <span><strong>Date:</strong> {{ Displaydateformat($safetypermit->date) }}</span>
            <span><strong>From Time:</strong> {{ $safetypermit->time_from }}</span>
            <span><strong>To Time:</strong> {{ $safetypermit->time_to }}</span>
            <span><strong>Unit:</strong> {{ getUnitname($safetypermit->unit_id) }}</span>
            <span><strong>Exact location of job</strong> {{$safetypermit->exact_location_job }}</span>
            <span><strong>Job Location & Area</strong> {{$safetypermit->job_location_area }}</span>
            <span><strong>Created By</strong> {{getUsername($safetypermit->created_by) }}</span>
        </div>
        <img src="{{ $qrBase64 }}" alt="QR Code" class="qr-code">
        <div class="text">{{$permit_no}}</div>
    </div>
</body>
</html>
