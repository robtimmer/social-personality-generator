<?php
//Autoload
require 'vendor/autoload.php';
//Include class
include 'SocialPersonalityGenerator.php';

$facebookUserAccessToken = '<INSERT-USER-ACCESS-TOKEN>';

$socialPersonalityGenerator = new SocialPersonalityGenerator();
//Initialize Facebook application
$facebookApp = $socialPersonalityGenerator->initializeFacebookApp();
//Gather the needed user posts
$contentString = $socialPersonalityGenerator->getFacebookUserPosts($facebookApp, $facebookUserAccessToken);
//Get Watson profile
$watsonProfile = $socialPersonalityGenerator->getWatsonProfile($contentString);

//JSON response output
header('Content-Type: application/json');
echo $watsonProfile;
die();