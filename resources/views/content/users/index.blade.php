@extends('layouts.app')

@section('title', 'Pengiriman Dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Users</h6>
                        @if ($activeMenu['can_create'])
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                Add User
                            </button>
                        @endif
                    </div>


                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($activeMenu['can_read'])
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Is Verified</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        @php
                                            $isSuperadmin = collect($user->roles)->contains(function ($role) {
                                                return $role['role_id'] === 1;
                                            });
                                        @endphp

                                        @if (!($activeRole['role_id'] != $code_superadmin && $isSuperadmin))
                                        <tr>
                                            <td>{{ $user->code }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->is_verify ? 'Yes' : 'No' }}</td>
                                            <td>
                                                @if ($activeMenu['can_update'])
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                        data-id="{{ $user->user_id }}" data-code="{{ $user->code }}"
                                                        data-name="{{ $user->name }}" data-phone="{{ $user->phone }}"
                                                        data-email="{{ $user->email }}" data-is_verify="{{ $user->is_verify }}">
                                                        <i data-feather="edit"></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    onclick="showUserDetails({{ json_encode($user->attribute) }}, '{{ $user->user_id }}', '{{ $user->code }}', '{{ $user->name }}')">
                                                    <i data-feather="eye"></i>
                                                </button>

                                                @if ($activeMenu['can_delete'])
                                                    @if (($activeRole['role_id'] == $code_admin || $activeRole['role_id'] == $code_superadmin) && $user->user_id != $currentUser->data->user_id)
                                                    <form action="{{ route('users.delete', $user->user_id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                @endif
                                                
                                                @if ($activeRole['role_id'] == $code_admin || $activeRole['role_id'] == $code_superadmin)
                                                    <div class="dropdown d-inline">
                                                        <button class="btn btn-sm btn-outline-dark" type="button"
                                                            id="dropdownMenuButton{{ $user->user_id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i data-feather="more-horizontal"></i>
                                                        </button>
                                                        <ul class="dropdown-menu"
                                                            aria-labelledby="dropdownMenuButton{{ $user->user_id }}">
                                                            <li>
                                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#roleAccessModal"
                                                                    onclick="loadRoles({{ $user->user_id }}, {{ json_encode($user->roles) }})">
                                                                    <i data-feather="lock"></i> Role Access
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Role Access -->
    <div class="modal fade" id="roleAccessModal" tabindex="-1" aria-labelledby="roleAccessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleAccessModalLabel">Manage Role Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('users.updateRoleAccess') }}">
                    @csrf
                    <input type="hidden" id="userId" name="user_id">
                    <div class="modal-body">
                        <div class="form-check">
                            @foreach ($roles as $role)
                                @if ($role->role_id != 1 || $activeRole['role_id'] == $code_superadmin)
                                    <div class="mb-2">
                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                            id="role_{{ $role->role_id }}" value="{{ $role->role_id }}"
                                            {{-- Cek jika roles tidak null, kosong atau undefined --}}
                                            @if (!empty($user->roles) && is_array($user->roles) && in_array($role->role_id, array_column($user->roles, 'role_id')))
                                                checked
                                            @endif>
                                        <label class="form-check-label" for="role_{{ $role->role_id }}">
                                            {{ $role->role_name }}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Editing User -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('users.save') }}">
                    @csrf
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editCode" class="form-label">Code</label>
                            <input type="text" class="form-control" id="editCode" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
                            <small class="form-text text-muted">Leave blank if you don't want to change the
                                password</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing User Details -->
    <div class="modal fade" id="detailUserModal" tabindex="-1" aria-labelledby="detailUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 700px; overflow-y: auto;">
                    <div id="userDetailsContent">
                        <!-- User details will be dynamically loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts')
    <script>
        function loadRoles(userId, userRoles) {
            $('#userId').val(userId);

            // Loop through all roles and check if user has that role
            userRoles.forEach(function(role) {
                $('#role_' + role.role_id).prop('checked', true);
            });
        }

        $('#editUserModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            // Jika tombol Add User ditekan, reset semua field
            if (button.text().trim() === "Add User") {
                modal.find('#editUserId').val('');
                modal.find('#editCode').val('');
                modal.find('#editName').val('');
                modal.find('#editPhone').val('');
                modal.find('#editEmail').val('');
                modal.find('#editIsVerify').val(1); // Set default value ke 'Yes'
                modal.find('#editPassword').val(''); // Kosongkan field password
            }

            // Jika tombol Edit ditekan, isikan field dengan data user
            if (button.data('id')) {
                var user_id = button.data('id');
                var code = button.data('code');
                var name = button.data('name');
                var phone = button.data('phone');
                var email = button.data('email');
                var is_verify = button.data('is_verify');

                modal.find('#editUserId').val(user_id);
                modal.find('#editCode').val(code);
                modal.find('#editName').val(name);
                modal.find('#editPhone').val(phone);
                modal.find('#editEmail').val(email);
            }
        });


        $(document).ready(function() {
            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 4000);

            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 4000);
        });

        function showUserDetails(attributes, userId, code, name) {
            var detailsHtml = `
                <form class="user-details-section">
                    <div class="mb-3">
                        <label for="userId" class="form-label">User ID</label>
                        <input type="text" class="form-control" id="userId" value="${userId}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="code" value="${code}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" value="${name}" readonly>
                    </div>
                    <hr>
                    <h6 class="text-secondary">User Attributes</h6>
                    <hr>
            `;

            for (var key in attributes) {
                if (attributes.hasOwnProperty(key)) {
                    detailsHtml += `
                        <div class="mb-3">
                            <label for="${key}" class="form-label">${capitalizeFirstLetter(key)}</label>
                            <input type="text" class="form-control" id="${key}" value="${attributes[key]['value']}" readonly>
                        </div>
                    `;
                }
            }

            detailsHtml += `
                </form>
            `;

            $('#userDetailsContent').html(detailsHtml);
            $('#detailUserModal').modal('show');
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
@endsection

@section('styles')
    <style>
        .user-detail {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-detail-title {
            margin-bottom: 15px;
            color: #333333;
            font-weight: bold;
        }

        .user-detail-content {
            margin-bottom: 15px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .detail-section {
            grid-column: span 2;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
@endsection
