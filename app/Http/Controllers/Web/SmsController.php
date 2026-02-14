<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SmsMessage;
use App\Models\SmsTemplate;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = SmsMessage::where('company_id', $companyId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('recipient_name', 'like', "%{$request->search}%")
                  ->orWhere('recipient_phone', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $messages = $query->latest()->paginate(20)->withQueryString();

        // Stats
        $totalSent = SmsMessage::where('company_id', $companyId)->where('status', 'sent')->count();
        $totalPending = SmsMessage::where('company_id', $companyId)->where('status', 'pending')->count();
        $totalFailed = SmsMessage::where('company_id', $companyId)->where('status', 'failed')->count();
        $todaySent = SmsMessage::where('company_id', $companyId)
            ->where('status', 'sent')
            ->whereDate('sent_at', Carbon::today())
            ->count();

        return view('sms.index', compact('messages', 'totalSent', 'totalPending', 'totalFailed', 'todaySent'));
    }

    public function compose()
    {
        $companyId = auth()->user()->company_id;
        $templates = SmsTemplate::where('company_id', $companyId)->where('is_active', true)->get();
        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->get();

        return view('sms.compose', compact('templates', 'employees'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'send_type' => 'required|in:individual,bulk,employees',
            'phone' => 'required_if:send_type,individual|nullable|string',
            'name' => 'nullable|string|max:255',
            'phones' => 'required_if:send_type,bulk|nullable|string',
            'employee_ids' => 'required_if:send_type,employees|nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'message' => 'required|string|max:500',
        ]);

        $companyId = auth()->user()->company_id;
        $batchId = Str::uuid()->toString();
        $recipients = [];

        if ($validated['send_type'] === 'individual') {
            $recipients[] = [
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'],
            ];
        } elseif ($validated['send_type'] === 'bulk') {
            $phones = preg_split('/[\n,;]+/', $validated['phones']);
            foreach ($phones as $phone) {
                $phone = trim($phone);
                if (!empty($phone)) {
                    $recipients[] = [
                        'name' => null,
                        'phone' => $phone,
                    ];
                }
            }
        } elseif ($validated['send_type'] === 'employees') {
            $employees = Employee::whereIn('id', $validated['employee_ids'])
                ->where('company_id', $companyId)
                ->get();
            foreach ($employees as $emp) {
                if ($emp->phone) {
                    $recipients[] = [
                        'name' => $emp->name,
                        'phone' => $emp->phone,
                    ];
                }
            }
        }

        $count = 0;
        foreach ($recipients as $recipient) {
            SmsMessage::create([
                'company_id' => $companyId,
                'recipient_name' => $recipient['name'],
                'recipient_phone' => $recipient['phone'],
                'message' => $validated['message'],
                'status' => 'pending', // Will be 'sent' when API is integrated
                'type' => count($recipients) > 1 ? 'bulk' : 'individual',
                'batch_id' => $batchId,
            ]);
            $count++;
        }

        return redirect()->route('sms.index')
            ->with('success', "{$count} SMS message(s) queued successfully. (API integration pending)");
    }

    public function templates()
    {
        $companyId = auth()->user()->company_id;
        $templates = SmsTemplate::where('company_id', $companyId)->latest()->get();

        return view('sms.templates', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:500',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        SmsTemplate::create($validated);

        return back()->with('success', 'Template created successfully.');
    }

    public function destroyTemplate(SmsTemplate $template)
    {
        if ($template->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $template->delete();

        return back()->with('success', 'Template deleted successfully.');
    }

    public function destroy(SmsMessage $smsMessage)
    {
        if ($smsMessage->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $smsMessage->delete();

        return back()->with('success', 'Message deleted successfully.');
    }
}
