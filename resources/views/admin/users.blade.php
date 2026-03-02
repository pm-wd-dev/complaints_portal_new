@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">User Management</h1>
                </div>
                <div class="d-flex gap-3 align-items-center">
                <div class="search-box">
                    <input type="text" id="searchUsers" class="form-control" placeholder="Search users..." aria-label="Search users">
                </div>
                    <button class="btn btn-primary add-user-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-plus-lg me-2"></i> Add User
                    </button>
                </div>
            </div>

            <div class="users-table p-4">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge {{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit"
                                            data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE') 
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user?')" 
                                                    title="Delete" 
                                                    data-bs-toggle="tooltip">
                                                <i class="bi bi-trash"></i>
                                                <span class="visually-hidden">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    @if(request('search'))
                                        <div class="text-muted">No users found matching "{{ request('search') }}"</div>
                                    @else
                                        <div class="text-muted">No users found</div>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-4 pagination">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="respondent">Respondent</option>
                                <option value="lawyer">Lawyer</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name{{ $user->id }}" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email{{ $user->id }}" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email{{ $user->id }}" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password{{ $user->id }}" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password{{ $user->id }}" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="role{{ $user->id }}" class="form-label">Role</label>
                            <select class="form-select" id="role{{ $user->id }}" name="role" required>
                                <option value="respondent" {{ $user->role == 'respondent' ? 'selected' : '' }}>Respondent</option>
                                <option value="lawyer" {{ $user->role == 'lawyer' ? 'selected' : '' }}>Lawyer</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            let searchTimeout;
            const searchInput = document.getElementById('searchUsers');
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const searchQuery = this.value.trim();
                    const currentUrl = new URL(window.location.href);
                    
                    if (searchQuery) {
                        currentUrl.searchParams.set('search', searchQuery);
                    } else {
                        currentUrl.searchParams.delete('search');
                    }
                    
                    window.location.href = currentUrl.toString();
                }, 500); // Debounce for 500ms
            });

            // Set search input value from URL if exists
            const urlParams = new URLSearchParams(window.location.search);
            const searchValue = urlParams.get('search');
            if (searchValue) {
                searchInput.value = searchValue;
            }
        });
    </script>
    
    <style>
        .role-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .role-badge.respondent {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .role-badge.lawyer {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        
        .role-badge.admin {
            background-color: #ffebee;
            color: #c62828;
        }
    </style>
    
@endsection