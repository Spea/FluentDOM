<?php
require(dirname(__FILE__).'/../../../vendor/autoload.php');

$_ = FluentDOM::create();
echo $_('ul');