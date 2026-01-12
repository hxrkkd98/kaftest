<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\FirebaseRestTrait;
use Carbon\Carbon;

/**
 * Dashboard Controller
 * Uses Firebase REST API (HTTP/JSON) for all operations
 */
class DashboardController extends Controller
{
    use FirebaseRestTrait;

    public function index()
    {
        // Fetch contracts using REST API
        $contractsRef = $this->executeFirestoreOperation(function () {
            $db = $this->getFirestoreDatabase();
            return $db->collection('contracts')->documents();
        }, 'Fetch contracts for dashboard');
        
        $stats = [
            'active' => 0,
            'expiring_30' => 0,
            'expiring_60' => 0,
            'expiring_90' => 0,
            'expired' => 0,
        ];

        $today = Carbon::today();

        foreach ($contractsRef as $doc) {
            if (!$doc->exists()) continue;
            
            $data = $doc->data();
            
            // Parse Dates
            $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : null;
            $endDate = isset($data['end_date']) ? Carbon::parse($data['end_date']) : null;

            if ($endDate) {
                // 1. Check Expired
                if ($endDate->lt($today)) {
                    $stats['expired']++;
                } 
                // 2. Check Active (Started and NOT Expired)
                elseif ($startDate && $startDate->lte($today)) {
                    $stats['active']++;

                    // 3. Check Expiring in 60 Days
                    $daysRemaining = $today->diffInDays($endDate, false);

                    if ($daysRemaining <= 30) {
                        $stats['expiring_30']++;
                    }
                    
                    if ($daysRemaining <= 60) {
                        $stats['expiring_60']++;
                    }
                    
                    // 4. Check Expiring in 90 Days
                    if ($daysRemaining <= 90) {
                        $stats['expiring_90']++;
                    }
                }
            }
        }

        return view('dashboard', ['stats' => $stats]);
    }
}