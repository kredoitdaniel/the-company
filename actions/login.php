<?php

include "../classes/User.php";

// create object
$user = new User;

$user->login($_POST);