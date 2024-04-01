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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
class MatchController extends Controller
{
    public function createFormMatch(Request $request)
    {
        $clubList = Club::all();
        return view('match.formCreateMatch', ['clubList' => $clubList]);
    }
    public function postCreateMatch(Request $request)
    {
        //validate
        try {
            $matchTime = $request->match_time;
            $matchTimeArr = explode('-', $matchTime);
            $startMatch = $matchTimeArr[0];
            $endMatch = $matchTimeArr[1];

            $request['startMatch'] = $startMatch;
            $request['endMatch'] = $endMatch;

            $validator = Validator::make($request->all(), [
                'home_team' => ['required', function ($attribute, $value, $fail) use ($request) {
                    $otherFieldValue = $request->input('away_team');

                    if ($value === $otherFieldValue) {
                        $fail("The $attribute must be different from away_team.");
                    }
                },],
                'away_team' => ['required'],
                'startMatch' => 'required|date|after:now',
                'endMatch' => 'required|date|after:startMatch',
                'betting_odds' => 'required|numeric|min:0',
                'betting_odds_stop' => 'required|date|before:startMatch',
                'isPublic' => Rule::in(['1']),
            ]);
            if ($validator->fails()) {
                // Error
                $errors = $validator->errors();
                return back()->withErrors($errors);
            } else {
                // luu vao db
                $data = [
                    'home_id' => $request->home_team,
                    'away_id' => $request->away_team,
                    'match_start_time' => Carbon::parse($startMatch),
                    'match_end_time' => Carbon::parse($endMatch),
                    'stop_bet_time' => Carbon::parse($request->betting_odds_stop),
                    'betting_odds' => $request->betting_odds,
                    'result' => $request->score,
                    'is_public' => $request->isPublic
                ];
                MatchModel::create($data);
                return Redirect()->route('match.list');
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function getMatchList(Request $request)
    {
        $matchList = MatchModel::with(['home', 'away'])->get();
        return view('match.list', ['matchList' => $matchList]);
    }

    public function getMatchDetail(Request $request)
    {
        $matchId = $request->matchId;
        $match = MatchModel::find($matchId);
        $clubList = Club::all();
        if (!$match) {
            return redirect()->route('match.list');
        }
        return view('match.formCreateMatch', ['match' => $match, 'clubList' => $clubList]);
    }

    public function postEditMatch(Request $request)
    {
        $matchId = $request->matchId;
        try {
            DB::beginTransaction();
            $matchTime = $request->match_time;
            $matchTimeArr = explode('-', $matchTime);
            $startMatch = $matchTimeArr[0];
            $endMatch = $matchTimeArr[1];

            $request['startMatch'] = $startMatch;
            $request['endMatch'] = $endMatch;

            $validator = Validator::make($request->all(), [
                'home_team' => ['required', function ($attribute, $value, $fail) use ($request) {
                    $otherFieldValue = $request->input('away_team');

                    if ($value === $otherFieldValue) {
                        $fail("The $attribute must be different from away_team.");
                    }
                },],
                'away_team' => ['required'],
                'startMatch' => 'required|date|after:now',
                'endMatch' => 'required|date|after:startMatch',
                'betting_odds' => 'required|numeric|min:0',
                'betting_odds_stop' => 'required|date|before:startMatch',
                'isPublic' => Rule::in(['1']),
                'score' =>  ['required'],
            ]);
            if ($validator->fails()) {
                // Error
                $errors = $validator->errors();
                return back()->withErrors($errors);
            } else {
                // luu vao db
                $data = [
                    'home_id' => $request->home_team,
                    'away_id' => $request->away_team,
                    'match_start_time' => Carbon::parse($startMatch),
                    'match_end_time' => Carbon::parse($endMatch),
                    'stop_bet_time' => Carbon::parse($request->betting_odds_stop),
                    'betting_odds' => $request->betting_odds,
                    'result' => $request->score,
                    'is_public' => $request->isPublic,
                ];
            MatchModel::where('id', $matchId)->update($data);

            $match = MatchModel::find($matchId);
            if ($match->result) {
                $userBettingList = BettingHistory::with('user')->where('match_id', $matchId)->get();
                $scoreArr = explode('-', $match->result);
                $homeClubResult = 0;

                if ((int)$scoreArr[0] > (int)$scoreArr[1]) {
                    $homeClubResult = 1;
                } else if ($scoreArr[0] == $scoreArr[1]) {
                    $homeClubResult = 2;
                } else {
                    $homeClubResult = 3;
                }
                $bettingOdds = $match->betting_odds;
                foreach ($userBettingList as $userBetting) {
                    $userChoice = $match->choice;
                    $moneyBet = $match->money_bet;
                    if ($userChoice == $homeClubResult) {
                        $moneyWin = $moneyBet * $bettingOdds;
                        $currentCoin = $userBetting->user->total_coin;
                        $newCoin = $currentCoin + $moneyWin;
                        User::where('id', $userBetting->user_id)->update('total_coin'->$newCoin);
                    }
                }
            }

            DB::commit();
            return Redirect()->route('match.detail', ['matchId' => $matchId]);
        }
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
    public function getMatchDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'matchId' => ['required', 'exists:matches,id']
        ]);
        if ($validator->fails()) {
            // Validation failed
            $errors = $validator->errors();
            return back()->withErrors($errors);
            // Handle the errors
        } else {
            MatchModel::destroy($request->matchId);
            return Redirect()->route('match.list');
        }
    }

    public function getUserBettingList(Request $request)
    {
        $matchId = $request->matchId;
        $validator = Validator::make($request->all(), [
            'matchId' => ['required', 'exists:matches,id']
        ]);
        if ($validator->fails()) {
            // Validation failed
            $errors = $validator->errors();
            return back()->withErrors($errors);
            // Handle the errors
        } else {
            MatchModel::with(['bettingHistories', 'home', 'away', 'bettingHistories.user'])->where('id', $matchId)->first();
            return Redirect()->route('match.userBettingList');
        }
    }
}
