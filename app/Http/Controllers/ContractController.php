<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\FirebaseRestTrait;

/**
 * Contract Controller
 * Uses Firebase REST API (HTTP/JSON) for all operations
 */
class ContractController extends Controller
{
    use FirebaseRestTrait;

    public function index()
    {
        // 1. FETCH VENDORS (For the Dropdown) using REST API
        $vendors = $this->getCollectionDocuments('vendors');

        // 2. FETCH CONTRACTS (For the Table) using REST API
        $contracts = $this->getCollectionDocuments('contracts');

        return view('contracts.index', [
            'contracts' => $contracts,
            'vendors' => $vendors
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|string', // The ID from dropdown
            'description' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'renewal_date' => 'required|date',
        ]);
        
        // Fetch the Vendor Name using REST API
        $vendor = $this->getDocument('vendors', $request->vendor_id);
        $data['vendor_name'] = $vendor['vendor_name'] ?? 'Unknown Vendor';

        // Create contract using REST API
        $newId = $this->createDocument('contracts', $data);

        return response()->json([
            'success' => 'Contract created successfully!',
            'id' => $newId,
            'contract' => array_merge(['id' => $newId], $data)
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'vendor_id' => 'required|string',
            'description' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'renewal_date' => 'required|date',
        ]);

        // Update Vendor Name using REST API
        $vendor = $this->getDocument('vendors', $request->vendor_id);
        $data['vendor_name'] = $vendor['vendor_name'] ?? 'Unknown Vendor';

        // Update contract using REST API
        $this->updateDocument('contracts', $id, $data);

        return response()->json([
            'success' => 'Contract updated successfully!',
            'contract' => array_merge(['id' => $id], $data)
        ]);
    }

    public function destroy($id)
    {
        // Delete contract using REST API
        $this->deleteDocument('contracts', $id);
        
        return response()->json([
            'success' => 'Contract deleted successfully!'
        ]);
    }
}