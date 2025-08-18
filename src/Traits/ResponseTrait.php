<?php
 
namespace SrvKit\Auth\Traits;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;

trait ResponseTrait{
	protected function autoRespond(array $data, int $statusCode = 200, $template = ''): ResponseInterface
	{	
		/** @var IncomingRequest $request [description] */
		$request = service('request');

		/** @var ResponseInterface $response [description] */
		$response = service('response');

		// It will check 'Accept:' value from header
		$accept = $request->negotiate('media', ['text/html', 'application/json', 'application/xml']);

		$body = json_encode($data, JSON_PRETTY_PRINT);
		$template = config('Auth')->views[$template] ?? '';
		if(!empty($template)){
			$body = view($template, $data);
		}

		return match ($accept) {
			'text/html' => $response->setStatusCode($statusCode)->setHeader('Content-Type', 'text/html')->setBody($body),
			'application/json' => $response->setStatusCode($statusCode)->setJSON($data),
			'application/xml' => $response->setStatusCode($statusCode)->setHeader('Content-Type', 'application/xml')->setBody($this->arrayToXml($data)),
			default => $response->setStatusCode($statusCode)->setBody(json_encode($data)),
		};
	}

	protected function arrayToXml(array $data, \SimpleXMLElement $xml = null): string
	{
		if ($xml === null) {
			$xml = new \SimpleXMLElement('<response/>');
		}

		foreach ($data as $key => $value) {
			is_array($value)
			? $this->arrayToXml($value, $xml->addChild($key))
			: $xml->addChild(is_numeric($key) ? "item$key" : $key, htmlspecialchars((string) $value));
		}

		return $xml->asXML();
	}
}