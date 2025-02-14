<!DOCTYPE html>
<html>

<head>
    <title>Medicine Slip| KARAM</title>

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



        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td,
        .table th {
            border: 1px solid black;
            padding: 5px;
            word-wrap: break-word;
            max-width: 100px;
            /* Adjust as needed */
        }

        .table-striped tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .05) !important;
        }

        body {
            font-size: 13px;
        }

        .full-width {
            width: 100%;
            font-size: 11px;
        }

        .tblborder {
            border: 1px solid black;
        }

        .activity,
        .activity th,
        .activity td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .header-cell {
            background-color: #ce0f1f;
            color: #000;
            font-weight: bold;
            padding: 5px;
        }

        .table_card {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        .table_card th,
        .table_card td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table_card th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .table_card tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table_card tr:hover {
            background-color: #f1f1f1;
        }

        .table_card td {
            text-align: center;
        }

        .table-container {
            padding: 20px;
        }
    </style>
</head>

<body>
    <htmlpageheader name="myHeader1" style="display:block;">
        <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
            <tr style="">
                <td border="0" style="width:50%;float:left;text-align:left;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                    Medicine Requisition Details
                </td>
            </tr>
        </table>
    </htmlpageheader>


<div>
    <table class="table table-bordered scrolldown">
       <thead>
        <tr>
            <th style="border:1px solid black;height:50;width:40">
                <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
            </th>
            <th colspan="4" style="border:1px solid black;">
                <h3>
                   <span><b>Medical Treatment Slip</b></span>
                   <br>
                   <span><b>PN International Pvt Ltd. </b></span>
                </h3>
            </th>
            <th colspan="2" style="border:1px solid black;width:40">
                <h3>
                    <span><b>{{$isreffered->hospital_name}}</b>
                    </span>
                </h3>
            </th>
            <th colspan="4" style="border:1px solid black;">
                <table class="table table-bordered scrolldown">
                    <thead>
                        <tr>
                            <td style="border: 1px solid black;width:70;">Doc.No</td>
                            <td style="border: 1px solid black;">OF/SA/50</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                            <td style="border: 1px solid black;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                            <td style="border: 1px solid black;">0</td>
                        </tr>
                    </thead>
                </table>

            </th>
        </tr>
       </thead>
       <tbody>
        <tr>
            <td colspan="2" style="border:1px solid black;text-align: left;">
                <b>Employee Code</b>
            </td>
            <td colspan="10" style="border:1px solid black;text-align: left;">
               {{$opdpatient->emp_id}}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid black;text-align: left;">
                <b>Employee Name</b>
            </td>
            <td colspan="10" style="border:1px solid black;text-align: left;">
               {{$opdpatient->emp_name}}
            </td>
        </tr>

        <tr>
            <td colspan="2" style="border:1px solid black;text-align: left;">
                <b>Department</b>
            </td>
            <td colspan="10" style="border:1px solid black;text-align: left;">
               {{getDepartment($opdpatient->department_id)}}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid black;text-align: left;">
                <b>Department</b>
            </td>
            <td colspan="10" style="border:1px solid black;text-align: left;">
               {{($isreffered->first_aider)}}
            </td>
        </tr>

       </tbody>
    </table>
</div>





</body>

</html>
