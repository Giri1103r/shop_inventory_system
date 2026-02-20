@extends('admin.layouts.admin')
@section('title', 'Type of work View')
@section('pageurl', admin_url('ptw/typeofworkmaster/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ptw/typeofworkmaster/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Type Of work</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label">Work Name</label>
                                        <div>
                                            {{ $typeofwork->work_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label">Description</label>
                                        <div>
                                            {{ $typeofwork->description }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label ">Image</label>
                                        <div>
                                            <a href="{{ asset($typeofwork->file_path) }}" target="_blank">
                                                <img src="{{ asset($typeofwork->file_path) }}" alt="Image"
                                                    style="max-width: 20%;">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-wrapper">
                                        <div class="table-container">
                                            <h3>
                                                Protective Equipment's To be Worn
                                            </h3>

                                            <div>
                                                <table class="table view_card" id = "protective">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th><input type="checkbox" id="select-all"
                                                                    class="validate-radio-required" disabled></th>
                                                            <th>Check Points</th>
                                                            <th>Is Default Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 1;
                                                        @endphp

                                                        @foreach ($protectivequip_checklist as $detail)
                                                            <tr>
                                                                <input type="hidden"
                                                                    name="protective[record_id][{{ isset($protective[$detail->id]) ? $protective[$detail->id]->id : '' }}]"
                                                                    value="{{ isset($protective[$detail->id]) ? $protective[$detail->id]->id : '' }}">

                                                                <td>{{ $index }}</td>
                                                                <td>
                                                                    @if (isset($protective[$detail->id]) && $protective[$detail->id]->checked == '1')
                                                                        <i class="fa-solid fa-check"
                                                                            style="color: #267709;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-x"
                                                                            style="color: #f72626;"></i>
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <input type="hidden"
                                                                        name="protective[protective_equip][type1][{{ $detail->id }}]"
                                                                        value="{{ $detail->id }}">
                                                                    {{ $detail->protective_equip }}
                                                                </td>

                                                                <td>
                                                                    @if (isset($protective[$detail->id]) && $protective[$detail->id]->default_enable == '1')
                                                                        <i class="fa-solid fa-check"
                                                                            style="color: #267709;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-x"
                                                                            style="color: #f72626;"></i>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $index++;
                                                            @endphp
                                                        @endforeach

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                        <div class="table-container">
                                            <h3>
                                                Equipment Involved
                                            </h3>

                                            <div>
                                                <table class="table view_card" id="equipment">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th><input type="checkbox" id="select-all1"
                                                                    class="validate-radio-required" disabled></th>
                                                            <th>Check Points</th>
                                                            <th>Is Default Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 1;
                                                        @endphp

                                                        @foreach ($equipinvalve_checklist as $equipinvalve_checklist)
                                                        <tr>
                                                            <input type="hidden"
                                                                name="equipment[record_id][{{ isset($equipment[$equipinvalve_checklist->id]) ? $equipment[$equipinvalve_checklist->id]->id : '' }}]"
                                                                value="{{ isset($equipment[$equipinvalve_checklist->id]) ? $equipment[$equipinvalve_checklist->id]->id : '' }}">

                                                            <td>{{ $index }}</td>
                                                            <td>
                                                                @if (isset($equipment[$equipinvalve_checklist->id]) && $equipment[$equipinvalve_checklist->id]->checked == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>

                                                            <td>

                                                                {{ $equipinvalve_checklist->equip_involve }}
                                                            </td>

                                                            <td>
                                                                @if (isset($equipment[$equipinvalve_checklist->id]) && $equipment[$equipinvalve_checklist->id]->default_enable == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $index++;
                                                        @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="table-wrapper">

                                        <div class="table-container">
                                            <h3>
                                             Precaution to be taken</h3>

                                            <div>
                                                <table class="table view_card" id="manual">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th><input type="checkbox" id="select-all2"
                                                                    class="validate-radio-required" disabled></th>
                                                            <th>Check Points</th>
                                                            <th>Is Default Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 1;
                                                        @endphp

                                                        @foreach ($precaution_checklist as $precaution_checklist)
                                                        <tr>

                                                            <td>{{ $index }}</td>
                                                            <td>
                                                                @if (isset($manual[$precaution_checklist->id]) && $manual[$precaution_checklist->id]->checked == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>

                                                            <td>

                                                                {{ $precaution_checklist->precaution }}
                                                            </td>

                                                            <td>
                                                                @if (isset($manual[$precaution_checklist->id]) && $manual[$precaution_checklist->id]->default_enable == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $index++;
                                                        @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                        <div class="table-container">
                                            <h3>
                                             Equipment Inspection</h3>

                                            <div>
                                                <table class="table view_card" id="check">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th><input type="checkbox" id="select-all3"
                                                                    class="validate-radio-required" disabled></th>
                                                            <th>Check Points</th>
                                                            <th>Is Default Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 1;
                                                        @endphp
                                                        @foreach ($equipchecklist_checklist as $equipchecklist_checklist)
                                                        <tr>

                                                            <td>{{ $index }}</td>
                                                            <td>
                                                                @if (isset($check[$equipchecklist_checklist->id]) && $check[$equipchecklist_checklist->id]->checked == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>

                                                            <td>

                                                                {{ $equipchecklist_checklist->checklist }}
                                                            </td>

                                                            <td>
                                                                @if (isset($check[$equipchecklist_checklist->id]) && $check[$equipchecklist_checklist->id]->default_enable == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $index++;
                                                        @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                    </div>


                                    <div class="table-wrapper">
                                        <div class="table-container">
                                            <h3>
                                               Safe Work Instructions</h3>

                                            <div style="overflow-y: auto; max-height: 400px;">
                                                <table class="table view_card" id="instruction">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th><input type="checkbox" id="select-all4"
                                                                    class="validate-radio-required" disabled></th>
                                                            <th>Check Points</th>
                                                            <th>Is Default Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 1;
                                                        @endphp
                                                        @foreach ($safework_checklist as $safework_checklist)
                                                        <tr>

                                                            <td>{{ $index }}</td>
                                                            <td>
                                                                @if (isset($instruction[$safework_checklist->id]) && $instruction[$safework_checklist->id]->checked == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>

                                                            <td>

                                                                {{ $safework_checklist->safe_work }}
                                                            </td>

                                                            <td>
                                                                @if (isset($instruction[$safework_checklist->id]) && $instruction[$safework_checklist->id]->default_enable == '1')
                                                                    <i class="fa-solid fa-check"
                                                                        style="color: #267709;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-x"
                                                                        style="color: #f72626;"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $index++;
                                                        @endphp
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop
