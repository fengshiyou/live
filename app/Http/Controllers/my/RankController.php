<?php

namespace App\Http\Controllers\my;

use App\model\MyCollectDayRankModel;
use App\model\MyPlatModel;
use App\my_plat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RankController extends Controller
{
    public function getRankList()
    {
        //一页多少条数据
        $per_page = request('per_page') ? request('per_page') : 20;
        //第几页
        $page_no = request('page_no') ? request('page_no') : 0;
        //要查询的周日期
        $date = request('date');
        //要查询的主播ID
        $liver = request('liver');
        //要查询的平台
        $plat = request('plat');
        DB::connection()->enableQueryLog();
        if (!$date) {
            //取数据库中最大的日期
            $date = MyCollectDayRankModel::max('created_at_timestamp');
//            $log1 = DB::getQueryLog();
        }
//        dd($date);

        $rank_list = MyCollectDayRankModel::where('created_at_timestamp', '=', $date);
        if ($plat) {
            $rank_list = $rank_list->where('plat', '=', $plat);
        }

        $rank_list = $rank_list->orderBy('plat_currency', "desc")
            ->skip($per_page * $page_no)
            ->limit($per_page)
            ->get()
            ->toArray();


        $log2 = DB::getQueryLog();
        $return_data = array(
            'rank_list' => $rank_list,
            'active_date' => $date,
            'active_liver' => $liver,
//            'sql1' => $log1,
            'sql2' => $log2,
            'active_plat' => $plat

        );


//        var_dump($log);
        return resp_suc($return_data);
    }
    /**
     * 获取平台列表
     */
    public function getPlatList()
    {
        $date = MyPlatModel::get()->toArray();
        return resp_suc($date);
    }

    /**
     * 获取近x天
     */
    public function getNearDay()
    {
        $num = request('num') ? request('num') : 6;
        $return_data = array();

        //库中已存的最大日期
        $max_date = date("Y-m-d", strtotime(MyCollectDayRankModel::max('created_at')));

        for ($i = 1; $i <= $num; $i++) {

            $day = date("Y-m-d", strtotime("-" . $i . 'days'));
            $return_data['id'] = strtotime($day . "00:00:00");
            $return_data['date'] = date("m月d日", strtotime($day));
            if ($day == $max_date) {
                $return_data['active'] = true;
            } else {
                $return_data['active'] = false;
            }
            $return[] = $return_data;
        }
        return resp_suc($return);
    }
    /**
     * 获取主播详情
     */
    public function getLiverDetail(){
        $liver_id = request("liver_id");
        $plat = request('plat');
        $info = MyCollectDayRankModel::where('liver_id','=',$liver_id)
            ->where('plat','=',$plat)
            ->limit(7)
            ->orderBy('created_at','desc')
            ->get()->toArray();
//        dd($info->toArray());die;
        array_multisort(array_column($info,'rank_start_timestamp'),SORT_DESC,$info);
        return resp_suc($info);
    }
}
