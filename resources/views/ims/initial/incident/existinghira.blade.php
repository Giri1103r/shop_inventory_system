<div>
    <form action="" id="hiramoc" method="POST" novalidate>
        @csrf
        <div class="modal-header">
            <h5 class="modal-title">HIRA</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body m-3">
            <div class="d-flex justify-content-left my-3">
                <button type="button" class="btn btn-primary mx-2" id="newHira">New</button>
                <button type="button" class="btn btn-secondary mx-2" id="existingHira">Existing</button>
            </div>
            <input type="hidden" name="incident_id" value="{{ $incident_id }}">

            <div class="row" id="existingdiv">
                <label for="hira_id" class="form-label require">HIRA</label>
                <div class="col-sm-7 form-input">
                    <select name="hira_id" id="hira_id" class="form-control" style="width: 100%">
                        <option value="">Select HIRA</option>
                        @foreach ($hiraList as $hira)
                            <option value="{{ encryptId($hira->id) }}">{{ $hira->services }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


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
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveHira">Save HIRA</button>

        </div>
    </form>
</div>
<script type="text/javascript" nonce="projectcab">
    $(document).ready(function() {
        var savedHiraId = null; // Store the selected HIRA ID after saving

        $('#saveHira').on('click', function() {
            var hiraId = $('#hira_id').val(); // Get selected HIRA ID
            var incidentId = "{{ $incident_id }}"; // Get incident ID

            if (!hiraId) {
                alert('Please select a HIRA before proceeding.');
                return;
            }

            $.ajax({
                url: "{{ admin_url('incident/initial-incident/savehira') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    hira_id: hiraId,
                    incident_id: incidentId
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        alert("HIRA saved successfully!");
                        $('#saved_hira_id').val(response
                        .hira_id); // Update the hidden input
                        $('#hiraModal').modal('hide');
                    } else {
                        alert("Failed to save HIRA: " + response.message);
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

        $('#existingHira').on('click', function() {
            $('#existingdiv').show();

        });

        // Handle dropdown change event
        $('#hira_id').on('change', function() {
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
                            $('#likelihood').text(data.hira.likelihood);
                            $('#riskLevel').text(data.hira.risk_levels);
                            $('#hiraDetails').show(); // Show the details table
                        } else {
                            $('#hiraDetails').hide(); // Hide if no data
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error); // Debugging log for errors
                    }
                });
            } else {
                $('#hiraDetails').hide(); // Hide details if no HIRA selected
            }
        });

        // Cancel button action for closing modal
        $('#cancelHira').on('click', function() {
            $('#hiraModal').modal('hide');
            $('#existingdiv').hide();
            $('#hiraDetails').hide();
        });

        // Confirm button action (if needed)
        $('#confirmHira').on('click', function() {
            // Add logic to handle confirmation
            console.log("HIRA confirmed");
            $('#hiraModal').modal('hide');
        });
    });
</script>
