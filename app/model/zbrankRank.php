<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class zbrankRank extends Model
{
    protected $table = "zbrank_rank_";
    protected static $date = '';

    /**
     * zbrankRank constructor.
     * @param array $date 20170918,20170912,20170916
     */
//    public function __construct($date = '')
//    {
//        if ($date) {
//            self::$date = $date;
//            $this->table = $this->table . self::$date;
//        }
//        parent::__construct();
//    }

    /**
     *
     */
    public function rankQuery($date , $plat = '', $liver = '')
    {
        $date_array = explode(',', self::$date);
        $base_rank_model = new zbrankRank($date_array[0]);
        if ($plat) {
            $base_rank_model = $base_rank_model->where('platform', '=', $plat);
        }
        if ($liver) {
            $base_rank_model = $base_rank_model->where('userId', '=', $liver);
        }
        foreach ($date_array as $k => $date) {
            if($k > 0){
                $rank_model = new zbrankRank($date);
                if ($plat) {
                    $rank_model = $rank_model->where('platform', '=', $plat);
                }
                if ($liver) {
                    $rank_model = $rank_model->where('userId', '=', $liver);
                }
                $base_rank_model= $base_rank_model->unionAll($rank_model);
            }
        }
        return $base_rank_model;
    }

    public function scopeRankQuery($query, $date , $plat = '', $liver = '')
    {
        $date_array = explode(',', $date);
        $query = $query->where('rank_start_timestamp','=',$date_array[0]);
        if ($plat) {
            $query = $query->where('platform', '=', $plat);
        }
        if ($liver) {
            $query = $query->where('userId', '=', $liver);
        }
        return $query;
    }
}
