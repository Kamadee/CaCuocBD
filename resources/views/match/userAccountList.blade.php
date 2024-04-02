@extends('layout.master')

@section('title', 'User Account List')

@section('styles')
<style>
  .match-table {
    margin-top: 25px;
  }
</style>
@stop

@section('content')
<div class="user-account-list">
  <table class="table table-dark match-table">
    <thead>
      <tr>
        <td>User name</td>
        <td>Email</td>
        <td>Total coin</td>
      </tr>
    </thead>
    <tbody>
      @foreach($userList as $user)
      <tr>
        <td>{{ $user->username }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->total_coin }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@stop