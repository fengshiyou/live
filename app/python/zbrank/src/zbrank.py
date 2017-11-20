# -*- coding: UTF-8 -*-

# 导入拓展包开始
import json
import os
import time
# 导入拓展包结束


# 自定义py导入开始

# 其他包的路径增加
# 获取当前执行该文件的脚本的相对路径
dir = os.path.dirname(__file__)
os.sys.path.append(os.path.join(dir, ".."))

# print sys.path

from package.tool import function
from package.db.mysql import database


# 自定义py导入结束
now = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))
class getList:
    def __init__(self, url):
        self.url = url
        self.soup = function.get_doc(url)

    def getRankTypeList(self):
        rank_type_list = list()
        type_doc = self.soup.find(class_="rank-head-in")
        for type in type_doc:
            if (type != '\n'):
                rank_type_list.append(type['id'])
        # print rank_type_list
        return rank_type_list

    def getPlatList(self):
        plat_list = dict()
        doc = self.soup.find(id="plats")
        for plat in doc:
            if (plat != '\n'):
                if (plat['value'] != ''):
                    plat_list[plat['value']] = plat.text
        # print plat_list
        return plat_list

    def getDateList(self):
        return_date_list = dict()
        type_doc = self.soup.find(class_="show_date center")
        for date_info in type_doc:
            if (date_info != '\n'):
                return_date_list[date_info['id']] = date_info.text

        return return_date_list


class getDataList:
    def __init__(self ):
        # ajax url
        self.ajax_url = "http://www.zbrank.com/ranking/getRatingList"
        self.main_url = "http://www.zbrank.com";

    def getTotalRank(self, data_key, plat_key):
        post_data = {
            'skip': 500,
            'time': data_key,
            'isAppend': 'false',
            'platform': plat_key,
        }
        try:
            self.data = function.get_doc(self.ajax_url, 20, post_data, False)
            data_list = json.loads(self.data)["data"]
        except:
            # @todo 记录采集错误信息  错误内容，获取xx平台主播信息时出错
            print post_data
            data_list = ''
        return data_list

    def getTrueLiveAddr(self,plat,liver_id):
        try:
            url = self.main_url + "/anchor/detail/" + plat + "/" + str(liver_id)
            self.data = function.get_doc(url)
            doc = self.data.find(class_="anchor-head center")
            return doc.a['href']
        except:
            return ''

