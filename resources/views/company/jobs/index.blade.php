@extends('layouts.app')
@section('title', 'Mis Vacantes')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #273475 0%, #1d2659 100%);
        border-radius: 16px;
        padding: 2.5rem 2rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .page-header::before {
        content: ''; 
        position: absolute; 
        top: -40px; right: -40px;
        width: 250px; height: 250px; 
        background: rgba(0,150,63,.12); 
        border-radius: 50%;
    }
    .job-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eef0f9;
        padding: 1.5rem;
        transition: box-shadow .2s, transform .2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .job-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,.08);
        transform: translateY(-3px);
    }
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
    }
    .status-active { background: #e6f7ed; color: #00963F; }
    .status-closed { background: #fee2e2; color: #b91c1c; }
    .status-paused { background: #fef3c7; color: #b45309; }
    
    .btn-create {
        background: #00963F; color: #fff; border: none; border-radius: 10px;
        padding: 0.65rem 1.3rem; font-weight: 700; display: inline-flex;
        align-items: center; gap: 0.5rem; transition: background .18s; text-decoration: none;
    }
    .btn-create:hover { background: #007832; color: #fff; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 shadow-sm">
        <div style="position: relative; z-index: 1;">
            <h3 class="fw-bold mb-1">Mis Vacantes</h3>
            <p class="mb-0 text-white-50">Gestiona y revisa el estado de las ofertas de empleo de tu empresa.</p>
        </div>
        @if($company->isApproved())
            <a href="{{ route('company.jobs.create') }}" class="btn-create" style="position: relative; z-index: 1; box-shadow: 0 4px 12px rgba(0,150,63,0.3);">
                <i class="bi bi-plus-circle-fill"></i> Crear Nueva Vacante
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse($jobPostings as $job)
            <div class="col-md-6 col-lg-4">
                <div class="job-card position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold text-dark mb-0 pe-2" style="font-size: 1.1rem; line-height: 1.3;">{{ $job->title }}</h5>
                        @php
                            $statusClass = match($job->status) {
                                'active' => 'status-active',
                                'closed' => 'status-closed',
                                'paused' => 'status-paused',
                                default => 'bg-secondary text-white'
                            };
                            $statusLabel = match($job->status) {
                                'active' => 'Activa',
                                'closed' => 'Cerrada',
                                'paused' => 'Pausada',
                                default => ucfirst($job->status)
                            };
                            if ($job->isExpired() && $job->status === 'active') {
                                $statusClass = 'status-closed';
                                $statusLabel = 'Expirada';
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }} ms-auto" style="white-space: nowrap;">{{ $statusLabel }}</span>
                    </div>
                    
                    <div class="mb-4" style="font-size: 0.85rem; color: #6b7280;">
                        <div class="mb-2"><i class="bi bi-geo-alt me-2 text-primary opacity-75"></i>{{ $job->location }}</div>
                        <div class="mb-2"><i class="bi bi-briefcase me-2 text-primary opacity-75"></i>{{ ucfirst($job->modality) }} · {{ $job->contract_type }}</div>
                        <div class="mb-2"><i class="bi bi-cash-stack me-2 text-primary opacity-75"></i>{{ $job->salary_label }}</div>
                        <div><i class="bi bi-calendar-event me-2 text-primary opacity-75"></i>Cierra el {{ $job->deadline->format('d/m/Y') }}</div>
                    </div>

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <div style="font-size: 0.85rem; font-weight: 700; color: #273475; background: #eef0f9; padding: 0.3rem 0.8rem; border-radius: 8px;">
                            <i class="bi bi-people-fill me-1"></i> {{ $job->applications_count }} <span class="d-none d-sm-inline">Postulaciones</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('company.jobs.edit', $job) }}" class="btn btn-sm btn-light border text-primary bg-white" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('company.jobs.destroy', $job) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar permanentemente esta vacante?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger bg-white" title="Eliminar Vacante">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 px-3" style="background: #fff; border-radius: 14px; border: 2px dashed #d1d5db;">
                    <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="bi bi-briefcase text-muted mb-0 d-block" style="font-size: 2.5rem; opacity: 0.6;"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-2">Aún no has publicado vacantes</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">Empieza a conectar con el mejor talento estudiantil y egresados de UNIPAZ creando tu primera oferta de empleo.</p>
                    @if($company->isApproved())
                        <a href="{{ route('company.jobs.create') }}" class="btn-create shadow-sm">
                            <i class="bi bi-plus-circle-fill"></i> Publicar mi primera vacante
                        </a>
                    @else
                        <div class="alert alert-warning d-inline-block border-0 shadow-sm" style="background: #fef3c7; color: #b45309;">
                            <i class="bi bi-info-circle-fill me-2"></i>Tu empresa debe ser aprobada antes de publicar vacantes.
                        </div>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $jobPostings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
