<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function show(Request $request)
    {
        $company = $request->user()->company;
        
        if (!$company) {
            return $this->successResponse(null);
        }

        return $this->successResponse($company);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'panNumber' => 'nullable|string|max:50',
            'vatNumber' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        $user = $request->user();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = '/storage/' . $logoPath;
        }

        $company = $user->company()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return $this->successResponse($company, 'Company details saved successfully');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'panNumber' => 'nullable|string|max:50',
            'vatNumber' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        $user = $request->user();
        $company = $user->company;

        if (!$company) {
            return $this->store($request);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo) {
                $oldPath = str_replace('/storage/', '', $company->logo);
                Storage::disk('public')->delete($oldPath);
            }
            
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = '/storage/' . $logoPath;
        }

        $company->update($validated);

        return $this->successResponse($company, 'Company details updated successfully');
    }
}
