<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LeadController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Lead::query();

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Lead::count(),
            'new' => Lead::where('status', 'New')->count(),
            'contacted' => Lead::where('status', 'Contacted')->count(),
            'follow_up' => Lead::where('status', 'Follow Up')->count(),
            'converted' => Lead::where('status', 'Converted')->count(),
        ];

        return $this->noIndex('leads.index', [
            'leads' => $leads,
            'stats' => $stats,
        ]);
    }

    public function create(): Response
    {
        return $this->noIndex('leads.create', [
            'lead' => new Lead(['status' => 'New']),
        ]);
    }

    public function store(Request $request)
    {
        Lead::create($this->validatedData($request));

        return redirect()
            ->route('summary.leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead): Response
    {
        return $this->noIndex('leads.show', [
            'lead' => $lead,
        ]);
    }

    public function edit(Lead $lead): Response
    {
        return $this->noIndex('leads.edit', [
            'lead' => $lead,
        ]);
    }

    public function update(Request $request, Lead $lead)
    {
        $lead->update($this->validatedData($request));

        return redirect()
            ->route('summary.leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()
            ->route('summary.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['status'] = trim($data['status'] ?? '') !== '' ? $data['status'] : 'New';

        return $data;
    }

    private function noIndex(string $view, array $data = []): Response
    {
        return response()
            ->view($view, $data)
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
