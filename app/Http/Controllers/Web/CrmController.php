<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmNote;
use App\Models\CrmTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CrmController extends Controller
{
    // ── Dashboard ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Pipeline stats
        $stages = ['lead', 'qualified', 'proposal', 'negotiation', 'won', 'lost'];
        $pipeline = [];
        foreach ($stages as $stage) {
            $pipeline[$stage] = CrmDeal::where('company_id', $companyId)
                ->where('stage', $stage)->count();
        }
        $pipelineValue = CrmDeal::where('company_id', $companyId)
            ->whereNotIn('stage', ['won', 'lost'])->sum('value');
        $wonValue = CrmDeal::where('company_id', $companyId)
            ->where('stage', 'won')->sum('value');

        // Counts
        $totalContacts = CrmContact::where('company_id', $companyId)->count();
        $activeDeals   = CrmDeal::where('company_id', $companyId)
            ->whereNotIn('stage', ['won', 'lost'])->count();
        $pendingTasks  = CrmTask::where('company_id', $companyId)
            ->where('status', '!=', 'completed')->count();

        // Recent items
        $recentContacts = CrmContact::where('company_id', $companyId)
            ->latest()->take(5)->get();
        $recentDeals = CrmDeal::where('company_id', $companyId)
            ->with('contact')->latest()->take(5)->get();
        $upcomingTasks = CrmTask::where('company_id', $companyId)
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')->take(5)->get();

        return view('crm.index', compact(
            'pipeline', 'pipelineValue', 'wonValue',
            'totalContacts', 'activeDeals', 'pendingTasks',
            'recentContacts', 'recentDeals', 'upcomingTasks'
        ));
    }

    // ── Contacts ────────────────────────────────────────────────
    public function contacts(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = CrmContact::where('company_id', $companyId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('company_name', 'like', "%{$request->search}%");
            });
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $contacts = $query->withCount(['deals', 'tasks'])->latest()->paginate(15);

        return view('crm.contacts', compact('contacts'));
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type'  => 'required|in:lead,prospect,customer,partner',
        ]);

        CrmContact::create([
            'company_id'   => auth()->user()->company_id,
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'company_name' => $request->company_name,
            'designation'  => $request->designation,
            'address'      => $request->address,
            'type'         => $request->type,
            'source'       => $request->source,
            'status'       => $request->status ?? 'active',
            'notes'        => $request->notes,
        ]);

        return redirect()->route('crm.contacts')->with('success', 'Contact created successfully.');
    }

    public function updateContact(Request $request, CrmContact $contact)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type'  => 'required|in:lead,prospect,customer,partner',
        ]);

        $contact->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'company_name' => $request->company_name,
            'designation'  => $request->designation,
            'address'      => $request->address,
            'type'         => $request->type,
            'source'       => $request->source,
            'status'       => $request->status ?? 'active',
            'notes'        => $request->notes,
        ]);

        return redirect()->route('crm.contacts')->with('success', 'Contact updated successfully.');
    }

    public function destroyContact(CrmContact $contact)
    {
        $contact->crmNotes()->delete();
        $contact->tasks()->delete();
        $contact->deals()->delete();
        $contact->delete();

        return redirect()->route('crm.contacts')->with('success', 'Contact deleted successfully.');
    }

    // ── Deals ───────────────────────────────────────────────────
    public function deals(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = CrmDeal::where('company_id', $companyId)->with('contact');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhereHas('contact', fn($c) => $c->where('name', 'like', "%{$request->search}%"));
            });
        }
        if ($request->stage) {
            $query->where('stage', $request->stage);
        }

        $deals = $query->latest()->paginate(15);
        $contacts = CrmContact::where('company_id', $companyId)
            ->orderBy('name')->get(['id', 'name']);

        // Pipeline summary
        $stages = ['lead', 'qualified', 'proposal', 'negotiation', 'won', 'lost'];
        $pipelineSummary = [];
        foreach ($stages as $stage) {
            $pipelineSummary[$stage] = [
                'count' => CrmDeal::where('company_id', $companyId)->where('stage', $stage)->count(),
                'value' => CrmDeal::where('company_id', $companyId)->where('stage', $stage)->sum('value'),
            ];
        }

        return view('crm.deals', compact('deals', 'contacts', 'pipelineSummary'));
    }

    public function storeDeal(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'crm_contact_id' => 'required|exists:crm_contacts,id',
            'value'          => 'nullable|numeric|min:0',
            'stage'          => 'required|in:lead,qualified,proposal,negotiation,won,lost',
        ]);

        CrmDeal::create([
            'company_id'          => auth()->user()->company_id,
            'crm_contact_id'      => $request->crm_contact_id,
            'title'               => $request->title,
            'value'               => $request->value ?? 0,
            'stage'               => $request->stage,
            'priority'            => $request->priority ?? 'medium',
            'expected_close_date' => $request->expected_close_date,
            'description'         => $request->description,
        ]);

        return redirect()->route('crm.deals')->with('success', 'Deal created successfully.');
    }

    public function updateDeal(Request $request, CrmDeal $deal)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'crm_contact_id' => 'required|exists:crm_contacts,id',
            'value'          => 'nullable|numeric|min:0',
            'stage'          => 'required|in:lead,qualified,proposal,negotiation,won,lost',
        ]);

        $data = [
            'crm_contact_id'      => $request->crm_contact_id,
            'title'               => $request->title,
            'value'               => $request->value ?? 0,
            'stage'               => $request->stage,
            'priority'            => $request->priority ?? 'medium',
            'expected_close_date' => $request->expected_close_date,
            'description'         => $request->description,
        ];

        if ($request->stage === 'won' || $request->stage === 'lost') {
            $data['closed_date'] = now();
        } else {
            $data['closed_date'] = null;
        }

        $deal->update($data);

        return redirect()->route('crm.deals')->with('success', 'Deal updated successfully.');
    }

    public function destroyDeal(CrmDeal $deal)
    {
        $deal->delete();
        return redirect()->route('crm.deals')->with('success', 'Deal deleted successfully.');
    }

    // ── Notes ───────────────────────────────────────────────────
    public function storeNote(Request $request, CrmContact $contact)
    {
        $request->validate(['content' => 'required|string']);

        CrmNote::create([
            'company_id'     => auth()->user()->company_id,
            'crm_contact_id' => $contact->id,
            'content'        => $request->content,
            'created_by'     => auth()->id(),
        ]);

        return redirect()->route('crm.contacts')->with('success', 'Note added.');
    }

    public function destroyNote(CrmNote $note)
    {
        $note->delete();
        return redirect()->route('crm.contacts')->with('success', 'Note deleted.');
    }

    // ── Tasks ───────────────────────────────────────────────────
    public function storeTask(Request $request, CrmContact $contact)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        CrmTask::create([
            'company_id'     => auth()->user()->company_id,
            'crm_contact_id' => $contact->id,
            'title'          => $request->title,
            'description'    => $request->description,
            'due_date'       => $request->due_date,
            'priority'       => $request->priority ?? 'medium',
            'status'         => 'pending',
        ]);

        return redirect()->route('crm.contacts')->with('success', 'Task created.');
    }

    public function updateTask(Request $request, CrmTask $task)
    {
        $task->update($request->only(['title', 'description', 'due_date', 'priority', 'status']));
        return redirect()->route('crm.contacts')->with('success', 'Task updated.');
    }

    public function destroyTask(CrmTask $task)
    {
        $task->delete();
        return redirect()->route('crm.contacts')->with('success', 'Task deleted.');
    }
}
