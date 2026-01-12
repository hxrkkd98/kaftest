<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\FirebaseRestTrait;

/**
 * Vendor Controller
 * Uses Firebase REST API (HTTP/JSON) for all operations
 */
class VendorController extends Controller
{
    use FirebaseRestTrait;

    public function index()
    {
        // Fetch vendors using REST API
        $vendors = $this->getCollectionDocuments('vendors');
        
        return view('vendors.index', ['vendors' => $vendors]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);

        // Create vendor using REST API
        $newId = $this->createDocument('vendors', $data);

        return response()->json([
            'success' => 'Vendor created successfully!',
            'id' => $newId,
            'vendor' => array_merge(['id' => $newId], $data)
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);

        // Update vendor using REST API
        $this->updateDocument('vendors', $id, $data);

        return response()->json([
            'success' => 'Vendor updated successfully!',
            'vendor' => array_merge(['id' => $id], $data)
        ]);
    }

    public function destroy($id)
    {
        // Delete vendor using REST API
        $this->deleteDocument('vendors', $id);
        
        return response()->json([
            'success' => 'Vendor deleted successfully!'
        ]);
    }
}