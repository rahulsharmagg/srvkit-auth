<?php
declare(strict_types=1);

class Vite{

	protected ?string $host;

	protected ?int $port;

	public function __construct(string $host, int $port)
	{
		$this->host = $host;
		$this->port = $port;
	}

	public function isRunning() {
	    $connection = @fsockopen($this->host, $this->port, $errno, $errstr, 1); // 1-second timeout
	    
	    if ($connection) {
	        fclose($connection);
	        return true;
	    }

	    return false;
	}
}


if (! function_exists('vite')) {
    
    /**
     * 
     * @param  int|null $port [description]
     * @return Vite         [description]
     */
    function vite(?string $host = 'localhost', ?int $port = 5173): Vite
    {	
        return new Vite('localhost', $port);
    }
}