class zbrankSave:
    # 采集的状态表插入新的采集标记
    def insertZbrankCollectStatus(self, insert_data):
        mysql = database()

        select_sql = "select * from zbrank_collect_status WHERE zbrank_date_key = '" + insert_data[0] + "' AND zbrank_plat_id = '" + insert_data[1] + "' LIMIT 1"
        select_result = mysql.query(select_sql, 'count')

        if (select_result == 0):
            insert_data.insert(0, 0)
            inset_sql = "insert into zbrank_collect_status VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
            mysql.insert_by_data(inset_sql, insert_data)

        mysql.close()


        # 插入数据

    def saveZbrankRank(self):
        mysql = database()
        # 查询采集状态
        status_select = "select * from zbrank_collect_status WHERE collect_status = 0"
        status_list = mysql.query(status_select)
        # 查询列名
        column_select = "select COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = 'zbrank_rank_tmp'"
        column_list = mysql.query(column_select)
        # 列名组成模板
        tmplate = list()
        for column in column_list:
            if (column['COLUMN_NAME'] != 'id'):
                tmplate.append(column['COLUMN_NAME'])

        for status in status_list:
            # 表名 如果不存在则创建新表
            table_name = 'zbrank_rank_' + function.timestamp_to_array("%Y%m%d", status['rank_start_timestamp'])
            # 依照模板 创建表
            mysql.create_table_by_tmp(table_name, 'zbrank_rank_tmp')
            # 获取排名详情
            data_list = getDataList().getTotalRank(status['zbrank_date_key'], status['zbrank_plat_id']);

            insert_list = function.dictList_to_list_by_tmpList(data_list, tmplate, True)

            insert_sql = "insert into " + table_name + " VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"

            insert_result = mysql.insert_by_data(insert_sql, insert_list, "many")

            if (insert_result == True):
                change_status_sql = "UPDATE zbrank_collect_status SET collect_status = 1 WHERE zbrank_date_key = '" + status['zbrank_date_key'] + "' AND zbrank_plat_id = '" + status['zbrank_plat_id'] + "'"
                mysql.dml(change_status_sql)

        mysql.close()

    # 插入排行数据 新

    def saveZbrankRankNew(self):
        mysql = database()
        # 查询采集状态
        status_select = "select * from zbrank_collect_status WHERE collect_status = 0"
        status_list = mysql.query(status_select)
        # 查询列名
        column_select = "select COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_NAME = 'zbrank_rank'"
        column_list = mysql.query(column_select)
        # 列名组成模板
        tmplate = list()
        for column in column_list:
            if (column['COLUMN_NAME'] != 'id'):
                tmplate.append(column['COLUMN_NAME'])

        for status in status_list:
            # 获取排名详情
            data_list = getDataList().getTotalRank(status['zbrank_date_key'], status['zbrank_plat_id']);


            for data in data_list:
                data["rank_start"] = status["rank_start"]
                data["rank_start_timestamp"] = status["rank_start_timestamp"]
                data["updated_at"] = now

            insert_list = function.dictList_to_list_by_tmpList(data_list, tmplate, True)

            insert_sql = "insert into zbrank_rank VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"

            insert_result = mysql.insert_by_data(insert_sql, insert_list, "many")

            if (insert_result == True):
                change_status_sql = "UPDATE zbrank_collect_status SET collect_status = 1 WHERE zbrank_date_key = '" + status['zbrank_date_key'] + "' AND zbrank_plat_id = '" + status['zbrank_plat_id'] + "'"
                mysql.dml(change_status_sql)

        mysql.close()

#     直播地址
#     date 取哪天直播数据里的addr
    def saveLiveAddr(self,date):
        # 从zbrank_rank 表中获取主播基础信息
        livers_sql = "select userId,platform,username,platname,rank_start from zbrank_rank WHERE rank_start_timestamp = " + str(date)
        mysql = database()
        livers = mysql.query(livers_sql)
        for liver in livers:
            addr = getDataList().getTrueLiveAddr(liver['platform'],liver['userId'])
            check_sql = "select count(*) AS cou from liver_addr where userId = '" + liver['userId'] + "'and platform = '" + liver['platform'] +"'LIMIT 1"
            check = mysql.query(check_sql)
            if(check[0]['cou'] == 0 ):
                insert_sql = "insert into liver_addr VALUES (%s,%s,%s,%s,%s,%s,%s,%s)"
                insert_data = list()
                insert_data.append(0)
                insert_data.append(liver['userId'])
                insert_data.append(liver['username'])
                insert_data.append(liver['platform'])
                insert_data.append(liver['platname'])
                insert_data.append(liver['rank_start'])
                insert_data.append(addr)
                insert_data.append(now)
                mysql.insert_by_data(insert_sql, insert_data,'one')
            else:
                update_sql = "UPDATE liver_addr SET liverAddr = '" + addr + "', rank_start = '" + str(liver['rank_start']) + "', updated_at = '" + str(now) +"' WHERE userId = '" + str(liver['userId']) + "' AND platform = '" + liver['platform']  + "'"
                mysql.dml(update_sql)
        mysql.close()

if __name__ == '__main__':

    # getList('http://www.zbrank.com/ranking/affect/').getRankTypeList()
    # getList('http://www.zbrank.com/ranking/affect/').getDateList()
    # getList('http://www.zbrank.com/ranking/affect/').getPlatList()
    # zbrankSave().insertZbrankCollectStatus([0,"a","b",3,4,5,6,None,"1997-05-23 09:15:28","1997-05-23 09:15:28"]);
    # zbrankSave().saveZbrankRank()
    # zbrankSave().saveZbrankRankNew()
    sql = "select DISTINCT(rank_start_timestamp) from zbrank_rank "
    mysql = database()
    test_date_list = mysql.query(sql)
    for date in test_date_list:
        zbrankSave().saveLiveAddr(date['rank_start_timestamp'])