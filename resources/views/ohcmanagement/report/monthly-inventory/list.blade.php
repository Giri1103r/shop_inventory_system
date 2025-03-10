@extends('admin.layouts.admin')
@section('title', 'Monthly Inventory Report')
@section('pageurl', admin_url('ohc/monthly-inventory/list'))
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
                            <div class="col-md-3 mb-3 form-input">
                                <label for="emp_name" class="form-label ">Month</label>
                                <div class="input-group date form-input  custom-height">
                                    <input type="text" class="form-control " name="month" id="month"
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
                                <tr style="font-size: 19px; background-color:red; color:rgb(0, 0, 0) ">
                                    <td colspan="5"><img src="{{ url('public/assets/images/logo-dark.png') }}"
                                            style="background-color: white" alt=""></td>
                                    <td colspan="70" align="center">
                                        <center>
                                            <b>Occupational Health Center Inventory Record <br>
                                                PN International Pvt Ltd. <br>
                                                <span class="selectedMonthYear"></span>
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
            // $(document).ready(function() {
            //     var fromDatepicker = datepicker("#month", {
            //         dateFormat: "mm",

            //     });

            //     var toDatepicker = flatpickr("#year", {
            //         dateFormat: "Y",
            //         minDate: "today"
            //     });
            // });
            $('#month').datepicker({
                format: 'mm',
                minViewMode: 1,
                autoclose: true
            });
            $('#year').datepicker({
                format: 'yyyy',
                minViewMode: 'years',
                viewMode: 'years',
                autoclose: true
            });
            $(document).ready(function() {
                $('#exportexcel').on('click', function(e) {
                    e.preventDefault();

                    let unitId = $('#unit_id').val();
                    let year = $('#year').val();
                    let month = $('#month').val();
                    if (!unitId || !year || !month) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Please select the Unit,Year and Month.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }


                    let exportUrl =
                        `{{ url('ohc/monthly-inventory/exportexcel') }}?unit_id=${unitId}&year=${year}&month=${month}`;

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
                var selectedMonth = $("#month").val();
                var selectedYear = $("#year").val();

                if (selectedUnit && selectedMonth && selectedYear) {
                    $.ajax({
                        url: "{{ url('ohc/monthly-inventory/medicinereport') }}",
                        method: "GET",
                        data: {
                            unit_id: selectedUnit,
                            month: selectedMonth,
                            year: selectedYear,
                        },
                        success: function(response) {
                            let medicineData = response.inventory.length ? response.inventory : response
                                .medicine; // Use inventory if available, otherwise fallback to medicine

                            if (medicineData && Array.isArray(medicineData)) {
                                $(".selectedMonthYear").text(monthNames[parseInt(selectedMonth) - 1] + " " +
                                    selectedYear);
                                $("#dataDiv").show();
                                fillMonthDays(medicineData, selectedMonth, selectedYear, response
                                    .receiving || [], response.issuing || []);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning',
                                    text: 'No data is available for the selected filter.',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("AJAX Error:", error);
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: 'Fail to fetch data please try again after sometime.',
                                confirmButtonColor: '#3085d6'
                            });
                        },
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Please select the Unit, Month and Year.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });


            function fillMonthDays(inventoryData, month, year, receivingData, issueData) {
                let daysInMonth = new Date(year, month, 0).getDate();

                let headerRow1 = `<tr style="font-size: 15px; background-color: #e1acc9; color:black">
        <td colspan="2">Month: <span class="selectedMonthYear">${$(".selectedMonthYear").text()}</span></td>
        <td colspan="${daysInMonth}"><center><b>Purchased Medicine Quantity (Date Wise)</b></center></td>
        <td></td>
        <td colspan="${daysInMonth}"><center><b>Issued Medicine Quantity (Date Wise)</b></center></td>
        <td colspan="4"><center><b>Grant</b></center></td>
    </tr>`;

                let headerRow2 = `<tr style="font-size: 14px; background-color: #7ecfa1; color: black">
        <td>ID</td>
        <td>Name of Item</td>`;

                for (let day = 1; day <= daysInMonth; day++) {
                    headerRow2 += `<td>${day}</td>`;
                }

                headerRow2 += `<td>Name of Item</td>`;

                for (let day = 1; day <= daysInMonth; day++) {
                    headerRow2 += `<td>${day}</td>`;
                }

                headerRow2 += `<td>Purchase Total</td>
                   <td>Issue Total</td>
                   <td>Previous Month</td>
                   <td>Balance</td>
               </tr>`;

                let tableBody = "";
                let idCounter = 1;

                inventoryData.forEach((item) => {
                    let medicineName = item.medicine || item
                        .medicine_name; // Use `medicine` if available, otherwise `medicine_name`

                    let purchaseData = new Array(daysInMonth).fill(0);
                    let issueQuantities = new Array(daysInMonth).fill(0);

                    receivingData.forEach((received) => {
                        if (received.medicine_name === medicineName) {
                            let approvedDate = new Date(received.approved_date);
                            if (!isNaN(approvedDate.getTime())) {
                                let dayIndex = approvedDate.getUTCDate() - 1;
                                let monthIndex = approvedDate.getUTCMonth() + 1;

                                if (monthIndex === parseInt(month) && approvedDate.getUTCFullYear() ===
                                    parseInt(year)) {
                                    purchaseData[dayIndex] = received.quantity || 0;
                                }
                            }
                        }
                    });

                    issueData.flat().forEach((issued) => {
                        if (issued.medicine_name === medicineName) {
                            let issuedDate = new Date(issued.created_at);

                            if (!isNaN(issuedDate.getTime())) {
                                let dayIndex = issuedDate.getUTCDate() - 1;
                                let monthIndex = issuedDate.getUTCMonth() + 1;

                                if (monthIndex === parseInt(month) && issuedDate.getUTCFullYear() === parseInt(
                                        year)) {
                                    issueQuantities[dayIndex] = issued.quantity || 0;
                                }
                            }
                        }
                    });

                    tableBody += `<tr>
            <td>${idCounter++}</td>
            <td>${medicineName}</td>
            ${purchaseData.map(qty => `<td>${qty}</td>`).join("")}
            <td>${medicineName}</td>
            ${issueQuantities.map(qty => `<td>${qty}</td>`).join("")}
            <td>${item.total_purchase || 0}</td>
            <td>${item.total_issue || 0}</td>
            <td>${item.previous_month_total || 0}</td>
            <td>${item.balance || 0}</td>
        </tr>`;
                });

                $("#tableHeader").html(headerRow1 + headerRow2);
                $("#tableBody").html(tableBody);
            }
        </script>
    @endpush
