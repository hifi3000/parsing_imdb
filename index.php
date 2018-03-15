<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html"><meta charset="utf-8"/>
    <title>IMDB_parsing</title>
	<link rel="stylesheet" type="text/css" href="css/mystyle.css">
	<script src="js/selecttable.js"></script>
  </head>
  <body>
	<h1>Get the soundtrack of a movie as a table</h1>
	<p>Which movie?
		<form method="get" action="index.php?">
			<input type="text" name="go01" value="http://www.imdb.com/title/MOVIE_EXAMPLE/soundtrack">
			<input type="submit" name="ok" value="get soundtrack">
		</form>
	</p>
	<?php
	include 'php/build_table.php';
	
	if(isset($_GET['go01'])){
		$url=$_GET['go01'];
		echo "<p>Link: <a class=link href='$url' target='_blank'>".$url."</a></p>";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		preg_match('!itemprop=\'url\'>(.*?)<\/a>!',$result,$title);
		preg_match('!<span class="nobr">.*?\n.*?\((\d{4})\).*?\n.*?</span>!',$result,$year);
		echo "<h2>".$title[1]." (".$year[1].")</h2>";
		$results=[];
		$helper=[];
		preg_match_all('!<div.*?class="soundTrack soda[\s\S\n]+?<\/div>!',$result,$match);
		$helper = $match[0];
		for($i=0;$i<count($helper);$i++){
			preg_match_all('!erformed.*?<a href=".*?">(.*?)<\/a>!',$helper[$i],$match);
			preg_match_all('!erformed (.*?)<br!',$helper[$i],$match3);
			if(!$match[1][0]){
				preg_match_all('!erformed .y (.*?) <br!',$helper[$i],$match2);
				if(!$match2[1][0]) $results['artist'][$i]='';
				else $results['artist'][$i]=$match2[1][0];
			}
			else{
				$results['artist'][$i]=$match[1][0];
				if(isset($match3[1][0])){
					preg_match_all('!\(as (.*?)\)!',$match3[1][0],$match2);
					if(isset($match2[1][0])) $results['artist'][$i]=$match2[1][0];
				}
			}
		}
		preg_match_all('!class="soundTrack soda .*?">(.*) <br!',$result,$match);
		$results['title'] = $match[1];
		
		echo "<h4>".count($results['artist'])." songs</h4>";	
		echo build_table_swap($results);
	}
	?>
	<p><br></p>
	<p><br></p>
  </body>
</html> 
