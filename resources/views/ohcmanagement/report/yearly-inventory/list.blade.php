@extends('admin.layouts.admin')
@section('title', 'Yearly Inventory Report')
@section('pageurl', admin_url('ohc/yearly-inventory-report/list'))
@section('content')
    @push('style')
        <style>
            .table-responsive {
                overflow-x: auto;
                width: 100%
            }
        </style>
    @endpush
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">


                    </div>



                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3 form-input">
                                <label for="medicine_name" class="form-label ">Unit Name</label>
                                <select name="unit_id" id="unit_id" class="form-control single-select form-control-sm"
                                    style="width: 100%">
                                    <option value="">Select the Unit Name</option>
                                    @foreach ($unit as $list)
                                        <option value="{{ $list->id }}">{{ $list->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3 form-input">
                                <label for="emp_name" class="form-label ">Year</label>
                                <div class="input-group date form-input custom-height">
                                    <input type="text" class="form-control " name="year" id="year"
                                        autocomplete="off">
                                    <div class="input-group-addon input-group-text">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 d-flex mt-3 align-items-center gap-2">
                                <button class="btn btn-primary" name="search" id="search">Search</button>
                                <a href="#" class="btn btn-secondary" id="exportexcel">Export Excel</a>

                            </div>

                        </div>

                        <div id="dataDiv" class="table-responsive" style="display: none;">
                            <table class="table table-bordered table-responsive" id="tableToExport" style="width:100%;"
                                border="0">
                                <tr style="font-size: 19px; background-color:red; color:rgb(10, 1, 1) ">
                                    <td colspan="5"><img src="{{ url('public/assets/images/logo-dark.png') }}"
                                            style="background-color: white" alt=""></td>

                                    <td colspan="70" align="center">
                                        <center>
                                            <b>Medical Treatment Slip<br>
                                                PN International Pvt Ltd. <br>
                                                <span class="year"></span>
                                            </b>
                                        </center>

                                    </td>
                                    <tbody id="tableHeader"></tbody>
                                    <tbody id="tableBody"></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @stop
    @push('script')
        <script>
            $(document).ready(function() {
                let today = new Date();
                let currentYear = today.getFullYear();
                let currentMonth = today.getMonth() + 1;

                let startYear, endYear;

                if (currentMonth > 3) {

                    startYear = currentYear;
                    endYear = currentYear + 1;
                } else {

                    startYear = currentYear - 1;
                    endYear = currentYear;
                }

                let financialYearText = `Financial Year (April ${startYear} - March ${endYear})`;
                $(".year").text(financialYearText);
            });

            $(document).ready(function() {


                $('#year').datepicker({
                    format: 'yyyy',
                    minViewMode: 'years',
                    viewMode: 'years',
                    autoclose: true
                });
            });
            $(document).ready(function() {
                $('#exportexcel').on('click', function(e) {
                    e.preventDefault();

                    let unitId = $('#unit_id').val();
                    let year = $('#year').val();

                    if (!unitId || !year) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Please select the Unit,Year.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }


                    let exportUrl =
                        `{{ url('ohc/yearly-inventory-report/exportexcel') }}?unit_id=${unitId}&year=${year}`;

                    // Redirect to the new URL
                    window.location.href = exportUrl;
                });
            });

            var monthNames = [
                "January", "February", "March", "April", "May", "June", "July", "August",
                "September", "October", "November", "December"
            ];
            $("#search").click(function() {
                var selectedUnit = $("#unit_id").val();
                var selectedYear = $("#year").val();

                if (selectedUnit && selectedYear) {
                    $.ajax({
                        url: "{{ url('ohc/yearly-inventory-report/medicinereport') }}",
                        method: "GET",
                        data: {
                            unit_id: selectedUnit,
                            year: selectedYear,
                        },
                        success: function(response) {
                            let medicineData = response.inventory.length ? response.inventory : response
                                .medicine;

                            if (medicineData && Array.isArray(medicineData) && medicineData.length > 0) {
                                $(".selectedMonthYear").text(monthNames[parseInt(selectedYear) - 1] + " " +
                                    selectedYear);
                                $("#dataDiv").show();
                                fillMonthDays(medicineData, selectedYear, response
                                    .receiving || [], response.issuing || []);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning',
                                    text: 'No data found for the selected filters.',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },

                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Please select the Unit,Year.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });


            function fillMonthDays(inventoryData, year, receivingData, issueData) {
                let headerRow1 = `<tr style="font-size: 15px; background-color: #e1acc9; color:black">
                    <td colspan="2">Year: <span class="selectedMonthYear">${year}</span></td>
                    <td colspan="12"><center><b>Medicine(s) Available (Month Wise)</b></center></td>
                    <td colspan="12"><center><b>Issued Medicine Quantity (Month Wise)</b></center></td>
                    <td colspan="4"><center><b>Grand</b></center></td>
                 </tr>`;

                let headerRow2 = `<tr style="font-size: 14px; background-color: #7ecfa1; color: black">
                    <td>ID</td>
                    <td>Medicine Name</td>`;


                const monthNamesShort = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                monthNamesShort.forEach(month => {
                    headerRow2 += `<td>${month}</td>`;
                });

                monthNamesShort.forEach(month => {
                    headerRow2 += `<td>${month}</td>`;
                });

                headerRow2 += `<td>Total Purchase</td>
                    <td>Total Issue</td>
                    <td>Balance</td>
                    </tr>`;

                let tableBody = "";
                let idCounter = 1;

                inventoryData.forEach((item) => {
                    let purchaseData = new Array(12).fill(0);
                    let issueQuantities = new Array(12).fill(0);
                    let medicineName = item.medicine || item
                        .medicine_name;

                    receivingData.forEach((received) => {
                        if (received.medicine_name === item.medicine_name) {
                            let approvedDate = new Date(received.approved_date);
                            if (!isNaN(approvedDate.getTime()) && approvedDate.getUTCFullYear() === parseInt(
                                    year)) {
                                let monthIndex = approvedDate
                                    .getUTCMonth();
                                purchaseData[monthIndex] += received.quantity || 0;
                            }
                        }
                    });

                    // Process issued medicines
                    issueData.flat().forEach((issued) => {
                        if (issued.medicine_name === item.medicine_name) {
                            let issuedDate = new Date(issued.created_at);
                            if (!isNaN(issuedDate.getTime()) && issuedDate.getUTCFullYear() === parseInt(
                                    year)) {
                                let monthIndex = issuedDate.getUTCMonth();
                                issueQuantities[monthIndex] += Number(issued.quantity) || 0;
                            }
                        }
                    });

                    tableBody += `<tr>
            <td>${idCounter++}</td>
            <td>${medicineName}</td>
           ${purchaseData.map(qty => `<td>${qty !== 0 ? qty : ""}</td>`).join("")}
            ${issueQuantities.map(qty => `<td>${qty !== 0 ? qty : ""}</td>`).join("")}
            <td>${item.total_purchase ? item.total_purchase : ""}</td>
            <td>${item.total_issue ? item.total_issue : ""}</td>
            <td>${item.balance ? item.balance : ""}</td>
        </tr>`;
                });

                $("#tableHeader").html(headerRow1 + headerRow2);
                $("#tableBody").html(tableBody);
            }
        </script>
    @endpush
