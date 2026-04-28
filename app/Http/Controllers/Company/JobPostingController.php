<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobPostingController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        $jobPostings = JobPosting::where('company_id', $company->id)
            ->withCount('applications')
            ->latest()
            ->paginate(10);

        return view('company.jobs.index', compact('jobPostings', 'company'));
    }

    public function create()
    {
        $company = Auth::user()->company;
        if (!$company->isApproved()) {
            return redirect()->route('company.dashboard')
                ->with('error', 'Tu empresa debe ser aprobada por UNIPAZ antes de publicar vacantes.');
        }
        return view('company.jobs.create');
    }

    public function store(Request $request)
    {
        $company = Auth::user()->company;
        if (!$company->isApproved()) abort(403);

        $request->validate([
            'title'             => 'required|string|max:200',
            'description'       => 'required|string|max:5000',
            'requirements'      => 'required|string|max:3000',
            'responsibilities'  => 'nullable|string|max:3000',
            'area'              => 'required|string|max:100',
            'contract_type'     => 'required|string|max:100',
            'modality'          => 'required|in:onsite,remote,hybrid',
            'location'          => 'required|string|max:150',
            'salary_range'      => 'nullable|string|max:100',
            'salary_negotiable' => 'boolean',
            'positions'         => 'required|integer|min:1|max:100',
            'deadline'          => 'required|date|after:today',
            'requires_cv'       => 'boolean',
            'programs_targeted' => 'nullable|string|max:500',
        ]);

        JobPosting::create([
            ...$request->except('_token'),
            'company_id'        => $company->id,
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'requires_cv'       => $request->boolean('requires_cv'),
            'status'            => 'active',
        ]);

        return redirect()->route('company.jobs.index')
            ->with('success', 'Vacante publicada exitosamente.');
    }

    public function edit(JobPosting $jobPosting)
    {
        $company = Auth::user()->company;
        if ($jobPosting->company_id !== $company->id) abort(403);

        return view('company.jobs.edit', compact('jobPosting'));
    }

    public function update(Request $request, JobPosting $jobPosting)
    {
        $company = Auth::user()->company;
        if ($jobPosting->company_id !== $company->id) abort(403);

        $request->validate([
            'title'             => 'required|string|max:200',
            'description'       => 'required|string|max:5000',
            'requirements'      => 'required|string|max:3000',
            'responsibilities'  => 'nullable|string|max:3000',
            'area'              => 'required|string|max:100',
            'contract_type'     => 'required|string|max:100',
            'modality'          => 'required|in:onsite,remote,hybrid',
            'location'          => 'required|string|max:150',
            'salary_range'      => 'nullable|string|max:100',
            'salary_negotiable' => 'boolean',
            'positions'         => 'required|integer|min:1|max:100',
            'deadline'          => 'required|date',
            'requires_cv'       => 'boolean',
            'programs_targeted' => 'nullable|string|max:500',
            'status'            => 'required|in:active,paused,closed',
        ]);

        $jobPosting->update([
            ...$request->except(['_token', '_method']),
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'requires_cv'       => $request->boolean('requires_cv'),
        ]);

        return redirect()->route('company.jobs.index')
            ->with('success', 'Vacante actualizada correctamente.');
    }

    public function destroy(JobPosting $jobPosting)
    {
        $company = Auth::user()->company;
        if ($jobPosting->company_id !== $company->id) abort(403);

        $jobPosting->delete();

        return back()->with('success', 'Vacante eliminada correctamente.');
    }
}
