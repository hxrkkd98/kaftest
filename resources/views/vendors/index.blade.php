<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#5a1f24] leading-tight">Vendor List</h2>
            <button onclick="openModal()" class="bg-[#5a1f24] text-white px-4 py-2 rounded-md hover:bg-brown-700 text-sm transition shadow-sm flex items-center">
                <span class="text-xl mr-1 pb-1">+</span> Add New Vendor
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
                <table id="vendorTable" class="w-full text-left border-collapse display nowrap" style="width:100%">
                    <thead>
                        <tr class="bg-[#5a1f24] uppercase text-xs">
                            <th class="p-3 border-b !text-white">Vendor Name</th>
                            <th class="p-3 border-b !text-white">Email</th>
                            <th class="p-3 border-b !text-white">Phone</th>
                            <th class="p-3 border-b !text-white">Contact Person</th>
                            <th class="p-3 border-b !text-white">Address</th>
                            <th class="p-3 border-b text-right !text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                        <tr id="row_{{ $vendor['id'] }}" class="border-b hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td>{{ $vendor['vendor_name'] ?? '' }}</td>
                            <td>{{ $vendor['email'] ?? '' }}</td>
                            <td>{{ $vendor['phone_number'] ?? '' }}</td>
                            <td>{{ $vendor['contact_person'] ?? '' }}</td>
                            <td>{{ Str::limit($vendor['address'] ?? '', 30) }}</td>
                            <td>
                                <div class="space-x-2">
                                    <button 
                                        onclick="editVendor('{{ $vendor['id'] }}', '{{ $vendor['vendor_name'] ?? '' }}', '{{ $vendor['email'] ?? '' }}', '{{ $vendor['phone_number'] ?? '' }}', '{{ $vendor['contact_person'] ?? '' }}', '{{ json_encode($vendor['address'] ?? '') }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-[#5a1f24] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#5a1f24] active:bg-[#5a1f24] transition ease-in-out duration-150">
                                        Edit
                                    </button>
                                    <button onclick="deleteVendor('{{ $vendor['id'] }}')" 
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
    <div id="vendorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative bg-white rounded-lg shadow-xl p-8 w-full max-w-lg transform transition-all scale-100">
            <h3 id="modalTitle" class="text-lg font-bold mb-4 text-gray-800">Add Vendor</h3>
            
            <form id="vendorForm">
                @csrf
                <input type="hidden" id="vendor_id">

                {{-- Name --}}
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Vendor Name</label>
                    <input type="text" id="vendor_name" name="vendor_name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                </div>

                {{-- Phone --}}
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                </div>

                {{-- Contact Person --}}
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                </div>

                {{-- Address --}}
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <textarea id="address" name="address" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required></textarea>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeModal()" class="mr-3 px-4 py-2 text-gray-500 hover:text-gray-700 transition">Cancel</button>
                    <button type="submit" id="submitBtn" class="bg-[#5a1f24] text-white px-4 py-2 rounded hover:bg-brown-700 transition flex items-center shadow-md">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- LOADING OVERLAY --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-[100] flex items-center justify-center">
        <div class="bg-white p-5 rounded-lg shadow-lg flex items-center flex-col">
            <div class="spinner-large mb-3"></div>
            <span class="text-gray-700 font-semibold">Processing...</span>
        </div>
    </div>

    {{-- SCRIPTS (Page Specific Logic Only) --}}
    <script>
        let table;

        $(document).ready(function() {
            table = $('#vendorTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: { search: "_INPUT_", searchPlaceholder: "Search vendors..." },
                columnDefs: [
                    { targets: 0, className: "p-3 font-medium text-gray-900" }, // Name
                    { targets: [1, 2, 3, 4], className: "p-3 text-gray-600" }, // Email, Phone, Contact, Address
                    { targets: 5, className: "p-3 text-right", orderable: false } // Actions
                ]
            });
        });

        function generateButtons(id, name, email, phone, contact, address) {
            const safeName = name ? name.replace(/'/g, "\\'") : '';
            const safeEmail = email ? email.replace(/'/g, "\\'") : '';
            const safePhone = phone ? phone.replace(/'/g, "\\'") : '';
            const safeContact = contact ? contact.replace(/'/g, "\\'") : '';
            const safeAddress = JSON.stringify(address).replace(/"/g, '&quot;'); 

            return `
                <div class="space-x-2">
                    <button onclick='editVendor("${id}", "${safeName}", "${safeEmail}", "${safePhone}", "${safeContact}", ${safeAddress})' 
                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition ease-in-out duration-150">
                        Edit
                    </button>
                    <button onclick="deleteVendor('${id}')" 
                        class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 transition ease-in-out duration-150">
                        Delete
                    </button>
                </div>
            `;
        }

        $('#vendorForm').on('submit', function(e) {
            e.preventDefault();
            $('#loadingOverlay').removeClass('hidden');

            let id = $('#vendor_id').val();
            let url = id ? `/vendors/${id}` : "{{ route('vendors.store') }}";
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#loadingOverlay').addClass('hidden');
                    closeModal();
                    showAlert('success', response.success);

                    let v = response.vendor;
                    let rowId = response.id || id;
                    let buttons = generateButtons(rowId, v.vendor_name, v.email, v.phone_number, v.contact_person, v.address);

                    let rowData = [
                        v.vendor_name,
                        v.email,
                        v.phone_number,
                        v.contact_person,
                        v.address,
                        buttons
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

        function deleteVendor(id) {
            if(confirm('Are you sure you want to delete this vendor?')) {
                $('#loadingOverlay').removeClass('hidden');
                $.ajax({
                    url: `/vendors/${id}`,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        $('#loadingOverlay').addClass('hidden');
                        showAlert('success', 'Vendor deleted.');
                        table.row('#row_' + id).remove().draw(false);
                    },
                    error: function(xhr) {
                        $('#loadingOverlay').addClass('hidden');
                        showAlert('error', 'Failed to delete.');
                    }
                });
            }
        }

        function openModal() {
            $('#modalTitle').text('Add Vendor');
            $('#vendorForm')[0].reset(); 
            $('#vendor_id').val('');     
            $('#vendorModal').removeClass('hidden');
        }

        function editVendor(id, name, email, phone, contact, address) {
            $('#modalTitle').text('Edit Vendor');
            $('#vendor_id').val(id);
            $('#vendor_name').val(name);
            $('#email').val(email);
            $('#phone_number').val(phone);
            $('#contact_person').val(contact);
            $('#address').val(address);
            $('#vendorModal').removeClass('hidden');
        }

        function closeModal() { $('#vendorModal').addClass('hidden'); }
        
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