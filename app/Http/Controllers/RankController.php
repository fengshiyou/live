<?php

namespace App\Http\Controllers;

use App\model\ZbrankCollectStatusModel;
use App\model\zbrankRank;
use Illuminate\Http\Request;
use DB;

class RankController extends Controller
{
    public function index()
    {
        return view('rank');
    }

    public function getRankList()
    {
        //@todo 封装统一返回
        //@todo 封装同意带页码的返回，在里面配置默认页数等东西
        //一页多少条数据
        $per_page = request('per_page') ? request('per_page') : 20;
        //第几页
        $page_no = request('page_no') ? request('page_no') : 0;
        //要查询的周日期，起始日期  即周一
        $date = request('date');
        //要查询的主播ID
        $liver = request('liver');
        //要查询的平台
        $plat = request('plat');
        if (!$date) {
            //获取今天是周几
            $now_week = date('w');
            //往前挪几天
            $date = date("Ymd", strtotime("-" . ($now_week - 1 + 7) . 'days'));
        }
        DB::connection()->enableQueryLog();
        $rank_model = new zbrankRank($date);
        $rank_list = $rank_model
            ->rankQuery($plat, $liver)
            ->where("rank_all","<=",$per_page)
            ->orderBy('rank_all', "asc")
            ->skip($per_page * $page_no)
            ->limit($per_page)
            ->get()
            ->toArray();
        $return_data = array(
            'rank_list' => $rank_list,
//            'active_date'=>$date,
            'active_liver' => $liver,
            'active_plat' => $plat
        );

        $log = DB::getQueryLog();
        dd($log);
        return response()->json($return_data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取近7周的date
     */
    public function getNear7Date()
    {
        $num = request('num') ? request('num') : 6;
        $return_data = array();
        //今天周几
        $now_week = date("w");

        //往前挪几天
        $date = date("Ymd", strtotime("-" . ($now_week - 1 + 7) . 'days'));
        //获取前七周日期
        for ($i = 1; $i <= $num; $i++) {
            //周一日期 20170610
            $mon = date("Ymd", strtotime("-" . ($now_week - 1 + 7 * $i) . 'days'));
            //周一日期 6月1日
            $date_mon = date("m月d日", strtotime("-" . ($now_week - 1 + 7 * $i) . 'days'));
            //周日日期 6月6日
            $date_sta = date("m月d日", strtotime("-" . ($now_week - 1 + 7 * $i + 6) . 'days'));
            $return_data['id'] = $mon;
            $return_data['date'] = $date_mon . "-" . $date_sta;
            if ($i == 1) {
                $return_data['active'] = true;
            } else {
                $return_data['active'] = false;
            }
            $return[] = $return_data;
        }
        return response()->json($return, 200, [], JSON_UNESCAPED_UNICODE);
    }
    /**
     * 获取平台列表
     */
    public function getPlatList(){
        $date = array();
        $date = ZbrankCollectStatusModel::select('zbrank_plat_id','zbrank_plat_name')->distinct('zbrank_plat_id')->get()->toArray();
        return response()->json($date, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
