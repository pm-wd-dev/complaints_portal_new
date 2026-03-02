@extends('layouts.admin')

@section('title', 'Stages Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Stages Management</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStageModal">
            <i class="bi bi-plus-circle"></i> Add New Stage
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Step #</th>
                            <th>Stage Name</th>
                            <th>Action By</th>
                            <th>Color</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stages as $stage)
                            <tr>
                                <td>{{ $stage->step_number }}</td>
                                <td>{{ $stage->name }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $stage->color }}; color: white;">
                                        {{ $stage->color }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.stages.toggle', $stage) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if ($stage->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="editStage({{ $stage->id }}, '{{ $stage->name }}', {{ $stage->step_number }},'{{ $stage->color }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.stages.destroy', $stage) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this stage?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No stages found. Add your first stage!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Stage Modal -->
    <div class="modal fade" id="addStageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Stage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.stages.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Stage Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="step_number" class="form-label">Step Number</label>
                            <input type="number" class="form-control" id="step_number" name="step_number" min="1"
                                value="{{ $stages->max('step_number') + 1 }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="color" name="color"
                                value="#007bff" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Stage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Stage Modal -->
    <div class="modal fade" id="editStageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Stage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editStageForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Stage Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_step_number" class="form-label">Step Number</label>
                            <input type="number" class="form-control" id="edit_step_number" name="step_number"
                                min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="edit_color" name="color"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Stage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editStage(id, name, stepNumber, color) {
            document.getElementById('editStageForm').action = `{{ url('admin/stages') }}/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_step_number').value = stepNumber;
            document.getElementById('edit_color').value = color;

            new bootstrap.Modal(document.getElementById('editStageModal')).show();
        }
    </script>
@endsection
