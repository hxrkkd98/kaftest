<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class VendorController extends Controller
{
    public function index()
    {
        $vendorsRef = Firebase::firestore()->database()->collection('vendors');
        $documents = $vendorsRef->documents();

        $vendors = [];
        foreach ($documents as $doc) {
            if ($doc->exists()) {
                $vendors[] = array_merge(['id' => $doc->id()], $doc->data());
            }
        }
        return view('vendors.index', ['vendors' => $vendors]);
    }

    public function store(Request $request)
    {
        // 1. ADD VALIDATION FOR EMAIL & ADDRESS
        $data = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',       // New
            'phone_number' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
            'address' => 'required|string|max:500',    // New
        ]);
        
        $data['created_at'] = now()->toIso8601String();

        $newRef = Firebase::firestore()->database()->collection('vendors')->add($data);

        return response()->json([
            'success' => 'Vendor created successfully!',
            'id' => $newRef->id(),
            'vendor' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        // 2. ADD VALIDATION FOR EMAIL & ADDRESS
        $data = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',       // New
            'phone_number' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
            'address' => 'required|string|max:500',    // New
        ]);

        $data['updated_at'] = now()->toIso8601String();

        Firebase::firestore()->database()->collection('vendors')->document($id)->set($data, ['merge' => true]);

        return response()->json([
            'success' => 'Vendor updated successfully!',
            'vendor' => $data
        ]);
    }

    public function destroy($id)
    {
        Firebase::firestore()->database()->collection('vendors')->document($id)->delete();
        return response()->json(['success' => 'Vendor deleted successfully!']);
    }
}