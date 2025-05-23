<!DOCTYPE html>
<html>

<head>
    <title>Weekly Ambulance Inspection Checklist | KARAM</title>

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
                    Weekly Ambulance Inspection Checklist</td>
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

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Weekly Ambulance Inspection Checklist
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Document Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($weeklyAmbulance->created_by) ? $weeklyAmbulance->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($weeklyAmbulance->created_at) }}</td>
        </tr>
    </table>

    <br>
    @php
        $user_response = json_decode($weeklyAmbulance->checklist, true);
    @endphp
    {{-- <div class="table-responsive">
        <div class="col-md-12">
            <table class="table table-bordered table-hover tblborder">
                <thead>
                    <tr>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            Sr. No
                        </th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;"
                            colspan="4">
                            Check Points
                        </th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            Status
                        </th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            Remarks
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $decodedData = json_decode($inspectionCkeclist->checklist, true);
                        $checkItems = $decodedData['check_item'] ?? [];
                        $statuses = $decodedData['status'] ?? [];
                        $remarks = $decodedData['remarks'] ?? [];
                        $srNo = 1;
                    @endphp

                    @foreach ($checkItems as $groupId => $checkPoints)
                        @php $rowCount = count($checkPoints); @endphp

                        @foreach ($checkPoints as $index => $checkPoint)
                            <tr>
                                @if ($index == 0)
                                    <td rowspan="{{ $rowCount }}"
                                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                        {{ $srNo++ }}
                                    </td>
                                    <td rowspan="{{ $rowCount }}"
                                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                        {{ getSubcategoryname($groupId) }}
                                    </td>
                                @endif
                                <td colspan="3" style="border: 1px solid black; padding: 8px;">
                                    {{ getSubcategoryDataname($checkPoint) }}
                                </td>
                                <td
                                    style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                    @if (!empty($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Ok')
                                        <span style="color: green; font-size: 20px;">✓</span>
                                    @elseif (!empty($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Not-Ok')
                                        <span style="color: red; font-size: 20px;">X</span>
                                    @else
                                        <i class="fa-solid fa-minus" style="color: #808080; width: 15px;"></i>
                                    @endif
                                </td>
                                <td style="border: 1px solid black; padding: 8px;">
                                    {{ $remarks[$checkPoint] ?? 'No Remarks' }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> --}}

    <table
        style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
        <tr>
            <th colspan="4" style="border:1px solid black;height:50;width:40">
                <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
            </th>
            <th colspan="6" style="border:1px solid black;">
                <h3>
                    <span><b>WEEKLY AMBULANCE INSPECTION CHECKLIST</b></span>
                    <br>

                </h3>
            </th>

            <th colspan="6" style="border:1px solid black;">
                <table class="table table-bordered scrolldown">
                    <thead>
                        <tr>
                            <td style="border: 1px solid black;width:70;">Doc.No</td>
                            <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                            <td style="border: 1px solid black;">{{ $document_no->issue_date }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                            <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                        </tr>
                    </thead>
                </table>

            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                DATE OF INSPECTION: {{ Displaydateformat($weeklyAmbulance->date_of_inspection) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                UNIT: {{ getUnitname($weeklyAmbulance->unit) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                SHIFT: {{ $weeklyAmbulance->shift ?? 'N/A' }}
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="8">
                NEXT DUE: {{ Displaydateformat($weeklyAmbulance->next_due) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="8">
                LOCATION: {{ getLocationname($weeklyAmbulance->location) ?? 'N/A' }}
            </th>

        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">SR. NO</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">CHECK ITEMS
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">OK/NOT-OK
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">REMARKS
            </th>

        </tr>

        @php
            $checklist = json_decode($weeklyAmbulance->checklist, true);
        @endphp

        @foreach ($checklist['check_item'] as $groupId => $items)
            @foreach ($items as $itemId)
                <tr>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}</td>
                    <td colspan="4"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ getSubcategoryDataname($itemId) }}</td>
                    <td colspan="5"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">

                        @php
                            $status = strtolower(trim($checklist['status'][$itemId] ?? ''));
                        @endphp

                        @if ($status === 'ok')
                            <span style="color: green; font-size: 20px;">Ok</span>
                        @elseif ($status === 'not ok')
                            <span style="color: red; font-size: 20px;">Not Ok</span>
                        @else
                            <span style="color: red; font-size: 20px;">N/A</span>
                        @endif
                    </td>

                    <td colspan="5"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $checklist['remarks'][$itemId] ?? '' }}</td>
                </tr>
            @endforeach
        @endforeach





    </table>




    @if ($weeklyAmbulance->approve_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('inspection.ehs_officer_verify') }}
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            @if (isset($weeklyAmbulance->verified_by))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ getUserName($weeklyAmbulance->verified_by) }}</td>
                </tr>
            @endif
            @if (isset($weeklyAmbulance->created_at))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($weeklyAmbulance->created_at) }}
                    </td>
                </tr>
            @endif
            @if (isset($weeklyAmbulance->approved_by))
                @if ($weeklyAmbulance->verified_by == $weeklyAmbulance->approved_by)
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUsername($weeklyAmbulance->approved_by) }}
                        </td>
                    </tr>
                @endif
            @endif
            @if (isset($weeklyAmbulance->capa_recomendation))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_recomendation') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $weeklyAmbulance->capa_recomendation }}
                    </td>
                </tr>
            @else
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.remarks') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $weeklyAmbulance->remarks }}
                </tr>
            @endif
        </table>
        <br>
    @endif

    @if (isset($weeklyAmbulance->capa_remarks))
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('inspection.fire_associate_action') }}
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.name') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ getUserName($weeklyAmbulance->created_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($weeklyAmbulance->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_action_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $weeklyAmbulance->capa_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if ($weeklyAmbulance->capa_ehs_remarks)
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('inspection.ehs_officer_reverification') }}
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ getUserName($weeklyAmbulance->verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($weeklyAmbulance->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_reverifcation_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $weeklyAmbulance->capa_ehs_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if (isset($weeklyAmbulance->level_one_manager_remarks))
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('inspection.level_one_manager_action') }}
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUserName($weeklyAmbulance->l1_manager_verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($weeklyAmbulance->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $weeklyAmbulance->level_one_manager_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif

    @if (isset($weeklyAmbulance->level_two_manager_remarks))
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('inspection.level_two_manager_action') }}
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUserName($weeklyAmbulance->l2_manager_verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($weeklyAmbulance->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername($weeklyAmbulance->approved_by) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_two_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $weeklyAmbulance->level_two_manager_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    <div>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                        Status Logs
                    </td>
                </tr>
            </table>
        </div>
        <div class="table-responsive">
            <div class="col-md-12">
                @if (isset($statuslog) && $statuslog->isNotEmpty())
                    <table class="table table-bordered table-hover tblborder">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>From Status</th>
                                <th>To Status</th>
                                <th>Remarks</th>
                                <th>Approved By</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statuslog as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ getInspectionStatus($log->from_status) }}</td>
                                    <td>{{ getInspectionStatus($log->to_status) }}</td>
                                    <td>{{ $log->remarks ?? 'N/A' }}</td>
                                    <td>{{ getUserName($log->approved_by) ? getUserName($log->approved_by) : '-' }}
                                    </td>
                                    <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}</td>
                                    <td>{{ displaydateformat($log->created_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="text-dark">{{ __('No status logs available.') }}</p>
                    </div>
                @endif
            </div>
        </div>
        <br>
    </div>
    <br>

</body>

</html>
