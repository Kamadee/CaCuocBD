@extends('layout.master')

@section('title', 'form create match')

@section('styles')
<style>
  .form {
    margin: 20px 0 0 0;
  }

  .form-group {
    margin: 10px 0;
  }
</style>
@stop

@section('content')
<div class="container">
  @if ($errors  ->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif
  <form
    @if(isset($match)) action="{{ route('match.postEditMatch', ['matchId' => $match->id]) }}"
    @else action="{{ route('match.postCreateMatch') }}"
    @endif
    method="post" class="form">
    @csrf
    <div class="form-group row">
      <label for="home-team" class="col-sm-2 col-form-label">Home team</label>
      <select name="home_team" class="col-sm-10" aria-label="Default select example">
        <option selected>Select home team</option>
        @foreach($clubList as $club)
        <option
          @if(isset($match) && $match->home_id == $club->id)
          selected
          @endif
          value="{{ $club->id }}">{{ $club->club_name }}
        </option>
        @endforeach
      </select>
    </div>

    <div class="form-group row">
      <label for="away-team" class="col-sm-2 col-form-label">Away team</label>
      <select name="away_team" class="col-sm-10" aria-label="Default select example">
        <option selected>Select home team</option>
        @foreach($clubList as $club)
        <option
          <?php if (isset($match) && $match->away_id == $club->id) { ?> selected <?php } ?>
          value="{{ $club->id }}">{{ $club->club_name }}
        </option>
        @endforeach
      </select>
    </div>

    <div class="form-group row">
      <label for="away-team" class="col-sm-2 col-form-label">Match Time</label>
      <input type="text" class="col-sm-10" name="match_time" />
    </div>

    <div class="form-group row">
      <label for="away-team" class="col-sm-2 col-form-label">Betting Odds</label>
      <input
        <?php
          if (isset($match)) {
        ?>
          value="<?php echo $match->betting_odds ?>"
        <?php
          }
        ?>
        type="number" class="col-sm-10" name="betting_odds" placeholder="x3, x5, x9"
      />
    </div>
    
    <div class="form-group row">
      <label for="away-team" class="col-sm-2 col-form-label">Betting Stop Time</label>
      <input type="text" class="col-sm-10" name="betting_odds_stop" placeholder="Time stop betting" />
    </div>

    <div class="form-group row">
      <label for="away-team" class="col-sm-2 col-form-label">Score</label>
      <input
        <?php
            if (isset($match)) {
          ?>
            value="<?php echo $match->result ?>"
          <?php
            }
          ?>
        type="text" class="col-sm-10" name="score" placeholder="2-2"
        />
    </div>

    @if (isset($match))
    <div class="form-group row">
      <label for="" class="col-sm-2 col-form-label">Is Public</label>
      <div class="form-check form-switch col-sm-10">
        <input
          name="isPublic"
          @if($match->is_public) checked @endif
          class="form-check-input"
          type="checkbox" 
          role="switch" id="flexSwitchCheckChecked"
          value="1">
        <label class="form-check-label" for="flexSwitchCheckChecked">On: public; Off: private</label>
      </div>
    </div>
    @endif


    <div class="text-center">
      <button type="submit" class="btn btn-primary">submit</button>
      <a href="{{route('match.list', ['clubList' => $clubList])}}"><button type="submit" class="btn btn-primary">Back</button></a>

    </div>
  </form>
</div>
@stop

@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
  $(function() {
    let match;
    @if(isset($match))
     match = {!! json_encode($match) !!}; 
    @else
      match = null;
    @endif
  
    let startMatch;
    let endMatch;
    let bettingStopTime;
    if (match) {
      startMatch = match.match_start_time;
      endMatch = match.match_end_time;
      bettingStopTime = match.stop_bet_time;
    } else {
      startMatch = moment().startOf('hour');
      endMatch = moment().startOf('hour').add(2, 'hour');
      bettingStopTime = moment().startOf('hour');
    }

    $('input[name="match_time"]').daterangepicker({
      opens: 'left',
      timePicker: true,
      startDate: startMatch,
      endDate: endMatch,
      locale: {
        format: 'YYYY/MM/DD hh:mm A'
      }
    }, function(start, end, label) {
      console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });


    $('input[name="betting_odds_stop"]').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      timePicker: true,
      startDate: bettingStopTime,
      minYear: 2023,
      maxYear: 2025,
      locale: {
        format: 'YYYY/MM/DD hh:mm A'
      }
    });
  });
</script>
@stop