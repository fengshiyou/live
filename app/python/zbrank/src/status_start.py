# -*- coding: UTF-8 -*-

# 自定义py导入开始
# from getList import getList
import zbrank
# 导入拓展包开始
import json
import time


# 导入拓展包结束


# 自定义py导入结束

now = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))
class status_start:
    def __init__(self):
        # 主url
        self.main_url = "http://www.zbrank.com/ranking/affect/"
        # ajax url
        self.ajax_url = "http://www.zbrank.com/ranking/getRatingList"

    def saveCollectStatus(self):

        # ajax 请求的url
        ajax_url = "http://www.zbrank.com/ranking/getRatingList"
        # getList 类
        getList = zbrank.getList(self.main_url)
        # getData 类
        # getDataList = collect_python.zbrank.getDataList(ajax_url)
        # 平台列表
        plat_list = getList.getPlatList()
        # 日期列表
        date_list = getList.getDateList()

        # 当前月
        now_month = time.strftime('%m', time.localtime(time.time()))
        # 当前年
        now_year = time.strftime('%Y', time.localtime(time.time()))

        for date_key in date_list:
            # 获取起始周日期 date_val:10.02-10.08

            # [u'10.02', u'10.08']
            tmp_star_end = date_list[date_key].split("-")

            # 起始月
            tmp_star_month = tmp_star_end[0].split(".")[0]
            # 起始日
            tmp_star_day = tmp_star_end[0].split(".")[1]

            # 如果当前月小于日期月   证明跨年了
            if(tmp_star_month > now_month):
                tmp_star_year = now_year - 1
            else:
                tmp_star_year = now_year

            date = tmp_star_year + "-" + tmp_star_month + "-" + tmp_star_day +" 00:00:00"

            for plat_key in plat_list:
                insert_zbrank_collect_status_data = list()

                # 第三方直播榜的时间标签   post表单数据
                insert_zbrank_collect_status_data.append(date_key)
                # 第三方直播榜的平台标签    post表单数据
                insert_zbrank_collect_status_data.append(plat_key)
                # 10.02~10.8   第三方直播平台的一个显示值
                insert_zbrank_collect_status_data.append(date_list[date_key])
                # 平台名称
                insert_zbrank_collect_status_data.append(plat_list[plat_key])
                # 采集状态 0未采集 1采集
                insert_zbrank_collect_status_data.append(0)
                # 排行周起始日期  (周一时间，数据记录的是一周的)
                insert_zbrank_collect_status_data.append(date)

                # 转换成时间数组
                # time.mktime 转换成时间戳
                date_strptime =  time.strptime(date, "%Y-%m-%d %H:%M:%S")
                # 排行周起始日期  (周一时间，数据记录的是一周的)  时间戳
                insert_zbrank_collect_status_data.append(time.mktime(date_strptime))
                # 更新时间
                insert_zbrank_collect_status_data.append(now)
                # 创建时间
                insert_zbrank_collect_status_data.append(now)

                zbrank.zbrankSave().insertZbrankCollectStatus(insert_zbrank_collect_status_data)


status_start().saveCollectStatus()
