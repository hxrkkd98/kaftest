<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

        <link href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
        <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

        <style>
            /* --- LAYOUT: Align 'Show Entries' Left and 'Search' Right --- */
            .dataTables_wrapper .dataTables_length { float: left; }
            .dataTables_wrapper .dataTables_filter { float: right; text-align: right; }
            
            /* --- INPUT STYLING --- */
            .dataTables_wrapper .dataTables_length select {
                padding-right: 2rem; padding-left: 0.75rem;
                padding-top: 0.25rem; padding-bottom: 0.25rem;
                border: 1px solid #d1d5db; border-radius: 0.375rem;
            }
            .dataTables_wrapper .dataTables_filter input {
                border: 1px solid #d1d5db; border-radius: 0.375rem;
                padding: 0.25rem 0.5rem; margin-left: 0.5rem;
            }

            /* --- CLEAR FLOATS --- */
            .dataTables_wrapper::after { content: ""; display: block; clear: both; }

            /* --- SPACING --- */
            .dataTables_wrapper .dataTables_length, 
            .dataTables_wrapper .dataTables_filter { margin-bottom: 1rem; }
            table.dataTable { margin-bottom: 1rem !important; border-bottom: 1px solid #e5e7eb; }

            /* --- BOTTOM PAGINATION FIX --- */
            .dataTables_wrapper .dataTables_info { float: left; padding-top: 0.75rem; color: #4b5563; font-size: 0.875rem; }
            .dataTables_wrapper .dataTables_paginate { float: right; padding-top: 0.5rem; }

            /* Pagination Buttons */
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                display: inline-block; padding: 0.25rem 0.75rem; margin-left: 0.25rem;
                border: 1px solid #d1d5db; border-radius: 0.375rem;
                background: white; color: #374151 !important; font-size: 0.875rem;
                cursor: pointer; text-decoration: none; transition: all 0.2s;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
                background-color: #f3f4f6 !important; color: #111827 !important; border-color: #9ca3af;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background-color: #5a1f24 !important; color: white !important; border-color: #5a1f24;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
                cursor: default; color: #9ca3af !important; border: 1px solid #e5e7eb; background: transparent !important;
            }

            .dataTables_empty{
                padding: 16px;
            }

            /* --- LARGE SPINNER FOR OVERLAY --- */
            .spinner-large {
                border: 4px solid rgba(0, 0, 0, 0.1);
                border-radius: 50%;
                border-top: 4px solid #3b82f6;
                width: 3rem; height: 3rem;
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* --- MOBILE RESPONSIVE --- */
            @media (max-width: 640px) {
                .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter,
                .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate {
                    float: none; text-align: center;
                }
                .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_paginate { margin-top: 1rem; }
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-[#bebbb4]">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-[#DBD5CE] shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <div class="text-center text-[#5a1f24] font-[900] py-4 bg-[#e6e0d4]">© KAF IT-VCM Copyright</div>
    </body>
</html>
