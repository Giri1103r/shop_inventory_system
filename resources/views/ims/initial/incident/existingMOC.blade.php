<div>
    <form action="" id="hiramoc" method="POST" novalidate>
        @csrf
        <div class="modal-header">
            <h5 class="modal-title">MOC</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body m-3">
            @if ($newHiraList == null)
                <div class="d-flex justify-content-left my-3">
                    <a href="{{ admin_url('incident/hira-master/incident-investigation/add/' . $incident_id . '/' . encryptId(2)) }}"
                        class="btn btn-primary mx-2" id="newHira">New</a>
                    <button type="button" class="btn btn-secondary mx-2" id="existingHira">Existing</button>
                </div>
            @endif
            <input type="hidden" name="incident_id" value="{{ $incident_id }}">

            <div class="row" id="existingMOCdiv" style="display: none;">
                <label for="moc_id" class="form-label require">MOC</label>
                <div class="col-sm-7 form-input">
                    <select name="hira_id" id="hira_id" class="form-control" style="width: 100%">
                        <option value="">Select HIRA</option>
                        @if ($selectedhira != null)
                            @foreach ($hiraList as $hira)
                                <option value="{{ encryptId($hira->id) }}"
                                    {{ $selectedhira->hira_id == $hira->id ? 'selected' : '' }}>
                                    {{ $hira->services }}
                                </option>
                            @endforeach
                        @else
                            @foreach ($hiraList as $hira)
                                <option value="{{ encryptId($hira->id) }}">{{ $hira->services }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            @if ($newHiraList != null)
                <div id="hiraDetails" class="mt-3">
                    <h5>HIRA Details</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Source, Situation, Act,Activity, Product,Services</th>
                            <td style="font-weight: bold">{{ $newHiraList->services }}</td>
                        </tr>
                        <tr>
                            <th>Likelihood</th>
                            <td style="font-weight: bold">{{ $newHiraList->likelihood }}</td>
                        </tr>
                        <tr>
                            <th>Risk Level</th>
                            <td style="font-weight: bold">{{ $newHiraList->risk_levels }}</td>
                        </tr>
                    </table>
                </div>
            @else
                <div id="hiraDetails" class="mt-3" style="display: none;">
                    <h5>HIRA Details</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Source, Situation, Act,Activity, Product,Services</th>
                            <td id="service"></td>
                        </tr>
                        <tr>
                            <th>Likelihood</th>
                            <td id="likelihood"></td>
                        </tr>
                        <tr>
                            <th>Risk Level</th>
                            <td id="riskLevel"></td>
                        </tr>
                    </table>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            @if ($newHiraList == null)
                <button type="button" class="btn btn-primary" id="saveHira">Save HIRA</button>
            @endif
        </div>
    </form>
</div>
<script type="text/javascript" nonce="projectcab">
    $(document).ready(function() {
        var savedHiraId = null; // Store the selected HIRA ID after saving

        $('#savemoc').on('click', function() {
            var mocId = $('#moc_id').val(); // Get selected HIRA ID
            var incidentId = "{{ $incident_id }}"; // Get incident ID

            if (!mocId) {
                alert('Please select a MOC before proceeding.');
                return;
            }

            $.ajax({
                url: "{{ admin_url('incident/initial-incident/savehira') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    moc_id: mocId,
                    incident_id: incidentId,
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        alert("MOC saved successfully!");
                        $('#saved_hira_id').val(response
                            .moc_id); // Update the hidden input
                        $('#hiraModal').modal('hide');
                    } else {
                        alert("Failed to save MOC: " + response.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("AJAX Error: " + xhr.responseText);
                }
            });


        });

        // Ensure investigation form is not submitted without a saved HIRA
        $('#hiramoc').on('submit', function(e) {
            if (!savedHiraId) {
                alert("Please save HIRA first before submitting the investigation!");
                e.preventDefault(); // Stop form submission if no HIRA is selected
            }
        });
    });


    $(document).ready(function() {

        $('#existingMOC').on('click', function() {
            $('#existingMOCdiv').show();

        });

        // Handle dropdown change event
        $('#moc_id').on('change', function() {
            var hiraId = $(this).val();
            console.log("Selected HIRA ID:", hiraId);

            if (hiraId) {
                $.ajax({
                    url: "{{ url('incident/initial-incident/gethiradetails') }}/" + hiraId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        console.log("AJAX Response:", data); // Debugging log

                        if (data.hira) {
                            $('#moclikelihood').text(data.hira.likelihood);
                            $('#mocriskLevel').text(data.risk_levels);
                            $('#hiramocDetails').show(); // Show the details table
                        } else {
                            $('#hiramocDetails').hide(); // Hide if no data
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error); // Debugging log for errors
                    }
                });
            } else {
                $('#hiramocDetails').hide(); // Hide details if no HIRA selected
            }
        });

        // Cancel button action for closing modal
        $('#cancelHira').on('click', function() {
            $('#mocModal').modal('hide');
            $('#existingMOCdiv').hide();
            $('#hiramocDetails').hide();
        });

        // Confirm button action (if needed)
        $('#confirmHira').on('click', function() {
            // Add logic to handle confirmation
            console.log("HIRA confirmed");
            $('#mocModal').modal('hide');
        });
    });
</script>
