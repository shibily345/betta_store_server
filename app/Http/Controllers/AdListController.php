<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdList;

class AdListController extends Controller
{
    public function index()
    {
        $adList = AdList::all();

        return view('ad_list.index', ['adList' => $adList]);
    }
   
    public function getAllRecords()
    {
        $allRecords = AdList::all();

        return response()->json($allRecords);
    }
}
 