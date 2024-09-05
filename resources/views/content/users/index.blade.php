@extends('layouts.app')

@section('title', 'Pengiriman Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Users</h6>
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
                                    <tr>
                                        <td>{{ $user->code }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->is_verify ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#editUserModal" data-id="{{ $user->user_id }}"
                                                data-code="{{ $user->code }}" data-name="{{ $user->name }}"
                                                data-phone="{{ $user->phone }}" data-email="{{ $user->email }}"
                                                data-is_verify="{{ $user->is_verify }}">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                onclick="getUserAttributes('{{ $user->token }}')">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
                            <label for="editIsVerify" class="form-label">Is Verified</label>
                            <select class="form-control" id="editIsVerify" name="is_verify" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="editPassword" name="password">
                            <small class="form-text text-muted">Leave blank if you don't want to change the password</small>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="userDetailsContent" class="p-3"
                        style="border-radius: 10px; background-color: #f9f9f9; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <!-- User details will be loaded here -->
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
        // Fungsi untuk Mengambil dan Menampilkan Detail User
        function getUserAttributes(token) {
            var formData = new FormData();
            formData.append('guid', token); // Pastikan nama parameter sesuai dengan yang diharapkan API

            $.ajax({
                url: '{{ route('api.getUserAttribute') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.code === 0) {
                        var userAttributes = response.data;
                        var detailsHtml = `
                        <div class="user-detail">
                            <h5 class="user-detail-title">User Details</h5>
                            <div class="user-detail-content">
                                <div class="detail-grid">
                    `;
                        for (var label in userAttributes) {
                            detailsHtml +=
                                `<div class="detail-section"><strong>${label}:</strong> ${userAttributes[label]}</div>`;
                        }
                        detailsHtml += '</div></div></div>';
                        $('#userDetailsContent').html(detailsHtml);
                        $('#detailUserModal').modal('show');
                    } else {
                        alert(response.info);
                    }
                },
                error: function() {
                    alert('Failed to retrieve user details.');
                }
            });
        }

        // Event Listener untuk Modal Edit User
        $('#editUserModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var user_id = button.data('id');
            var code = button.data('code');
            var name = button.data('name');
            var phone = button.data('phone');
            var email = button.data('email');
            var is_verify = button.data('is_verify');

            var modal = $(this);
            modal.find('#editUserId').val(user_id);
            modal.find('#editCode').val(code);
            modal.find('#editName').val(name);
            modal.find('#editPhone').val(phone);
            modal.find('#editEmail').val(email);
            if (is_verify) {
                modal.find('#editIsVerify').val(1); // Set dropdown ke 'Yes'
            } else {
                modal.find('#editIsVerify').val(0); // Set dropdown ke 'No'
            }
        });

        $(document).ready(function() {
            // Menghilangkan alert error setelah 4 detik
            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 4000);

            // Menghilangkan alert success setelah 4 detik
            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 4000);
        });
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
