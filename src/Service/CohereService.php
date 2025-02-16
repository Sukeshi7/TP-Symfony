<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CohereService
{
	private Client $client;
	private string $apiKey;
	
	public function __construct(string $apiKey)
	{
		$this->client = new Client();
		$this->apiKey = $_ENV['COHERE_API_KEY'] ?? '';
	}
	
	public function generateText(string $prompt): ?array
	{
		try {
			$response = $this->client->post('https://api.cohere.com/v1/generate', [
					'json' => [
							'model' => 'command',
							'prompt' => $prompt,
					],
					'headers' => [
							'Authorization' => 'Bearer ' . $this->apiKey,
							'Content-Type' => 'application/json',
					],
					'verify' => false,
			]);
			
			$body = json_decode($response->getBody(), true);
			$generatedText = $body['generations'][0]['text'] ?? null;
			
			if ($generatedText) {
				// Séparer le texte en deux parties : titre et contenu
				preg_match('/Titre:\s*(.*?)\s*Article:\s*(.*)/s', $generatedText, $matches);
				return [
						'title' => trim($matches[1] ?? ''),
						'article' => trim($matches[2] ?? '')
				];
			}
			
			return null;
		} catch (GuzzleException $e) {
			dump($e->getMessage());
			return null;
		}
	}
}