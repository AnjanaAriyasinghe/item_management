@extends('layouts.main')

@section('title', 'Customers')
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Customers')

@section('content')
    <!-- Create / Edit Modal -->
    <div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModelLabel">
                        <span id="model-main-title">Create</span> Customer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitForm" method="POST" action="{{ route('admin.customers.store') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6" id="customer_code_div" style="display:none">
                                <div class="form-floating">
                                    <input class="form-control" type="text" id="customer_code" name="customer_code" readonly placeholder="">
                                    <label for="customer_code">Customer Code</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input class="form-control" type="text" id="name" name="name" placeholder="">
                                    <label for="name">Customer Name <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input class="form-control" type="tel" id="phone" name="phone" maxlength="10" placeholder="">
                                    <label for="phone">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input class="form-control" type="text" id="city" name="city" placeholder="">
                                    <label for="city">City</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="address" name="address" style="height: 100px" placeholder=""></textarea>
                                    <label for="address">Address</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="submitFormBtn" class="btn btn-primary">Save</button>
                    <div class="spinner-border text-primary" role="status" style="display: none"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Customers</h4>
                </div>
                <div class="card-body">
                    <button type="button"
                            class="btn btn-primary btn-sm btn-add-new mb-3"
                            data-bs-toggle="modal"
                            data-bs-target="#createModel">
                        <i class="ph-duotone ph-plus-circle"></i> Create Customer
                    </button>
                    <div class="table-responsive">
                        <table class="table table-sm data-table" id="pc-dt-simple">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Reset form on "Create" button click
        $(document).on('click', '.btn-add-new', function () {
            resetForm();
            $('#model-main-title').text('Create');
            $('#customer_code_div').hide();
            $('#submitForm').attr('action', "{{ route('admin.customers.store') }}");
            $('#submitForm input[name="_method"]').remove();
        });

        // DataTable
        $(function () {
            var table = $('.data-table').DataTable({
                dom: '<"top"lBf>rt<"bottom"ip><"clear">',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.customers.index') }}",
                columns: [
                    { data: 'id',            name: 'id' },
                    { data: 'customer_code', name: 'customer_code' },
                    { data: 'name',          name: 'name' },
                    { data: 'phone',         name: 'phone' },
                    { data: 'city',          name: 'city' },
                    { data: 'address',       name: 'address' },
                    {
                        data: 'created_at', name: 'created_at',
                        render: function (data) {
                            return data ? new Date(data).toLocaleDateString('en-US') : '-';
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });
        });

        // Edit button
        $(document).on('click', '.btnEdit', function () {
            var id = $(this).data('id');
            $('#customer_code_div').show();
            $('#model-main-title').text('Edit');

            $.ajax({
                url: '/admin/customers/' + id + '/edit',
                method: 'GET',
                success: function (response) {
                    var c = response.customer;
                    $('#customer_code').val(c.customer_code);
                    $('#name').val(c.name);
                    $('#phone').val(c.phone);
                    $('#city').val(c.city);
                    $('#address').val(c.address);

                    $('#submitForm').attr('action', "{{ route('admin.customers.update', '') }}/" + id);
                    $('#submitForm input[name="_method"]').remove();
                    $('#submitForm').append('<input type="hidden" name="_method" value="PUT">');

                    $('#createModel').modal('show');
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                }
            });
        });

        // Save button
      function resetForm() {
            $('#submitForm')[0].reset();
            $('#submitForm input[name="_method"]').remove();
        }

        // Reusable toast helpers — adjust to your project's toast library
        function toastSuccess(msg) {
            alert('✅ ' + msg);
        }
        function toastError(msg) {
            alert('❌ ' + msg);
        }
    </script>
@endsection
