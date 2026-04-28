@extends('layouts.app')
@section('title', 'Panel Empresa')

@push('styles')
<style>
    .company-welcome {
        background: linear-gradient(135deg, #273475 0%, #1d2659 100%);
        border-radius: 16px;
        border: none;
        position: relative;
        overflow: hidden;
    }
    .company-welcome::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(0,150,63,.12);
        border-radius: 50%;
    }
    .company-welcome > * { position: relative; z-index: 1; }
    .company-logo-img {
        width: 68px; height: 68px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid rgba(255,255,255,.2);
        background: rgba(255,255,255,.1);
        flex-shrink: 0;
    }

    .kpi-card {
        background: #fff;
        border-radius: 14px;
        border: none;
        box-shadow: 0 1px 8px rgba(0,0,0,.06);
        padding: 1.1rem 1.3rem;
        transition: box-shadow .2s, transform .2s;
    }
    .kpi-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); transform: translateY(-2px); }
    .kpi-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .kpi-value { font-size: 1.8rem; font-weight: 800; line-height: 1; color: #1a1f36; }
    .kpi-label { font-size: .78rem; color: #6b7280; font-weight: 500; margin-top: .2rem; }

    .panel-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eef0f9;
        box-shadow: 0 1px 6px rgba(0,0,0,.05);
        overflow: hidden;
    }
    .panel-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f0f2fb;
        font-weight: 700;
        font-size: .88rem;
        color: #1a1f36;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .panel-header i { color: #273475; }

    /* Tabla */
    .table th {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #9ca3af;
        background: #fafafa;
        border-bottom: 1px solid #eef0f9;
        padding: .7rem 1rem;
    }
    .table td { padding: .8rem 1rem; vertical-align: middle; border-color: #f5f5f7; }
    .table tbody tr:hover { background: #fafbff; }

    .student-avatar {
        width: 34px; height: 34px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid #eef0f9;
    }

    .btn-sm-unipaz {
        background: #eef0f9;
        color: #273475;
        border: none;
        border-radius: 7px;
        font-size: .78rem;
        font-weight: 600;
        padding: .3rem .75rem;
        text-decoration: none;
        transition: background .15s;
    }
    .btn-sm-unipaz:hover { background: #273475; color: #fff; }

    .btn-action {
        border: none;
        border-radius: 7px;
        padding: .3rem .6rem;
        font-size: .82rem;
        transition: all .15s;
        cursor: pointer;
    }
    .btn-action-cv { background: #fee2e2; color: #b91c1c; }
    .btn-action-cv:hover { background: #b91c1c; color: #fff; }
    .btn-action-edit { background: #eef0f9; color: #273475; }
    .btn-action-edit:hover { background: #273475; color: #fff; }

    .pending-alert {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 1.5px solid #f59e0b;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* Modal */
    .modal-content { border: none; border-radius: 16px; box-shadow: 0 16px 48px rgba(0,0,0,.15); }
    .modal-header { border-bottom: 1px solid #eef0f9; padding: 1.25rem 1.5rem; }
    .modal-footer { border-top: 1px solid #eef0f9; padding: .75rem 1.5rem; }
    .modal-title { font-size: .95rem; font-weight: 700; color: #1a1f36; }
    .btn-modal-cancel { background: #f4f5f7; color: #6b7280; border: none; border-radius: 8px; font-weight: 600; }
    .btn-modal-save { background: #273475; color: #fff; border: none; border-radius: 8px; font-weight: 700; }
    .btn-modal-save:hover { background: #1d2659; color: #fff; }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- Alerta pendiente --}}
    @if(!$company->isApproved())
        <div class="pending-alert mb-4">
            <i class="bi bi-hourglass-split fs-3" style="color:#b45309; flex-shrink:0;"></i>
            <div>
                <div style="font-weight:700; color:#92400e; font-size:.92rem;">Tu empresa está en proceso de verificación</div>
                <div style="font-size:.83rem; color:#b45309; margin-top:.2rem;">
                    El equipo de UNIPAZ revisará tu solicitud. Recibirás un correo cuando sea aprobada y podrás publicar vacantes.
                </div>
            </div>
        </div>
    @endif

    {{-- Bienvenida empresa --}}
    <div class="company-welcome mb-4">
        <div class="card-body py-4 px-4 text-white d-flex align-items-center gap-4 flex-wrap">
            <img src="{{ $company->logo_url }}" class="company-logo-img" alt="logo">
            <div class="flex-grow-1">
                <div style="font-size:.7rem; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:rgba(255,255,255,.45); margin-bottom:.2rem;">
                    Panel Empresarial
                </div>
                <h4 class="fw-bold mb-1">{{ $company->company_name }}</h4>
                <p class="mb-0" style="font-size:.85rem; color:rgba(255,255,255,.65);">
                    <i class="bi bi-tag me-1"></i>{{ $company->sector }}
                    @if($company->address)
                        <span class="mx-2 opacity-40">·</span>
                        <i class="bi bi-geo-alt me-1"></i>{{ $company->address }}
                    @endif
                </p>
            </div>
            @if($company->isApproved())
                <div class="d-flex gap-2">
                    <a href="{{ route('company.jobs.index') }}"
                       style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fff; border-radius:10px; padding:.65rem 1.3rem; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:.5rem; transition:all .2s; flex-shrink:0;"
                       onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
                        <i class="bi bi-list-ul"></i>Mis Vacantes
                    </a>
                    <a href="{{ route('company.jobs.create') }}"
                       style="background:#00963F; color:#fff; border:none; border-radius:10px; padding:.65rem 1.3rem; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:.5rem; transition:background .18s; flex-shrink:0;"
                       onmouseover="this.style.background='#007832'" onmouseout="this.style.background='#00963F'">
                        <i class="bi bi-plus-circle-fill"></i>Publicar vacante
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('company.jobs.index') }}" style="text-decoration:none; color:inherit; display:block;">
                <div class="kpi-card d-flex align-items-center gap-3"
                     style="border-left:4px solid #273475;">
                    <div class="kpi-icon" style="background:#eef0f9; color:#273475;">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-value">{{ $totalVacantes }}</div>
                        <div class="kpi-label">Total vacantes</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('company.jobs.index') }}" style="text-decoration:none; color:inherit; display:block;">
                <div class="kpi-card d-flex align-items-center gap-3"
                     style="border-left:4px solid #00963F;">
                    <div class="kpi-icon" style="background:#e6f7ed; color:#00963F;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-value">{{ $activeVacantes }}</div>
                        <div class="kpi-label">Vacantes activas</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card d-flex align-items-center gap-3"
                 style="border-left:4px solid #6366f1;">
                <div class="kpi-icon" style="background:#ede9fe; color:#4f46e5;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalPostulaciones }}</div>
                    <div class="kpi-label">Total postulaciones</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card d-flex align-items-center gap-3"
                 style="border-left:4px solid #f59e0b;">
                <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
                    <i class="bi bi-inbox-fill"></i>
                </div>
                <div>
                    <div class="kpi-value">{{ $pendingPostulaciones }}</div>
                    <div class="kpi-label">Sin revisar</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla postulaciones --}}
    <div class="panel-card">
        <div class="panel-header">
            <span><i class="bi bi-people me-2"></i>Postulaciones recientes</span>
            <a href="{{ route('company.applications') }}" class="btn-sm-unipaz">Ver todas</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Programa</th>
                        <th>Vacante</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentApplications as $app)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $app->user->avatar_url }}"
                                         class="student-avatar" alt="avatar">
                                    <div>
                                        <div style="font-size:.875rem; font-weight:600; color:#1a1f36;">
                                            {{ Str::limit($app->user->name, 22) }}
                                        </div>
                                        <div style="font-size:.74rem; color:#9ca3af;">
                                            {{ Str::limit($app->user->email, 26) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:.8rem; color:#6b7280;">
                                    {{ $app->user->studentProfile?->program ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:.85rem; font-weight:600; color:#1a1f36;">
                                    {{ Str::limit($app->jobPosting->title, 28) }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:.8rem; color:#6b7280;">
                                    {{ $app->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $app->status_badge }}" style="font-size:.72rem;">
                                    {{ $app->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($app->cv_path)
                                        <a href="{{ Storage::url($app->cv_path) }}"
                                           target="_blank"
                                           class="btn-action btn-action-cv"
                                           title="Ver CV">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                    @endif
                                    <button class="btn-action btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalStatus{{ $app->id }}"
                                            title="Cambiar estado">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>

                                {{-- Modal estado --}}
                                <div class="modal fade" id="modalStatus{{ $app->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="{{ route('company.applications.update', $app) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">
                                                        <i class="bi bi-pencil-square me-2 text-unipaz"></i>
                                                        Actualizar postulación
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body px-4 py-3">
                                                    <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded"
                                                         style="background:#f8f9ff; border:1px solid #eef0f9;">
                                                        <img src="{{ $app->user->avatar_url }}"
                                                             class="student-avatar" alt="avatar">
                                                        <div>
                                                            <div style="font-size:.875rem; font-weight:600;">{{ $app->user->name }}</div>
                                                            <div style="font-size:.75rem; color:#9ca3af;">{{ $app->jobPosting->title }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nuevo estado</label>
                                                        <select name="status" class="form-select" required>
                                                            @foreach(['pending' => 'Pendiente', 'reviewed' => 'En revisión', 'interview' => 'Entrevista programada', 'accepted' => 'Aceptado', 'rejected' => 'No seleccionado'] as $val => $label)
                                                                <option value="{{ $val }}" @selected($app->status === $val)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Notas internas <span class="text-muted fw-normal">(opcional)</span></label>
                                                        <textarea name="company_notes" class="form-control" rows="2"
                                                                  placeholder="Comentarios internos sobre el candidato...">{{ $app->company_notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-modal-cancel btn-sm px-3" data-bs-dismiss="modal">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-modal-save btn-sm px-4">
                                                        <i class="bi bi-check-lg me-1"></i>Guardar cambio
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color:#9ca3af;">
                                <i class="bi bi-inbox d-block fs-2 mb-2 opacity-25"></i>
                                <p class="mb-0 small">Aún no hay postulaciones en tus vacantes.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
