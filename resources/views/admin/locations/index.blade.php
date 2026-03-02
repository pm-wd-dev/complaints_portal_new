@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Locations')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Locations</h1>

        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Add Location</span>
                </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Location Name</th>
                            <th>City</th>
                            <th>State</th>
                            <th>QR Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                            <tr>
                                <td>{{ $location->name }}</td>
                                <td>{{ $location->city }}</td>
                                <td>{{ $location->state }}</td>
                                <td>    
                                    @if($location->qrCode)
                                        <a href="{{ asset($location->qrCode->file_path) }}" class="btn btn-sm btn-outline-primary" download="{{ Str::slug($location->name) }}-qr-code.png">
                                            <i class="bi bi-qr-code"></i> Download QR
                                        </a>
                                    @else
                                        <span class="text-muted">No QR Code</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editLocationModal{{ $location->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.locations.destroy', $location) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this location?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Location Modal -->
                            <div class="modal fade" id="editLocationModal{{ $location->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Location</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.locations.update', $location) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name{{ $location->id }}" class="form-label">Location Name</label>
                                                    <input type="text" class="form-control" id="name{{ $location->id }}"
                                                           name="name" value="{{ $location->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="city{{ $location->id }}" class="form-label">City</label>
                                                    <input type="text" class="form-control" id="city{{ $location->id }}"
                                                           name="city" value="{{ $location->city }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="state{{ $location->id }}" class="form-label">State</label>
                                                    <input type="text" class="form-control" id="state{{ $location->id }}"
                                                           name="state" value="{{ $location->state }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="address{{ $location->id }}" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="address{{ $location->id }}"
                                                           name="address" value="{{ $location->address }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.locations.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Location Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state" required>
                    </div>
                    {{-- <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Location</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
