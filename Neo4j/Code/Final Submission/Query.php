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

require_once 'vendor/autoload.php';

	use GraphAware\Neo4j\Client\ClientBuilder;

	$client = ClientBuilder::create()
	    ->addConnection('default', 'http://neo4j:meet1234@localhost:7474') // Example for HTTP connection configuration (port is optional)
	    ->addConnection('bolt', 'bolt://neo4j:meet1234@localhost:7687') // Example for BOLT connection configuration (port is optional)
	    ->build();

	// $client->run('CREATE (n:Person)');
	// $result = $client->run('MATCH (n:Person) RETURN n');
	// echo var_dump($result);
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
			<input id="q1_input" type="text" value="<?php echo isset($_POST['q1_input']) && $_POST['query_type']=="q1" ? $_POST['q1_input'] : '' ?>"  type="text" name="q1_input" placeholder="Location"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​ 
			&emsp;
			<div class="tooltip1"> 
				<input type="radio" name="query_type" class="radioBtn" value="q2"  <?php if (isset($_POST['query_type']) && $_POST['query_type']=="q2") echo "checked";?> > 
			     <span style="font-size: 20px">2 :</span> 
				<span class="tooltip1text"> 
					Given a keyword, retrieve the tweets containing the keyword and sort them by their popularity in decreasing order.
				</span> 
			</div>
			<input id="q2_input" type="text" value="<?php echo isset($_POST['q2_input']) && $_POST['query_type']=="q2" ? $_POST['q2_input'] : '' ?>" type="text" name="q2_input" placeholder="Hashtag"/>​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​​
			
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
			
			
			$query = "MATCH (u:USER)-[:POSTED]-(t:POST)-[:POSTED_FROM]-(l:LOCATION) WHERE l.location={location}
			RETURN u.author_screen_name as user,l.location as location, COUNT(t) as number_of_tweets
			ORDER BY number_of_tweets DESC
			LIMIT 2";
			$result = $client->run($query, ['location' => $q1_input]);
			
			// foreach ($result->getRecords() as $record) {
			//     echo sprintf(' name is : %s and has %d number of friends', $record->value('name'), count($record->value('friends'));
			// }

			// $statement = new Cassandra\SimpleStatement(       
			//    'SELECT * FROM q1 WHERE author_screen_name=:author_screen_name'
			// );
			// $options = array('arguments' =>
		 //        array(
		 //            'author_screen_name' => $q1_input
		 //        )
			// );

			// $future    = $session->executeAsync($statement,$options);  // fully asynchronous and easy parallel execution
			// $result    = $future->get();                      		   // wait for the result, with an optional timeout
			
			if($result->size()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->size() . " rows </b> </h4>\n";
				// echo var_dump($result);

			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n"; 
			}
			if($result->size()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
		                <th>User</th>
		                <th>Location</th>
		                <th>Number of Tweets</th>
		               
	               </tr>
	             </thead>
	             <tbody>';
	        }
	        foreach ($result->getRecords() as $record) {
	        	$user=$record->get('user');
	        	$location=$record->get('location');
	        	$number_of_tweets=$record->get('number_of_tweets');

				echo "<tr>
						<td>".$user."</td> \n
						<td>".$location."</td> \n
						<td>".$number_of_tweets."</td> \n
								      	
				      </tr> ";
				     
				}
			echo " </tbody>
		</table>";
		
		}
		if($_POST["query_type"]=="q2")
		{
			$q2_input = $_POST["q2_input"];
			
			
			$query = "MATCH (h:HASHTAG)-[:TAGS]-(t:TWEET)-[:POSTED]-(u:USER) where h.hashtag = {hashtag}
					RETURN h.hashtag as hashtag, u.author_screen_name as user, COUNT(t) as number_of_tweets, COLLECT(t.tid) as tweets
					ORDER BY number_of_tweets DESC
					LIMIT 3";
			$result = $client->run($query, ['hashtag' => $q2_input]);
			
			// foreach ($result->getRecords() as $record) {
			//     echo sprintf(' name is : %s and has %d number of friends', $record->value('name'), count($record->value('friends'));
			// }

			// $statement = new Cassandra\SimpleStatement(       
			//    'SELECT * FROM q1 WHERE author_screen_name=:author_screen_name'
			// );
			// $options = array('arguments' =>
		 //        array(
		 //            'author_screen_name' => $q1_input
		 //        )
			// );

			// $future    = $session->executeAsync($statement,$options);  // fully asynchronous and easy parallel execution
			// $result    = $future->get();                      		   // wait for the result, with an optional timeout
			
			if($result->size()>0)
			{
				echo "<h4> <b>&emsp;&emsp;Result contains " . $result->size() . " rows </b> </h4>\n";
				// echo var_dump($result);

			}
			else
			{
				echo "<h4> <b>&emsp;&emsp;No result found. </b> </h4>\n"; 
			}
			if($result->size()>0)
			{
				echo '<table class="display" , border="1" width="97%" align="center">
	            <thead align="center">
	            	<tr>
		                <th>Hashtag</th>
		                <th>User</th>
		                <th>Tweet ids</th>
		                <th>Tweet Count</th>
		               
	               </tr>
	             </thead>
	             <tbody>';
	        }
	        foreach ($result->getRecords() as $record) {
	        	$hashtag=$record->get('hashtag');
	        	$user=$record->get('user');
	        	$number_of_tweets=$record->get('number_of_tweets');
	        	$tweets=$record->get('tweets');
	        	$arrlength = count($tweets);

				echo "<tr>
						<td>".$hashtag."</td> \n
						<td>".$user."</td> \n <td>";
						for($x = 0; $x < $arrlength; $x++) {
    echo $tweets[$x];
    echo "<br>";
}
						echo "</td> <td>".$number_of_tweets."</td> \n
								      	
				      </tr> ";
				     
				}
			echo " </tbody>
		</table>";
		
		}
	}
?>
<script>
	check(q1);
</script>

</body>
</html>
