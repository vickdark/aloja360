@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-tags text-primary me-2"></i> Categorías de Gasto
            <span class="badge bg-light text-dark ms-3 rounded-pill fs-6">{{ $categories->total() }} Creadas</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver a Gastos
            </a>
            <a href="{{ route('expense_categories.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Categoría
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @forelse($categories as $cat)
        <div class="col d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm rounded-4 overflow-hidden transition-all hover-lift">
                <div class="card-body p-4 border-top border-5" style="border-top-color: {{ $cat->color ?? '#6c757d' }} !important;">
                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <div class="rounded-4 p-3 text-white shadow-sm" style="background-color: {{ $cat->color ?? '#6c757d' }};">
                            <i class="{{ $cat->icon ?? 'fa-solid fa-wallet' }} fa-xl"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="mb-0 fw-bold text-truncate">{{ $cat->name }}</h5>
                            @if($cat->code)
                                <span class="small text-muted">#{{ $cat->code }}</span>
                            @endif
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('expense_categories.show', $cat) }}" class="btn btn-sm btn-outline-secondary" title="Ver">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('expense_categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('expense_categories.destroy', $cat) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta categoría?')" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @if($cat->description)
                        <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $cat->description }}
                        </p>
                    @endif
                    <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                        <div class="d-flex gap-2">
                            @if($cat->is_default)
                                <span class="badge text-bg-success rounded-pill"><i class="fa-solid fa-star"></i></span>
                            @endif
                            @if($cat->is_tax_deductible)
                                <span class="badge text-bg-info rounded-pill">Deducible</span>
                            @endif
                        </div>
                        <div class="small text-muted">
                            {{ $cat->expenses_count }} movs.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 text-center py-5 bg-light">
                <div class="card-body text-muted py-5">
                    <i class="fa-solid fa-tags fa-4x mb-4 opacity-25"></i>
                    <h4 class="mb-2">Sin Categorías de Gasto</h4>
                    <p class="mb-4">Organiza tus finanzas creando grupos: Limpieza, Marketing, Nómina, etc.</p>
                    <a href="{{ route('expense_categories.create') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Crear Categoría
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($categories->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    @endif
</div>
<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; }
</style>
@endsection
