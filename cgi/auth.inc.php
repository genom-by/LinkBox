<?php
if (isset($_REQUEST[session_name()])) session_start();

include_once 'authroutines.php';
include_once 'auth.class.php';