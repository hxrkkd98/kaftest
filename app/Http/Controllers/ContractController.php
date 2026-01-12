<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ContractController extends Controller
{
    public function index()
    {
        $db = Firebase::firestore()->database();

        // 1. FETCH VENDORS (For the Dropdown)
        $vendorsRef = $db->collection('vendors')->documents();
        $vendors = [];
        foreach ($vendorsRef as $doc) {
            if ($doc->exists()) {
                $vendors[] = array_merge(['id' => $doc->id()], $doc->data());
            }
        }

        // 2. FETCH CONTRACTS (For the Table)
        $contractsRef = $db->collection('contracts')->documents();
        $contracts = [];
        foreach ($contractsRef as $doc) {
            if ($doc->exists()) {
                $contracts[] = array_merge(['id' => $doc->id()], $doc->data());
            }
        }

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
        
        // Fetch the Vendor Name based on ID to store it with the contract
        // This avoids complex lookups later in the table
        $vendorDoc = Firebase::firestore()->database()->collection('vendors')->document($request->vendor_id)->snapshot();
        $vendorName = $vendorDoc->exists() ? $vendorDoc->data()['vendor_name'] : 'Unknown Vendor';

        $data['vendor_name'] = $vendorName; 
        $data['created_at'] = now()->toIso8601String();

        $newRef = Firebase::firestore()->database()->collection('contracts')->add($data);

        return response()->json([
            'success' => 'Contract created successfully!',
            'id' => $newRef->id(),
            'contract' => $data
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

        // Update Vendor Name in case it changed
        $vendorDoc = Firebase::firestore()->database()->collection('vendors')->document($request->vendor_id)->snapshot();
        $data['vendor_name'] = $vendorDoc->exists() ? $vendorDoc->data()['vendor_name'] : 'Unknown Vendor';
        $data['updated_at'] = now()->toIso8601String();

        Firebase::firestore()->database()->collection('contracts')->document($id)->set($data, ['merge' => true]);

        return response()->json([
            'success' => 'Contract updated successfully!',
            'contract' => $data
        ]);
    }

    public function destroy($id)
    {
        Firebase::firestore()->database()->collection('contracts')->document($id)->delete();
        return response()->json(['success' => 'Contract deleted successfully!']);
    }
}