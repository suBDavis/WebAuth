<?php
// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_URL => 'https://api.mojang.com/users/profiles/minecraft/subdavis',
  CURLOPT_USERAGENT => 'Codular Sample cURL Request'
));
// Send the request & save response to $resp
$resp = curl_exec($curl);

echo $resp;
// Close request to clear up some resources
curl_close($curl);

/*$json_a = json_decode($res,true);
echo $json_a[name];
echo $json_a[id];*/
?>
