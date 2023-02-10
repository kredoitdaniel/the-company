<?php

include "../classes/User.php";

// create object
$user = new User;

// $_POST holds all the data from the form in views/register.php
$user->store($_POST);