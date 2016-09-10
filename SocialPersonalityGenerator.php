<?php
use Facebook\Facebook as Facebook;
use Facebook\Exceptions\FacebookResponseException as FacebookResponseException;
use FAcebook\Exceptions\FacebookSDKException as FacebookSDKException;

class SocialPersonalityGenerator
{

    private $facebookAppConfig = array(
        'app_id' => '1769515346629314',
        'app_secret' => '<INSERT APP SECRET>',
        'default_graph_version' => 'v2.7',
        'enable_beta_mode' => false,
        'http_client_handler' => null,
        'persistent_data_handler' => null,
        'pseudo_random_string_generator' => null,
        'url_detection_handler' => null,
    );

    private $contentWordCountMinimum = 200;

    /**
     * Initializes an Facebook application for SDK usage
     * @return \Facebook\Facebook
     */
    public function initializeFacebookApp()
    {
        return new Facebook($this->facebookAppConfig);
    }

    public function getFacebookUserPosts($facebookApp, $accessToken)
    {
        try {
            $response = $facebookApp->get('/me/feed', $accessToken);
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        //Decode the response
        $decodedResponse = $response->getDecodedBody();

        //Extract the content
        $contentString = '';
        foreach($decodedResponse['data'] as $post) {
            if(!isset($post['message'])) {
                continue;
            }
            $contentString .= $post['message'] . "\n";
        }

        //Validate the amount of words
        if($t = str_word_count($contentString) <= $this->contentWordCountMinimum) {
            throw new InvalidArgumentException("To few words in determined content");
        }
        return $contentString;
    }

}