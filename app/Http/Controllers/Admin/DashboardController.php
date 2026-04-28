<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents    = User::where('role', 'student')->count();
        $totalCompanies   = Company::where('status', 'approved')->count();
        $pendingCompanies = Company::where('status', 'pending')->count();
        $totalVacantes    = JobPosting::where('status', 'active')->count();
        $totalPostulaciones = Application::count();

        // Estadísticas para gráficos (últimos 6 meses) — compatible con SQLite y MySQL
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $monthlyApplications = Application::selectRaw("strftime('%m', created_at) as month, COUNT(*) as total")
                ->whereRaw("strftime('%Y', created_at) = ?", [now()->year])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $monthlyApplications = Application::selectRaw("DATE_FORMAT(created_at, '%m') as month, COUNT(*) as total")
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        $recentCompanies = Company::with('user')->latest()->take(5)->get();
        $recentApplications = Application::with(['user', 'jobPosting.company'])->latest()->take(8)->get();

        // Áreas con más vacantes
        $topAreas = JobPosting::active()
            ->selectRaw('area, COUNT(*) as total')
            ->groupBy('area')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalCompanies', 'pendingCompanies',
            'totalVacantes', 'totalPostulaciones', 'monthlyApplications',
            'recentCompanies', 'recentApplications', 'topAreas'
        ));
    }

    // ─── Gestión de empresas ──────────────────────────────────────
    public function companies(Request $request)
    {
        $query = Company::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('company_name', 'like', "%{$search}%");
        }

        $companies = $query->latest()->paginate(15)->withQueryString();
        return view('admin.companies.index', compact('companies'));
    }

    public function approveCompany(Company $company)
    {
        $company->update(['status' => 'approved']);
        // Notificar al empresario
        $company->user->notify(new \App\Notifications\CompanyApprovedNotification($company));
        return back()->with('success', "Empresa \"{$company->company_name}\" aprobada.");
    }

    public function rejectCompany(Request $request, Company $company)
    {
        $company->update(['status' => 'rejected']);
        return back()->with('success', "Empresa \"{$company->company_name}\" rechazada.");
    }

    // ─── Gestión de usuarios ──────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function toggleUser(User $user)
    {
        if ($user->isAdmin()) abort(403);
        $user->update(['active' => !$user->active]);
        $status = $user->active ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$status} correctamente.");
    }

    // ─── Reportes ─────────────────────────────────────────────────
    public function reports()
    {
        $stats = [
            'students_total'      => User::where('role', 'student')->count(),
            'companies_approved'  => Company::where('status', 'approved')->count(),
            'jobs_active'         => JobPosting::where('status', 'active')->count(),
            'jobs_closed'         => JobPosting::where('status', 'closed')->count(),
            'applications_total'  => Application::count(),
            'applications_accepted' => Application::where('status', 'accepted')->count(),
        ];

        $companiesByStatus = Company::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $applicationsByStatus = Application::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $jobsByArea = JobPosting::selectRaw('area, COUNT(*) as total')->groupBy('area')->orderByDesc('total')->get();

        return view('admin.reports', compact('stats', 'companiesByStatus', 'applicationsByStatus', 'jobsByArea'));
    }
}
