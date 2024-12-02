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
                                                            <th><input type="checkbox" id="select-all" class="validate-radio-required" disabled></th>
                                                            <th>Check Points</th>
                                                            <th>Is Default Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 1;
                                                        @endphp
                                                       
                                                        @foreach ($getprotectivedetails as $getprotectivedetails)
                                                        {{-- @dd($detail); --}}
                                                       
                                                            <tr>
                                                        
                                                                <td>{{ $index }}</td>

                                                                <td>
                                                                    @if ($getprotectivedetails->checked == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getprotectivedetails->checked == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
                                                                    @endif
                                                                </td>
                                                                {{-- <td>
                                                                    <input type="checkbox" name="protective[protective_check][type1][{{ $detail->id }}]" class="row-checkbox validate-radio-required" value="1"{{ getCheckedVal($protective[$detail->id]['checked'], '1') }}>
                                                                    
                                                                </td> --}}
                                                                <td> {{ $getprotectivedetails->protective_equip }}</td>

                                                                <td>
                                                                    @if ($getprotectivedetails->default_enable == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getprotectivedetails->default_enable == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
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
                                                <table class="table view_card" id = "equipment">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr.No</th>
                                                            <th><input type="checkbox"
                                                                    id="select-all1"
                                                                    class="validate-radio-required" disabled></th>
                                                            <th>Check Points</th>
                                                            <th>Is Default Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $index = 1;
                                                        @endphp
                                                        @foreach ($getequipmentdetails as $getequipmentdetails)
                                                            <tr>
                                                               
                                                                <td>{{ $index }}</td>


                                                                
                                                                <td>
                                                                    @if ($getequipmentdetails->checked == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getequipmentdetails->checked == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
                                                                    @endif
                                                                </td>
                                                             
                                                                <td>
                                                                    {{ $getequipmentdetails->equip_involve }}
                                                                </td>

                                                                
                                                                <td>
                                                                    @if ($getequipmentdetails->default_enable == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getequipmentdetails->default_enable == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
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
                                                Manual List</h3>
                                               
                                            <div>
                                                <table class="table view_card" id = "manual">
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
                                                        @foreach ($getmanualdetails as $getmanualdetails)
                                                            <tr>

                                                                <td>{{ $index }}</td>
 
                                                                <td>
                                                                    @if ($getmanualdetails->checked == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getmanualdetails->checked == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
                                                                    @endif
                                                                </td>
                                                               
                                                                <td>
                                                                    {{ $getmanualdetails->precaution }}
                                                                </td>

                                                                <td>
                                                                    @if ($getmanualdetails->default_enable == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getmanualdetails->default_enable == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
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
                                                Check List</h3>
                                                
                                            <div>
                                                <table class="table view_card" id = "check">
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
                                                        @foreach ($getcheckdetails as $getcheckdetails)
                                                            <tr>

                                                                <td>{{ $index }}</td>

                                                                <td>
                                                                    @if ($getcheckdetails->checked == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getcheckdetails->checked == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
                                                                    @endif
                                                                </td>
                                                          
                                                                <td>
                                                                   
                                                                    {{ $getcheckdetails->checklist }}
                                                                </td>

                                                                <td>
                                                                    @if ($getcheckdetails->default_enable == 1)
                                                                        <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                    @elseif ($getcheckdetails->default_enable == 0)
                                                                        <i class="fa-solid fa-x" style="color: #f72626;"></i>
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
                                                Instruction List</h3>
                                               
                                            <div style="overflow-y: auto; max-height: 400px;">
                                                <table class="table view_card" id = instruction>
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
                                                        @foreach ($getinstructiondetails as $getinstructiondetails)
                                                        <tr>
                                                
                                                            <td>{{ $index }}</td>

                                                            <td>
                                                                @if ($getinstructiondetails->checked == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                @elseif ($getinstructiondetails->checked == 0)
                                                                    <i class="fa-solid fa-x" style="color: #f72626;"></i>
                                                                @endif
                                                            </td>
                                                           
                                                            <td>
                                                                {{ $getinstructiondetails->safe_work }}
                                                            </td>

                                                            <td>
                                                                @if ($getinstructiondetails->default_enable == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709;"></i>
                                                                @elseif ($getinstructiondetails->default_enable == 0)
                                                                    <i class="fa-solid fa-x" style="color: #f72626;"></i>
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
