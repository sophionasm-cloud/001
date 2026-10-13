<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the customer's saved addresses.
     */
    public function index()
    {
        $addresses = auth()->user()->addresses()->latest()->get();
        return view('customer.addresses.index', compact('addresses'));
    }

    /**
     * Store a newly created address for the authenticated customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|string|in:Home,Office,Other',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'nullable|string|max:100',
            'zip_code'      => 'required|string|max:20',
            'country'       => 'required|string|max:100',
            'phone'         => 'nullable|string|max:30',
            'is_default'    => 'sometimes|boolean',
        ]);

        $user = auth()->user();

        // If marked as default or first address, manage defaults
        $isDefault = $request->boolean('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $validated['user_id'] = $user->id;
        $validated['is_default'] = $isDefault;

        Address::create($validated);

        return redirect()->route('customer.addresses.index')->with('success', 'Address added successfully!');
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address)
    {
        // Enforce user ownership authorization
        abort_unless($address->user_id === auth()->id(), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'type'          => 'required|string|in:Home,Office,Other',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'nullable|string|max:100',
            'zip_code'      => 'required|string|max:20',
            'country'       => 'required|string|max:100',
            'phone'         => 'nullable|string|max:30',
            'is_default'    => 'sometimes|boolean',
        ]);

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $address->update($validated);

        return redirect()->route('customer.addresses.index')->with('success', 'Address updated successfully!');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address)
    {
        // Enforce user ownership authorization
        abort_unless($address->user_id === auth()->id(), 403, 'Unauthorized action.');

        $wasDefault = $address->is_default;
        $address->delete();

        // If the deleted address was default, make the newest remaining address default
        if ($wasDefault) {
            $remaining = auth()->user()->addresses()->latest()->first();
            if ($remaining) {
                $remaining->update(['is_default' => true]);
            }
        }

        return redirect()->route('customer.addresses.index')->with('success', 'Address deleted successfully!');
    }
}
