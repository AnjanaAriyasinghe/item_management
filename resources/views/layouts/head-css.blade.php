<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.3/flatpickr.css">
<!-- [Google Font : Public Sans] icon -->
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ URL::asset('build/fonts/tabler-icons.min.css') }}">
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ URL::asset('build/fonts/feather.css') }}">
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ URL::asset('build/fonts/fontawesome.css') }}">
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ URL::asset('build/fonts/material.css') }}">
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ URL::asset('build/css/style.css') }}" id="main-style-link">
<link rel="stylesheet" href="{{ URL::asset('build/css/style-preset.css') }}">
<style>
    .form-control-custom {
        min-height: calc(3.0rem + calc(var(--bs-border-width)* 1)) !important;
        height: calc(3.0rem + calc(var(--bs-border-width)* 2)) !important;
    }

    .form-control-custom-select {
        min-height: calc(3.3rem + calc(var(--bs-border-width)* 1)) !important;
        height: calc(3.0rem + calc(var(--bs-border-width)* 2)) !important;
    }

    .bg-success-1 {
        background-color: rgb(5, 138, 5) !important;
    }

</style>
