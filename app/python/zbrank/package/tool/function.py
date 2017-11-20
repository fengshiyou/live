# -*- coding: UTF-8 -*-

import MySQLdb
import urllib
# 导入拓展包开始
import urllib2
from bs4 import BeautifulSoup
import os
import time

# 导入拓展包结束

# 自定义py导入开始
# 其他包的路径增加
# 获取当前执行该文件的脚本的相对路径
dir = os.path.dirname(__file__)
os.sys.path.append(os.path.join(dir, ".."))

import config

os.sys.path.append(os.path.join(dir, config.env_path))
import env


# 自定义py导入结束

def get_env(env_name):
    return eval("env." + env.app_env + "_" + env_name)


def get_doc(url, timeout=20, post_data='', return_soup=True, read_type='utf-8'):
    if (post_data == ''):
        request = urllib2.Request(url)
    else:
        data = urllib.urlencode(post_data)
        request = urllib2.Request(url, data)
    try:
        response = urllib2.urlopen(request, timeout=timeout)
        html_doc = response.read().decode(read_type)
        if (return_soup):
            soup = BeautifulSoup(html_doc, 'html.parser', from_encoding='utf8')
            return soup
        else:
            return html_doc
    except:
        now = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))
        db_host = get_env(config.url_open_error_db_number + '_host')
        db_user = get_env(config.url_open_error_db_number + '_user')
        db_passwd = get_env(config.url_open_error_db_number + '_passwd')
        db_db = get_env(config.url_open_error_db_number + '_db')
        db_charset = get_env(config.url_open_error_db_number + '_charset')

        connect = MySQLdb.connect(
            host=db_host,
            user=db_user,
            passwd=db_passwd,
            db=db_db,
            charset=db_charset)

        cursor = connect.cursor(cursorclass=MySQLdb.cursors.DictCursor)

        insert_sql = "insert into " + config.url_open_error_table + " VALUES (%s,%s,%s)"
        create_error_table = "CREATE TABLE IF NOT EXISTS `url_open_error` (`id` int(11) NOT NULL AUTO_INCREMENT,`url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,`updated_at` datetime DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        insert_data = list()
        insert_data.append(0)
        insert_data.append(url)
        insert_data.append(now)

        cursor.execute(create_error_table)
        cursor.execute(insert_sql, insert_data)
        connect.commit()
        cursor.close()
        connect.close()
        print "url错误，已经将信息记录在" + db_db + "数据库的" + config.url_open_error_table + "中"
        exit()


def timestamp_to_array(format='%Y-%m-%d %H:%M:%S', timestamp=time.time()):
    return time.strftime(format, time.localtime(timestamp))


# 检查字段是否在dict内  如果在则返回  否则返回空
def in_dict(dict, key):
    if len(dict) < 1:
        return ""
    if key in dict:
        return dict[key]
    return ''


def dictList_to_list_by_tmpList(dictList, tmpList, need_sql_id=False):
    return_list = list()
    for dict in dictList:
        mid_list = list()
        if (need_sql_id):
            mid_list.append(0)

        for tmp_key in tmpList:
            mid_list.append(in_dict(dict, tmp_key))
        return_list.append(mid_list)

    return return_list


if __name__ == "__main__":
    print get_env("db_host")
    print timestamp_to_array()
