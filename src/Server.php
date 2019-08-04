<?php

namespace Eston\PHPServer;

use Exception;

class Server
{
	protected $host = null;
	protected $port = null;
	protected $socket = null;


	public function __construct($host, $port) {
		$this->host = $host;
		$this->port = (int)$port;

		// Create a socket
		$this->createSocket();

		// Bind the socket
		$this->bind();
	}

	protected function createSocket()
	{
		$this->socket = socket_create( AF_INET, SOCK_STREAM, 0);
	}

	protected function bind()
	{
		if (!socket_bind($this->socket, $this->host, $this->port)) {
			throw new Exception('Could not bind: ' . $this->host . ':'  . $this->port .' - ' . socket_strerror( socket_last_error())); 
		}
	}

	public function listen($callback)
	{
		// Check if the callback is valid. Throw and exception if not
		if(!is_callable($callback)) {
			throw new Exception('The given argument should be callable.');
		}

		while(1) {

			// Listen for connections
			socket_listen($this->socket);

			// Try to get the client socket resource
			// If false we got an error close the connection and skip
			if(!$client = socket_accept($this->socket)) {
				socket_close($client);
				continue;
			}

			// Create new request instance with the clients header
			// In the real world of course you cannot just fix the max size to 1024
			$request = Request::withHeaderString(socket_read($client, 1024));

			// Execute the callback
			$response = call_user_func($callback, $request);

			// Check if we really received an Response Object
			// If not return a 404 response object
			if(!$response || !$response instanceof Response) {
				$response = Response::error(404);
			}

			// Make a string out of our response
			$response = (string) $response;

			// Write the response to the client socket
			socket_write($client, $response, strlen($response));

			// Close the connection so we can accept new ones
			socket_close($client);
		}

	}
		
}
