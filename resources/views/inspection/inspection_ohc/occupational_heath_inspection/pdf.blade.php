<!DOCTYPE html>
<html>

<head>
    <title>OCCUPATIONAL HEALTH CENTER | KARAM</title>

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

    @foreach ($content as $detail)
        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('title.first_aid_record') }}
                    </td>
                </tr>
            </table>
        </div>
        <br>

          <table
          style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">

          <tr>
              <th colspan="6" style="border:1px solid black;height:50;width:40">
                  <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
              </th>
              <th colspan="6" style="border:1px solid black;">
                  <h3>
                      <span><b> OCCUPATIONAL HEALTH CENTER</b></span>

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
              <th colspan="6"
                  style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                  DATE OF INSPECTION: {{ Displaydateformat($detail->date_of_inspection) ?? 'N/A' }}
              </th>
              <th colspan="6"
                  style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                  LOCATION : {{ getLocationname($detail->location ?? 'N/A') }}
              </th>
              <th colspan="6"
                  style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                  SHIFT: {{ getShift($detail->shift) ?? 'N/A' }}
              </th>
          </tr>
          </tr>
          <tr>
              <th colspan="6"
                  style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                  NEXT DUE DATE OF INSPECTION :- {{ Displaydateformat($detail->next_due) ?? 'N/A' }}
              </th>
              <th colspan="6"
                  style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                  UNIT :- {{ getunitname($detail->unit) ?? 'N/A' }}
              </th>
              <th colspan="6"
                  style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                  FREQUENCY :- {{ getfrequencyname($detail->frequency) ?? 'N/A' }}
              </th>
          </tr>
          <tr>
              <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">SR. NO</th>
              <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">CHECK ITEMS
              </th>
              <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                  QUANTITY</th>
              <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">STATUS
              </th>
              <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">REMARK
              </th>

          </tr>
          @php
              $decodedData = json_decode($detail->checklist, true);
              $checkItems = $decodedData['check_item'] ?? [];
              $statuses = $decodedData['status'] ?? [];
              $remarks = $decodedData['remarks'] ?? [];
              $quantity = $decodedData['quantity'] ?? [];
              $srNo = 1;
          @endphp
          @php
              $decodedData = json_decode($detail->checklist, true);
              $checkItems = $decodedData['check_item'] ?? [];
              $statuses = $decodedData['status'] ?? [];
              $remarks = $decodedData['remarks'] ?? [];
              $quantity = $decodedData['quantity'] ?? [];
              $srNo = 1;
          @endphp

          @foreach ($checkItems as $groupId => $checkPoints)
              @php
                  $rowCount = count($checkPoints);
                  $firstRow = true;
              @endphp

              @foreach ($checkPoints as $checkPoint)
                  <tr>
                      {{-- @if ($firstRow)
                          <td rowspan="{{ $rowCount }}" colspan="1"
                              style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                              {{ $srNo++ }}
                          </td>
                          <td rowspan="{{ $rowCount }}" colspan="1"
                              style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                              {{ getSubcategoryname($groupId) }}
                          </td>
                          @php $firstRow = false; @endphp
                      @endif --}}

                      <td colspan="2" style="border:1px solid black; padding: 8px;">
                        {{$loop->iteration}}
                      </td>

                      {{-- CHECK ITEM (4 columns) --}}
                      <td colspan="4" style="border: 1px solid black; padding: 8px;">
                          {{ getSubcategoryDataname($checkPoint) }}
                      </td>

                      {{-- QUANTITY (4 columns) --}}
                      <td colspan="4" style="border: 1px solid black; padding: 8px;">
                          {{ $quantity[$checkPoint] ?? 'No Quantity is Available' }}
                      </td>


                      <td colspan="4"
                          style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                          @if (!empty($statuses[$checkPoint]) && $statuses[$checkPoint] == 'YES')
                              <span style="color: green; font-size: 20px;">✓</span>
                          @elseif (!empty($statuses[$checkPoint]) && ($statuses[$checkPoint] == 'NO' || $statuses[$checkPoint] == 'N/A'))
                              <span style="color: red; font-size: 20px;">X</span>
                          @else
                              <i class="fa-solid fa-minus" style="color: #808080; width: 15px;"></i>
                          @endif
                      </td>

                      {{-- REMARK (6 columns) --}}
                      <td colspan="6" style="border: 1px solid black; padding: 8px;">
                          {{ $remarks[$checkPoint] ?? 'No Remarks' }}
                      </td>
                  </tr>
              @endforeach
          @endforeach

          @php
              $signature = GetOHCSignature(
                  $detail->created_by,
                  $detail->id,
                  OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
              );
              $Verifiedsignature = GetOHCSignature(
                  $detail->verified_by,
                  $detail->id,
                  OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
              );
              $Approvedsignature = GetOHCSignature(
                  $detail->approved_by,
                  $detail->id,
                  OHC_TYPE_OCCUPATIONAL_HEALTH_CENTER_INSPECTION_CHECKLIST,
              );
          @endphp

          {{-- <tr>
              <th colspan="6" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                  <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                      style="width: 150px; margin-top: -10px;" />
                  <div style="margin-top: 5px;">Checked By:{{getUsername($detail->created_by)}}</div>
              </th>
              <th colspan="6" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                  <img src="{{ admin_url($Verifiedsignature) }}" alt="Signature Upload"
                      style="width: 150px; margin-top: -10px;" />
                  <div style="margin-top: 5px;">Verified By:{{getUsername($detail->verified_by)}}</div>
              </th>
              <th colspan="6" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                  <img src="{{ admin_url($Verifiedsignature) }}" alt="Signature Upload"
                      style="width: 150px; margin-top: -10px;" />
                  <div style="margin-top: 5px;">Approved By:{{getUsername($detail->approved_by)}}</div>
              </th>
          </tr> --}}


          <tr>
            <th colspan="6" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                @if ($detail->created_by != null)
                    <div style="margin-top: 5px;">Checked By:{{ getUsername($detail->created_by) }}</div>
                @endif
            </th>
            <th colspan="6" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                @if ($detail->verified_by != null)
                    <div style="margin-top: 5px;">Verified By:{{ getUsername($detail->verified_by) }}</div>
                @else
                    <div style="margin-top: 5px;">Has Not Yet Been Verified</div>
                @endif
            </th>
            <th colspan="6" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                @if ($detail->approved_by != null)
                    <div style="margin-top: 5px;">Approved By:{{ getUsername($detail->approved_by) }}</div>
                @else
                    <div style="margin-top: 5px;">Has Not Yet Been Approved</div>
                @endif
            </th>
        </tr>

      </table>
        <br>

        <div class="page-break"></div>
    @endforeach

</body>

</html>
