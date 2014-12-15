<?php

$res = curl_init("https://api.mojang.com/users/profiles/minecraft/subdavis");
$json_a = json_decode($res,true);
echo $json_a[name];
echo $json_a[id];
?>
