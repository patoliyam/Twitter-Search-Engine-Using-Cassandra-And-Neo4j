from py2neo import authenticate, Graph, Path, Node, Relationship
import os
import json
import datetime

# set up authentication parameters
authenticate("localhost:7474", "neo4j", "meet1234")

# connect to authenticated graph database
graph = Graph("http://localhost:7474/db/data/")


def add_tweet_node(tx,user,location,label, entry):
	tweet = Node("POST", tid= entry['tid'])
	tweet.add_label(label)
	tx.merge(tweet, primary_label="POST", primary_key="tid")
	
	tx.create(Relationship(user,"POSTED", tweet))
	tx.create(Relationship(tweet,"POSTED_FROM", location))
	
	return tweet

def add_hashtag_kw_mentions(tx,tweet, entry):
	if(entry['hashtags'] != None):
		for ht in entry['hashtags'] :
			hashtag = Node("HASHTAG", hashtag = ht)
			tx.merge(hashtag, primary_label="HASHTAG", primary_key="hashtag")
			tx.merge(Relationship(tweet,"TAGS", hashtag))
	

def set_tweet_property(tx,tweet, entry):
	tweet["type"] = entry["type"]
	tx.merge(tweet, primary_label="USER", primary_key="tid")


def main():

	tx = graph.begin()
	tx.run("CREATE CONSTRAINT ON (h:HASHTAG) ASSERT h.hashtag_name IS UNIQUE")
	tx.run("CREATE CONSTRAINT ON (u:USER) ASSERT u.author_screen_name IS UNIQUE")
	tx.run("CREATE CONSTRAINT ON (p:POST) ASSERT p.tid IS UNIQUE")
	tx.commit()
	

	tx = graph.begin()
	path = os.getcwd()+'/workshop_dataset1'
	for filename in os.listdir(path):

		a = datetime.datetime.now()
		json1_file = open(path+'/'+filename)
		json1_str = json1_file.read()
		json_data = json.loads(json1_str)
		for key in json_data.keys():
			entry = json_data[key]
			user = Node("USER", author_screen_name=entry['author_screen_name'])
			user["author_id"]=entry["author_id"]
			user["author"]=entry["author"]
			user["author_profile_image"] = entry["author_profile_image"]
			tx.merge(user, primary_label="USER",primary_key="author_screen_name")
			

			location = Node("LOCATION", location = entry["location"])
			tx.merge(location, primary_label="LOCATION",primary_key="location")
			
			if entry["type"]=='Tweet' :
				tweet=add_tweet_node(tx,user,location, "TWEET", entry)
				set_tweet_property(tx,tweet, entry)
				add_hashtag_kw_mentions(tx,tweet, entry)
			

		b = datetime.datetime.now()
		delta = b-a
		print ( 'time to insert : ',delta.total_seconds())		
	tx.commit()


if __name__ == "__main__":
    main()
