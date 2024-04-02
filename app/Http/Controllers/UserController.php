<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchModel;

class UserController extends Controller
{
    public function getListMatch(Request $request) {
        $matchList = MatchModel::with(['home', 'away'])->get();
        return view('dashboard.listMatch', ['matchList' => $matchList]);
    }
}
