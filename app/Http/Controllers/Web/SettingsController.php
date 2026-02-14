<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $settings = $company->settings ?? (object)[];
        
        // Detect the max category level used in the system
        $maxCategoryLevel = ProductCategory::where('company_id', $company->id)->max('level') ?? 0;
        // Show levels 0 through maxCategoryLevel (at minimum level 0)
        $categoryLevelCount = max($maxCategoryLevel + 1, 1);
        
        return view('settings.index', compact('company', 'settings', 'categoryLevelCount'));
    }

    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
        ]);

        $company = auth()->user()->company;

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);

        return back()->with('success', 'Company profile updated successfully.');
    }

    public function updateUser(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateBilling(Request $request)
    {
        $validated = $request->validate([
            'bill_prefix' => 'nullable|string|max:10',
            'quotation_prefix' => 'nullable|string|max:10',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'currency_symbol' => 'nullable|string|max:5',
            'bill_footer' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
        ]);

        $company = auth()->user()->company;
        $settings = $company->settings ?? [];
        
        $company->update([
            'settings' => array_merge($settings, $validated),
        ]);

        return back()->with('success', 'Billing settings updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function updateCategoryLabels(Request $request)
    {
        $company = auth()->user()->company;
        
        $labels = [];
        foreach ($request->input('level_labels', []) as $level => $label) {
            if (!empty(trim($label))) {
                $labels[(int)$level] = trim($label);
            }
        }

        $company->update([
            'category_level_labels' => $labels,
        ]);

        return back()->with('success', 'Category level labels updated successfully.');
    }

    public function updateTaxSystem(Request $request)
    {
        $validated = $request->validate([
            'tax_system' => 'required|in:none,pan,vat',
            'panNumber' => 'nullable|string|max:50',
            'vatNumber' => 'nullable|string|max:50',
        ]);

        $company = auth()->user()->company;
        $settings = $company->settings ?? [];
        $settings['tax_system'] = $validated['tax_system'];

        $company->update([
            'settings' => $settings,
            'panNumber' => $validated['panNumber'],
            'vatNumber' => $validated['vatNumber'],
        ]);

        return back()->with('success', 'Tax system settings updated successfully.');
    }
}
