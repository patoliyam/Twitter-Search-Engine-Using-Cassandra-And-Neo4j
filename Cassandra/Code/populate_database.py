import os
import json

import logging
log = logging.getLogger()
log.setLevel('DEBUG')
handler = logging.StreamHandler()
handler.setFormatter(logging.Formatter("%(asctime)s [%(levelname)s] %(name)s: %(message)s"))
log.addHandler(handler)

from cassandra import ConsistencyLevel
from cassandra.cluster import Cluster
from cassandra.query import SimpleStatement

KEYSPACE = "twitter_dataset"

def create_table_for_q1(session):
	session.execute("DROP TABLE IF EXISTS q1")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q1 (
			author_screen_name varchar,			
			datetime timestamp,
			date date,
			tid varchar,
			tweet_text varchar,
			author_id varchar,
			location varchar,
			lang varchar,
			PRIMARY KEY ((author_screen_name), date, datetime, tid)
		) WITH CLUSTERING ORDER BY (date desc, datetime desc);
		
		""")
	session.execute("CREATE INDEX idate1 ON q1(date)")
	#author name means author or author_screen_name?

def create_table_for_q2(session):
	session.execute("DROP TABLE IF EXISTS q2")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q2 (
			keyword	varchar,
			like_count int,
			date date,			
			tid varchar,
			tweet_text varchar,
			author_id varchar,
			location varchar,
			lang varchar,
			PRIMARY KEY ((keyword), like_count, date, tid)
		) WITH CLUSTERING ORDER BY (like_count desc, date desc);
		""")
	session.execute("CREATE INDEX idate2 ON q2(date)")

def create_table_for_q3(session):
	session.execute("DROP TABLE IF EXISTS q3")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q3 (
			hashtag varchar,
			datetime timestamp,
			date date,
			tid varchar,
			tweet_text varchar,
			author_id varchar,
			location varchar,
			lang varchar,
			PRIMARY KEY ((hashtag), date, datetime, tid)
		) WITH CLUSTERING ORDER BY ( date desc, datetime desc);
		""")
	session.execute("CREATE INDEX idate3 ON q3(date)")


def create_table_for_q4(session):
	session.execute("DROP TABLE IF EXISTS q4")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q4 (
			mention varchar,			
			datetime timestamp,
			date date,
			tid varchar,
			tweet_text varchar,
			author_id varchar,
			location varchar,
			lang varchar,
			PRIMARY KEY ((mention), date ,datetime, tid)
		) WITH CLUSTERING ORDER BY (date desc, datetime desc);
		""")
	session.execute("CREATE INDEX idate4 ON q4(date)")


def create_table_for_q5(session):
	session.execute("DROP TABLE IF EXISTS q5")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q5 (
			date date,
			like_count int,			
			tid varchar,
			tweet_text varchar,
			author_id varchar,
			location varchar,
			lang varchar,
			PRIMARY KEY ((date),like_count, tid)
		) WITH CLUSTERING ORDER BY (like_count desc);
		""")
	
def create_table_for_q6(session):
	session.execute("DROP TABLE IF EXISTS q6")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q6 (
			date date,
			tid varchar,
			tweet_text varchar,
			author_id varchar,
			location varchar,
			lang varchar,
			PRIMARY KEY ((location), date, tid)
		) WITH CLUSTERING ORDER BY (date desc);
		""")
	session.execute("CREATE INDEX idate6 ON q6(date)")

