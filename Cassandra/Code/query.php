<html>
 <head>
  <title>Query Engine</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>
 </head>

 <style>
.tooltip1 {
    position: relative;
    display: inline-block;
    border-bottom: 1px dotted black;
}

.tooltip1 .tooltip1text {
    visibility: hidden;
    width: 150px;
    top: 100%;
    left: 50%;
    margin-left: 0px; /* Use half of the width (120/2 = 60), to center the tooltip1 */
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px 0;
    position: absolute;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s;
}


.tooltip1:hover .tooltip1text {
    visibility: visible;
    opacity: 1;
}

input[type=text], select {
    padding: 4px 20px;
    margin: 4px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=submit] {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 18px;
}

input[type=submit]:hover {
    background-color: #45a049;
}

input[type=radio]{
    height: 20px;
    width: 20px;
}


</style>

 <body>
 <?php
 	$cluster   = Cassandra::cluster()                 // connects to localhost by default
				 ->withContactPoints('127.0.0.1')
                 ->build();
	$keyspace  = 'twitter_dataset';
	$session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace
 ?>


<br>
<h3>&emsp;Query Type</body></h3>
<div>
	<form method="post">
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q1"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q1") echo "checked";?> >
			     <span style="font-size: 20px">1 :</span>
				<span class="tooltip1text">
					Given an author name, display all tweets posted by that author sorted by decreasing order of date and time.
				</span>
			</div>
			<input id="q1_input" type="text" value="<?php echo isset($_POST['q1_input']) && $_POST['query_type']=="q1" ? $_POST['q1_input'] : '' ?>"  type="text" name="q1_input" placeholder="Author name"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q2"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q2") echo "checked";?> >
			     <span style="font-size: 20px">2 :</span>
				<span class="tooltip1text">
					Given a keyword, retrieve the tweets containing the keyword and sort them by their popularity in decreasing order.
				</span>
			</div>
			<input id="q2_input" type="text" value="<?php echo isset($_POST['q2_input']) && $_POST['query_type']=="q2" ? $_POST['q2_input'] : '' ?>" type="text" name="q2_input" placeholder="Keyword"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q3"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q3") echo "checked";?> >
			     <span style="font-size: 20px">3 :</span>
				<span class="tooltip1text">
					Given a hashtag, retrieve all tweets containing the hashtag and sort them in decreasing order of date and time.
				</span>
			</div>
			<input id="q3_input" type="text" value="<?php echo isset($_POST['q3_input']) && $_POST['query_type']=="q3" ? $_POST['q3_input'] : '' ?>" type="text" name="q3_input" placeholder="Hashtag"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q4"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q4") echo "checked";?> >
			     <span style="font-size: 20px">4 :</span>
				<span class="tooltip1text">
					Given an author name, retrieve all tweets that mentions the author. Sort them in decreasing order of date and time.
				</span>
			</div>
			<input id="q4_input" type="text" value="<?php echo isset($_POST['q4_input']) && $_POST['query_type']=="q4" ? $_POST['q4_input'] : '' ?>" type="text" name="q4_input" placeholder="Author name"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			<br>
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q5"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q5") echo "checked";?> >
			     <span style="font-size: 20px">5 :</span>
				<span class="tooltip1text">
					Retrieve all tweets of a particular date sorted in decreasing order of their popularity.
				</span>
			</div>
			<input id="q5_input" type="text" value="<?php echo isset($_POST['q5_input']) && $_POST['query_type']=="q5"? $_POST['q5_input'] : '' ?>" type="text" name="q5_input" placeholder="YYYY-MM-DD"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q6"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q6") echo "checked";?> >
			     <span style="font-size: 20px">6 :</span>
				<span class="tooltip1text">
					Retrieve all tweets from a given location.
				</span>
			</div>
			<input id="q6_input" type="text" value="<?php echo isset($_POST['q6_input']) && $_POST['query_type']=="q6" ? $_POST['q6_input'] : '' ?>" type="text" name="q6_input" placeholder="Location"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q7"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q7") echo "checked";?> >
			     <span style="font-size: 20px">7 :</span>
				<span class="tooltip1text">
					Given a date, retrieve top 20 popular hashtags over the last 7 days.
				</span>
			</div>
			<input id="q7_input" type="text" value="<?php echo isset($_POST['q7_input']) && $_POST['query_type']=="q7"? $_POST['q7_input'] : '' ?>" type="text" name="q7_input" placeholder="YYYY-MM-DD"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			&emsp;
			<div class="tooltip1">
				<input type="radio" name="query_type" class="radioBtn" value="q8"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q8") echo "checked";?> >
			     <span style="font-size: 20px">8 :</span>
				<span class="tooltip1text">
					Given a date, delete all tweets posted on that day
				</span>
			</div>
			<input id="q8_input" type="text" value="<?php echo isset($_POST['q8_input'])  && $_POST['query_type']=="q8" ? $_POST['q8_input'] : '' ?>" type="text" name="q8_input" placeholder="YYYY-MM-DD"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
	<br><br> &emsp;
	<input type="submit" name="submit" value="Evaluate">
