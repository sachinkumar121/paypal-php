<?php
echo date_default_timezone_get();
echo date("Y-m-d\TH:i:s\Z");
date_default_timezone_set("UTC");
echo date("Y-m-d\TH:i:s\Z");

require_once('dbConfig.php');
echo gmdate("Y-m-d\TH:i:s\Z");
echo USER;