<div>
    <div class="modal-header">
        <h5 class="modal-title">HIRA</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body m-3">
        <div id="hiraDetails" class="mt-3">
            <h5>HIRA Details</h5>

            <table class="table table-bordered">
                <tr>
                    <th>Source, Situation, Act,Activity, Product,Services</th>
                    <th>Likelihood</th>
                    <th>Risk Level</th>
                    {{-- <th>Action</th> --}}

                </tr>
                @foreach ($hiraList as $hira)
                    <tr>
                        <td style="font-weight: bold">{{ $hira->services }}</td>
                        <td style="font-weight: bold">{{ $hira->likelihood }}</td>
                        @php
                            if ($hira->risk_levels == 1) {
                                $risk_levels = '1 to 9';
                            } elseif ($hira->risk_levels == 2) {
                                $risk_levels = '10 to 16';
                            } elseif ($hira->risk_levels == 3) {
                                $risk_levels = '17 to 25';
                            } elseif ($hira->risk_levels == 4) {
                                $risk_levels = 'Legal';
                            }
                        @endphp

                        <td style="font-weight: bold">{{ $risk_levels }}</td>
                    </tr>
                @endforeach
            </table>


        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</div>
