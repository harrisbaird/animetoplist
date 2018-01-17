<?php
App::import('Core', array('Xml', 'HttpSocket'));
/**
 * Twitter api implememntation. All return index with first letter capital.
 *
 * @author Pedro Valencia
 * @email pvalencia+twittercomponent@gmail.com
 */

class TwitterComponent extends Object {
	/*
	 * Username of your twitter account
	 */
	var $username = '';

	/*
	 * Password of your twitter account
	 */
	var $password = '';

	/*
	 * Headers to be sent to twitter
	 */
	var $headers = array(
		'X-Twitter-Client' => 'CakePHP Twitter Component',
		'X-Twitter-Client-Version' => '1.0'
	);


	/**
	 * Generic request to twitter api.
	 *
	 * @param type Type of twitter request.
	 * @param params Params to use in the request to twitter api.
	 */
	private function request($type, $params = array()) {
		/* unset NULLs from params */
		foreach($params as $k => $p)
			if($p === NULL)
				unset($params[$k]);

		/* Choose method */
		$method = '';
		switch($type) {
			case 'statuses/user_timeline':
			case 'statuses/friends_timeline':
			case 'statuses/public_timeline':
			case 'statuses/mentions':
			case 'statuses/show':
			case 'users/show':
			case 'statuses/friends':
			case 'statuses/followers':
				$method = 'GET';
				break;
			case 'statuses/update':
				$method = 'POST';
				break;
			case 'statuses/destroy':
				$method = 'DELETE';
				break;
			default:
				return false;
		}

		$socket = new HttpSocket();

		$result = $socket->request(
			array(
				'method' => $method,
				'uri' => array(
					'scheme' => 'http',
					'host' => "twitter.com/{$type}.xml",
					'query' => $params
				),
				'auth' => array(
					'method' => 'Basic',
					'user' => $this->username,
					'pass' => $this->password
				),
				'header' =>	$this->headers,
				'version' => '1.1'
			)
		);
		if(!$result) return false;

		$xml = new XML($result); /* parse xml */
		$xml = Set::reverse($xml); /* array from parsed xml */

		//if($xml === false && !(isset($xml['Statuses'] || isset($xml['Status'])))) return false;
		return $xml;
	}

	/**
	 * Return the 20 most recent statuses.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-public_timeline
	 */
	function statusesPublicTimeline() {
		return $this->request('statuses/public_timeline');
	}

	/**
	 * Get the user timeline
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-user_timeline
	 */
	function statusesUserTimeline($id, $since_id = NULL, $max_id = NULL, $count = 20, $page = 1) {
		return $this->request('statuses/user_timeline', array(
			'id' => $id,
			'since_id' => $since_id,
			'max_id' => $max_id,
			'count' => $count,
			'page' => $page
		));
	}

	/**
	 * Get the friends timeline.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-user_timeline
	 */
	function statusesFriendsTimeline($since_id = NULL, $max_id = NULL, $count = 20 , $page = 1) {
		return $this->request('statuses/friends_timeline', array(
			'since_id' => $id,
			'count' => $count,
			'page' => $page
		));
	}

	/**
	 * Return mentions for the authenticated user.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses-mentions
	 */
	function statusesMentions($since_id = NULL, $max_id = NULL, $count = 20, $page = 1) {
		return $this->request('statuses/mentions', array(
			'since_id' => $since_id,
			'max_id' => $max_id,
			'count' => $count,
			'page' => $page
		));
	}

	/**
	 * Return a single status.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0show
	 */
	function statusesShow($id) {
		return $this->request('statuses/show', array(
			'id' => $id
		));
	}

	/**
	 * Update your status in twitter.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0update
	 */
	function statusesUpdate($status, $in_reply_to_status_id = NULL) {
		return $this->request('statuses/update', array(
			'status' => $status,
			'in_reply_to_status_id' => $in_reply_to_status_id
		));
	}

	/**
	 * Destroy a status.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0destroy
	 */
	function statusesDestroy($id) {
		return $this->request('statuses/destroy', array(
			'id' => $id
		));
	}


	/**
	 * Return extended information of a given user
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-users%C2%A0show
	 */
	function usersShow($id) {
		return $this->request('users/show', array(
			'id' => $id
		));
	}

	/**
	 * Return user's friends with the status inline.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0friends
	 */
	function statusesFriends($id, $page = 1) {
		return $this->request('statuses/friends', array(
			'id' => $id,
			'page' => $page
		));
	}

	/**
	 * Returns the authenticating user's followers.
	 *
	 * @see http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-statuses%C2%A0followers
	 */
	function statusesFollowers($id, $page = 1) {
		return $this->request('statuses/followers', array(
			'id' => $id,
			'page' => $page
		));
	}

}
?>