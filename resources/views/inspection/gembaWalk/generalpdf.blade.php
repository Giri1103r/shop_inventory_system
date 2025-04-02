<!DOCTYPE html>
<html>

<head>
    <title>Gemba Walk Details| KARAM</title>

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
                    GembaWalk Details
                </td>
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
                    GembaWalk Details

                </td>
            </tr>
        </table>
    </div>

    @if ($gembaWalk_details->isNotEmpty())
        @php $gembaWalk = $gembaWalk_details->first(); @endphp

        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Gemba Walk ID</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">{{ $gembaWalk->gemba_walk_auto_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Document No</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ ($document_no->doc_no ?? '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ displaydateformat($document_no->issue_date ?? '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ ($document_no->rev_dt ?? '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created By</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername($gembaWalk->created_by ?? '') }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Created Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ displayDateformat($gembaWalk->created_at ?? '') }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Signature</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> <img src="{{ admin_url($gembaWalk_approved_singnature) }}"
                        alt="" style="height: 60px; width:60px;"></td>
            </tr>
        </table>
    @else
        <p style="text-align:center; color:red; font-weight:bold;">No Gemba Walk Details Available</p>
    @endif
    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                    Checklist Details
                </td>
            </tr>
        </table>
    </div>


    <div class="table-responsive">
        <div class="col-md-12">
            @if ($gembaWalk_details->isNotEmpty())
                <table class="table table-bordered table-hover tblborder">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Checklist ID</th>
                            <th>Location</th>
                            <th>Unit</th>
                            <th>Date of Observation</th>
                            <th> Observation Type</th>
                            <th>Description</th>
                            <th>Hazard</th>
                            <th>capa</th>
                            <th>date_of_compliance</th>
                            <th>responsibility_id</th>
                            <th>Status</th>
                            <th>remark</th>
                            <th>observation</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gembaWalk_details as $index => $gembaWalk)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $gembaWalk->gemba_walk_checklist_no ?? 'N/A' }}</td>
                                <td>{{ getLocationname($gembaWalk->location_id ?? 'N/A') }}</td>
                                <td>{{ getUnitname($gembaWalk->unit_id ?? 'N/A') }}</td>
                                <td>{{ displaydateformat($gembaWalk->date_of_observation ?? 'N/A') }}</td>
                                <td>{{ getObservationType($gembaWalk->observation_type_id ?? 'N/A') }}</td>
                                <td>{{ $gembaWalk->description ?? 'N/A' }}</td>
                                <td>{{ $gembaWalk->hazard ?? 'N/A' }}</td>
                                <td>{{ $gembaWalk->capa ?? 'N/A' }}</td>
                                <td>{{ displaydateformat($gembaWalk->date_of_compliance ?? 'N/A') }}</td>
                                <td>{{ getEmployeename($gembaWalk->responsibility_id ?? 'N/A') }}</td>
                                <td>{{ getGembaWalkStatus($gembaWalk->gemba_walk_checklist_status ?? 'N/A') }}</td>
                                <td>{{ $gembaWalk->remark ?? 'N/A' }}</td>
                                <td>
                                    @if (!empty($gembaWalk->observation))
                                        @php $observations = json_decode($gembaWalk->observation, true); @endphp
                                        @if (is_array($observations))
                                            @foreach ($observations as $key => $obs)
                                                {{ $key + 1 }}. {{ $obs }}<br>
                                            @endforeach
                                        @else
                                            N/A
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
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



    @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION)
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        CAPA Action
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            @if (isset($gembaWalk_ehs_capa_details->created_by))
                <tr>
                    <td width="50%" style="padding:5px;"><b>Created By</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ getUserName($gembaWalk_ehs_capa_details->created_by) }}
                    </td>
                </tr>
            @endif
            @if (isset($gembaWalk_ehs_capa_details->created_at))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ Displaydateformat($gembaWalk_ehs_capa_details->created_at) }}</td>
                </tr>
            @endif
            @if (isset($gembaWalk_ehs_capa_details->capa))
                <tr>
                    <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        @if ($gembaWalk_ehs_capa_details->capa == 1)
                            Yes
                        @elseif ($gembaWalk_ehs_capa_details->capa == 0)
                            No
                        @else
                            {{ $gembaWalk_ehs_capa_details->capa }}
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">N/A</td>
                </tr>
            @endif
            @if (isset($gembaWalk_ehs_capa_details->remarks))
                <tr>
                    <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">{{ $gembaWalk_ehs_capa_details->remarks }}</td>
                </tr>
            @else
                <tr>
                    <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">N/A</td> <!-- Displaying 'N/A' if Remarks is not set -->
                </tr>
            @endif
        </table>
        <br>
    @endif
    <br>

    @if (
        $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION ||
            $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_REJECTED)
        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            CAPA Action
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                @if (isset($gembaWalk_ehs_capa_details->created_by))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Created By</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUserName($gembaWalk_ehs_capa_details->created_by) }}
                        </td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_capa_details->created_at))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ Displaydateformat($gembaWalk_ehs_capa_details->created_at) }}</td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_capa_details->capa))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            @if ($gembaWalk_ehs_capa_details->capa == 1)
                                Yes
                            @elseif ($gembaWalk_ehs_capa_details->capa == 0)
                                No
                            @else
                                {{ $gembaWalk_ehs_capa_details->capa }}
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">N/A</td>
                    </tr>
                @endif

                @if (isset($gembaWalk_ehs_capa_details->remarks))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">{{ $gembaWalk_ehs_capa_details->remarks }}</td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">N/A</td>
                        <!-- Displaying 'N/A' if Remarks is not set -->
                    </tr>
                @endif
            </table>
        </div>

        <br>

        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            Floor Manager Action
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                @if (isset($gembaWalk_ehs_floor_manager_details->created_by))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Verified By</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUserName($gembaWalk_ehs_floor_manager_details->created_by) }}
                        </td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_floor_manager_details->created_at))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at) }}</td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_floor_manager_details->capa))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">{{ $gembaWalk_ehs_floor_manager_details->capa }}</td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">N/A</td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_floor_manager_details->remarks))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">{{ $gembaWalk_ehs_floor_manager_details->remarks }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">N/A</td>
                    </tr>
                @endif
            </table>
        </div>
        <br>
    @endif

    @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_CLOSED)
        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            CAPA Action
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                @if (isset($gembaWalk_ehs_capa_details->created_by))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Created By</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUserName($gembaWalk_ehs_capa_details->created_by) }}
                        </td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_capa_details->created_at))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ Displaydateformat($gembaWalk_ehs_capa_details->created_at) }}</td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_capa_details->capa))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            @if ($gembaWalk_ehs_capa_details->capa == 1)
                                Yes
                            @elseif ($gembaWalk_ehs_capa_details->capa == 0)
                                No
                            @else
                                {{ $gembaWalk_ehs_capa_details->capa }}
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">N/A</td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_capa_details->remarks))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">{{ $gembaWalk_ehs_capa_details->remarks }}</td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">N/A</td>
                        <!-- Displaying 'N/A' if Remarks is not set -->
                    </tr>
                @endif
            </table>
        </div>
        <br>
        @if (isset($gembaWalk_ehs_floor_manager_details))

            <div>
                <div style="width:100%;">
                    <table style="width:100%;">
                        <tr>
                            <td
                                style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                                Floor Manager Action
                            </td>
                        </tr>
                    </table>
                </div>
                <table width="100%" style="width:100%;">
                    @if (isset($gembaWalk_ehs_floor_manager_details->created_by))
                        <tr>
                            <td width="50%" style="padding:5px;"><b>Verified By</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">
                                {{ getUserName($gembaWalk_ehs_floor_manager_details->created_by) }}
                            </td>
                        </tr>
                    @endif
                    @if (isset($gembaWalk_ehs_floor_manager_details->created_at))
                        <tr>
                            <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">
                                {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at) }}</td>
                        </tr>
                    @endif
                    @if (isset($gembaWalk_ehs_floor_manager_details->capa))
                        <tr>
                            <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">{{ $gembaWalk_ehs_floor_manager_details->capa }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td width="50%" style="padding:5px;"><b>CAPA</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">N/A</td>
                        </tr>
                    @endif
                    @if (isset($gembaWalk_ehs_floor_manager_details->remarks))
                        <tr>
                            <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">
                                {{ $gembaWalk_ehs_floor_manager_details->remarks }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">N/A</td>
                        </tr>
                    @endif
                </table>
            </div>

        @endif
        <br>

        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            EHS Officer Action
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                @if (isset($gembaWalk_ehs_verificatioin_details->created_by))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Approved By</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUserName($gembaWalk_ehs_verificatioin_details->created_by) }}
                        </td>
                    </tr>
                @endif
                @if (isset($gembaWalk_ehs_verificatioin_details->created_at))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Date</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ Displaydateformat($gembaWalk_ehs_verificatioin_details->created_at) }}</td>
                    </tr>
                @endif

                @if (isset($gembaWalk_ehs_verificatioin_details->remarks))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">{{ $gembaWalk_ehs_verificatioin_details->remarks }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">N/A</td>
                    </tr>
                @endif
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> <img src="{{ admin_url($gembaWalk_verified_singnature) }}"
                            alt="" style="height: 60px; width:60px;"></td>
                </tr>
            </table>
        </div>


    @endif

    <br>
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

        <div class="card-body ">
            <div class="row">
                <div class="card-header-inner">
                    <h4 class="text-white">Status logs</h4>
                </div>
            </div>

            <div class="table-responsive">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>From Status</th>
                                <th>To Status</th>
                                <th>Approved By</th>
                                <th>Remarks</th>
                                <th>Date</th>

                            </tr>
                        </thead>

                        <tbody>
                            @if ($status_log->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="5">No data is available</td>
                                </tr>
                            @else
                                @foreach ($status_log as $status)
                                    <tr>
                                        <td>{{ isset($status['to_status']) ? $status['to_status'] : '-' }}
                                        </td>
                                        <td>{{ isset($status['status_name']) ? $status['status_name'] : '-' }}
                                        </td>
                                        <td>{{ isset($status['approved_by']) ? getUsername($status['approved_by']) : '-' }}
                                        </td>
                                        <td>{{ isset($status['remarks']) ? $status['remarks'] : '-' }}
                                        </td>
                                        <td>{{ null !== Displaydateformat($status['created_at']) ? Displaydateformat($status['created_at']) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach

                            @endif
                        </tbody>
                    </table>

                </div>
            </div>


        </div>
        <br>
    </div>
</body>

</html>
