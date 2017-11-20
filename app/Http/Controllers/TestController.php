<?php

namespace App\Http\Controllers;

use App\model\ZbrankCollectStatusModel;
use App\model\zbrankRank;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function index(){
        $d= "1508688000";
        $rank_model = new zbrankRank();
        $test = $rank_model->rankByDate($d);
        dd($test->orderBy('id','desc')->limit(100)->get()->toArray());
    }
}
