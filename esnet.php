<?php

$user_name = "apm";
$password = "TYUghjBNM";
$database = "apm";
$server = "gallinago.gatech.edu:33306";
$count=0;
$edges=0;
$tcount=0;

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database, $db_handle);

if ($db_found) {
  $SQL = "SELECT * FROM flows;";
  $result = mysql_query($SQL);
  while ( $db_field = mysql_fetch_assoc($result) ) {
    $time[$tcount]=$db_field['ts'];
    $path[$tcount]=$db_field['path'];
    $tput[$tcount]=$db_field['tput'];
    $units[$tcount]=$db_field['units'];
    $tcount++;
  }

  $SQL = "SELECT * FROM nodes;";
  $result = mysql_query($SQL);
  while ( $db_field = mysql_fetch_assoc($result) ) {
    $id[$count]=$db_field['id'];
    $name[$count]=$db_field['label'];
    $x[$count]=$db_field['x'];
    $y[$count]=$db_field['y'];
    $size[$count]=$db_field['size'];
    $count++;
  }

  $SQL = "SELECT * FROM edges;";
  $result = mysql_query($SQL);
  while ( $db_field = mysql_fetch_assoc($result) ) {
    $eid[$edges]=$db_field['id'];
    $source[$edges]=$db_field['source'];
    $target[$edges]=$db_field['target'];
    $edges++;
  }
  mysql_close($db_handle);
  //print "So far so good<br>";
}
else {
  print "Database NOT Found<br>";
  mysql_close($db_handle);
}

// Generate JSON file from database
$json="{\n";
$json.="  \"nodes\": [\n";
for ($i=0; $i<$count; $i++) {
//  print "$i $id[$i] $name[$i] $x[$i] $y[$i] $size[$i]<br>\n";
  $json.="    {\"id\": \"$id[$i]\", \"label\": \"$name[$i]\", \"x\": \"$x[$i]\", \"y\": \"$y[$i]\", \"size\": \"$size[$i]\"},\n";
}
$json.="    {\"id\": \"null\", \"label\": \"\", \"x\": \"10\", \"y\": 2.6, \"size\": 0}\n";
$json.="  ],\n";
$json.="  \"edges\": [\n";
for ($i=0; $i<$edges; $i++) {
//  print "$i $eid[$i] $source[$i] $target[$i]<br>\n";
  $json.="    {\"id\": \"$eid[$i]\", \"source\": \"$source[$i]\", \"target\": \"$target[$i]\"},\n";
}
$json.="    {\"id\": \"nule\",   \"source\": \"atla\", \"target\": \"atla\"}\n";
$json.="  ]\n";
$json.="}\n";
file_put_contents("esnet.json", "$json");
?>

<html>
<head>
<style type="text/css">
  #container {
    max-width: 800px;
    height: 600px;
    margin: auto;
  }
</style>
</head>
<body>
<h1>APM Map of Large Throughput Flows Across ESNET.</h2>
<form name="input" action="demo_form_action.asp" method="get">
<select name="time">
<?php
for ($i=0; $i<$tcount; $i++) {
  print "<option value=\"$time[$i]\">$time[$i]</option>\n";
}
?>
</select>
<select name="source">
<?php
for ($i=0; $i<$count; $i++) {
  print "<option value=\"$id[$i]\">$name[$i]</option>\n";
}
?>
</select>
<select name="destination">
<?php
for ($i=0; $i<$count; $i++) {
  print "<option value=\"$id[$i]\">$name[$i]</option>\n";
}
?>
</select>


</form>

<!-- sigmajs.org -->
<div id="container"></div>
<script src="./sigma.min.js"></script>
<script src="plugins/sigma.parsers.json.min.js"></script>
<script>
  sigma.parsers.json('./esnet.json', {
    container: 'container',
    settings: {
      defaultNodeColor: '#ec5148'
    }
  });
</script>
</body>
</html>
