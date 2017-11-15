<?php

namespace App\Http\Controllers;

use App\model\ZbrankCollectStatusModel;
use App\model\zbrankRank;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function index(){
        $d= "20170918";
        $rank_model = new zbrankRank($d);
        $test = $rank_model->rankQuery('meme');
        dd($test->orderBy('id','desc')->limit(100)->get()->toArray());
    }
}
