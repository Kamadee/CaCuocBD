<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Club;
use App\Models\MatchModel;
use App\Models\BettingHistory;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class CustomerController extends Controller
{
    public function getListMatch(Request $request)
    {
        $matchList = MatchModel::with(['home', 'away'])->orderBy('match_start_time','DESC')->get();
        return view('customer.listMatch', ['matchList' => $matchList]);
    }
    public function getHistoryBetting(Request $request)
    {
        if (Auth::check()) {
            $customerId = Auth::user()->id;
            $bettingHistories = BettingHistory::with(['match.home', 'match.away'])->where('user_id', $customerId)->get();

            foreach ($bettingHistories as $history) {
                $matchResult = $history->match->result;
                if ($matchResult) {
                    $moneyBet = $history->money_bet;
                    $bettingOdds = $history->match->betting_odds;
                    $choiceList = ['', 'thắng', 'hòa', 'thua'];
                    $choice = $history->choice;
                    $matchResultArr = explode('-', $matchResult);
                    if ($matchResultArr[0] > $matchResultArr[1]) {
                        $kq = 1;
                    } else if ($matchResultArr[0] === $matchResultArr[1]) {
                        $kq = 2;
                    } else {
                        $kq = 3;
                    }
                    if ($kq === $choice) {
                        $moneyReceive = $bettingOdds * $moneyBet;
                        $history->moneyReceive = $moneyReceive;
                    } else {
                        $history->moneyReceive = 0;
                    }
                    // $history->moneyReceive = $moneyReceive

                    // $history->update(['money_receive' => $moneyReceive]);
                } else {
                    // $history->put('moneyReceive', '-');
                    $history->moneyReceive = '-';
                }
            }

            return view('customer.myHistoryBetting', ['bettingHistories' => $bettingHistories, 'choiceList' => $choiceList]);
        }
    }
    public function getBetting(Request $request)
    {
        try {

            $matchId = $request->macthId;
            $money_bet = $request->money_bet;
            $choice = $request->choice;
            $customerId = Auth::user()->id;

            $validator = Validator::make($request->all(), [
                'matchId' => 'required',
                'money_bet' => ['required', 'numeric', 'min:0', function ($attribute, $value, $fail) use ($request) {
                    $customerId = $request->user()->id;
                    $customer = User::find($customerId);

                    if ($value > $customer->total_coin) {
                        $fail('The ' . $attribute . ' must not be greater than the total coins.');
                    }
                }],
                'choice' => 'required',
            ]);
            if ($validator->fails()) {
                // Error
                $errors = $validator->errors();
                return response()->json(['errors' => $errors]);
            } else {
                // luu vao db
                $data = [
                    'match_id' => $request->matchId,
                    'money_bet' => $request->money_bet,
                    'choice' => $request->choice,
                    'user_id' => $customerId,
                ];
                BettingHistory::create($data);
                $customer = User::find($customerId);
                $customer->total_coin -= $money_bet;
                $customer->save();
                return response()->json(['success' => true, 'message' => 'Cược thành công']);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getMyAccount(Request $request)
    {
        $customerId = Auth::user()->id;
        $customer = User::find($customerId);
        if ($customer) {
            $customerName = $customer->username;
            $customerEmail = $customer->email;
            $customerCoin = $customer->total_coin;
        }
        return view('customer.myAccount', ['customer' => $customer, 'customerName' => $customerName]);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('C');
        // dd($keyword);
        $matches = MatchModel::with(['home', 'away'])
            ->where(function ($query) use ($keyword) {
                $query->whereHas('home', function ($query) use ($keyword) {
                    $query->where('club_name', 'LIKE', '%' . $keyword . '%');
                })
                    ->orWhereHas('away', function ($query) use ($keyword) {
                        $query->where('club_name', 'LIKE', '%' . $keyword . '%');
                    });
            })
            ->get();
        return view('customer.search', ['matches' => $matches]);
    }
}
