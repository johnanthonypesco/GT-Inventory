<?php

// app/Http/Controllers/CompanyController.php
namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'status' => 'required|in:active,inactive',
        ]);

        $company->update($validated);

        return redirect()->back()->with('success', 'Company details updated successfully!');
    }

    /**
     * Archive the specified company and its users.
     */
   public function destroy(Company $company)
{
    // 1. Loop through users and call your CUSTOM archive() method.
    foreach ($company->users as $user) {
        $user->archive(); // This will set the 'archived_at' timestamp on the user
    }

    // 2. Loop through deals and soft delete them (assuming they use SoftDeletes)
    // foreach ($company->exclusive_deals as $deal) {
    //     $deal->delete(); // This will set the 'deleted_at' timestamp on the deal
    // }

    // 3. Soft delete the company using Laravel's standard method.
    $company->delete(); // This will set the 'deleted_at' timestamp on the company

    return redirect()->back()->with('success', 'Company and all its users have been archived.');
}


// In app/Http/Controllers/CompanyController.php
public function restore($id)
{
    // Find the company only within the soft-deleted records
    $company = \App\Models\Company::onlyTrashed()->findOrFail($id);

    // Restore the company
    $company->restore();

    // Bonus: Restore its users and deals as well for data consistency
    // $company->users()->onlyTrashed()->restore();
    // $company->exclusiveDeals()->onlyTrashed()->restore();

    return redirect()->back()->with('success', 'Company has been successfully restored.');
}
}
