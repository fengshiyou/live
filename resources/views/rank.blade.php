<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>领先的直播影响力大数据|主播排行榜|直播排行榜</title>
    <meta content="seo优化 关键字" name="keywords">
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script type="text/javascript" src="{{URL::asset('/js/rank/rank.js')}}"></script>

    <link rel="stylesheet" href="{{URL::asset('/css/rank/style.css')}}">
    <link rel="stylesheet" href="{{URL::asset('/css/rank/layer.css')}}">
    <link rel="stylesheet" href="{{URL::asset('/css/rank/layer.ext.css')}}">
    <link rel="stylesheet" href="{{URL::asset('/css/rank/ranking.css')}}">

</head>
<body>
<header>
    <div class="header center">
        <div class="showLogo fl logo1" id="indexLogo"></div>
        <div class="in_nav fr">
            <ul>
                <li>
                    <a href="" id="index">首页</a>
                </li>
                <li>
                    <a href="" id="ranking" class="changeColor">榜单</a>
                </li>
                <li>
                    <a href="" id="article">资讯</a>
                </li>
                <li>
                    <a href="" id="about">关于</a>
                </li>
            </ul>
            <div class="in_search">
                <div class="search_logo"></div>
            </div>
        </div>
        <div class="in_input" id="input_search">
            <img src="" id="goSearch">
            <input type="text" placeholder="搜索主播或相关资讯" id="searchVal" onkeydown="keyDown(event)">
            <img src="" id="goClose">
        </div>
    </div>
</header>
<div id="rank">
    <div class="rank-head center">
        <div class="rank-head-in center">
            {{--  <a href="" id="affect" class="rank-active">总榜</a>
              <a href="" id="shows">秀场</a>
              <a href="" id="game">游戏</a>
              <a href="" id="entertainments">泛娱乐</a>
              <a href="" id="sports">体育</a>
              <a href="" id="ecommerce">电商</a>--}}
            <a class="fr" style="color: #b4b4b4" href="http://www.zbrank.com/ranking/explain" target="_blank">数据说明</a>
        </div>
        <div class="rank-plat center">
            <div style="float: left;width: 50px;color: #888;">平台</div>
            <div style="float: left;width: 990px;">
                <ul id="plats">
                    <li @click="platSelect(key)" :class="{plat_active:value.active}" v-for="(value,key) in plat_list"
                        :id="value.plat_id">@{{ value.plat_name }}
                    </li>
                </ul>
            </div>
            <div class="cl"></div>
        </div>
    </div>
    <div id="near7Date">

        <div class="date_line cl center" style="display: block;">
            <div class="show_date center" style="overflow: auto">
                <div @click="dateSelect(key)" style="cursor:pointer" :class="{date_sty:true,plat_active:value.active}"
                     v-for="(value,key) in near_7date" :id="value.id">@{{ value.date }}
                </div>
            </div>
        </div>
    </div>
    <div id="loading_shade" v-show="loading_shade">
        <div class="layui-layer-shade" id="layui-layer-shade7" times="7"
             style="z-index:19891020; background-color:#eee; opacity:0.5; filter:alpha(opacity=50);">

        </div>
        <div class="layui-layer layui-anim layui-layer-loading " id="layui-layer8" type="loading" times="8" showtime="0"
             contype="string" style="z-index: 19891022; top: 50%; left: 50%;">
            <div class="layui-layer-content layui-layer-loading3">

            </div>
        </div>
    </div>
    <div id="table-head" class="center" style="">
        <table>
            <thead>
            <tr>
                <th width="10%">排名</th>
                <th width="27.5%">主播</th>
                <th width="6.5%">平台</th>
                <th width="8%">财富值</th>
                <th width="8.5%">粉丝数</th>
                <th width="11.5%">最高在线人数</th>
                <th width="8.5%">增长率</th>
                <th width="8.5%">1w+次数</th>
                <th width="11%">播榜指数</th>
            </tr>
            </thead>
        </table>
    </div>
    <div class="rank-main center">
        <table>
            <thead>
            <tr>
                <th width="10%">排名</th>
                <th width="27.5%">主播</th>
                <th width="6.5%">平台</th>
                <th width="8%">财富值</th>
                <th width="8.5%">粉丝数</th>
                <th width="11.5%">最高在线人数</th>
                <th width="8.5%">增长率</th>
                <th width="8.5%">1w+次数</th>
                <th width="11%">播榜指数</th>
            </tr>
            </thead>
            <tbody id="dev_tbody">
            <tr v-for="(value,index) in myData">
                <td>
                    <span>NO.@{{index+1}}</span>
                </td>
                <td class="table-user">
                    {{--<img src="">--}}
                    <img :src="value.avatar" width="28px" height="28px">
                    <span :title="value.username">@{{value.username}}</span>
                    <a :href="value.liverAddr"  target="_blank" title="进入直播间" id="playBtn" class="square" style="margin: 10px">
                        <div class="square_inner_play">
                        </div>
                    </a>

                </td>
                <td>@{{value.platname}}</td>
                <td>@{{Math.ceil(value.rmb_week/10000)}}万</td>
                <td>@{{Math.ceil(value.fans_max/10000)}}万</td>
                <td>@{{Math.ceil(value.onlineCount_max/10000)}}万</td>
                <td>@{{Math.round(value.fans_growthRate*10000)/100}}%</td>
                <td>@{{value.gt10000onlineCount}}</td>
                <td>@{{Math.ceil(value.exponent1)}}</td>
            </tr>
            </tbody>
        </table>
        <div id="table-btn"></div>
    </div>
    <div class="rank-btn center">
        <button :class="{btn_active:value.active}" @click="pageSelect(key)" v-for="(value,key) in page_size"
                :id="value.id">
            显示@{{ value.name }}条
        </button>
        <div class="backToTop" id="toTop">
            <a href="javascript:scroll(0,0)"><img src=""></a>
        </div>
    </div>
    <div id="tb-btn"></div>
</div>

<!--底部-->
<footer>
    <div class="footer center">
        <div class="about_us">
            <p>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址：破破的地址</p>
            <p>电&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;话：010-110110110</p>
            <p>联系我们：501453944@qq.com</p>
        </div>
        <div class="off_wx">
            <img src="">
            <div>官方微信</div>
        </div>
        <div class="off_wb">
            <img src="">
            <div>官方微博</div>
        </div>
        <div class="ft_info" id="ttt" value="zzz">
            破破<span>©3016-3017</span> 京ICP备501453944号-1
        </div>
    </div>
</footer>
</body>
</html>
