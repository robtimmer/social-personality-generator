<?php
use Facebook\Facebook as Facebook;
use Facebook\Exceptions\FacebookResponseException as FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException as FacebookSDKException;
use GuzzleHttp\Client as GuzzleHttpClient;

class SocialPersonalityGenerator
{
    /**
     * Facebook Application configuration
     * @var array
     */
    private $facebookAppConfig = array(
        'app_id' => '1769515346629314',
        'app_secret' => '<INSERT-APP-SECRET>',
        'default_graph_version' => 'v2.7',
        'enable_beta_mode' => false,
        'http_client_handler' => null,
        'persistent_data_handler' => null,
        'pseudo_random_string_generator' => null,
        'url_detection_handler' => null,
    );

    /**
     * IBM Watson API endpoint configuration
     * @var array
     */
    private $IBMWatsonConfig = array(
        'endpoint' => 'https://gateway.watsonplatform.net/personality-insights/api/v2/profile',
        'auth' => array(
            'username' => 'b0e950d0-4edb-4924-a489-7aa6073e7afc',
            'password' => '<INSERT-WATSON-PASSWORD>'
        )
    );

    /**
     * Minimal content word count
     * @var int
     */
    private $contentWordCountMinimum = 200;

    /**
     * Initializes an Facebook application for SDK usage
     * @return \Facebook\Facebook
     */
    public function initializeFacebookApp()
    {
        return new Facebook($this->facebookAppConfig);
    }

    /**
     * Reads the Facebook posts content and returns it in a string
     * @param $facebookApp
     * @param $accessToken
     * @return string
     */
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

    /**
     * Reads an generated profile from the IBM Watson API by given content
     * @param $contentString
     * @return string
     */
    public function getWatsonProfile($contentString)
    {
        $client = new GuzzleHttpClient();
        //Open an new memory stream for writing and reading the Watson response attachments
        $memoryWriteStream = fopen('php://memory', 'w');

        //Make an request to the Watson API
        $client->post($this->IBMWatsonConfig['endpoint'], [
            'auth' => [
                $this->IBMWatsonConfig['auth']['username'],
                $this->IBMWatsonConfig['auth']['password']
            ],
            'headers' => [
                'content-type' => 'text/plain',
                'Content-Language' => 'en',
                'Accept' => 'application/json',
                'Accept-Language' => 'en'
            ],
            'body' => $contentString,
            'verify' => false,
            'save_to' => $memoryWriteStream
        ]);

        //Read the stream resource contents and return it
        return stream_get_contents($memoryWriteStream);
    }
}