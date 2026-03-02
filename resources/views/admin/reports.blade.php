@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Reports</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-bar-chart" style="font-size: 4rem; color: #6c757d;"></i>
        <h4 class="mt-3 text-muted">No Reports Available</h4>
        <p class="text-muted">There are currently no reports to display.</p>
    </div>
</div>
@endsection
