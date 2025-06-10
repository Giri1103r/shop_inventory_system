<!DOCTYPE html>
<html>

<head>
    <title>Medicine Requisition Slip Floor| KARAM</title>

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
            even-header-name: html_myHeader1;
            odd-footer-name: html_myFooter1;
            even-footer-name: html_myFooter1;
        }

        @page noheader {
            odd-header-name: _blank;
            even-header-name: _blank;
            odd-footer-name: _blank;
            even-footer-name: _blank;
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

        .page-break {
            page-break-before: always;
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

            </tr>
        </table>
    </htmlpageheader>

    <htmlpagefooter name="myFooter1" style="display:none">
        <table width="100%"
            style="width:100%;border:0;background-color: #FFF;border-top: 4px solid #000;padding-top:10px;padding-bottom:10px;">
            <tr>
                <td width="33%">
                    <span style="font-style: italic;">{DATE d-m-Y}</span>
                </td>
                <td width="33%" align="center" style="font-weight: bold; font-style: italic;">
                </td>
                <td width="33%" style="text-align: right;">
                    {PAGENO}/{nbpg}
                </td>
            </tr>
        </table>
    </htmlpagefooter>


    @foreach ($content as $details)
        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        MEDICAL REQUISITION SLIP
                    </td>
                </tr>
            </table>
        </div>
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black; margin-top:10px">
            <tr>
                <th colspan="6" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="6" style="border:1px solid black;">
                    <h3>
                        <span><b>MEDICAL REQUISITION SLIP
                            </b></span>
                        <br>

                    </h3>
                </th>

                <th colspan="6" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">{{ __('inspection.doc_no') }}</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">{{ __('inspection.issue_date') }}</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">{{ __('inspection.rev_date') }}</td>
                                <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>

                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="6">
                    DEPARTMENT: {{ getDepartment($details->department) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="6">
                    UNIT: {{ getUnitname($details->unit) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="6">
                    DATE: {{ Displaydateformat($details->date ?? 'N/A') }}
                </th>
            </tr>


            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">SR. NO</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">NAME OF
                    MEDICINE
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">FREEZE
                    QUANTITY
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">QUANTITY
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">REMARKS
                </th>

            </tr>
            @php
                $inspection_data = json_decode($details->checklist, true);
            @endphp


            @foreach ($inspection_data as $medicines)
                <tr>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}
                    </td>
                    <td colspan="6" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ getMedicinename($medicines['medicine_id']) }}
                    </td>
                    <td colspan="2" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['freeze_quantity'] }}
                    </td>

                    <td colspan="2" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['quantity'] }}
                    </td>

                    <td colspan="6" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['remarks'] }}
                    </td>
                </tr>
            @endforeach



            {{-- <tr>
                @php
                    $createdSignature = GetOHCSignature(
                        $details->created_by,
                        $details->id,
                        OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    );
                    $FloorManagerSignature = GetOHCSignature(
                        $details->verified_by,
                        $details->id,
                        OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    );
                    $SafetyOfficerManagerSignature = GetOHCSignature(
                        $details->approved_by,
                        $details->id,
                        OHC_TYPE_MEDICINE_REQUISTION_FLOOR,
                    );
                @endphp

                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    <img src="{{ admin_url($createdSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Requestor Signature </div>
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    <img src="{{ admin_url($FloorManagerSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Floor Manager Signature</div>
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="8">
                    <img src="{{ admin_url($SafetyOfficerManagerSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Medical Assitant / Safety Officer Signature</div>
                </th>

            </tr> --}}

            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    @if ($details->created_by != null)
                        <div style="margin-top: 5px;">Requestor Name :- {{ getUsername($details->created_by) }} </div>
                    @else
                        <div style="margin-top: 5px;">Not yet prepared</div>
                    @endif
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    @if ($details->verified_by != null)
                        <div style="margin-top: 5px;"> Floor Manager Name :- {{ getUsername($details->verified_by) }}
                        </div>
                    @else
                        <div style="margin-top: 5px;">Has Not yet been Verified</div>
                    @endif

                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="8">
                    @if ($details->approved_by != null)
                        <div style="margin-top: 5px;"> Medical Assitant / Safety Officer Name :-
                            {{ getUsername($details->approved_by) }} </div>
                    @else
                        <div style="margin-top: 5px;">Has Not yet been Approved</div>
                    @endif
                </th>

            </tr>


        </table>
        <div class="page-break"></div>
    @endforeach



    <br>

</body>

</html>
