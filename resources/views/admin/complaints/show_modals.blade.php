    <!-- Send to Respondent Modal -->
    <div class="modal fade" id="sendToRespondentModal" tabindex="-1" aria-labelledby="sendToRespondentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendToRespondentModalLabel">Send to Respondent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendToRespondentForm" method="POST" action="{{ route('admin.complaints.send-to', $complaint->id) }}">
                        @csrf
                        <input type="hidden" name="send_to" value="respondent">
                        
                        <div class="mb-3">
                            <label for="respondent_select" class="form-label">Select Respondent</label>
                            <select class="form-select" id="respondent_select" name="respondent_id" required>
                                <option value="">Choose a respondent...</option>
                                @foreach($complaint->respondents as $respondent)
                                    <option value="{{ $respondent->user_id }}">{{ $respondent->user->name }} ({{ $respondent->user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="respondent_stage" class="form-label">Update Stage</label>
                            <select class="form-select" id="respondent_stage" name="stage_id">
                                <option value="">Keep current stage</option>
                                @php
                                    $allStages = \App\Models\Stage::orderBy('step_number')->get();
                                @endphp
                                @foreach($allStages as $stage)
                                    <option value="{{ $stage->id }}" {{ $complaint->stage_id == $stage->id ? 'selected' : '' }}>
                                        Step {{ $stage->step_number }}: {{ $stage->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="respondent_message" class="form-label">Message (Optional)</label>
                            <textarea class="form-control" id="respondent_message" name="message" rows="3" placeholder="Add any additional message for the respondent..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="sendToRespondentForm" class="btn btn-primary">Send to Respondent</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send to Lawyer Modal -->
    <div class="modal fade" id="sendToLawyerModal" tabindex="-1" aria-labelledby="sendToLawyerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendToLawyerModalLabel">Send to Lawyer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendToLawyerForm" method="POST" action="{{ route('admin.complaints.send-to', $complaint->id) }}">
                        @csrf
                        <input type="hidden" name="send_to" value="lawyer">
                        
                        <!-- Existing Lawyer Selection -->
                        <div class="mb-3">
                            <label for="lawyer_select" class="form-label">Choose Existing Lawyer (Optional)</label>
                            <select class="form-select" id="lawyer_select" name="lawyer_id">
                                <option value="">-- Select existing lawyer or add new lawyer below --</option>
                                @foreach($complaint->lawyers as $assignedLawyer)
                                    <option value="{{ $assignedLawyer->user->id }}" class="text-success">{{ $assignedLawyer->user->name }} ({{ $assignedLawyer->user->email }}) - Already Assigned</option>
                                @endforeach
                                @foreach($lawyers as $lawyer)
                                    @if(!$complaint->lawyers->contains('user_id', $lawyer->id))
                                        <option value="{{ $lawyer->id }}">{{ $lawyer->name }} ({{ $lawyer->email }})</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select from existing lawyers or leave empty to add new lawyer</small>
                        </div>

                        <div class="text-center my-3">
                            <small class="text-muted">— OR ADD NEW LAWYER —</small>
                        </div>

                        <!-- New Lawyer Section -->
                        <div id="new_lawyer_section">
                            <div class="mb-3">
                                <label for="lawyer_name" class="form-label">Lawyer Name</label>
                                <input type="text" class="form-control" id="lawyer_name" name="lawyer_name" placeholder="Enter lawyer's full name">
                            </div>
                            <div class="mb-3">
                                <label for="lawyer_email" class="form-label">Lawyer Email</label>
                                <input type="email" class="form-control" id="lawyer_email" name="lawyer_email" placeholder="Enter lawyer's email address">
                                <small class="form-text text-muted">This email will receive the case notification</small>
                            </div>
                            <div class="mb-3">
                                <label for="lawyer_phone" class="form-label">Lawyer Phone (Optional)</label>
                                <input type="text" class="form-control" id="lawyer_phone" name="lawyer_phone" placeholder="Enter lawyer's phone number">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lawyer_stage" class="form-label">Update Stage</label>
                            <select class="form-select" id="lawyer_stage" name="stage_id">
                                <option value="">Keep current stage</option>
                                @php
                                    $allStages = \App\Models\Stage::orderBy('step_number')->get();
                                @endphp
                                @foreach($allStages as $stage)
                                    <option value="{{ $stage->id }}" {{ $complaint->stage_id == $stage->id ? 'selected' : '' }}>
                                        Step {{ $stage->step_number }}: {{ $stage->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lawyer_message" class="form-label">Message (Optional)</label>
                            <textarea class="form-control" id="lawyer_message" name="message" rows="3" placeholder="Add any additional message for the lawyer..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="sendToLawyerForm" class="btn btn-primary">Send to Lawyer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send to Complainant Modal -->
    <div class="modal fade" id="sendToComplainantModal" tabindex="-1" aria-labelledby="sendToComplainantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendToComplainantModalLabel">Send to Complainant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendToComplainantForm" method="POST" action="{{ route('admin.complaints.send-to', $complaint->id) }}">
                        @csrf
                        <input type="hidden" name="send_to" value="complainant">
                        
                        <div class="mb-3">
                            <label class="form-label">Complainant Details</label>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Name:</strong> {{ $complaint->name }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $complaint->email }}</p>
                                    @if($complaint->phone_number)
                                        <p class="mb-0"><strong>Phone:</strong> {{ $complaint->phone_number }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="complainant_stage" class="form-label">Update Stage</label>
                            <select class="form-select" id="complainant_stage" name="stage_id">
                                <option value="">Keep current stage</option>
                                @php
                                    $allStages = \App\Models\Stage::orderBy('step_number')->get();
                                @endphp
                                @foreach($allStages as $stage)
                                    <option value="{{ $stage->id }}" {{ $complaint->stage_id == $stage->id ? 'selected' : '' }}>
                                        Step {{ $stage->step_number }}: {{ $stage->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="complainant_message" class="form-label">Message (Optional)</label>
                            <textarea class="form-control" id="complainant_message" name="message" rows="3" placeholder="Add any additional message for the complainant..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="sendToComplainantForm" class="btn btn-primary">Send to Complainant</button>
                </div>
            </div>
        </div>
    </div>

<script>
// Handle lawyer form validation
document.addEventListener('DOMContentLoaded', function() {
    const lawyerForm = document.getElementById('sendToLawyerForm');
    const lawyerSelect = document.getElementById('lawyer_select');
    const lawyerName = document.getElementById('lawyer_name');
    const lawyerEmail = document.getElementById('lawyer_email');
    
    // Clear new lawyer fields when existing lawyer is selected
    if (lawyerSelect) {
        lawyerSelect.addEventListener('change', function() {
            if (this.value) {
                // Clear new lawyer fields when existing lawyer is selected
                lawyerName.value = '';
                lawyerEmail.value = '';
                document.getElementById('lawyer_phone').value = '';
            }
        });
    }
    
    // Clear existing lawyer selection when typing new lawyer details
    if (lawyerName && lawyerEmail) {
        lawyerName.addEventListener('input', function() {
            if (this.value.trim()) {
                lawyerSelect.value = '';
            }
        });
        
        lawyerEmail.addEventListener('input', function() {
            if (this.value.trim()) {
                lawyerSelect.value = '';
            }
        });
    }
    
    // Form submission validation
    if (lawyerForm) {
        lawyerForm.addEventListener('submit', function(e) {
            const selectedLawyer = lawyerSelect.value;
            const newLawyerName = lawyerName.value.trim();
            const newLawyerEmail = lawyerEmail.value.trim();
            
            // Must have either existing lawyer selected OR new lawyer details
            if (!selectedLawyer && (!newLawyerName || !newLawyerEmail)) {
                e.preventDefault();
                alert('Please either select an existing lawyer or provide new lawyer details (name and email are required).');
                return false;
            }
            
            // If both are filled, warn user
            if (selectedLawyer && (newLawyerName || newLawyerEmail)) {
                e.preventDefault();
                alert('Please either select an existing lawyer OR add new lawyer details, not both.');
                return false;
            }
        });
    }
});
</script>