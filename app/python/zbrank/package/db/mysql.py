# -*- coding: UTF-8 -*-
import MySQLdb
import os
import time, re


# 其他包的路径增加
# 获取当前执行该文件的脚本的相对路径
dir = os.path.dirname(__file__)
os.sys.path.append(os.path.join(dir, ".."))
# print dir
from tool import function


class database:
    def __init__(self, db_number='db'):
        # db_number   默认只有一个DB 如果有多个db 在env中配置  比如 db1

        ###获取数据库配置###
        self.db_host = function.get_env(db_number + '_host')
        self.db_user = function.get_env(db_number + '_user')
        self.db_passwd = function.get_env(db_number + '_passwd')
        self.db_db = function.get_env(db_number + '_db')
        self.db_charset = function.get_env(db_number + '_charset')

        try:
            self._connect = MySQLdb.connect(
                host=self.db_host,
                user=self.db_user,
                passwd=self.db_passwd,
                db=self.db_db,
                charset=self.db_charset)
        except MySQLdb.Error, e:
            self._error_code = e.args[0]
            error_msg = "%s --- %s" % (time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time())), type(e).__name__), e.args[0], e.args[1]
            print error_msg

        self._cursor = self._connect.cursor(cursorclass=MySQLdb.cursors.DictCursor)


    def query(self, sql, ret_type='all'):
        try:
            self._cursor.execute("SET NAMES utf8")
            self._cursor.execute(sql)
            if ret_type == 'all':
                return self.rows2array(self._cursor.fetchall())
            elif ret_type == 'one':
                return self._cursor.fetchone()
            elif ret_type == 'count':
                return self._cursor.rowcount
        except MySQLdb.Error, e:
            self._error_code = e.args[0]
            print "Mysql execute error:", e.args[0]
            return False

    def dml(self, sql):
        '''update or delete or insert'''
        try:
            self._cursor.execute("SET NAMES utf8")
            self._cursor.execute(sql)
            self._connect.commit()
            type = self.dml_type(sql)
            # if primary key is auto increase, return inserted ID.
            if type == 'insert':
                return self._connect.insert_id()
            else:
                return True
        except MySQLdb.Error, e:
            self._error_code = e.args[0]
            print "Mysql execute error:", e.args
            return False

    def insert_by_data(self, sql, data,type="one"):
        try:
            # todo 坑爹的东西 有空整理   创建连接以后，游标对象首先要执行一遍“SET NAMES utf8mb4;”这样就能保证数据库连接是以utf8mb4编码格式连接，数据库也就变成utf8mb4的啦
            self._cursor.execute("SET NAMES utf8mb4;")

            if(type =='one'):
                self._cursor.execute(sql, data)
            else:
                self._cursor.executemany(sql, data)
            self._connect.commit()

            return True
        except MySQLdb.Error, e:
            self._error_code = e.args[0]
            print "Mysql execute error:", e.args

            return False

    def dml_type(self, sql):
        re_dml = re.compile('^(?P<dml>\w+)\s+', re.I)
        m = re_dml.match(sql)
        if m:
            if m.group("dml").lower() == 'delete':
                return 'delete'
            elif m.group("dml").lower() == 'update':
                return 'update'
            elif m.group("dml").lower() == 'insert':
                return 'insert'
        print "%s --- Warning: '%s' is not dml." % (time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time())), sql)
        return False

    def rows2array(self, data):
        # '''transfer tuple to array.'''
        result = []
        for da in data:
            if type(da) is not dict:
                raise Exception('Format Error: data is not a dict.')
            result.append(da)
        return result

    def create_table_by_tmp(self,table_name,tmp_table_name):
        sql = "CREATE TABLE IF NOT EXISTS " + table_name + " LIKE " + tmp_table_name
        try:
            self._cursor.execute(sql)
            self._connect.commit()
            return True
        except MySQLdb.Error,e:
            self._error_code = e.args
            print "Mysql execute error:", e.args
            return False

    def __del__(self):
        # '''free source.'''
        try:
            self._cursor.close()
            self._connect.close()
        except:
            pass

    def close(self):
        self.__del__()
