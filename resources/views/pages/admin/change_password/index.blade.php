@extends('layouts.main')

Change Password
@section('breadcrumb-item', 'Admin')
@section('breadcrumb-item-active', 'Change Password')
@section('content')
<!-- [ Main Content ] start -->

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
                   <form id="submitForm" action="{{route('user.reset.store')}}" method="post">
                            @csrf
                            <div class="row g-2">
                                <div class="col-lg-4" id="alertId">
                                    <div class="form-group">
                                        <label for="oldpasswordInput" class="form-label">Old
                                            Password*</label>
                                        <input type="password" class="form-control" id="oldpasswordInput"
                                               placeholder="Enter current password" name="old_password">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="newpasswordInput" class="form-label">New
                                            Password*</label>
                                        <input type="password" class="form-control" id="newpasswordInput"
                                               placeholder="Enter new password" name="password">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-lg-4">
                                    <div>
                                        <label for="confirmpasswordInput" class="form-label">Confirm
                                            Password*</label>
                                        <input type="password" class="form-control" id="confirmpasswordInput"
                                               placeholder="Confirm password" name="password_confirmation">
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>

            <div class="modal-footer">
                <button type="button" id="submitFormBtn" class="btn btn-primary save-button">Change Password</button>
                <div class="spinner-border text-primary" role="status" style="display: none"></div>
            </div>
        </div>
    </div>


<!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script type="text/javascript">

    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data('id');
        $('#submitForm').attr('action', "{{ route('admin.company.update', '') }}/" + id);
        $('#submitForm').append('<input type="hidden" name="_method" value="put">');
        $.ajax({
            url: '/admin/company/' + id + '/edit'
            , method: 'GET'
            , success: function(response) {
                $('#system_title').val(response.system_title);
                $('#name').val(response.name);
                $('#contact_number').val(response.contact_number);
                $('#mobile').val(response.mobile);
                $('#description').val(response.description);
                $('#address').val(response.address);
                $('#pv_no').val(response.pv_no);
            }
            , error: function(xhr) {
                console.error('An error occurred:', xhr.responseText);
            }
        });
    });

</script>
@endsection
