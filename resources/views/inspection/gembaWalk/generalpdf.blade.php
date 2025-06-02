<!DOCTYPE html>
<html>

<head>
    <title>Gemba Walk Inspection (Safety Observation)| KARAM</title>

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

        .page-break {
            page-break-before: always;
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
                    Gemba Walk (Safety Observation)
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
                    Gemba Walk Inspection (Safety Observation)

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
                    {{ $document_no->doc_no ?? '' }}
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
                    {{ $document_no->rev_dt ?? '' }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Shift Name</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getShift($gembaWalk->shift_id ?? '') }}
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

            {{-- <tr>
                <td width="50%" style="padding:5px;"><b>Signature</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> <img src="{{ admin_url($gembaWalk_approved_singnature) }}"
                        alt="" style="height: 60px; width:60px;"></td>
            </tr> --}}
        </table>
    @else
        <p style="text-align:center; color:red; font-weight:bold;">No Gemba Walk Details Available</p>
    @endif

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                    {{ __('inspection.checklist_details') }}
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
                            <th>Location</th>
                            <th>Unit</th>
                            <th>Department</th>
                            <th>Exact Location</th>
                            <th>Date of Observation</th>
                            <th>Observation Time</th>
                            <th> Observation Type</th>
                            <th>Description</th>
                            <th>Risk Category</th>
                            <th>Hazard</th>
                            <th>Evidence</th>
                            <th>Recommended CAPA</th>

                            <th>Status</th>
                            <th>Closing Evidence</th>
                            <th>Remark</th>
                            <th>Recommanded Person for CAPA</th>
                            <th>Name Of the Observer</th>



                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gembaWalk_details as $index => $gembaWalk)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ getLocationname($gembaWalk->location_id ?? 'N/A') }}</td>
                                <td>{{ getUnitname($gembaWalk->unit_id ?? 'N/A') }}</td>
                                <td>{{ getDepartment($gembaWalk->department_id ?? 'N/A') }}</td>
                                <td>{{ $gembaWalk->exact_location ?? 'N/A' }}</td>
                                <td>{{ displaydateformat($gembaWalk->date_of_observation ?? 'N/A') }}</td>
                                <td>{{ $gembaWalk->time ?? 'N/A' }}</td>
                                <td>{{ getObservationType($gembaWalk->observation_type_id ?? 'N/A') }}</td>
                                <td>{{ $gembaWalk->description ?? 'N/A' }}</td>
                                <td>{{ getRiskcategory($gembaWalk->risk_category ?? 'N/A') }}</td>
                                <td>
                                </td>
                                <td>
                                    @if (!empty($gembaWalk->file_path))
                                        <img src="{{ $gembaWalk->file_path }}" style="width: 100px; height: auto;">
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>{{ $gembaWalk->capa ?? 'N/A' }}</td>

                                <td>{{ getGembaWalkStatus($gembaWalk->gemba_walk_checklist_status ?? 'N/A') }}</td>
                                 <td>
                                    @if (!empty($closingEvidence->file_path))
                                        <img src="{{ $closingEvidence->file_path }}"
                                            style="width: 100px; height: auto;">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $gembaWalk->remark ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $responsibility_id = explode(',', $gembaWalk->responsibility_id);
                                    @endphp
                                    @foreach ($responsibility_id as $responsibilityId)
                                        {{ getUsername($responsibilityId) }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>

                                <td>{{ getUsername($gembaWalk->created_by ?? 'N/A') }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="card-body">
                    <p class="text-dark">{{ __('No status data available.') }}</p>
                </div>
            @endif
        </div>
    </div>
    <br>




    <br>

    @if (
        $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION ||
            $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_REJECTED)


        <br>

        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            Action Taken
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
                        <td width="50%" style="padding:5px;"><b>Date of Compliance</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at) }}</td>
                    </tr>
                @endif
                {{-- @if (isset($gembaWalk_ehs_floor_manager_details->capa))
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
                @endif --}}
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
                        <td width="48%" style="padding:5px;">-</td>
                    </tr>
                @endif
            </table>
        </div>
        <br>
    @endif

    @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_CLOSED)

        <br>
        @if (isset($gembaWalk_ehs_floor_manager_details))

            <div>
                <div style="width:100%;">
                    <table style="width:100%;">
                        <tr>
                            <td
                                style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                                Action Taken
                            </td>
                        </tr>
                    </table>
                </div>
                <table width="100%" style="width:100%;">
                    @if (isset($gembaWalk_ehs_floor_manager_details->created_by))
                        <tr>
                            <td width="50%" style="padding:5px;"><b>{{ __('inspection.observer_name') }}</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">
                                {{ getUserName($gembaWalk_ehs_floor_manager_details->created_by) }}
                            </td>
                        </tr>
                    @endif
                    @if (isset($gembaWalk_ehs_floor_manager_details->created_at))
                        <tr>
                            <td width="50%" style="padding:5px;"><b>Date of Compliance</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">
                                {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at) }}</td>
                        </tr>
                    @endif
                    {{-- @if (isset($gembaWalk_ehs_floor_manager_details->capa))
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
                    @endif --}}
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
                            <td width="48%" style="padding:5px;">-</td>
                        </tr>
                    @endif

                    {{-- @if ($gembaWalk)
                        <tr>
                            <td width="50%" style="padding:5px;"><b>Images</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;"> <a href="{{ asset($gembaWalk->file_path) }}"
                                    target="_blank">
                                    <img src="{{ asset('public/' . $gembaWalk->file_path) }}" alt="image"
                                        style="max-width: 100px; max-height: 100px;">
                                </a></td>
                        </tr>
                    @else
                        <small class="text-muted">No file uploaded yet.</small>
                    @endif --}}

                    @if (isset($gembaWalk_ehs_floor_manager_details->capa_action_date))
                        <tr>
                            <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_action_date') }}</b>
                            </td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">
                                {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->capa_action_date) }}</td>
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
                        <td width="48%" style="padding:5px;">-</td>
                    </tr>
                @endif
                {{-- <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> <img src="{{ admin_url($gembaWalk_verified_singnature) }}"
                            alt="" style="height: 60px; width:60px;"></td>
                </tr> --}}
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
                                        <td>{{ getGembaWalkLogStatus($status['from_status'] ?? null) }}</td>
                                        <td>{{ getGembaWalkLogStatus($status['to_status'] ?? null) }}</td>
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
        <div class="page-break"></div>
    </div>
</body>

</html>
