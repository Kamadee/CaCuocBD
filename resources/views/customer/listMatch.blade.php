@extends('layout.masterUser')

@section('title', 'List match for customer')

@section('styles')
<style>
  .match-table {
    margin-top: 25px;
  }

  .error-message {
    color: red;
    font-size: 12px;
  }
</style>
@stop
@section('content1')
<div class="container">
  <form action="" method="get" class="mb-3">
    <div class="row">
      <div class="col-3">
        <select class="form-control">
          <option value="0">all</option>
        </select>
      </div>
      <div class="col-4">
        <input type="search" class="form-control" placeholder="Từ khóa tìm kiếm...">
      </div>
      <div class="col-2">
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
      </div>
    </div>
  </form>
  <table class="table">

    <thead class="thead-dark">
      <tr>
        <td>Match name</td>
        <td>Match time</td>
        <td>Stop bet time</td>
        <td>Betting Odds</td>
        <td>Score</td>
        <td>Bet</td>
      </tr>
    </thead>
    <tbody>
      @foreach($matchList as $match)
      <tr data-bet-time="{{ $match->stop_bet_time }}">
        <td>{{ $match->home->club_name . '-' . $match->away->club_name }}</td>
        <td>{{ $match->match_start_time. '~' . $match->match_end_time }}</td>
        <td>{{ $match->stop_bet_time }}</td>
        <td>{{ 'x' . $match->betting_odds }}</td>
        <td>{{ $match->result == '' ? '-' : $match->result }}</td>
        <td>
          <button type="button" data-matchid="{{$match->id}}" data-matchname="{{$match->home->club_name . '-' . $match->away->club_name}}" class="btn btn-warning btn-bet" data-bs-toggle="modal" data-bs-target="#exampleModal">Bet</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <form id="form-bet" action="{{ route('customer.betting', ['matchId' => $match->id]) }}" method="post">
    @csrf
    <input name="matchId" class="matchId d-none" value="" />
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <p class="matchName" id="exampleModalLabel"></p>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="money_bet" class="form-control" aria-label="Amount (to the nearest dollar)" placeholder="Số tiền cược">
          <div id="moneyBetError" class="error-message"></div>
          <div class="form-row align-items-center">

            <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Choice</label>
            <select class="custom-select my-1 mr-sm-2" name="choice" id="inlineFormCustomSelectPref">
              <option selected>Choose...</option>
              <option value="1">Thắng</option>
              <option value="2">Hòa</option>
              <option value="3">Thua</option>
            </select>
            <div id="choiceBetError" class="error-message"></div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="confirmButton" data-bs-toggle="modal" data-bs-target="#successModal">Confirm</button>
          </div>
        </div>
      </div>
  </form>
</div>

<div id="successModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successMessage">Cược thành công</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Cảm ơn bạn đã đặt cược!!!!</p>
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
  $(function() {
    $('.btn-bet').on('click', function() {
      const matchId = $(this).data('matchid');
      const matchName = $(this).data('matchname');
      $("#form-bet").find('input.matchId').val(matchId)
      $("#form-bet").find('p.matchName').text(matchName)
    })
  })

  $(document).ready(function() {
    // Xử lý sự kiện khi nhấp vào nút "confirm"
    $('#confirmButton').click(function(event) {
      // Gửi yêu cầu AJAX
      $.ajax({
        url: 'customer/betting',
        method: 'POST',
        data: {
          // Dữ liệu đặt cược
        },
        success: function(response) {
          if (response.success) {
            // Hiển thị modal "thành công"
            $('#successModal').modal('show');
            $('#successMessage').text(response.message);
          } else {
            // Hiển thị lỗi validate bên dưới các ô input
            if (response.errors) {
              $.each(response.errors, function(key, value) {
                $('#' + key + 'Error').text(value);
              });
            }
          }
        },
        error: function(xhr, status, error) {
          // Xử lý lỗi nếu có
        }
      });
    });
  });

  document.addEventListener("DOMContentLoaded", function() {
    // Truy cập danh sách các trận đấu
    var matches = document.getElementsByClassName("table")[0].getElementsByTagName("tr");

    // Lặp qua từng trận đấu và kiểm tra thời gian cược
    for (var i = 0; i < matches.length; i++) {
      var match = matches[i];
      var betButton = match.querySelector(".btn-bet");
      var betTime = match.dataset.betTime; // Thời gian dừng nhận cược (định dạng: "YYYY-MM-DD HH:MM:SS")

      // Chuyển đổi thời gian dừng nhận cược sang đối tượng Date
      var betDateTime = new Date(betTime);

      // Kiểm tra nếu thời gian dừng nhận cược nhỏ hơn thời gian hiện tại
      if (betDateTime < new Date()) {
        // Disable nút "Bet"
        betButton.disabled = true;
      }
    }
  });
</script>
@stop