<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#5a1f24] leading-tight">Contract List</h2>
            <button onclick="openModal()" class="bg-[#5a1f24] text-white px-4 py-2 rounded-md hover:bg-brown-700 text-sm transition shadow-sm flex items-center">
                <span class="text-xl mr-1 pb-1">+</span> Add New Contract
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ALERT BOX --}}
            <div id="global-alert" class="hidden mb-4 px-4 py-3 rounded relative shadow-sm transition-all duration-300" role="alert">
                <strong class="font-bold" id="alert-title">Notification</strong>
                <span class="block sm:inline" id="alert-message"></span>
                <span onclick="closeAlert()" class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer">
                    <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 relative">
                
                {{-- TABLE --}}
                <table id="contractTable" class="w-full text-left border-collapse display nowrap" style="width:100%">
                    <thead>
                        <tr class="bg-[#5a1f24] text-white uppercase text-xs">
                            <th class="p-3 border-b !text-white">Vendor</th>
                            <th class="p-3 border-b !text-white">Description</th>
                            <th class="p-3 border-b !text-white">Start Date</th>
                            <th class="p-3 border-b !text-white">End Date</th>
                            <th class="p-3 border-b !text-white">Renewal Date</th>
                            <th class="p-3 border-b text-center !text-white">Status</th> {{-- COL 5 --}}
                            <th class="p-3 border-b text-right !text-white">Actions</th> {{-- COL 6 --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contracts as $contract)
                            @php
                                // PHP STATUS CALCULATION (Initial Load)
                                $endDate = \Carbon\Carbon::parse($contract['end_date']);
                                $today = \Carbon\Carbon::today();
                                $diff = $today->diffInDays($endDate, false);
                                
                                $statusHtml = '';
                                if ($diff < 0) {
                                    $statusHtml = '<span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Expired</span>';
                                } elseif ($diff <= 7) {
                                    $statusHtml = '<span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Expiring Soon</span>';
                                } else {
                                    $statusHtml = '<span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>';
                                }
                            @endphp

                        <tr id="row_{{ $contract['id'] }}" class="border-b hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td>{{ $contract['vendor_name'] ?? 'Unknown' }}</td>
                            <td>{{ Str::limit($contract['description'] ?? '', 40) }}</td>
                            <td>{{ $contract['start_date'] ?? '' }}</td>
                            <td>{{ $contract['end_date'] ?? '' }}</td>
                            <td>{{ $contract['renewal_date'] ?? '' }}</td>
                            <td class="text-center">{!! $statusHtml !!}</td>
                            <td>
                                <div class="space-x-2 text-right">
                                    <button 
                                        onclick="editContract(
                                            '{{ $contract['id'] }}', 
                                            '{{ $contract['vendor_id'] ?? '' }}', 
                                            '{{ $contract['description'] ?? '' }}', 
                                            '{{ $contract['start_date'] ?? '' }}', 
                                            '{{ $contract['end_date'] ?? '' }}', 
                                            '{{ $contract['renewal_date'] ?? '' }}'
                                        )"
                                        class="inline-flex items-center px-3 py-1.5 bg-[#5a1f24] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5a1f24] active:bg-[#5a1f24] transition ease-in-out duration-150">
                                        Edit
                                    </button>
                                    <button onclick="deleteContract('{{ $contract['id'] }}')" 
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 transition ease-in-out duration-150">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="contractModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative bg-white rounded-lg shadow-xl p-8 w-full max-w-lg transform transition-all scale-100">
            <h3 id="modalTitle" class="text-lg font-bold mb-4 text-gray-800">Add Contract</h3>
            
            @if(empty($vendors))
                <div class="text-center py-6">
                    <div class="text-red-500 mb-4 font-semibold">No Vendors found. Create a vendor first.</div>
                    <a href="{{ route('vendors.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Go to Vendor List</a>
                    <div class="mt-4"><button type="button" onclick="closeModal()" class="text-gray-500 underline">Close</button></div>
                </div>
            @else
                <form id="contractForm">
                    @csrf
                    <input type="hidden" id="contract_id">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Vendor</label>
                        <select id="vendor_id" name="vendor_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                            <option value="">Select a Vendor</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v['id'] }}">{{ $v['vendor_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Contract Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Renewal Date</label>
                        <input type="date" id="renewal_date" name="renewal_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" onclick="closeModal()" class="mr-3 px-4 py-2 text-gray-500 hover:text-gray-700 transition">Cancel</button>
                        <button type="submit" id="submitBtn" class="bg-[#5a1f24] text-white px-4 py-2 rounded hover:bg-[#5a1f24] transition flex items-center shadow-md">
                            Save Contract
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- LOADING OVERLAY --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-[100] flex items-center justify-center">
        <div class="bg-white p-5 rounded-lg shadow-lg flex items-center flex-col">
            <div class="spinner-large mb-3"></div>
            <span class="text-gray-700 font-semibold">Processing...</span>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        let table;

        $(document).ready(function() {
            table = $('#contractTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: { search: "_INPUT_", searchPlaceholder: "Search contracts..." },
                columnDefs: [
                    { targets: 0, className: "p-3 font-medium text-gray-900" }, // Vendor
                    { targets: 1, className: "p-3 text-gray-600" },             // Description
                    { targets: [2, 3, 4], className: "p-3 text-gray-600" },     // Dates
                    { targets: 5, className: "p-3 text-center" },               // Status
                    { targets: 6, className: "p-3 text-right", orderable: false } // Actions
                ]
            });
        });

        function generateButtons(id, vendor_id, description, start, end, renewal) {
            const safeDesc = description ? description.replace(/'/g, "\\'").replace(/"/g, '&quot;') : '';
            return `
                <div class="space-x-2 text-right">
                    <button onclick='editContract("${id}", "${vendor_id}", "${safeDesc}", "${start}", "${end}", "${renewal}")' 
                        class="inline-flex items-center px-3 py-1.5 bg-[#5a1f24] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5a1f24] active:bg-[#5a1f24] transition ease-in-out duration-150">
                        Edit
                    </button>
                    <button onclick="deleteContract('${id}')" 
                        class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 transition ease-in-out duration-150">
                        Delete
                    </button>
                </div>
            `;
        }

        // --- AJAX SUBMIT ---
        $('#contractForm').on('submit', function(e) {
            e.preventDefault();
            $('#loadingOverlay').removeClass('hidden');

            let id = $('#contract_id').val();
            let url = id ? `/contracts/${id}` : "{{ route('contracts.store') }}";
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#loadingOverlay').addClass('hidden');
                    closeModal();
                    showAlert('success', response.success);

                    let c = response.contract;
                    let rowId = response.id || id;
                    
                    // 1. Buttons
                    let buttons = generateButtons(rowId, c.vendor_id, c.description, c.start_date, c.end_date, c.renewal_date);
                    
                    // 2. Short Desc
                    let shortDesc = c.description.length > 40 ? c.description.substring(0, 40) + '...' : c.description;

                    // 3. JS STATUS CALCULATION
                    let endDate = new Date(c.end_date);
                    let today = new Date();
                    endDate.setHours(0,0,0,0);
                    today.setHours(0,0,0,0);

                    // difference in days
                    let diffTime = endDate - today;
                    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    let statusHtml = '';
                    if (diffDays < 0) {
                         statusHtml = '<span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Expired</span>';
                    } else if (diffDays <= 7) {
                         statusHtml = '<span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Expiring Soon</span>';
                    } else {
                         statusHtml = '<span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>';
                    }

                    // 4. Data Array (7 items matching HTML Header)
                    let rowData = [
                        c.vendor_name,
                        shortDesc,
                        c.start_date,
                        c.end_date,
                        c.renewal_date,
                        statusHtml,     // Column 5
                        buttons         // Column 6
                    ];

                    if (id) {
                        table.row('#row_' + id).data(rowData).draw(false);
                    } else {
                        let newRow = table.row.add(rowData).draw(false).node();
                        $(newRow).attr('id', 'row_' + rowId);
                        $(newRow).addClass('border-b hover:bg-gray-50 transition duration-150 ease-in-out');
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').addClass('hidden');
                    let err = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                    showAlert('error', err);
                }
            });
        });

        // --- DELETE ---
        function deleteContract(id) {
            if(confirm('Are you sure you want to delete this contract?')) {
                $('#loadingOverlay').removeClass('hidden');
                $.ajax({
                    url: `/contracts/${id}`,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        $('#loadingOverlay').addClass('hidden');
                        showAlert('success', 'Contract deleted.');
                        table.row('#row_' + id).remove().draw(false);
                    },
                    error: function(xhr) {
                        $('#loadingOverlay').addClass('hidden');
                        showAlert('error', 'Failed to delete.');
                    }
                });
            }
        }

        // --- HELPERS ---
        function openModal() {
            $('#modalTitle').text('Add Contract');
            $('#contractForm')[0]?.reset(); 
            $('#contract_id').val('');     
            $('#contractModal').removeClass('hidden');
        }

        function editContract(id, vendor_id, description, start, end, renewal) {
            $('#modalTitle').text('Edit Contract');
            $('#contract_id').val(id);
            $('#vendor_id').val(vendor_id);
            $('#description').val(description);
            $('#start_date').val(start);
            $('#end_date').val(end);
            $('#renewal_date').val(renewal);
            $('#contractModal').removeClass('hidden');
        }

        function closeModal() { $('#contractModal').addClass('hidden'); }

        function showAlert(type, message) {
            const box = $('#global-alert');
            box.removeClass('hidden bg-green-100 border-green-400 text-green-700 bg-red-100 border-red-400 text-red-700');
            if(type==='success') box.addClass('bg-green-100 border-green-400 text-green-700');
            else box.addClass('bg-red-100 border-red-400 text-red-700');
            $('#alert-message').text(message);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function closeAlert() { $('#global-alert').addClass('hidden'); }
    </script>
</x-app-layout>