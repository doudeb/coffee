<?php
/**
 * Yammer OAuth2 Class
 *
 */
class Yammer {

	public $consumerKey;
	public $consumerSecret;
	public $oauthToken;
	public $oauthTokenSecret;
	public $callbackUrl;

	protected $authToken;

	/**
	 * Class Constructor
	 *
	 * @param array $config
	 */
	function __construct($config) {
		$this->consumerKey = $config['consumer_key'];
		$this->consumerSecret = $config['consumer_secret'];
		$this->callbackUrl = $config['callbackUrl'];

		/* Set Up OAuth Consumer */
		if (isset($config['oauth_token']) || $config['oauth_token_secret']):
			$this->oauthToken = $config['oauth_token'];
			$this->oauthTokenSecret = $config['oauth_token_secret'];
		endif;
	}

	/**
	 * Get Authorization Url
	 *
	 * @param string $callbackUrl
	 * @return $url
	 */
	function getAuthorizationUrl($callbackUrl = null) {

		/* Override if needed, else assume it was set at __construct() */
		if ($callbackUrl)
			$this->callbackUrl = $callbackUrl;

		/* Authorization URL */
		$url = sprintf('https://www.yammer.com/dialog/oauth?client_id=%s&redirect_uri=%s',
			$this->consumerKey,
			urlencode($this->callbackUrl)
		);

		return $url;
	}

	/**
	 * Get Access Token
	 *
	 * @param string $code
	 * @param string $isRefresh
	 * @return $response
	 */
	function getAccessToken($code = null, $isRefresh = false) {



        $data['code'] = $code;
        $data['client_id'] = $this->consumerKey;
        $data['client_secret'] = $this->consumerSecret;


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.yammer.com/dialog/oauth?client_id=[:client_id]&redirect_uri=[:redirect_uri]');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

        var_dump($response);

		if (in_array(curl_getinfo($ch, CURLINFO_HTTP_CODE), array(400,401)) ):
			$t_response = json_decode($response);
			if (isset($t_response->error) && $t_response->error != '')
				$response = $t_response->error;
			throw new YammerPHPException('Server: ' . $response, curl_getinfo($ch, CURLINFO_HTTP_CODE));
		endif;

		$response = json_decode($response);
		return $response;

	}

	/**
	 * Set OAuth token
	 *
	 * @param string $token
	 */
	function setOAuthToken($token = null) {
		$this->oauthToken = $token;
	}

	/**
	 * Get Resource
	 *
	 * @param string $resource - Path (after ../v0/) of the resource you're accessing
	 * @param array $data - $_GET variables to pass
	 */
	public function get($resource = null, $data = array()) {
		$url = 'https://www.yammer.com/api/v1/' . $resource;
		return $this->request($url, $data);
	}


	/* Helpers */

	/**
	 * Test Authentication
	 *
	 * @return boolean
	 */
	function testAuth() {
		$url = 'https://www.yammer.com/api/v1/messages/following.json';
		try {
			$result = $this->request($url);
			return true;
		} catch (YammerPHPException $e) {
			return false;
		}
	}


	// @todo: Add more helpers


	/* Private request method */

	/**
	 * Request Resource
	 *
	 * @param string $url
	 * @param array $data
	 * @return $return
	 */
	private function request($url, $data = array(),$isPost = false) {

		$headers = array();
		$headers[] = "Authorization: Bearer " . $this->oauthToken;

		$ch = curl_init();
		$data_stream = $url . '?' . http_build_query($data);
		//curl_setopt($ch, CURLOPT_URL, $data_stream);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		if($isPost)
		{
		    curl_setopt($ch, CURLOPT_POST,TRUE);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		    curl_setopt($ch, CURLOPT_URL, $url);
		}
		else
    		curl_setopt($ch, CURLOPT_URL, $data_stream);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);

		// Throw exception on no response from server
		if (!$output)
			throw new YammerPHPException('No response from server.', curl_getinfo($ch, CURLINFO_HTTP_CODE) );

		$return = json_decode($output);

		// Throw an exception on error
		if (isset($return->BODY->H2) && $return->BODY->H2 == 'Error 401')
			throw new YammerPHPException($return->BODY->H1, 401);

		return $return;
	}

	/**
	 * @see http://developer.yammer.com/restapi/
	 */
    function postMessage($body,$group_id=0,$replied_to_id = 0) {
		$url = 'https://www.yammer.com/api/v1/messages.json';
		$data = array(
		    'body' => $body,
		);

		if($group_id)
   	        $data['group_id'] =  $group_id;

   	    if($replied_to_id)
   	        $data['replied_to_id'] = $replied_to_id;

		error_log("posting to $url");
		try {
			$result = $this->request($url,$data,true);
			#print_r($result);
			$msg =  $result->messages[0];
			return $msg->id;
		} catch (YammerPHPException $e) {
			return false;
		}
	}


}

/**
 * Yammer Exception Class
 */
class YammerPHPException extends Exception {
	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}

//print_r($yammer->get('users/9596375.json'));
