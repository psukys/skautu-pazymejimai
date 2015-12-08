<?php 

Router\Helper::map("pagrindinis", array(
    "(/|/pagrindinis)/?"    => array("get" => "pagrindinis")
));

Router\Helper::map("forma", array(
    "/forma/"   =>  array("get" => "forma", "post" => "saugoti"),
    "/bukle/"    => array("get" => "bukle")
));

Router\Helper::map("administravimas", array(
    "/administravimas/" => array("get" => "administravimas", "post" => "administravimas"),
    "/admin/"   => array("get" => "administravimas", "post" => "administravimas"),
    "/prisijungimas/"   => array("get" => "prisijungimas", "post" => "prisijungimas"),
    "/atsijungimas/"    => array("get" => "atsijungimas")   
));

// For more(more!!) examples see : //
// https://gist.github.com/fidelisrafael/6592558 //

?>