@extends('admin.layouts.pdf')
@section('title', 'Medical Fitness Certificate')
@section('content')

    <div style="width:100%;">
        <table class="table" style="width:100%;border: 0.5px solid;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    @foreach ($header as $key => $value)
                        <td style='padding: 7px;border: 0.5px solid;font-weight:bold;text-align:center;'>
                            {{ $value }}
                        </td>
                    @endforeach
                </tr>
            </thead>

            <tbody>

                @php
                    $i = 1;
                @endphp
                  @foreach ($content as $key => $value)
                  <tr>
                      <td style='padding: 7px;border: 0.5px solid;text-align:center'>
                          {{ $i }}
                      </td>
                      <td style='padding: 7px;border: 0.5px solid'>
                          {{ $value->emp_id }}
                      </td>
                      <td style='padding: 7px;border: 0.5px solid'>
                          {{ ($value->emp_name) }}
                      </td>
                      <td style='padding: 7px;border: 0.5px solid'>
                        {{ getCompanyname($value->company_id) }}
                    </td>
                      <td style='padding: 7px;border: 0.5px solid'>
                          {{ displaydateformat($value->date) }}
                      </td>
                      <td style='padding: 7px;border: 0.5px solid'>
                          {{ $value->cheif_complaint}}
                      </td>
                        <td style='padding: 7px;border: 0.5px solid'>
                          {{ $value->remarks }}
                      </td>
                      <td style='padding: 7px; border: 0.5px solid'>
                          @if ($value->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                              Paramedicis Applied the fitness certificate
                          @elseif ($value->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                              Doctor Approval Pending
                          @elseif ($value->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                              Doctor Approved
                          @elseif ($value->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                              EHS Head Approval Pending
                              @elseif ($value->approve_status == STATUS_OHC_MEDICAL_DOCTOR_REJECTED)
                              Doctor Approval Pending
                          @else
                              {{ removeUnderScore(getStatus($value->approve_status)) }}
                          @endif
                      </td>

                      <td style='padding: 7px; border: 0.5px solid'>
                          @if ($value->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                              Doctor Approval Pending
                          @elseif ($value->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                              Doctor Approved
                          @elseif ($value->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                              EHS Head Approval Pending
                          @elseif ($value->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                              EHS Head Approved
                              @elseif ($value->approve_status == STATUS_OHC_MEDICAL_DOCTOR_REJECTED)
                             Doctor Rejected
                          @else
                              {{ removeUnderScore(getStatus($value->approve_status)) }}
                          @endif
                      </td>

                      <td style='padding: 7px;border: 0.5px solid'>
                          {{ getusername($value->created_by) }}
                      </td>
                      <td style='padding: 7px;border: 0.5px solid'>
                          {{ Displaydateformat($value->created_at) }}
                      </td>
                  </tr>
                  @php
                      $i++;
                  @endphp
              @endforeach
            </tbody>
        </table>
        <br>
    </div>

@stop
