<?php

@header("Content-Type: text/plain");

include_once "models/Utils.php";
include_once "config.php";
include_once "models/Base.php"; //echo " ". password_verify('meh', '$2y$10$xSBlLeLa0IN23KOEjdZKt.vUouYpjzVkKMYYbe/m2u2wzJxoSgcwC');

DBquery::init($dbs, array("nplite"));	
Requester::init();
Router::run();




