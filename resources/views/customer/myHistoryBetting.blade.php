@extends('layout.masterUser')

@section('title', 'My history betting')

@section('styles')
<style>
  .match-table {
    margin-top: 25px;
  }
</style>
@stop

@section('content1')
<div class="container">
  <table class="table ">

    <thead class="thead-dark">
      <tr>
        <td>Match name</td>
        <td>Match time</td>
        <td>Score</td>
        <td>Betting time</td>
        <td>Betting Odds</td>
        <td>Choice</td>
        <td>Money bet</td>
        <td>Money Receive</td>
      </tr>
    </thead>
    <tbody>
      @foreach($bettingHistories as $history)
      <tr>
        <td>
          {{ $history->match->home->club_name . '-' . $history->match->away->club_name }}
        </td>
        <td>{{ $history->match->match_start_time . '~' . $history->match->match_end_time }}</td>
        <td>{{ $history->match->result == '' ? '-' : $history->result }}</td>
        <td>{{ $history->match->created_at }}</td>
        <td>{{ 'x' . $history->match->betting_odds }}</td>
        <td>{{ $choiceList[$history->choice] }}</td>
        <td>{{ $history->money_bet }}</td>
        <td>{{ $history->moneyReceive }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @stop

  @section('scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script>
    $(function() {
      $('.btn-delete').on('click', function() {
        const matchId = $(this).data('matchid');
        const matchName = $(this).data('matchname');
        $("#form-delete").find('input.matchId').val(matchId)
        $("#form-delete").find('p.matchName').text(matchName)
      })
    })
  </script>
  @stop