<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class AdminFacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('license_number', 'LIKE', "%{$search}%");
            });
        }

        $facilities = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.facilities.index', compact('facilities'));
    }

    public function show(Facility $facility)
    {
        $facility->load(['users', 'approver', 'specimenRequests']);
        return view('admin.facilities.show', compact('facility'));
    }

    public function approve(Facility $facility)
    {
        $facility->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'active',
        ]);

        return back()->with('success', 'Facility approved successfully.');
    }

    public function reject(Facility $facility)
    {
        $facility->update([
            'status' => 'rejected',
            'is_approved' => false,
        ]);

        return back()->with('success', 'Facility rejected successfully.');
    }
}