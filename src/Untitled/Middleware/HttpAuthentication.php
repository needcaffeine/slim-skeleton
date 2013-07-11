<?php

namespace Untitled\Middleware;

use Slim\Middleware;

class HttpAuthentication extends Middleware
{
	public function call()
	{
		$request = $this->app->request();
		$response = $this->app->response();
		$authUser = $request->headers('PHP_AUTH_USER');
		$authPass = $request->headers('PHP_AUTH_PW');

		// If a username is passed in, verify that it is valid.
		if (!empty($authUser)) {

			global $em;
			$identity = $em->getRepository('\Users\Entity\Application')->find($authUser);

			if ($identity) {
				$this->next->call();
			}
		}
		
		if (!$identity) {
			$response->status(401);
			$response->header('WWW-Authenticate', 'Basic realm="Authentication required."');
		}
	}
}
