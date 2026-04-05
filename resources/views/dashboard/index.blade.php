@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard</h1>
    <p>Welcome, {{ auth()->user()->name }}</p>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Wallet Balance</h5>
                    <p class="card-text">KES {{ auth()->user()->wallet->balance }}</p>
                </div>
            </div>
        </div>
        <!-- More widgets -->
    </div>
</div>
@endsection
