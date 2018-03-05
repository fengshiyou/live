<?php

namespace App\Http\Controllers;

use App\model\liverAddrModel;
use App\model\ZbrankCollectStatusModel;
use App\model\zbrankPlatModel;
use App\model\zbrankRank;
use App\model\zbrankRankModel;
use Illuminate\Http\Request;
use DB;

class ImgAdsController extends Controller
{
    public function index()
    {
        return view('rank');
    }

    public function getImgAds()
    {
        $data = array(
            'a'=>'a'
        );
        resp_suc($data);
    }

}
