<!-- Required Js -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="{{ URL::asset('build/js/plugins/popper.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('build/js/fonts/custom-font.js') }}"></script>
<script src="{{ URL::asset('build/js/pcoded.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/feather.min.js') }}"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Include SweetAlert from CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js" aria-hidden="true"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

{{-- SEARCHABLE DROPDOWN CDN --}}
<script>
    $(document).on('click', '.btnApproval', function() {
        $('#submitFormBtnApproval')[0].reset();
        var id = $(this).data('id');
        $('#data_value').val(id);
        $('.modal-footer').show();
    });
    $(document).on('click', '.btn-close-new', function() {
        $('#submitForm')[0].reset();
        $('#sub_category_id').empty();
        $('#branch_id').empty();
    });

    $(document).on('click', '.btnReject', function() {
        $('#submitFormBtnReject')[0].reset();
        var id = $(this).data('id');
        $('#data_value_reject').val(id);
        $('.modal-footer').show();
    });
    $("#submitFormBtn").click(function() {
        $('.spinner-border').show();
        $('#submitFormBtn').hide();
        // Clear previous error messages and styling
        // $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Get the native DOM element using document.getElementById
        var formData = new FormData(document.getElementById('submitForm'));
        $.ajax({
            type: 'POST',
            url: $('#submitForm').attr('action'),
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.pdf_url) {
                    // Fetch the PDF blob and open it for printing
                    fetch(response.pdf_url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to fetch the PDF');
                            }
                            return response.blob();
                        })
                        .then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const printWindow = window.open(url);
                            if (printWindow) {
                                printWindow.onload = function() {
                                    printWindow.print();
                                };
                            }
                        })
                        .catch(error => {
                            console.error('Error printing the PDF:', error);
                        });
                }
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $('.spinner-border').hide();
                    location.reload();
                });

            },
            error: function(xhr, status, error) {
                $('.spinner-border').hide();
                $('#submitFormBtn').show();
                if (xhr.status === 422) {
                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();

                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            // Handle dot notation (like company_id.0)
                            var baseKey = key.split('.')[0];

                            // Try to match the input field
                            var inputField = $('[name="' + baseKey + '"]');

                            // If input not found, try for array-style name (e.g., company_id[])
                            if (inputField.length === 0) {
                                inputField = $('[name="' + baseKey + '[]"]');
                            }

                            // Add error styling if field is found
                            if (inputField.length) {
                                inputField.addClass('is-invalid');

                                // Avoid adding multiple errors inside a group
                                if (!inputField.closest('.form-group').find(
                                        '.invalid-feedback').length) {
                                    inputField.closest('.form-group').append(
                                        '<div class="invalid-feedback">' + value[0] +
                                        '</div>'
                                    );
                                }
                            }
                        });
                    }
                } else if (xhr.status === 500) {
                    var errorMessage = xhr.responseJSON.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errorMessage
                    });
                }
            },
            complete: function() {
                // Any cleanup or actions to perform after the request completes
            }
        });

    });
    $("#submitFormBtnApprovalBtn").click(function() {
        $('.spinner-border_1').show();
        $('#submitFormBtnApprovalBtn').hide();

        // Clear previous error messages and styling
        // $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        // Get the native DOM element using document.getElementById
        var formData = new FormData(document.getElementById('submitFormBtnApproval'));

        $.ajax({
            type: 'POST',
            url: $('#submitFormBtnApproval').attr('action'),
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.next) {
                    Swal.fire({
                        html: '<div class="mt-3">' +
                            '<lord-icon src="https://cdn.lordicon.com/lupuorrc.json" ' +
                            'trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px">' +
                            '</lord-icon>' + '<div class="mt-4 pt-2 fs-15">' +
                            '<h4>Well done !</h4>' +
                            '<p class="text-muted mx-4 mb-0">' + response.message +
                            '!</p>' +
                            '</div>' +
                            '</div>',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonClass: 'btn btn-primary w-xs mb-1',
                        cancelButtonText: 'OK',
                        buttonsStyling: false,
                        showCloseButton: true,
                        footer: '<a href="' + response.next_path + '?' + response
                            .next_param_name + '=' + response.next_param_value +
                            '">Next Process - ' + response.next_process_name + '</a>'
                    }).then(() => {
                        $('.spinner-border_1').hide();
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title: "Good job!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        $('.spinner-border_1').hide();
                        location.reload();
                    });
                }

            },
            error: function(xhr, status, error) {
                $('.spinner-border_1').hide();
                $('#submitFormBtnApprovalBtn').show();
                if (xhr.status === 422) {
                    // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            // Check if the key is an array field
                            if (key.includes('.')) {
                                var parts = key.split('.');
                                var fieldName = parts[0] + '[]';
                                var index = parts[1];
                                var inputField = $('[name="' + fieldName + '"]').eq(index);
                                inputField.addClass('is-invalid');
                                inputField.closest('.form-group').append(
                                    '<div class="invalid-feedback">' + value[0] +
                                    '</div>'
                                );
                            } else {
                                // For non-array fields
                                var inputField = $('[name="' + key + '"]');
                                inputField.addClass('is-invalid');
                                inputField.closest('.form-group').append(
                                    '<div class="invalid-feedback">' + value[0] +
                                    '</div>'
                                );
                            }
                        });
                    }
                } else if (xhr.status === 500) {
                    var errorMessage = xhr.responseJSON
                    .message; // Assuming the server sends an error message in the response
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: errorMessage
                    });
                }
            },
            complete: function() {

            }
        });
    });
    $("#submitFormBtnRejectBtn").click(function() {
        $('.spinner-border_3').show();
        $('#submitFormBtnRejectBtn').hide();
        // Clear previous error messages and styling
        // $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        // Get the native DOM element using document.getElementById
        var formData = new FormData(document.getElementById('submitFormBtnReject'));

        $.ajax({
            type: 'POST',
            url: $('#submitFormBtnReject').attr('action'),
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.next) {
                    Swal.fire({
                        html: '<div class="mt-3">' +
                            '<lord-icon src="https://cdn.lordicon.com/lupuorrc.json" ' +
                            'trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px">' +
                            '</lord-icon>' + '<div class="mt-4 pt-2 fs-15">' +
                            '<h4>Well done !</h4>' +
                            '<p class="text-muted mx-4 mb-0">' + response.message +
                            '!</p>' +
                            '</div>' +
                            '</div>',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonClass: 'btn btn-primary w-xs mb-1',
                        cancelButtonText: 'OK',
                        buttonsStyling: false,
                        showCloseButton: true,
                        footer: '<a href="' + response.next_path + '?' + response
                            .next_param_name + '=' + response.next_param_value +
                            '">Next Process - ' + response.next_process_name + '</a>'
                    }).then(() => {
                        $('.spinner-border_3').hide();
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title: "Good job!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        $('.spinner-border_3').hide();
                        location.reload();
                    });
                }

            },
            error: function(xhr, status, error) {
                $('.spinner-border_3').hide();
                $('#submitFormBtnRejectBtn').show();
                if (xhr.status === 422) {
                    // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            // Check if the key is an array field
                            if (key.includes('.')) {
                                var parts = key.split('.');
                                var fieldName = parts[0] + '[]';
                                var index = parts[1];
                                var inputField = $('[name="' + fieldName + '"]').eq(index);
                                inputField.addClass('is-invalid');
                                inputField.closest('.form-group').append(
                                    '<div class="invalid-feedback">' + value[0] +
                                    '</div>'
                                );
                            } else {
                                // For non-array fields
                                var inputField = $('[name="' + key + '"]');
                                inputField.addClass('is-invalid');
                                inputField.closest('.form-group').append(
                                    '<div class="invalid-feedback">' + value[0] +
                                    '</div>'
                                );
                            }
                        });
                    }
                } else if (xhr.status === 500) {
                    var errorMessage = xhr.responseJSON
                    .message; // Assuming the server sends an error message in the response
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: errorMessage
                    });
                }
            },
            complete: function() {

            }
        });
    });

    function handleDelete(url, data) {

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then(function(confirm) {
            if (confirm.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        Swal.fire({
                            title: "Deleted!",
                            text: result.message,
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 500) {
                            var errorMessage = xhr.responseJSON
                            .message; // Assuming the server sends an error message in the response
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: errorMessage
                            })
                        }
                    },
                });
            }
        });
    }

    $(document).on('click', '.btn-add-new', function(e) {
        $("#submitForm input").attr("disabled", false);
        $("#submitForm textarea").attr("disabled", false);
        let path = window.location.pathname;
        let newPath = '';
        if (path.startsWith('/')) {
            path = path.substring(1);
        }
        let dynamicUrl = "/" + path;
        let routeUrl = "{{ url('') }}" + dynamicUrl;
        $('#submitForm').attr('action', routeUrl);
        $('#submitForm')[0].reset();
        $('#model-main-title').text('Create');
        $('#submitForm').find('.is-invalid').removeClass('is-invalid');
        $('#submitForm').find('.invalid-feedback').remove();
        $('.modal-footer').show();
        $('#submitFormBtn').text('Save');
        $('.preview-container').empty();
        $('.pdf-preview').empty();
        $('#submitForm input[name="_method"][value="put"]').remove();
        $('#vendor_code_div').hide();
        $('#add_remove').show();
    });
    $(document).on('click', '.btnEdit', function(e) {
        $("#submitForm input").attr("disabled", false);
        $("#submitForm textarea").attr("disabled", false);
        $('#submitForm')[0].reset();
        $('#model-main-title').text('Edit');
        $('#submitForm').find('.is-invalid').removeClass('is-invalid');
        $('#submitForm').find('.invalid-feedback').remove();
        $('.modal-footer').show();
        $('#submitFormBtn').text('Update');
        $('.preview-container').empty();
        $('.pdf-preview').empty();
    });
    $(document).on('click', '.btnView', function(e) {
        $("#submitForm input").attr("disabled", true);
        $("#submitForm textarea").attr("disabled", true);
        $('.preview-container').empty();
        $('.pdf-preview').empty();
        $('#model-main-title').text('View');
        $('.modal-footer').hide();
    });

    function handleCancel(url, data) {
        Swal.fire({
            title: "Are you sure?",
            text: "You wont to cancel this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Cancel it!"
        }).then(function(confirm) {
            if (confirm.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        Swal.fire({
                            title: "Cancelled!",
                            text: result.message,
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 500) {
                            var errorMessage = xhr.responseJSON
                            .message; // Assuming the server sends an error message in the response
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: errorMessage
                            })
                        }
                    },
                });
            }
        });
    }
    $(document).on('click', '.btn-close', function() {
        $('#submitForm')[0].reset();
    });
