@extends('admin.layouts.admin')
@section('title', 'Type of work Show')
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
                                <div class="align-back-btc">
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
                                                style="max-width: 30%;">
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
                                                            <th><input type="checkbox" id="select-all" class="validate-radio-required"></th>
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
                                                            
                                                                <td>{{ $index }}</td>


                                                                <td>
                                                                    @if ($detail->yes_or_no_na == 'YES')
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($detail->yes_or_no_na == 'NO')
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
                                                                    @elseif ($detail->yes_or_no_na == 'NA')
                                                                        <span style="color: #FFA500;">NA</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <input type="hidden" name="protective[protective_check][type1][{{ $detail->id }}]" value="0">
                                                                    <input type="checkbox" name="protective[protective_check][type1][{{ $detail->id }}]" class="row-checkbox validate-radio-required" value="1"{{ getCheckedVal($protective[$detail->id]['checked'], '1') }}>
                                                                    
                                                                </td>
                                                                <td>
                                                                    <input type="hidden" name="protective[protective_equip][type1][{{ $detail->id }}]" value="{{$detail->id}}">
                                                                    {{ $detail->protective_equip }}
                                                                
                                                                </td>
                                                                <td class="form-input">

                                                                    <input type="hidden" name="protective[protectivequip_checklist][type1][{{ $detail->id }}]" value="0">
                                                                    <input type="checkbox" name="protective[protectivequip_checklist][type1][{{ $detail->id }}]" class="validate-radio-required" value="1"{{ getCheckedVal($protective[$detail->id]['default_enable'], '1') }}>
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
                                                <table class="table view_card" id = "equipment">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th><input type="checkbox"
                                                                    id="select-all1"
                                                                    class="validate-radio-required"></th>
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
                                                                {{-- @dd() --}}
                                                                <input type="hidden" name="equipment[equipmentrecord_id][{{$equipment[$equipinvalve_checklist->id]->id  }}]" value="{{$equipment[$equipinvalve_checklist->id]->id }}">

                                                                <td>{{ $index }}</td>

                                                                <td>

                                                                    <input type="hidden" name="equipment[equipinvalve_check][type2][{{ $equipinvalve_checklist->id }}]" value="0">
                                                                    <input type="checkbox" name="equipment[equipinvalve_check][type2][{{ $equipinvalve_checklist->id }}]" class="row-checkbox1 validate-radio-required" value="1"{{ getCheckedVal($equipment[$equipinvalve_checklist->id]['checked'], '1') }}>

                                                                </td>
                                                             
                                                                <td>
                                                                    <input type="hidden" name="equipment[equip_involve][type2][{{ $equipinvalve_checklist->id }}]" value="{{$equipinvalve_checklist->id}}">
                                                                    {{ $equipinvalve_checklist->equip_involve }}
                                                                </td>
                                                                <td class="form-input">
                                                            
                                                                    <input type="hidden" name="equipment[equipinvalve_checklist][type2][{{ $equipinvalve_checklist->id }}]" value="0">
                                                                    <input type="checkbox" name="equipment[equipinvalve_checklist][type2][{{ $equipinvalve_checklist->id }}]" class=" validate-radio-required" value="1"{{ getCheckedVal($equipment[$equipinvalve_checklist->id]['default_enable'], '1') }}>
                                                                    
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
