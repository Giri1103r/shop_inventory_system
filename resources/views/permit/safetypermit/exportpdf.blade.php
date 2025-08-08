<html>

<head>
    <title>Safety Work Permit | KARAM</title>
    <meta charset="UTF-8">
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
    </style>
</head>

<body>
    <htmlpageheader name="myHeader1" style="display:block;">
        <htmlpageheader name="myHeader1" style="display:block;">
            <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
                <tr style="">
                    <td border="0" style="width:50%;float:left;text-align:left;">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                    </td>
                    <td border="0"
                        style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                        Safety Work Permit
                    </td>
                </tr>
            </table>


        </htmlpageheader>


        <htmlpagefooter name="myFooter1" style="display:none">
            <table width="100%"
                style="width:100%;border:0;background-color: #FFF;border-top: 4px solid #070707;padding-top:0px;padding-bottom:10px;">
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
                        style="width:100%;background-color: #ce0f1f;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                        Safety Work Permit
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Work Permit No</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->permit_id) ? $safetypermit->permit_id : '' }}</td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat(isset($safetypermit->date) ? $safetypermit->date : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>To Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat(isset($safetypermit->to_date) ? $safetypermit->to_date : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Company</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getCompanyname(isset($safetypermit->company_id) ? $safetypermit->company_id : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Time(From)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->time_from) ? $safetypermit->time_from : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Time(To)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->time_to) ? $safetypermit->time_to : '' }}</td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Unit</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUnitname(isset($safetypermit->unit_id) ? $safetypermit->unit_id : '') }}</td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Exact location of job</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->exact_location_job) ? $safetypermit->exact_location_job : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Job Location & Area</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->job_location_area) ? $safetypermit->job_location_area : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created By</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername(isset($safetypermit->created_by) ? $safetypermit->created_by : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ displayDateformat($safetypermit->created_at) }}</td>
            </tr>

        </table>


        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td style="width:100%;background-color: #6c757d;color:#fff;padding: 10px 10px 10px;">
                        Type of Job
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="100%" style="padding:5px;">
                    @if (isset($safetypermit->sub_permit_names) && isset($safetypermit->sub_permit_images))
                        @php
                            $names = is_array($safetypermit->sub_permit_names)
                                ? $safetypermit->sub_permit_names
                                : explode(', ', $safetypermit->sub_permit_names);
                            $images = is_array($safetypermit->sub_permit_images)
                                ? $safetypermit->sub_permit_images
                                : explode(', ', $safetypermit->sub_permit_images);
                            $count = max(count($names), count($images));
                        @endphp
                        <table width="100%">
                            <tr>
                                @for ($i = 0; $i < $count; $i++)
                                    <td style="padding:5px; text-align: left; vertical-align: top;">
                                        <div style="display: flex; align-items: center;">
                                            @if (isset($images[$i]) && $images[$i] != '')
                                                <img src="{{ asset($images[$i]) }}" alt="Permit Image"
                                                    style="width: 50px; height: 50px; margin-right: 10px;">
                                            @endif
                                            @if (isset($names[$i]) && $names[$i] != '')
                                                <span>{{ $names[$i] }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    @if (($i + 1) % 3 == 0)
                                    @endif
                                @endfor
                            </tr>
                        </table>
                    @endif
                </td>
            </tr>


        </table>

        <table width="100%" style="width:100%;">
            <tr>
                <td width="20%" style="padding:5px;"><b>Job Description</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->job_description) ? $safetypermit->job_description : '' }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Shut Down Required (Yes/No)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($safetypermit->shutdown_req == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @elseif ($safetypermit->shutdown_req == 0)
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @else
                        <p>N/A</p>
                    @endif

                </td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Taken By (Name & Department)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->shut_down_takenby) ? $safetypermit->shut_down_takenby : 'N/A' }}</td>
            </tr>

            <tr>
                <td width="20%" style="padding:5px;"><b>Isolation/LOTO Required (Yes/No)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($safetypermit->loto_req == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @elseif ($safetypermit->loto_req == 0)
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @else
                        <p>N/A</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Taken By (Name & Department)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->loto_takenby) ? $safetypermit->loto_takenby : 'N/A' }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Loto No</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($safetypermit->loto_no) ? $safetypermit->loto_no : 'N/A' }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Tag Field properly (Yes/No)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($safetypermit->tagfield == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @elseif ($safetypermit->tagfield == 0)
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @else
                        <p>N/A</p>
                    @endif
                </td>
            </tr>
        </table>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                        List of Workman involved in Job
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <div class="table-responsive">
            <div class="col-md-12">
                <table class="table table-bordered table-hover">
                    <thead class=" text-white" style="background-color:#5b626b">
                        <tr>
                            <th>Employee Code / Visitor ID</th>
                            <th>Name of Workman</th>
                            <th>Designation</th>
                            <th>Department / Company</th>
                            <th>Nature of Job</th>
                        </tr>
                    </thead>

                    <tbody id="workman-list-entries">
                        @if (empty($workmaninvolved) ||
                                $workmaninvolved->every(function ($item) {
                                    return is_null($item->emp_id) &&
                                        is_null($item->workman_name) &&
                                        is_null($item->workman_desig) &&
                                        is_null($item->department_name) &&
                                        is_null($item->nature_of_job);
                                }))
                            <tr>
                                <td colspan="5" class="text-center">No data is available</td>
                            </tr>
                        @else
                            @foreach ($workmaninvolved as $workman)
                                <tr>
                                    <td>{{ $workman->emp_id }}</td>
                                    <td>{{ $workman->workman_name }}</td>
                                    <td>{{ $workman->workman_desig }}</td>
                                    <td>{{ $workman->department_name }}</td>
                                    <td>{{ $workman->nature_of_job }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
        </div>
        <br>

        <table width="100%" style="width:100%;">
            <tr>
                <td width="20%" style="padding:5px;"><b>Are all above employee competent for
                        assigned job & physically fit for duty (Yes/No)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($safetypermit->assigned_job == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @endif
                </td>
            </tr>

            <tr>
                <td width="20%" style="padding:5px;"><b>Total number of attendance in Tool box Talk</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $safetypermit->attendance_toolbox_talk }}
                </td>
            </tr>
        </table>


        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td style="width:100%;background-color: #6c757d;color:#fff;padding: 10px 10px 10px;">
                        State of Isolation & LOTO
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">

            <tr>
                @foreach (['Air', 'Gas', 'Electrical', 'Water/Liquid'] as $index => $item)
                    <td style="padding:10px; text-align: center; vertical-align: top; ">
                        @switch($item)
                            @case('Air')
                                <img src="{{ url('public/assets/images/safetypermit/person.png') }}" class="img-fluid"
                                    style="width: 50px; height: 50px;">
                            @break

                            @case('Gas')
                                <img src="{{ url('public/assets/images/safetypermit/natural-gas.png') }}" class="img-fluid"
                                    style="width: 50px; height: 50px;">
                            @break

                            @case('Electrical')
                                <img src="{{ url('public/assets/images/safetypermit/electrician.png') }}" class="img-fluid"
                                    style="width: 50px; height: 50px;">
                            @break

                            @case('Water/Liquid')
                                <img src="{{ url('public/assets/images/safetypermit/leak.png') }}" class="img-fluid"
                                    style="width: 50px; height: 50px;">
                            @break
                        @endswitch

                        <div style="margin-top: 5px; font-weight: bold;">{{ $item }}</div>

                        <span style="margin-top: 5px; display: inline-block;">
                            @if (isset($stateIsolationLoto) && in_array($item, $stateIsolationLoto))
                                <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                            @else
                                <b><i class="fa-solid fa-xmark" style="color: #ff0000; width: 15px;">✘</i></b>
                            @endif
                        </span>
                    </td>

                    @if (($index + 1) % 4 == 0)
                    @endif
                @endforeach
            </tr>

            @if ($stateIsolationLoto)
                <tr>
                    @foreach ($stateIsolationLoto as $item)
                        @if (!in_array($item, ['Air', 'Gas', 'Electrical', 'Water/Liquid']))
                            <td colspan="4" style="padding:10px;">
                                <div style="font-weight: bold;">Others (if any, please specify):</div>
                                <div style="margin-top: 5px;">{{ $item }}</div>
                            </td>
                            @break
                        @endif
                    @endforeach
                </tr>
            @endif

            <tr>
                <td width="20%" style="padding:5px;"><b>Isolation fire panel</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($safetypermit->isolationpanel_checkbox == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @else
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @endif
                </td>
            </tr>

            <tr>
                <td width="20%" style="padding:5px;"><b>Isolation fire panel Description</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $safetypermit->isolationpanel_description ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td style="width:100%;background-color: #6c757d;color:#fff;padding: 10px 10px 10px;">
                        Applicable for Confined Space Entry
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="20%" style="padding:5px;"><b>O2</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $confined_space_entry->o2_percentage ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>System Isolated</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if (isset($confined_space_entry) && $confined_space_entry->system_isolated == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @elseif (isset($confined_space_entry) && $confined_space_entry->system_isolated == 0)
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @else
                        <p>N/A</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Rescue System Available</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if (isset($confined_space_entry) && $confined_space_entry->rescue_system == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @elseif (isset($confined_space_entry) && $confined_space_entry->rescue_system == 0)
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @else
                        <p>N/A</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Confined Space Attendant</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if (isset($confined_space_entry) && $confined_space_entry->confined_attendant == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @elseif (isset($confined_space_entry) && $confined_space_entry->confined_attendant == 0)
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @else
                        <p>N/A</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Attendant Name</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $confined_space_entry->attendant_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Register for entry & exits</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if (optional($confined_space_entry)->register_entry_exits == 'on')
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @else
                        <b><i class="fa-solid fa-times" style="color: #f31008; width: 15px;">✖</i></b>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>Any Other Gas / PPM</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $confined_space_entry->other_gas ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>PPM and is therefore safe to enter
                        from</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $confined_space_entry->ppm_safe_to_enter ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding:5px;"><b>To</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $confined_space_entry->to ?? 'N/A' }}</td>
            </tr>
        </table>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                        Protective Equipment's to be Worn
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">
            @foreach ($safetypermit->mapped_protective_equip as $job => $details)
                @foreach ($details['checkpoint_names'] as $checkpoint_name)
                    <tr>
                        <td width="20%" style="padding:5px;"><b>{{ $checkpoint_name }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                        Name of Equipment's involved in Job
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">
            @foreach ($safetypermit->mapped_equiment_involved as $job => $details)
                @foreach ($details['checkpoint_names'] as $checkpoint_name)
                    <tr>
                        <td width="20%" style="padding:5px;"><b>{{ $checkpoint_name }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                        </td>
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <td width="20%" style="padding:5px;"><b>Other If any</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $safetypermit->equiment_involved_others }}
                </td>
            </tr>
        </table>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                        Precaution To be Taken
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">
            @foreach ($safetypermit->mapped_precaution_taken as $job => $details)
                @foreach ($details['checkpoint_names'] as $checkpoint_name)
                    <tr>
                        <td width="20%" style="padding:5px;"><b>{{ $checkpoint_name }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                        Equipment's Check List
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">
            @foreach ($safetypermit->mapped_equipment_checklist as $job => $details)
                @foreach ($details['checkpoint_names'] as $checkpoint_name)
                    <tr>
                        <td width="20%" style="padding:5px;"><b>{{ $checkpoint_name }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                        </td>
                    </tr>
                @endforeach
            @endforeach


            <tr>
                <td width="20%" style="padding:5px;"><b>All Involved Equipment's have been
                        inspected as per the inspection checklist prior to start work (Yes/No)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($safetypermit->equipment_checklist_inspection == 1)
                        <b><i class="fa-solid fa-check" style="color: #267709; width: 15px;">✔</i></b>
                    @endif
                </td>
            </tr>
        </table>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                        Safe Work Instructions
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;font-family: freeserif; ">
            @foreach ($safetypermit->mapped_safework_instruction as $job => $details)
                @foreach ($details['checkpoint_names'] as $checkpoint_name)
                    <tr>
                        <td width="20%" style="padding:5px;"><b>{{ $checkpoint_name }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            <span style="color: #267709; width: 15px;">✔</span>
                        </td>
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <td width="20%" style="padding:5px;"><b>Safe Work Procedure discussed in tool box
                        talk before start the work
                        (Yes/No)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($safetypermit->toolbox_talk == 1)
                        <span style="color: #267709; width: 15px;">✔</span>
                    @endif
                </td>
            </tr>

            <tr>
                <td width="20%" style="padding:5px;"><b>Tool box Talk Given By (Name)</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $safetypermit->talk_givenby }}
                </td>
            </tr>
        </table>




        @if (isset($getEhSverification) && $safetypermit['permit_status'] >= 2)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            EHS Verification
                        </td>
                    </tr>
                </table>
            </div>
            <table>

                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('Approver Name') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($getEhSverification->approve_reject_by) ? $getEhSverification->approve_reject_by : '' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('Date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ displaydateformat(isset($getEhSverification->date) ? $getEhSverification->date : '') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('Time') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ displaytimeformat($getEhSverification && $getEhSverification->created_at ? $getEhSverification->created_at : '') }}

                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Additional suggestion</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($getEhSverification->remarks) ? $getEhSverification->remarks : '' }}</td>
                </tr>
                <tr>
                    <td style="padding:5px;"><b>{{ __('Site Images') }}</b></td>
                    <td style="padding:5px;">:</td>
                    <td style="padding:5px;">
                        @if ($getEhSverification && $getEhSverification->file_paths)
                            @foreach (explode(',', $getEhSverification->file_paths) as $file_path)
                                <a href="{{ asset($file_path) }}" target="_blank">
                                    <img src="{{ asset($file_path) }}" alt="Signature" style="max-width: 10%;">
                                </a>
                            @endforeach
                        @else
                            <p>No files available</p>
                        @endif
                    </td>
                </tr>

            </table>
        @endif

        @if (isset($getsafetyPermitExtension) &&
                count($getsafetyPermitExtension) > 0 &&
                ($safetypermit['permit_status'] >= STATUS_PERMIT_EXTENDED ||
                    ($safetypermit['permit_status'] >= STATUS_EHS_APPROVE_PENDING &&
                        $safetypermit['permit_extension_status'] == 1 &&
                        $safetypermit['permit_status'] != STATUS_PLANTHEAD_REJECTED)))
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            Permit Extension
                        </td>
                    </tr>
                </table>
            </div>
            <table>
                @foreach ($getsafetyPermitExtension as $getsafetyPermitExtension)
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Name</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ isset($getsafetyPermitExtension->created_by) ? getUsername($getsafetyPermitExtension->created_by) : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Date</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ displaydateformat(isset($getsafetyPermitExtension->date) ? $getsafetyPermitExtension->date : '') }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>To Time</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ displaytimeformat(isset($getsafetyPermitExtension->to_time) ? $getsafetyPermitExtension->to_time : '') }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Reason</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ isset($getsafetyPermitExtension->remarks) ? $getsafetyPermitExtension->remarks : '' }}
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif

        @if (isset($getpermitextensionapproval) &&
                count($getpermitextensionapproval) > 0 &&
                ($safetypermit['permit_status'] >= STATUS_PERMIT_EXTENDED_APPROVAL ||
                    ($safetypermit['permit_status'] >= STATUS_EHS_APPROVE_PENDING &&
                        $safetypermit['permit_extension_status'] == 1)))
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            Permit Extension Approval
                        </td>
                    </tr>
                </table>
            </div>
            <table>
                @foreach ($getpermitextensionapproval as $getpermitextensionapproval)
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('ptw.approver_name') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ isset($getpermitextensionapproval->approve_reject_by) ? $getpermitextensionapproval->approve_reject_by : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('ptw.inspection_date') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ displaydateformat(isset($getpermitextensionapproval->date) ? $getpermitextensionapproval->date : '') }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('ptw.inspection_approve_time') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ displaytimeformat(null != $getpermitextensionapproval->created_at ? $getpermitextensionapproval->created_at : '') }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Reason</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ isset($getpermitextensionapproval->remarks) ? $getpermitextensionapproval->remarks : '' }}
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
        @if (isset($getEhsapproval) &&
                ($safetypermit['permit_status'] != 8 &&
                    $safetypermit['permit_status'] != STATUS_PERMIT_EXTENDED &&
                    $safetypermit['permit_status'] != STATUS_PLANTHEAD_REJECTED &&
                    $safetypermit['permit_status'] != 5 &&
                    ($safetypermit['permit_status'] > 3 || $safetypermit['permit_status'] > 4)))
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            Forwarded to Plant Head Aproval
                        </td>
                    </tr>
                </table>
            </div>
            <table>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ptw.approver_name') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($getEhsapproval->approve_reject_by) ? $getEhsapproval->approve_reject_by : '' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ptw.inspection_date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ displaydateformat(isset($getEhsapproval->date) ? $getEhsapproval->date : '') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ptw.inspection_approve_time') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ displaytimeformat(null != $getEhsapproval->created_at ? $getEhsapproval->created_at : '') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Reason</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($getEhsapproval->remarks) ? $getEhsapproval->remarks : '' }}</td>
                </tr>
            </table>
        @endif

        @if (isset($getplantheadapproval) &&
                ($safetypermit['permit_status'] >= STATUS_PLANT_HEAD_APPROVED &&
                    $safetypermit['permit_status'] != STATUS_EHS_RESUME &&
                    $safetypermit['permit_status'] != STATUS_PERMIT_EXTENDED &&
                    $safetypermit['permit_status'] != STATUS_PERMIT_EXTENDED_APPROVAL))
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                            Plant Head Approval
                        </td>
                    </tr>
                </table>
            </div>
            <table>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ptw.approver_name') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($getplantheadapproval->approve_reject_by) ? $getplantheadapproval->approve_reject_by : '' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ptw.inspection_date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ displaydateformat(isset($getplantheadapproval->date) ? $getplantheadapproval->date : '') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ptw.inspection_approve_time') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ displaytimeformat(null != $getplantheadapproval->created_at ? $getplantheadapproval->created_at : '') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Reason</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($getplantheadapproval->remarks) ? $getplantheadapproval->remarks : '' }}</td>
                </tr>
            </table>
        @endif


        @if ($safetypermit['permit_status'] == STATUS_CLOSED)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                            Permit Closed
                        </td>
                    </tr>
                </table>
            </div>
            <table>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Closed By</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ getUsername(isset($safetypermit->closed_by) ? $safetypermit->closed_by : '') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Cancelled Date</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($safetypermit->closed_date) ? Displaydateformat($safetypermit->closed_date) : '' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Reason</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($safetypermit->close_remarks) ? $safetypermit->close_remarks : '' }}
                    </td>
                </tr>
            </table>
        @endif
        @if ($safetypermit['permit_status'] == STATUS_CANCELLED)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#fff;font-weight:bold;padding: 10px 10px 10px;">
                            Permit Cancelled
                        </td>
                    </tr>
                </table>
            </div>
            <table>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Cancelled By</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ getUsername(isset($safetypermit->cancelled_by) ? $safetypermit->cancelled_by : '') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Cancelled Date</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($safetypermit->cancelled_date) ? Displaydateformat($safetypermit->cancelled_date) : '' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Reason</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($safetypermit->cancel_remarks) ? $safetypermit->cancel_remarks : '' }}
                    </td>
                </tr>
            </table>
        @endif

</body>

</html>
