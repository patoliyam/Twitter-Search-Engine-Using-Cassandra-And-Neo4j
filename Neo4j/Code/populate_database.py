from py2neo import authenticate, Graph, Path, Node, Relationship
import os
import json

# set up authentication parameters
authenticate("localhost:7474", "neo4j", "meet1234")

# connect to authenticated graph database
graph = Graph("http://localhost:7474/db/data/")

graph.run("CREATE CONSTRAINT ON (h:HASHTAG) ASSERT h.hashtag_name IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (u:USER) ASSERT u.author_screen_name IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (p:POST) ASSERT p.tid IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (t:TWEET) ASSERT t.tid IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (r:RETWEET) ASSERT r.tid IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (q:QUOTEDTWEET) ASSERT q.tid IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (l:LOCATION) ASSERT l.location IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (d:DATE) ASSERT d.date IS UNIQUE")
graph.run("CREATE CONSTRAINT ON (k:KEYWORD) ASSERT k.keyword IS UNIQUE")

def add_tweet_node(user,location,date,label, entry):
	tweet = graph.find_one("POST", "tid", entry['tid'])
	if tweet:
		tweet.add_label(label)
		tweet.push()
	else:
		tweet = Node("POST", tid = entry["tid"])
		tweet.add_label(label)
		graph.merge(tweet)
	graph.merge(Relationship(user,"POSTED", tweet))
	graph.merge(Relationship(tweet,"POSTED_FROM", location))
	graph.merge(Relationship(tweet,"POSTED_ON", date))
	return tweet


def add_hashtag_kw_mentions(tweet, entry):
	if(entry['hashtags'] != None):
		for ht in entry['hashtags'] :
			hashtag = graph.find_one("HASHTAG", "hashtag", ht)
			if not hashtag:
				hashtag = Node("HASHTAG", hashtag=ht )	
				graph.merge(hashtag)
			graph.merge(Relationship(tweet,"TAGS", hashtag))
	if(entry['mentions'] != None):
		for ml in entry['mentions'] :
			user2 = graph.find_one("USER", "author_screen_name", ml)
			if not user2:
				user2 = Node("USER", author_screen_name=ml)	
				graph.merge(user2)
			graph.merge(Relationship(tweet,"MENTIONS", user2))
	if(entry['keywords_processed_list'] != None):
		for kw in entry['keywords_processed_list'] :
			keyword = graph.find_one("KEYWORD", "keyword", kw)
			if not keyword:
				keyword = Node("KEYWORD", keyword=kw )	
				graph.merge(keyword)
			graph.merge(Relationship(tweet,"HAS_KEYWORD", keyword))

def set_tweet_property(tweet, entry):
	tweet["sentiment"] = entry["sentiment"]
	tweet["datetime"] = entry["datetime"]
	tweet["type"] = entry["type"]
	tweet["like_count"] = entry["like_count"]
	tweet["retweet_count"] = entry["retweet_count"]
	tweet["quote_count"] = entry["quote_count"]
	tweet["tweet_text"] = entry["tweet_text"]
	tweet["lang"] = entry["lang"]
	tweet["url_list"] = entry["url_list"]
	tweet.push()


				

def main():
	# graph = Graph()
	path = os.getcwd()+'/workshop_dataset1'
	for filename in os.listdir(path):
		json1_file = open(path+'/'+filename)
		json1_str = json1_file.read()
		json_data = json.loads(json1_str)
		for key in json_data.keys():
			entry = json_data[key]
			
			user = graph.find_one("USER", "author_screen_name", entry['author_screen_name'])
			if not user:
				user = Node("USER", author_screen_name=entry['author_screen_name'] )	
				graph.merge(user)
			user["author_id"]=entry["author_id"]
			user["author"]=entry["author"]
			user["author_profile_image"] = entry["author_profile_image"]
			user.push()

			location = graph.find_one("LOCATION", "location", entry['location'])
			if not location:
				location = Node("LOCATION", location = entry["location"])
				graph.merge(location)

			date = graph.find_one("DATE", "date", entry['date'])
			if not date:
				date = Node("DATE", date = entry["date"])
				graph.merge(date)

			if entry["type"]=='Tweet' :
				tweet=add_tweet_node(user, location,date, "TWEET", entry)
				#graph.merge(Relationship(user,"POSTED", tweet))
				set_tweet_property(tweet, entry)
				add_hashtag_kw_mentions(tweet, entry)
			
			if entry["type"]=='Reply' :
				reply=add_tweet_node(user, location,date, "REPLY", entry)
				#graph.merge(Relationship(user,"POSTED", tweet))	
				replyto_source = Node("POST", tid = entry["replyto_source_id"])
				if not replyto_source:
					replyto_source = Node("POST", tid = entry["replyto_source_id"])
					graph.merge(replyto_source)
				graph.merge(Relationship(reply,"REPLY_OF", replyto_source))
				set_tweet_property(reply, entry)
				
				add_hashtag_kw_mentions(reply, entry)
				
				
			if entry["type"]=='retweet' :
				retweet = add_tweet_node(user,location,date,"RETWEET", entry)
				#graph.merge(Relationship(user,"POSTED", tweet))
				retweet_source = Node("POST", tid = entry["retweet_source_id"])
				if not retweet_source:
					retweet_source = Node("POST", tid = entry["retweet_source_id"])
					graph.merge(retweet_source)
				graph.merge(Relationship(retweet,"RETWEETED", retweet_source))
				set_tweet_property(retweet, entry)

				add_hashtag_kw_mentions(retweet, entry)
				

			if entry["type"]=='QuotedTweet' :

				QuotedTweet = add_tweet_node(user, location,date,"QUOTEDTWEET", entry)
				# graph.merge(Relationship(user,"POSTED", QuotedTweet))
				quoted_source = Node("POST", tid = entry["quoted_source_id"])
				if not quoted_source:
					quoted_source = Node("POST", tid = entry["quoted_source_id"])
					graph.merge(quoted_source)
				graph.merge(Relationship(QuotedTweet,"QUOTED_TWEET", quoted_source))
				set_tweet_property(QuotedTweet, entry)

				add_hashtag_kw_mentions(QuotedTweet, entry)
		print ("inserted : ",date)

				





if __name__ == "__main__":
    main()
