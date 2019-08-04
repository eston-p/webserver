<?php

namespace Eston\PHPServer;


class Request
{
	protected $method = null;
	protected $uri = null;
	protected $parameters = [];
	protected $header = [];

	public function __construct($method, $uri, $headers = []) {
		$this->headers = $headers;
		$this->method = strtoupper($method);

		// Split uri and parameters string
		@list($this->uri, $params) = explode('?', $uri);

		// Parse the parameters
		parse_str($params, $this->parameters);
	}

	public static function withHeaderString($header)
	{
		// Explode the string into lines
		$lines = explode('\n', $header);

		// Extract the method and uri
		print_r($lines);
		die();
		list($method, $uri) = explode(' ', array_shift($lines));

		$headers = [];

		foreach($lines as $line) {

			// Clean the line
			$line = trim($line);

			if(strpos($line, ': ') !== false) {
				list($key, $value) = explode(': '. $line);
				$headers[$key] = $value;
			}
		}

		return new static($method, $uri, $headers);
	}


	public function method()
	{
		return $this->method;
	}


	public function uri()
	{
		return $this->uri;
	}


	public function header($key, $defaut = null)
	{
		if(!isset($this->headers[$key])) {
			return $default;
		}

		return $this->headers[$key];
	}

	public function param($key, $default = null)
	{
		if(!isset($this->parameters[$key])) {
			return $default;
		}

		return $this->parameters[$key];
	}
}
