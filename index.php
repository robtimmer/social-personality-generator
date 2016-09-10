<?php
//Autoload
require 'vendor/autoload.php';
//Include class
include 'SocialPersonalityGenerator.php';

$socialPersonalityGenerator = new SocialPersonalityGenerator();
//Initialize Facebook application
$facebookApp = $socialPersonalityGenerator->initializeFacebookApp();


$userAccessToken = '<INSERT ACCESS TOKEN>';
//Gather the needed user posts
echo '<pre>';
print_r($socialPersonalityGenerator->getFacebookUserPosts($facebookApp, $userAccessToken));