def create_table_for_q7_(session):
	session.execute("DROP TABLE IF EXISTS q7_")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q7_ (
			hashtag varchar,
			date date,
			occurrence counter,
			PRIMARY KEY ((date), hashtag)
		) WITH CLUSTERING ORDER BY (hashtag asc);
		""")

def create_table_for_q7(session):
	session.execute("DROP TABLE IF EXISTS q7")
	session.execute("""
		CREATE TABLE IF NOT EXISTS q7 (
			hashtag varchar,
			date date,
			occurrence int,
			PRIMARY KEY ((date), occurrence, hashtag)
		) WITH CLUSTERING ORDER BY (occurrence desc);
		""")


def main():
	cluster = Cluster(['127.0.0.1'])
	session = cluster.connect()
	log.info("creating keyspace...")
	session.execute("""
		CREATE KEYSPACE IF NOT EXISTS %s
		WITH replication = { 'class': 'SimpleStrategy', 'replication_factor': '2' }
		""" % KEYSPACE)
	log.info("setting keyspace...")
	session.set_keyspace(KEYSPACE)

	log.info("creating table...")
	create_table_for_q1(session)
	create_table_for_q2(session)
	create_table_for_q3(session)
	create_table_for_q4(session)
	create_table_for_q5(session)
	create_table_for_q6(session)
	create_table_for_q7_(session)
	create_table_for_q7(session)

	
	q1 = SimpleStatement("""
		INSERT INTO q1(author_screen_name,datetime,date,tid,tweet_text,author_id,location,lang)
		VALUES (%(author_screen_name)s,%(datetime)s,%(date)s,%(tid)s,%(tweet_text)s,%(author_id)s,%(location)s,%(lang)s)
		""", consistency_level=ConsistencyLevel.ONE)
	q2 = SimpleStatement("""
		INSERT INTO q2 (keyword,like_count,date,tid,tweet_text,author_id,location,lang)
		VALUES (%(keyword)s,%(like_count)s,%(date)s,%(tid)s,%(tweet_text)s,%(author_id)s,%(location)s,%(lang)s)
		""", consistency_level=ConsistencyLevel.ONE)
	q3 = SimpleStatement("""
		INSERT INTO q3 (hashtag,datetime,date,tid,tweet_text,author_id,location,lang)
		VALUES (%(hashtag)s,%(datetime)s,%(date)s,%(tid)s,%(tweet_text)s,%(author_id)s,%(location)s,%(lang)s)
		""", consistency_level=ConsistencyLevel.ONE)
	q4 = SimpleStatement("""
		INSERT INTO q4(mention,datetime,date,tid,tweet_text,author_id,location,lang)
		VALUES (%(mention)s,%(datetime)s,%(date)s,%(tid)s,%(tweet_text)s,%(author_id)s,%(location)s,%(lang)s)
		""", consistency_level=ConsistencyLevel.ONE)
	q5 = SimpleStatement("""
		INSERT INTO q5 (date,like_count,tid,tweet_text,author_id,location,lang)
		VALUES (%(date)s,%(like_count)s,%(tid)s,%(tweet_text)s,%(author_id)s,%(location)s,%(lang)s)
		""", consistency_level=ConsistencyLevel.ONE)	
	q6 = SimpleStatement("""
		INSERT INTO q6 (date,tid,tweet_text,author_id,location,lang)
		VALUES (%(date)s,%(tid)s,%(tweet_text)s,%(author_id)s,%(location)s,%(lang)s)
		""", consistency_level=ConsistencyLevel.ONE)
	q7_ = SimpleStatement("""
		UPDATE q7_
		SET occurrence = occurrence + 1
 		WHERE date = %(date)s AND hashtag = %(hashtag)s
		""", consistency_level=ConsistencyLevel.ONE)
	q7 = SimpleStatement("""
		INSERT INTO q7 (date,hashtag,occurrence)
		VALUES (%(date)s,%(hashtag)s,%(occurrence)s)
		""", consistency_level=ConsistencyLevel.ONE)
	
	
	path = os.getcwd()+'/workshop_dataset1'
	for filename in os.listdir(path):
		json1_file = open(path+'/'+filename)
		json1_str = json1_file.read()
		json1_data = json.loads(json1_str)
		for key in json1_data.keys():
			# print type(json1_data[key]['tid'].encode('utf-8'))
			log.info("inserting key %s" % key)
			author_screen_name=json1_data[key]['author_screen_name'] 
			like_count=json1_data[key]['like_count']
			date=json1_data[key]['date']
			datetime=json1_data[key]['datetime']
			tid=json1_data[key]['tid']
			tweet_text=json1_data[key]['tweet_text']
			author_id=json1_data[key]['author_id']
			location=json1_data[key]['location']
			lang=json1_data[key]['lang']			
			datetime=json1_data[key]['datetime']			
			if(json1_data[key]['keywords_processed_list'] != None):
				for keyword in json1_data[key]['keywords_processed_list'] :
					if(keyword != ""):					
						session.execute(q2, dict(keyword=keyword,like_count=like_count,date=date,tid=tid,tweet_text=tweet_text,author_id=author_id,location=location,lang=lang))			
			if(json1_data[key]['hashtags'] != None):
				for hashtag in json1_data[key]['hashtags'] :
					if(hashtag != ""):	
						session.execute(q3, dict(hashtag=hashtag,datetime=datetime,date=date,tid=tid,tweet_text=tweet_text,author_id=author_id,location=location,lang=lang))
						for i in range(7):
		
							date_ = dt.strptime(date, '%Y-%m-%d') - timedelta(days=i)
							datef = date_.strftime('%Y-%m-%d')
							#print datef
							session.execute(q7_, dict(date=datef, hashtag = hashtag))
			
			if(json1_data[key]['mentions'] != None):
				for mention in json1_data[key]['mentions'] :
					if(mention != "") :					
						session.execute(q4, dict(mention=mention,datetime=datetime,date=date,tid=tid,tweet_text=tweet_text,author_id=author_id,location=location,lang=lang))
			if(author_screen_name != None and author_screen_name != ""):
				session.execute(q1,dict(author_screen_name=author_screen_name,datetime=datetime,date=date,tid=tid,tweet_text=tweet_text,author_id=author_id,location=location,lang=lang))
			session.execute(q5, dict(date=date,like_count=like_count,tid=tid,tweet_text=tweet_text,author_id=author_id,location=location,lang=lang))
			if(location != None and location!=""):
				session.execute(q6, dict(date=date,tid=tid,tweet_text=tweet_text,author_id=author_id,location=location,lang=lang))
			print ("inserted key",tid,date)

	rows_q7_ = session.execute('SELECT * FROM q7_')
	for row in rows_q7_:
    		date = row.date
		hashtag = row.hashtag
		occurrence = row.occurrence
		session.execute(q7, dict(date=date, occurrence = occurrence, hashtag = hashtag))


if __name__ == "__main__":
    main()
