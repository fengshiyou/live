<?php

namespace App\Http\Controllers;

use App\model\ZbrankCollectStatusModel;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function index(){
        $test = ZbrankCollectStatusModel::first()->toArray();
        var_dump($test);
    }
}
