<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminVendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
    }

    public function pending()
    {
        $vendors = Vendor::where('approval_status', 'pending')->paginate(10);
        return view('admin.vendors.pending', compact('vendors'));
    }

    public function approve($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $vendor->update(['approval_status' => 'active']);
        return back()->with('success', 'Vendor approved');
    }

    public function reject(Request $request, $vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $vendor->update(['approval_status' => 'rejected']);
        return back()->with('success', 'Vendor rejected');
    }

    public function suspend($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $vendor->update(['approval_status' => 'suspended']);
        return back()->with('success', 'Vendor suspended');
    }

    public function list()
    {
        $vendors = Vendor::paginate(15);
        return view('admin.vendors.list', compact('vendors'));
    }
}