<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#5a1f24] leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ROW 1: Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                {{-- Card 1: Active --}}
                

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="bg-[#5a1f24] p-4 rounded-md mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-gray-800">{{ $stats['expiring_30'] }}</div>
                        <div class="text-gray-600">Expiring in 30 Days</div>
                    </div>
                </div>

                {{-- Card 2: Expiring 60 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="bg-[#5a1f24] p-4 rounded-md mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-gray-800">{{ $stats['expiring_60'] }}</div>
                        <div class="text-gray-600">Expiring in 60 Days</div>
                    </div>
                </div>

                {{-- Card 3: Expiring 90 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center">
                    <div class="bg-[#5a1f24]  p-4 rounded-md mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-gray-800">{{ $stats['expiring_90'] }}</div>
                        <div class="text-gray-600">Expiring in 90 Days</div>
                    </div>
                </div>

            </div>

            {{-- ROW 2: Expired (Centered) --}}
            <div class="flex justify-center gap-6 mb-10">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center w-full md:w-1/3">
                    <div class="bg-[#5a1f24] p-4 rounded-md mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-gray-800">{{ $stats['active'] }}</div>
                        <div class="text-gray-600">Active Contracts</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center w-full md:w-1/3">
                    <div class="bg-[#5a1f24] p-4 rounded-md mr-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-gray-800">{{ $stats['expired'] }}</div>
                        <div class="text-gray-600">Contract Expired</div>
                    </div>
                </div>
            </div>

            {{-- ROW 3: Navigation Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Vendor List Link --}}
                <a href="{{ route('vendors.index') }}" class="block group">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 flex items-center transition hover:shadow-md border border-transparent hover:border-brown-500">
                        <div class="bg-[#5a1f24] p-6 rounded-md mr-6 group-hover:bg-brown-100 transition">
                            <svg class="w-10 h-10 text-white group-hover:text-brown-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-800 group-hover:text-brown-600">View Vendor List</div>
                            <div class="text-gray-600 mt-1">Manage vendor profiles and company details</div>
                        </div>
                    </div>
                </a>

                {{-- Contract List Link --}}
                <a href="{{ route('contracts.index') }}" class="block group">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 flex items-center transition hover:shadow-md border border-transparent hover:border-brown-500">
                        <div class="bg-[#5a1f24] p-6 rounded-md mr-6 group-hover:bg-brown-100 transition">
                            <svg class="w-10 h-10 text-white group-hover:text-brown-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-800 group-hover:text-brown-600">View Contract List</div>
                            <div class="text-gray-600 mt-1">View-filter, or edit contract list</div>
                        </div>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>