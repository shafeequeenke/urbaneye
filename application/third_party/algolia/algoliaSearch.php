<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// composer autoload
require  __DIR__.'/../../../vendor/autoload.php';
/**
 * algolia search
 */
class algoliaSearch
{
	
	protected $CI;

	protected $client;

	protected $appId;

	protected $adminApiKey;

	protected $algoliaIndex;

	protected $firestoreObj;

	function __construct()
	{
		$this->CI =& get_instance();

		$this->appId 		=	$this->CI->config->item('algoliaAppId');

		$this->adminApiKey 	=	$this->CI->config->item('algoliaAdminKey');

		$this->client 		=	Algolia\AlgoliaSearch\SearchClient::create(
			  $this->appId,
			  $this->adminApiKey
			);

		$this->CI->load->library('firestore');
    	$this->firestoreObj 		=	$this->CI->firestore;
	}

	public function searchFullText($indexName,$searchText) {
		$this->algoliaIndex 	= 	$this->client->initIndex($indexName);
		// Search for a text given
		$searchResult 			=	$this->algoliaIndex->search($searchText);
		return $searchResult;
	}

	public function importToAlgolia($indexName,$data) {
		
		$this->algoliaIndex 	= 	$this->client->initIndex($indexName);
		
		$batch 	= 	json_decode($data, true);
		p($batch,"cc");
		return $this->algoliaIndex->saveObjects($batch, ['autoGenerateObjectIDIfNotExist' => true]);

	}

	public function deleteQuestion($indexName,$questionId) {
		$this->algoliaIndex 	= 	$this->client->initIndex($indexName);
		// Search for a text given
		$response 			=	$this->algoliaIndex->deleteObject($questionId);
		return $response;
	}
}