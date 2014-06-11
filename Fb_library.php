<? 
/* ------------------------------------------------
|
| Facebook Library v2.1
| Created by Ann Eliese Grey
| Licensed under the MIT License
| Distributed AS IS/WITHOUT WARRANTY
|
| IN DEVELOPMENT/UNSTABLE
|
--------------------------------------------------*/

//include facebook SDK
require_once("facebook.php");

class Fb_library {
	
	//define public variables
	public $facebook;
	public $fbsession;
	public $fbid = 0;
	public $access_token;
	
	/* ------------------------------------------------
	|
	| Function Facebook - Base Facebook function init
	| Parameters can be set in constants or sent in on
	| library initiation so that multiple applications
	| can be hosted in one application. 
	| VARIABLES:
	| fbparams optional array
	| 	- fbappid - id of the application
	|   - fbappsecret - secret key of the application
	|
	| ** AUTO LOGIN IS NOW CONTROLLED IN THE APPLICATION SETTINGS! **
	|
	--------------------------------------------------*/
	function Fb_library($fbparams = "") {
		
		//set constants if unique parameters don't exist
		if(!$fbparams) {
			$fbparams = array(
				'appId' => FACEBOOK_APP,
				'secret' => FACEBOOK_SECRET			
				);
		}
		
		//initiate facebook SDK with application settings
		$this->facebook = new Facebook($fbparams);
		$this->facebook->setFileUploadSupport(true);
	}
	
	//Functions name format: actionItem (ex. getFriends)
	/* ------------------------------------------------
	|
	| Function getRequest - get basic info
	| returns page like, user location and age minimum
	|
	--------------------------------------------------*/
	function getRequest() {
		$request = $this->facebook->getSignedRequest();
		
		//check for like
		if(isset($request['page']['id'])) {
			$req['page'] = $request['page']['id'];
			$req['userlike'] = $request['page']['liked'];
		} else {
			$req['page'] = 0;
			$req['userlike'] = FALSE;
		}
		
		//check for user info
		if(isset($request['user'])) {
			$req['userlocale'] = $request['user']['country'];
			$req['userage'] = $request['user']['age']['min'];
		} else {
			$req['userlocale'] = 'us';
			$req['userage'] = 1;
		}
		
		return $req;
	}
	
	/* ------------------------------------------------
	|
	| Function getUser - gets users info
	| PERMS REQUIRED: basic 
	|
	--------------------------------------------------*/
	function getUser() {
		
		//get login url
		//$loginparams = array('scope' => FACEBOOK_PERMS);
		//$loginURL = $this->facebook->getLoginUrl($loginparams);
		
		//check for logged in user
		$fbid = $this->facebook->getUser();
		
		if($fbid == 0) {
			//user is not logged in, return null
			return 0;
		} else {
			//user is logged in, get info
			$user['fbid'] = $fbid;
			$user['user'] = $this->facebook->api('/me');
			return $user;
		}
	}
	
	/* ------------------------------------------------
	|
	| Function getPage - gets page info
	|
	--------------------------------------------------*/
	function getPage($pageid) {
		try {
			$page = $this->facebook->api('/'.$pageid.'/', 'GET');
  		} catch (FacebookApiException $e) {
			log_message('error', 'Facebook Library - Error: ' . $e);
			$page = $e;
  		}
			return $page;
	}
	
	/* ------------------------------------------------
	|
	| Function postWall - makes a post on the users wall
	| PERMS REQUIRED: publish_actions
	| VARIABLES:
	|   - message
	|   - picture(optional) 
	|   - link(optional) 
	|   - name(optional)
	|   - caption(optional)
	|   - description(optional)
	|
	--------------------------------------------------*/
	function postWall($data) {
		
		//message is required
		if(!$data['message']) {
			$data['message'] = '!';
		}
		
		try {
			$response = $this->fb->api('/me/feed', 'POST', $data);
		} catch (FacebookApiException $e) {
			log_message('error', 'Facebook Library - Error: ' . $e);
			$response = $e;
		}
			
		return $response;
	}
	
	/* ----------------------------------------------
	|
	| Function postPhoto - Posts photo 
	| PERMS REQUIRED: publish_actions
	| VARIABLES:
	|   - photo (relative to php doc)
	|   - message (optional)
	|
	 --------------------------------------------- */
	function postPhoto($data) {	
		
		$this->fb->setFileUploadSupport(true); 
		$photo = array( 'source' => '@' . realpath($data['photo']), 'message' => $data['message'] );
		
		try {
			$response = $this->fb->api('/me/photos', 'POST', $photo);
		} catch (FacebookApiException $e) {
			log_message('error', 'Facebook Library - Error: ' . $e);
			$response = $e;
		}
		return $response;
	}
	
	/* ----------------------------------------------
	|
	| Function postVideo - Posts video to page 
	| PERMS REQUIRED: publish_actions
	| VARIABLES:
	|   - video (relative to php doc)
	|   - message (optional)
	|
	 --------------------------------------------- */
	function postVideo($data) {	
		
		$this->fb->setFileUploadSupport(true); 
		$photo = array( 'source' => '@' . realpath($data['video']), 'message' => $data['message'] );
		
		try {
			$response = $this->fb->api('/me/videos', 'POST', $photo);
		} catch (FacebookApiException $e) {
			log_message('error', 'Facebook Library - Error: ' . $e);
			$response = $e;
		}
		return $response;
	}
	
	/* ----------------------------------------------
	|
	| Function getFriends - gets users friend list
	| PERMS REQUIRED: basic
	|
	 --------------------------------------------- */
	function getFriends() {		
		try {
			$response = $this->fb->api('/me/friends', 'GET');
		} catch (FacebookApiException $e) {
			log_message('error', 'Facebook Library - Error: ' . $e);
			$response = $e;
		}
	return $response;
	}	
	
	/* ----------------------------------------------
	|
	| Function getAlbums - gets users albums
	| PERMS REQUIRED: user_photos
	|
	 --------------------------------------------- */
	function getAlbums() {
		try {
			$response = $this->fb->api('/me/albums', 'GET');
		} catch (FacebookApiException $e) {
			log_message('error', 'Facebook Library - Error: ' . $e);
			$response = $e;
		}
		return $response;
	}
	
	/* ----------------------------------------------
	|
	| Function getPhotos - gets users photos from an album
	| PERMS REQUIRED: user_photos
	| VARIABLES:
	|   - album (album ID)
	|
	 --------------------------------------------- */
	function getPhotos($album) {
		try {
			$response = $this->fb->api('/'. $album .'/photos', 'GET');
		} catch (FacebookApiException $e) {
			log_message('error', 'Facebook Library - Error: ' . $e);
			$response = $e;
		}
		return $response;
	}
}
