@extends('layout.master')

@section('title', 'User Betting List')

@section('styles')
<style>
    .match-table {
        margin-top: 25px;
    }
</style>
@stop

@section('content')
<div class="container">
    <div class="jumbotron">
        <h1 class="display-4">{{ $match->home->club_name . ' vs ' . $match->away->club_name }}</h1>
    </div>
    <div class="user-betting-list">
        <table class="table table-dark match-table">
            <thead>
                <tr>
                    <td>UserName</td>
                    <td>Choice</td>
                    <td>Money Bet</td>
                </tr>
            </thead>
            <tbody>
                @foreach($match->bettingHistories as $history)
                <tr>
                    <td>{{ $history->user->username }}</td>
                    <td>{{ $choiceList[$history->choice] }}</td>
                    <td>{{ $history->money_bet }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>
@stop