</script>
@if (env('APP_DARK_LAYOUT') == 'default')
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            dark_layout = 'true';
        } else {
            dark_layout = 'false';
        }
        layout_change_default();
        if (dark_layout == 'true') {
            layout_change('dark');
        } else {
            layout_change('light');
        }
    </script>
@endif

@if (env('APP_DARK_LAYOUT') != 'default')
    @if (env('APP_DARK_LAYOUT') == 'true')
        <script>
            layout_change('dark');
        </script>
    @endif
    @if (env('APP_DARK_LAYOUT') == false)
        <script>
            layout_change('light');
        </script>
    @endif
@endif


@if (env('APP_DARK_NAVBAR') == 'true')
    <script>
        layout_sidebar_change('dark');
    </script>
@endif

@if (env('APP_DARK_NAVBAR') == false)
    <script>
        layout_sidebar_change('light');
    </script>
@endif

@if (env('APP_BOX_CONTAINER') == false)
    <script>
        change_box_container('true');
    </script>
@endif

@if (env('APP_BOX_CONTAINER') == false)
    <script>
        change_box_container('false');
    </script>
@endif

@if (env('APP_CAPTION_SHOW') == 'true')
    <script>
        layout_caption_change('true');
    </script>
@endif

@if (env('APP_CAPTION_SHOW') == false)
    <script>
        layout_caption_change('false');
    </script>
@endif

@if (env('APP_RTL_LAYOUT') == 'true')
    <script>
        layout_rtl_change('true');
    </script>
@endif

@if (env('APP_RTL_LAYOUT') == false)
    <script>
        layout_rtl_change('false');
    </script>
@endif

@if (env('APP_PRESET_THEME') != '')
    <script>
        preset_change("{{ env('APP_PRESET_THEME') }}");
    </script>
@endif
