@extends('layout.master')

@section('title', 'match list')

@section('styles')
<style>
  .match-table {
    margin-top: 25px;
  }
</style>
@stop

@section('content')
<div class="container">
  <table class="table table-dark match-table">

    <thead>
      <tr>
        <td>Match name</td>
        <td>Match time</td>
        <td>Betting Odds</td>
        <td>Score</td>
        <td>Bet</td>
      </tr>
    </thead>
    <tbody>
      @foreach($matchList as $match)
      <tr>
        <td>{{ $match->home->club_name . '-' . $match->away->club_name }}</td>
        <td>{{ $match->match_start_time . '~' . $match->match_end_time }}</td>
        <td>{{ 'x' . $match->betting_odds }}</td>
        <td>{{ $match->result == '' ? '-' : $match->result }}</td>
        <td>
          <a href=""><button type="submit">Bet</button></a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>



  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form id="form-delete" action="{{ route('match.delete') }}" method="post">
      @csrf
      <input name="matchId" class="matchId d-none" value="" />
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Are u sure to delete?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="matchName"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Confirm</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

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