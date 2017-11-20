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

    def status_start(self):

        # zbrank.zbrankSave().saveZbrankRank()
        zbrank.zbrankSave().saveZbrankRankNew()

status_start().status_start()
