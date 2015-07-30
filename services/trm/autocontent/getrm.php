<?php
$json = file_get_contents('http://104.236.116.23/services/trm/autocontent/trm.php?economy=TRM');
$trm=json_decode($json, true);
print_r($trm['value']);
