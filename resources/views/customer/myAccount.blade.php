@extends('layout.masterUser')

@section('title', 'My Account')

@section('styles')
<style>
    .match-table {
        margin-top: 25px;
    }
</style>
@stop

@section('content1')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h1 class="card-header text-center">Profile</h1>

                <div class="card-body">
                    <div class="form-group row mb-3">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Name:</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" value="{{ $customer->username }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="email" class="col-md-4 col-form-label text-md-right">Email:</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" value="{{ $customer->email }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="total_coin" class="col-md-4 col-form-label text-md-right">Total Coin:</label>

                        <div class="col-md-6">
                            <input id="total_coin" type="text" class="form-control" value="{{ $customer->total_coin }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@stop