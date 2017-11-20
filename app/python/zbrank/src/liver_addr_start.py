# -*- coding: UTF-8 -*-

# 自定义py导入开始
# from getList import getList
import zbrank
# 导入拓展包开始
import json
import time, datetime


# 导入拓展包结束


# 自定义py导入结束
date = (datetime.datetime.today() - datetime.timedelta(days=time.localtime().tm_wday + 7)).strftime("%Y-%m-%d") + " 00:00:00"

zbrank().saveLiveAddr(date)
