<!-- Add Respondent Modal -->
<div class="modal fade" id="addRespondentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Respondent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.add-respondent', $complaint) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Toggle between existing and new respondent -->
                    <div class="mb-3">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="respondent_type" id="existing_respondent" value="existing" 
                                   @if($availableRespondents && $availableRespondents->count() > 0) checked @endif>
                            <label class="btn btn-outline-primary" for="existing_respondent">Select Existing</label>
                            
                            <input type="radio" class="btn-check" name="respondent_type" id="new_respondent" value="new"
                                   @if(!$availableRespondents || $availableRespondents->count() == 0) checked @endif>
                            <label class="btn btn-outline-primary" for="new_respondent">Create New</label>
                        </div>
                    </div>
                    
                    <!-- Existing Respondent Selection -->
                    <div id="existing_respondent_section" style="display: @if($availableRespondents && $availableRespondents->count() > 0) block @else none @endif;">
                        @if($availableRespondents && $availableRespondents->count() > 0)
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Select Respondent</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">Choose a respondent...</option>
                                    @foreach($availableRespondents as $respondent)
                                        <option value="{{ $respondent->id }}">
                                            {{ $respondent->name }} ({{ $respondent->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Only users with respondent role who are not already added to this complaint are shown.
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                No existing respondents available. Please create a new one.
                            </div>
                        @endif
                    </div>
                    
                    <!-- New Respondent Creation -->
                    <div id="new_respondent_section" style="display: @if(!$availableRespondents || $availableRespondents->count() == 0) block @else none @endif;">
                        <div class="mb-3">
                            <label for="new_respondent_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="new_respondent_name" name="new_respondent_name" 
                                   placeholder="Enter respondent's full name">
                        </div>
                        <div class="mb-3">
                            <label for="new_respondent_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="new_respondent_email" name="new_respondent_email" 
                                   placeholder="Enter respondent's email address">
                        </div>
                        <div class="mb-3">
                            <label for="new_respondent_phone" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" class="form-control" id="new_respondent_phone" name="new_respondent_phone" 
                                   placeholder="Enter phone number">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="message" name="message" rows="3" 
                                  placeholder="Add any additional message or instructions for the respondent..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Add Respondent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send to Respondent Modal -->
<div class="modal fade" id="sendToRespondentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send to Respondent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.send-to', $complaint) }}" method="POST">
                @csrf
                <input type="hidden" name="send_type" value="respondent">
                <div class="modal-body">
                    @if($complaint->respondents && $complaint->respondents->count() > 0)
                        <div class="mb-3">
                            <label for="respondent_id" class="form-label">Select Respondent</label>
                            <select class="form-select" id="respondent_id" name="respondent_id" required>
                                <option value="">Choose a respondent...</option>
                                @foreach($complaint->respondents as $respondent)
                                    <option value="{{ $respondent->user_id }}">
                                        {{ $respondent->user->name }} ({{ $respondent->user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No respondents have been added to this complaint yet. Please add a respondent first.
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="message" name="message" rows="4" 
                                  placeholder="Add any additional message or instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info" @if(!$complaint->respondents || $complaint->respondents->count() == 0) disabled @endif>
                        <i class="bi bi-send me-2"></i>Send to Respondent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Lawyer Modal -->
<div class="modal fade" id="addLawyerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Lawyer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.add-lawyer', $complaint) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Toggle between existing and new lawyer -->
                    <div class="mb-3">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="lawyer_type" id="existing_lawyer" value="existing" 
                                   @if($availableLawyers && $availableLawyers->count() > 0) checked @endif>
                            <label class="btn btn-outline-success" for="existing_lawyer">Select Existing</label>
                            
                            <input type="radio" class="btn-check" name="lawyer_type" id="new_lawyer" value="new"
                                   @if(!$availableLawyers || $availableLawyers->count() == 0) checked @endif>
                            <label class="btn btn-outline-success" for="new_lawyer">Create New</label>
                        </div>
                    </div>
                    
                    <!-- Existing Lawyer Selection -->
                    <div id="existing_lawyer_section" style="display: @if($availableLawyers && $availableLawyers->count() > 0) block @else none @endif;">
                        @if($availableLawyers && $availableLawyers->count() > 0)
                            <div class="mb-3">
                                <label for="lawyer_user_id" class="form-label">Select Lawyer</label>
                                <select class="form-select" id="lawyer_user_id" name="user_id">
                                    <option value="">Choose a lawyer...</option>
                                    @foreach($availableLawyers as $lawyer)
                                        <option value="{{ $lawyer->id }}">
                                            {{ $lawyer->name }} ({{ $lawyer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Only users with lawyer role who are not already added to this complaint are shown.
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                No existing lawyers available. Please create a new one.
                            </div>
                        @endif
                    </div>
                    
                    <!-- New Lawyer Creation -->
                    <div id="new_lawyer_section" style="display: @if(!$availableLawyers || $availableLawyers->count() == 0) block @else none @endif;">
                        <div class="mb-3">
                            <label for="new_lawyer_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="new_lawyer_name" name="new_lawyer_name" 
                                   placeholder="Enter lawyer's full name">
                        </div>
                        <div class="mb-3">
                            <label for="new_lawyer_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="new_lawyer_email" name="new_lawyer_email" 
                                   placeholder="Enter lawyer's email address">
                        </div>
                        <div class="mb-3">
                            <label for="new_lawyer_phone" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" class="form-control" id="new_lawyer_phone" name="new_lawyer_phone" 
                                   placeholder="Enter phone number">
                        </div>
                        <div class="mb-3">
                            <label for="new_lawyer_firm" class="form-label">Law Firm/Organization (Optional)</label>
                            <input type="text" class="form-control" id="new_lawyer_firm" name="new_lawyer_firm" 
                                   placeholder="Enter law firm or organization name">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lawyer_message" class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="lawyer_message" name="message" rows="3" 
                                  placeholder="Add any additional message or instructions for the lawyer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-briefcase me-2"></i>Add Lawyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send to Lawyer Modal -->
<div class="modal fade" id="sendToLawyerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send to Lawyer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.send-to', $complaint) }}" method="POST">
                @csrf
                <input type="hidden" name="send_type" value="lawyer">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lawyer_name" class="form-label">Lawyer Name</label>
                        <input type="text" class="form-control" id="lawyer_name" name="lawyer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="lawyer_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="lawyer_email" name="lawyer_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="lawyer_phone" class="form-label">Phone Number (Optional)</label>
                        <input type="tel" class="form-control" id="lawyer_phone" name="lawyer_phone">
                    </div>
                    <div class="mb-3">
                        <label for="law_firm" class="form-label">Law Firm/Organization (Optional)</label>
                        <input type="text" class="form-control" id="law_firm" name="law_firm">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="message" name="message" rows="4" 
                                  placeholder="Add any additional message or instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send me-2"></i>Send to Lawyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send to Complainant Modal -->
<div class="modal fade" id="sendToComplainantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Update to Complainant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.send-to', $complaint) }}" method="POST">
                @csrf
                <input type="hidden" name="send_type" value="complainant">
                <div class="modal-body">
                    @if($complaint->email)
                        <div class="mb-3">
                            <label class="form-label">Complainant Email</label>
                            <div class="form-control-plaintext">{{ $complaint->email }}</div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No email address available for this complainant.
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               value="Update on your complaint: {{ $complaint->case_number }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="6" required 
                                  placeholder="Enter your update message for the complainant..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" @if(!$complaint->email) disabled @endif>
                        <i class="bi bi-send me-2"></i>Send Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Resolution Document Modal -->
@if ($complaint->status === 'resolved')
<div class="modal fade" id="resolutionDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Resolution Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.generate-resolution', $complaint) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="resolution_summary" class="form-label">Resolution Summary</label>
                        <textarea class="form-control" id="resolution_summary" name="resolution_summary" rows="4" required 
                                  placeholder="Provide a detailed summary of how this complaint was resolved..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resolution_outcome" class="form-label">Outcome</label>
                        <select class="form-select" id="resolution_outcome" name="resolution_outcome" required>
                            <option value="">Select outcome...</option>
                            <option value="complaint_upheld">Complaint Upheld</option>
                            <option value="complaint_dismissed">Complaint Dismissed</option>
                            <option value="complaint_partially_upheld">Complaint Partially Upheld</option>
                            <option value="resolved_amicably">Resolved Amicably</option>
                            <option value="withdrawn">Withdrawn by Complainant</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="corrective_actions" class="form-label">Corrective Actions Taken (Optional)</label>
                        <textarea class="form-control" id="corrective_actions" name="corrective_actions" rows="3" 
                                  placeholder="List any corrective actions that were implemented..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="recommendations" class="form-label">Future Recommendations (Optional)</label>
                        <textarea class="form-control" id="recommendations" name="recommendations" rows="3" 
                                  placeholder="Any recommendations for preventing similar issues..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-file-earmark-text me-2"></i>Generate Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Upload Signature Modal -->
@php
    $userSignature = $complaint->signatures->where('user_id', Auth::user()->id)->first();
@endphp
@if ($complaint->status === 'resolved' && $userSignature && !$userSignature->signature_path)
<div class="modal fade" id="uploadSignatureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Your Signature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resolution.upload-signature') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="complaint_id" value="{{ $complaint->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="signature_file" class="form-label">Signature File</label>
                        <input type="file" class="form-control" id="signature_file" name="signature_file" 
                               accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Accepted formats: JPG, PNG, PDF. Maximum size: 2MB.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="signature_name" class="form-label">Signer Name</label>
                        <input type="text" class="form-control" id="signature_name" name="signature_name" 
                               value="{{ Auth::user()->name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="signature_title" class="form-label">Title/Position</label>
                        <input type="text" class="form-control" id="signature_title" name="signature_title" 
                               placeholder="e.g., HR Manager, Administrator" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-2"></i>Upload Signature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// Toggle between existing and new respondent sections
document.addEventListener('DOMContentLoaded', function() {
    // Respondent Modal Toggle
    const existingRespondentRadio = document.getElementById('existing_respondent');
    const newRespondentRadio = document.getElementById('new_respondent');
    const existingRespondentSection = document.getElementById('existing_respondent_section');
    const newRespondentSection = document.getElementById('new_respondent_section');

    if (existingRespondentRadio && newRespondentRadio) {
        existingRespondentRadio.addEventListener('change', function() {
            if (this.checked) {
                existingRespondentSection.style.display = 'block';
                newRespondentSection.style.display = 'none';
                // Make existing user_id required, remove required from new fields
                document.getElementById('user_id').required = true;
                document.getElementById('new_respondent_name').required = false;
                document.getElementById('new_respondent_email').required = false;
            }
        });

        newRespondentRadio.addEventListener('change', function() {
            if (this.checked) {
                existingRespondentSection.style.display = 'none';
                newRespondentSection.style.display = 'block';
                // Make new fields required, remove required from existing
                document.getElementById('user_id').required = false;
                document.getElementById('new_respondent_name').required = true;
                document.getElementById('new_respondent_email').required = true;
            }
        });
    }

    // Lawyer Modal Toggle
    const existingLawyerRadio = document.getElementById('existing_lawyer');
    const newLawyerRadio = document.getElementById('new_lawyer');
    const existingLawyerSection = document.getElementById('existing_lawyer_section');
    const newLawyerSection = document.getElementById('new_lawyer_section');

    if (existingLawyerRadio && newLawyerRadio) {
        existingLawyerRadio.addEventListener('change', function() {
            if (this.checked) {
                existingLawyerSection.style.display = 'block';
                newLawyerSection.style.display = 'none';
                // Make existing user_id required, remove required from new fields
                document.getElementById('lawyer_user_id').required = true;
                document.getElementById('new_lawyer_name').required = false;
                document.getElementById('new_lawyer_email').required = false;
            }
        });

        newLawyerRadio.addEventListener('change', function() {
            if (this.checked) {
                existingLawyerSection.style.display = 'none';
                newLawyerSection.style.display = 'block';
                // Make new fields required, remove required from existing
                document.getElementById('lawyer_user_id').required = false;
                document.getElementById('new_lawyer_name').required = true;
                document.getElementById('new_lawyer_email').required = true;
            }
        });
    }
});
</script>