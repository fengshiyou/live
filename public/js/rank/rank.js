$(function () {
    var rank_vue = new Vue({
        el: "#rank",
        data: {
            myData: '',
            active_date: '',
            active_plat: '',
            active_liver: '',
            active_page: 20,
            loading_shade:false,
            near_7date: [
                {id: '20171106', date: '11月6日-11月12日', active: true},
                {id: '20171030', date: '10月30日-11月5日', active: true},
                {id: '20171023', date: '10月23日-10月29日', active: false},
                {id: '20171016', date: '10月16日-10月22日', active: false},
                {id: '20171009', date: '10月9日-10月15日', active: false},
                {id: '20171002', date: '10月2日-10月8日', active: false},
            ],
            plat_list: '',
            page_size: [
                {id: 'dev_50_counts', name: 50, value: 50, active: false},
                {id: 'dev_100_counts', name: 100, value: 100, active: false},
                {id: 'dev_all_counts', name: "全部", value: 500, active: false},
            ]
        },
        methods: {
            dateSelect: function (key) {
                if (this.near_7date[key]['active'] == true) return;
                for (var i = 0; i < this.near_7date.length; i++) {
                    this.near_7date[i]['active'] = false;
                }
                this.near_7date[key]['active'] = true;
                this.active_date = this.near_7date[key]['id'];
                getRankList(this.active_date, this.active_plat ,this.active_page);
            },
            platSelect: function (key) {
                if (this.plat_list[key]['active'] == true) return;

                for (var i = 0; i < this.plat_list.length; i++) {
                    this.plat_list[i]['active'] = false;
                }
                this.plat_list[key]['active'] = true;
                this.active_plat = this.plat_list[key]['plat_id'];
                getRankList(this.active_date, this.active_plat ,this.active_page);
            },
            pageSelect: function (key) {
                if (this.page_size[key]['active'] == true) return;
                for (var i = 0; i < this.page_size.length; i++) {
                    this.page_size[i]['active'] = false;
                }
                this.page_size[key]['active'] = true;
                this.active_page = this.page_size[key]['value'];
                getRankList(this.active_date, this.active_plat ,this.active_page);
            },
            liverAddr:function (user_id) {
                $.ajax({
                    url: '/getLiveAddr',
                    type: "GET",
                    data: {user_id: user_id},
                    dataType: 'json',
                    success: function (data) {
                        if(data.code == 200){
                            window.open(data.data.liverAddr)
                        }else{
                            alert("暂无数据");
                        }
                    }
                })
            }
        }
    });
    //获取排行榜
    getRankList();
    //获取近7天日期
    getNear7Date();
    //获取平台列表
    getPlatList();

    function getRankList(date, plat ,per_page) {
        rank_vue.loading_shade = true;
        $.ajax({
            url: '/getRankList',
            type: "GET",
            data: {date: date, plat: plat,per_page:per_page},
            dataType: 'json',
            success: function (data) {
                rank_vue.myData = data.data.rank_list;
                rank_vue.loading_shade = false;
            }
        })
    };

    function getNear7Date() {
        $.ajax({
            url: "/getNear7Date",
            type: "GET",
            data: {num: 6},
            dataType: 'json',
            success: function (data) {
                rank_vue.near_7date = data.data;
            }
        })
    };

    function getPlatList() {
        $.ajax({
            url: "/getPlatList",
            type: "GET",
            dataType: 'json',
            success: function (data) {
                var tmp = {plat_id: '',plat_name: "全部"};
                var data = data.data
                data.unshift(tmp);
                for (var i = 0; i < data.length; i++) {
                    if (i == 0) {
                        data[i]['active'] = true;
                    } else {
                        data[i]['active'] = false;
                    }
                }
                rank_vue.plat_list = data;
            }
        })
    }
    window.onscroll = function () {
        if($(document).scrollTop()>$(".rank-main").offset().top && $(document).scrollTop()<=$("#table-btn").offset().top){
            $('#toTop').show();
            $('#table-head').show();
            if($(document).scrollTop()>=$("#table-btn").offset().top-360){
                $('#toTop').removeClass('backToTop').addClass('backToTop-bt');
            }
            if($(document).scrollTop()<$("#table-btn").offset().top-360){
                $('#toTop').removeClass('backToTop-bt').addClass('backToTop');
            }
        }else{
            $('#toTop').hide();
            $('#table-head').hide();
        }

    }
})