</form>

</div>

<?php

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{

		$query_type = $_POST["query_type"];



		if($_POST["query_type"]=="q1")
		{
			$q1_input = $_POST["q1_input"];


			$statement = new Cassandra\SimpleStatement(
			   'SELECT * FROM q1 WHERE author_screen_name=:author_screen_name'
			);
			$options = array('arguments' =>
		        array(
		            'author_screen_name' => $q1_input
		        )
			);

			$future    = $session->executeAsync($statement,$options);  // fully asynchronous and easy parallel execution
			$result    = $future->get();                      		   // wait for the result, with an optional timeout

			if($result->count()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->count() . " rows </b> </h4>\n";
			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n";
			}
			if($result->count()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
		                <th>DateTime</th>
		                <th>Tweet id</th>
		                <th>Tweet text</th>
		                <th>Tweet author id</th>
		                <th>Location</th>
		                <th>Language</th>
	               </tr>
	             </thead>
	             <tbody>';
	        }
			foreach ($result as $row) {
				echo "<tr>
						<td>";
				 	    $dateTemp =  date('Y-m-d H:i:s', $row['datetime']->time());
				     	echo $dateTemp;
				      	echo "</td> \n".
				      	"<td>".$row['tid']."</td> \n".
				      	"<td>".$row['tweet_text']."</td> \n".
				      	"<td>".$row['author_id']."</td> \n".
				      	"<td>".$row['location']."</td> \n".
				      	"<td>".$row['lang']."</td> \n".
				      "</tr> ";
				}
			echo " </tbody>
			</table>";
		}
		if($_POST["query_type"]=="q2")
		{
			$q2_input = $_POST["q2_input"];
			$statement = new Cassandra\SimpleStatement(
			   'SELECT * FROM q2 WHERE keyword=:keyword'
			);
			$options = array('arguments' =>
		        array(
		            'keyword' => $q2_input
		        )
			);
			$future    = $session->executeAsync($statement,$options);
			$result    = $future->get();

			if($result->count()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->count() . " rows </b> </h4>\n";
			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n";
			}
			if($result->count()>0)
			{
				echo '<table class="display", border="1" width="97%" align="center">
	            	<tr>
		                <th>Like count</th>
		                <th>Tweet id</th>
		                <th>Tweet text</th>
		                <th>Tweet author id</th>
		                <th>Location</th>
		                <th>Language</th>
	               </tr>
	             </thead>
	             <tbody>';
	        }

			foreach ($result as $row) {
				echo "<tr>".
				 	    "<td>".$row['like_count']."</td> \n".
				      	"<td>".$row['tid']."</td> \n".
				      	"<td>".$row['tweet_text']."</td> \n".
				      	"<td>".$row['author_id']."</td> \n".
				      	"<td>".$row['location']."</td> \n".
				      	"<td>".$row['lang']."</td> \n".
				      "</tr> ";
				}
			echo " </tbody>
			</table>";

		}
		if($_POST["query_type"]=="q3")
		{
			$q3_input = $_POST["q3_input"];
			$statement = new Cassandra\SimpleStatement(
			   'SELECT * FROM q3 WHERE hashtag=:hashtag'
			);
			$options = array('arguments' =>
		        array(
		            'hashtag' => $q3_input
		        )
			);
			$future    = $session->executeAsync($statement,$options);
			$result    = $future->get();
			if($result->count()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->count() . " rows </b> </h4>\n";
			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n";
			}
			if($result->count()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
		                <th>DateTime</th>
		                <th>Tweet id</th>
		                <th>Tweet text</th>
		                <th>Tweet author id</th>
		                <th>Location</th>
		                <th>Language</th>
	               </tr>
	             </thead>
	             <tbody>';
	        }
			foreach ($result as $row) {
				echo "<tr>
						<td>";
				 	    $dateTemp =  date('Y-m-d H:i:s', $row['datetime']->time());
				     	echo $dateTemp;
				      	echo "</td> \n".
				      	"<td>".$row['tid']."</td> \n".
				      	"<td>".$row['tweet_text']."</td> \n".
				      	"<td>".$row['author_id']."</td> \n".
				      	"<td>".$row['location']."</td> \n".
				      	"<td>".$row['lang']."</td> \n".
				      "</tr> ";
				}
			echo " </tbody>
			</table>";
		}
		if($_POST["query_type"]=="q4")
		{
			$q4_input = $_POST["q4_input"];
			$statement = new Cassandra\SimpleStatement(
			   'SELECT * FROM q4 WHERE mention=:mention'
			);
			$options = array('arguments' =>
		        array(
		            'mention' => $q4_input
		        )
			);
			$future    = $session->executeAsync($statement,$options);
			$result    = $future->get();
			if($result->count()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->count() . " rows </b> </h4>\n";
			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n";
			}
			if($result->count()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
		                <th>DateTime</th>
		                <th>Tweet id</th>
		                <th>Tweet text</th>
		                <th>Tweet author id</th>
		                <th>Location</th>
		                <th>Language</th>
	               </tr>
	             </thead>
	             <tbody>';
        	}

			foreach ($result as $row) {
				echo "<tr>
						<td>";
				 	    $dateTemp =  date('Y-m-d H:i:s', $row['datetime']->time());
				     	echo $dateTemp;
				      	echo "</td> \n".
				      	"<td>".$row['tid']."</td> \n".
				      	"<td>".$row['tweet_text']."</td> \n".
				      	"<td>".$row['author_id']."</td> \n".
				      	"<td>".$row['location']."</td> \n".
				      	"<td>".$row['lang']."</td> \n".
				      "</tr> ";
				}
			echo " </tbody>
			</table>";
		}
		if($_POST["query_type"]=="q5")
		{
			$q5_input = $_POST["q5_input"];
			$statement = new Cassandra\SimpleStatement(
			   "SELECT * FROM q5 WHERE date=:date1"
			);
			$dt=strtotime(date($q5_input));
			$offset = 45000;
			$ymd = new \Cassandra\Date($dt+$offset);
			$options = array('arguments' =>
		        array(
		            'date1' => $ymd
		        )
			);
			$future= $session->executeAsync($statement,$options);
			$result = $future->get();
			if($result->count()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->count() . " rows </b> </h4>\n";
			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n";
			}
			if($result->count()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
		                <th>Like count</th>
	            		<th>Tweet id</th>
		                <th>Tweet text</th>
		                <th>Tweet author id</th>
		                <th>Location</th>
		                <th>Language</th>
	               </tr>
	             </thead>
	             <tbody>';
	        }
			foreach ($result as $row) {
				echo "<tr>".
				      	"<td>".$row['like_count']."</td> \n".
				      	"<td>".$row['tid']."</td> \n".
				      	"<td>".$row['tweet_text']."</td> \n".
				      	"<td>".$row['author_id']."</td> \n".
				      	"<td>".$row['location']."</td> \n".
				      	"<td>".$row['lang']."</td> \n".
				      "</tr> ";
				}
			echo " </tbody>
			</table>";
		}
		if($_POST["query_type"]=="q6")
		{
			$q6_input = $_POST["q6_input"];
			$statement = new Cassandra\SimpleStatement(
			   'SELECT * FROM q6 WHERE location=:location'
			);
			$options = array('arguments' =>
		        array(
		            'location' => $q6_input
		        )
			);
			$future    = $session->executeAsync($statement,$options);
			$result    = $future->get();
			if($result->count()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->count() . " rows </b> </h4>\n";
			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n";
			}
			if($result->count()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
		                <th>Tweet id</th>
		                <th>Tweet text</th>
		                <th>Tweet author id</th>
		                <th>Location</th>
		                <th>Language</th>
	               </tr>
	             </thead>
	             <tbody>';
	         }

			foreach ($result as $row) {
				echo "<tr>".
				      	"<td>".$row['tid']."</td> \n".
				      	"<td>".$row['tweet_text']."</td> \n".
				      	"<td>".$row['author_id']."</td> \n".
				      	"<td>".$row['location']."</td> \n".
				      	"<td>".$row['lang']."</td> \n".
				      "</tr> ";
				}
			echo " </tbody>
			</table>";

		}
		if($_POST["query_type"]=="q7")
		{
			$q7_input = $_POST["q7_input"];
			$statement = new Cassandra\SimpleStatement(
			   "SELECT hashtag, occurrence FROM q7 WHERE date=:date1 LIMIT 20"
			 );
			$dt=strtotime(date($q7_input));
			$offset = 45000;
			$ymd = new \Cassandra\Date($dt + $offset);
			$options = array('arguments' =>
		        array(
		            'date1' => $ymd
		        )
			);
			$future= $session->executeAsync($statement,$options);
			$result = $future->get();
			if($result->count()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
	            		<th>Hashtag</th>
	            		<th>Occurance</th>
	               </tr>
	             </thead>
	             <tbody>';
	         }
			foreach ($result as $row) {

				echo "<tr>".
				      	"<td>".$row['hashtag']."</td> \n".
				      	"<td>".$row['occurrence']."</td> \n".
				      "</tr> ";
			}
			echo " </tbody>
			</table>";
		}
        if($_POST["query_type"]=="q8")
		{
			$q8_input = $_POST["q8_input"];
			$select1 = new Cassandra\SimpleStatement(
			   "SELECT author_screen_name FROM q1 WHERE date=:date1"
			 );
			$select2 = new Cassandra\SimpleStatement(
			   "SELECT keyword, like_count FROM q2 WHERE date=:date1"
			 );
			$select3 = new Cassandra\SimpleStatement(
			   "SELECT hashtag FROM q3 WHERE date=:date1"
			 );
			$select4 = new Cassandra\SimpleStatement(
			   "SELECT mention FROM q4 WHERE date=:date1"
			 );
			$select6 = new Cassandra\SimpleStatement(
			   "SELECT location FROM q6 WHERE date=:date1"
			 );

			$dt=strtotime(date($q8_input));
			$offset = 45000;
			$ymd = new \Cassandra\Date($dt + $offset);
			$options = array('arguments' =>
		        array(
		            'date1' => $ymd
		        )
			);
			$future1= $session->executeAsync($select1,$options);
			$result1 = $future1->get();
			$future2= $session->executeAsync($select2,$options);
			$result2 = $future2->get();
			$future3= $session->executeAsync($select3,$options);
			$result3 = $future3->get();
			$future4= $session->executeAsync($select4,$options);
			$result4 = $future4->get();
			$future6= $session->executeAsync($select6,$options);
			$result6 = $future6->get();
			$batch = new Cassandra\BatchStatement(Cassandra::BATCH_LOGGED);
			$delete1 = "DELETE FROM q1 WHERE author_screen_name = ? AND date = ?";
			$delete2 = "DELETE FROM q2 WHERE like_count = ? AND keyword = ? AND date = ?";
			$delete3 = "DELETE FROM q3 WHERE hashtag = ? AND date = ?";
			$delete4 = "DELETE FROM q4 WHERE mention = ? AND date = ?";
			$delete5 = "DELETE FROM q5 WHERE date = ?";
			$delete6 = "DELETE FROM q6 WHERE location = ? AND date = ?";
			$prepared1    = $session->prepare($delete1);
			$prepared2    = $session->prepare($delete2);
			$prepared3    = $session->prepare($delete3);
			$prepared4    = $session->prepare($delete4);
			$prepared5    = $session->prepare($delete5);
			$prepared6    = $session->prepare($delete6);
		    $batch->add($prepared5, array('date' => $ymd));
			foreach ($result1 as $row) {
		        	$batch->add($prepared1, array(
		        		'author_screen_name' => $row['author_screen_name'],
		        		'date' => $ymd));
		    }
		    foreach ($result2 as $row) {
		        	$batch->add($prepared2, array(
		        		'like_count' => $row['like_count'],
		        		'keyword' => $row['keyword'],
		        		'date' => $ymd));
		    }
		    foreach ($result3 as $row) {
		        	$batch->add($prepared3, array(
		        		'hashtag' => $row['hashtag'],
		        		'date' => $ymd));
		    }
		    foreach ($result4 as $row) {
		        	$batch->add($prepared4, array(
		        		'mention' => $row['mention'],
		        		'date' => $ymd));
		    }
		    foreach ($result6 as $row) {
		        	$batch->add($prepared6, array(
		        		'location' => $row['location'],
		        		'date' => $ymd));
		    }
			$session->execute($batch);
			echo "<h4> <b>&emsp;&emsp;Deleted records of date : " . $q8_input . " </b> </h4>\n";
		}
	}
?>
<script>
	check(q1);
</script>

</body>
</html>
