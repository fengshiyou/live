<?php

namespace App\Http\Controllers;

use App\model\liverAddrModel;
use App\model\ZbrankCollectStatusModel;
use App\model\zbrankPlatModel;
use App\model\zbrankRank;
use App\model\zbrankRankModel;
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
        DB::connection()->enableQueryLog();
        if (!$date) {
            //获取今天是周几
            // $now_week = date('w');
            //往前挪几天
            //$date =  strtotime("-" . ($now_week - 1 + 7) . 'days');
            //取数据库中最大的日期
            $date = zbrankRankModel::max('rank_start_timestamp');
            $log1 = DB::getQueryLog();
        }
//        dd($date);

        $rank_list = zbrankRankModel::where('rank_start_timestamp','=',$date);
        if($plat){
            $rank_list = $rank_list->where('platform','=',$plat);
        }else{//优化sql  如果没传 关键字  就限定查找范围
            $rank_list = $rank_list->where("rank_all",">",1)->where("rank_all","<=",$per_page);
        }
        $rank_list = $rank_list->orderBy('rank_all', "asc")
            ->skip($per_page * $page_no)
            ->limit($per_page)
            ->get()
            ->toArray();


        $log2 = DB::getQueryLog();
        $return_data = array(
            'rank_list' => $rank_list,
            'active_date'=>$date,
            'active_liver' => $liver,
            'active_plat' => $plat
//            'sql1'=>$log1,
//            'sql2'=>$log2,
        );


//        var_dump($log);
        return resp_suc($return_data);
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
        //库中已存的最大日期
        $max_date = zbrankRankModel::max('rank_start_timestamp');
        //获取前七周日期
        for ($i = 1; $i <= $num; $i++) {
            //周一日期 20170610
            $mon = date("Y-m-d ", strtotime("-" . ($now_week - 1 + 7 * $i) . 'days'));
            //周一日期 6月1日
            $date_mon = date("m月d日", strtotime("-" . ($now_week - 1 + 7 * $i) . 'days'));
            //周日日期 6月6日
            $date_sta = date("m月d日", strtotime("-" . ($now_week - 1 + 7 * $i + 6) . 'days'));
            $return_data['id'] =strtotime($mon . "00:00:00") ;
            $return_data['date'] = $date_mon . "-" . $date_sta;
            if ($return_data['id'] == $max_date) {
                $return_data['active'] = true;
            } else {
                $return_data['active'] = false;
            }
            $return[] = $return_data;
        }
        return resp_suc($return);
    }
    /**
     * 获取平台列表
     */
    public function getPlatList(){
        $date = zbrankPlatModel::get()->toArray();
        return resp_suc($date);
    }
    /**
     * 获取直播地址
     */
    public function getLiveAddr(){
        $user_id = request('user_id');
        $plat_form = request('plat_form');
        $liver_addr = liverAddrModel::select('liverAddr')->where('userId','=',$user_id);
        if ($plat_form){
            $liver_addr = $liver_addr->where('platform','=',$plat_form);
        }
        $liver_addr = $liver_addr->first();
        return resp_suc($liver_addr);

    }
}
