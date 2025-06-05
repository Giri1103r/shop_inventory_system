<!DOCTYPE html>
<html>

<head>
    <title>MSDS INSPECTION | KARAM</title>

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


    @foreach ($content as $msdsDetails)
        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('title.msds') }}
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="8" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="9" style="border:1px solid black;">
                    <h3>
                        <span><b>{{ __('title.msds') }}</b></span>
                        <br>
                    </h3>
                </th>
                @php
                    $fist_data = $msdsDetails->first();
                @endphp

                <th colspan="14" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}
                                </td>
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
                <td colspan="10" style="border: 1px solid black; text-align: left;">
                    <strong>Location:</strong> {{ $fist_data->location_name ?? '' }}
                </td>
                <td colspan="10" style="border: 1px solid black; text-align: left;">
                    <strong>Unit:</strong> {{ $fist_data->unit_name ?? '' }}
                </td>
                <td colspan="11" style="border: 1px solid black; text-align: left;">
                    <strong>Department:</strong> {{ $fist_data->department_name ?? '' }}
                </td>
            </tr>


            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">SR. NO</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">ITEM CODE
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">NAME OF
                    CHEMICAL
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">Storage
                    Capacity
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">NPFS Rating
                    AND VALUE
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">NPFA Rating
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4"> MSDS
                    AVAILABILITY STATUS
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">REMARKS
                </th>

            </tr>

            @foreach ($msdsDetails as $msdsDetail)
                <tr>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}
                    </td>
                    <td colspan="4"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $msdsDetail->item_code ?? '' }}
                    </td>
                    <td colspan="4"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $msdsDetail->name_of_chemical ?? '' }}
                    </td>
                    <td colspan="4"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $msdsDetail->storage_capacity ?? '' }}
                    </td>

                    {{-- Combine NPFA Type and Value into one cell with nested table --}}
                    <td colspan="8" style="border: 1px solid black; padding: 0;">
                        @php
                            $nfaRatings = json_decode($msdsDetail->nfa_rating, true) ?? [];
                        @endphp

                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid black; padding: 4px;">NFPA Rating</th>
                                    <th style="border: 1px solid black; padding: 4px;">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($nfaRatings as $rating)
                                    <tr>
                                        <td style="border: 1px solid black; padding: 4px;">
                                            {{ getNFARating($rating['id'] ?? '') ?? '-' }}
                                        </td>
                                        <td style="border: 1px solid black; padding: 4px;">
                                            {{ isset($rating['value']) && $rating['value'] !== '' ? $rating['value'] : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>

                    <td colspan="4"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        @if ($msdsDetail->msds_availability_status== YES)
                            <span style="color: green; font-size: 20px;">✓</span>
                        @elseif ($msdsDetail->msds_availability_status == NO)
                            <span style="color: red; font-size: 20px;">X</span>

                        @else
                            <span style="color: gray; font-size: 20px;">-</span>
                        @endif
                    </td>
                    <td colspan="5"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $msdsDetail->remark ?? '' }}
                    </td>
                </tr>
            @endforeach

        </table>

        <div class="page-break"></div>
    @endforeach



</body>

</html>
