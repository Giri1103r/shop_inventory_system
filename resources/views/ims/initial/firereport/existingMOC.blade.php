<div>
    <form action="" id="hiramoc" method="POST" novalidate>
        @csrf
        <div class="modal-header">
            <h5 class="modal-title">HIRA</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body m-3">
            <div class="d-flex justify-content-left my-3">
                <a href="{{ admin_url('incident/hira-master/add')}}" class="btn btn-primary mx-2" id="newHira">New</a>
                <button type="button" class="btn btn-secondary mx-2" id="existingMOC">Existing</button>
            </div>
            <input type="hidden" name="incident_id" value="{{ $incident_id }}">
            <input type="hidden" name="accident_id" value="{{ $accident_id }}">
            <input type="hidden" name="fireincident_id" value="{{ $fireincident_id }}">

            <div class="row" id="existingMOCdiv" style="display: none;">
                <label for="moc_id" class="form-label require">MOC</label>
                <div class="col-sm-7 form-input">
                    <select name="moc_id" id="moc_id" class="form-control" style="width: 100%">
                        <option value="">Select MOC</option>
                        @foreach ($hiraList as $hira)
                            <option value="{{ encryptId($hira->id) }}">{{ $hira->services }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div id="hiramocDetails" class="mt-3" style="display: none;">
                <h5>HIRA Details</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Source, Situation, Act,Activity, Product,Services</th>
                        <td id="mocservice"></td>
                    </tr>
                    <tr>
                        <th>Likelihood</th>
                        <td id="moclikelihood"></td>
                    </tr>
                    <tr>
                        <th>Risk Level</th>
                        <td id="mocriskLevel"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="savemoc">Save HIRA</button>

        </div>
    </form>
</div>
<script type="text/javascript" nonce="projectcab">
    $(document).ready(function() {
        var savedHiraId = null; // Store the selected HIRA ID after saving

        $('#savemoc').on('click', function() {
            var mocId = $('#moc_id').val(); // Get selected HIRA ID
            var incidentId = "{{ $incident_id }}"; // Get incident ID
            var accidentId = "{{ $accident_id }}";
            var fireincident_id = "{{ $fireincident_id }}";  // Get incident ID

            if (!mocId) {
                alert('Please select a MOC before proceeding.');
                return;
            }

            $.ajax({
                url: "{{ admin_url('incident/fire-incident/savehira') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    moc_id: mocId,
                    incident_id: incidentId,
                    accidentId: accidentId,
                    fireincident_id: fireincident_id
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
                    url: "{{ url('incident/fire-incident/gethiradetails') }}/" + hiraId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        console.log("AJAX Response:", data); // Debugging log

                        if (data.hira) {
                            $('#moclikelihood').text(data.hira.likelihood);
                            $('#mocriskLevel').text(data.hira.risk_levels);
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
