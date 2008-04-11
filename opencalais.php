<?php

/**
* Open Calais Tags
* Last updated 4/7/2008
* Copyright (c) 2008 Dan Grossman 
* http://www.dangrossman.info
* 
* Please see http://www.dangrossman.info/open-calais-tagsopen-calais-tags
* for documentation and license information.
*/

class OpenCalaisException extends Exception {}

class OpenCalais {

	private $apikey;
	private $allowDistribution = false;
	private $allowSearch = false;
	private $externalID = '';
	private $submitter = 'Open Calais Tags';
	private $contentType = 'text/html';
	private $outputFormat = 'xml/rdf';
	private $prettyTypes = true;
	private $entities;
	
	public function OpenCalais($apikey = null) {
		if (empty($apikey)) {
			throw new OpenCalaisException("You must provide an OpenCalais API key to use this class.");
		} else {
			$this->apikey = $apikey;
		}
	}
	
	public function getAllowDistribution() { return $this->allowDistribution; } 
	public function getAllowSearch() { return $this->allowSearch; } 
	public function getExternalID() { return $this->externalID; } 
	public function getSubmitter() { return $this->submitter; } 
	public function getContentType() { return $this->contentType; } 
	public function getOutputFormat() { return $this->outputFormat; } 
	public function getPrettyTypes() { return $this->prettyTypes; }
	public function setAllowDistribution($x) { $this->allowDistribution = $x; } 
	public function setAllowSearch($x) { $this->allowSearch = $x; } 
	public function setExternalID($x) { $this->externalID = $x; } 
	public function setSubmitter($x) { $this->submitter = $x; } 
	public function setContentType($x) { $this->contentType = $x; } 
	public function setOutputFormat($x) { $this->outputFormat = $x; }
	public function setPrettyTypes($x) { $this->prettyTypes = $x; }
	
	public function getEntities($content) {
	
		$response = $this->callAPI($content);
		
		$xml = substr($response, strpos($response, 'c:document'));
		$matches = preg_match_all('#' . preg_quote('<!--', '#') . '(.*?)' . preg_quote('-->', '#') . '#ms', $xml, $rdf, PREG_SET_ORDER);

		foreach ($rdf as $key => $val) {
			if (strpos($val[1], ": ") !== false) {
				$parts = split(": ", $val[1]);
				$this->addEntity($parts[0], $parts[1]);
			}
		}
		
		return $this->entities;

	}
	
	private function addEntity($key, $val) {
	
		$entityTypes = array('Anniversary' => 'Anniversary',
							 'City' => 'City',
							 'Company' => 'Company',
							 'Continent' => 'Continent',
							 'Currency' => 'Currency',
							 'EmailAddress' => 'Email Address',
							 'Facility' => 'Facility',
							 'FaxNumber' => 'Fax Number',
							 'Holiday' => 'Holiday',
							 'IndustryTerm' => 'Industry Term',
							 'NaturalDisaster' => 'Natural Disaster',
							 'NaturalFeature' => 'Natural Feature',
							 'Organization' => 'Organization',
							 'Person' => 'Person',
							 'PhoneNumber' => 'Phone Number',
							 'ProvinceOrState' => 'Province or State',
							 'Region' => 'Region',
							 'Technology' => 'Technology',
							 'URL' => 'URL');
							 
		$key = trim($key);
		$val = trim($val);
		
		if (!array_key_exists($key, $entityTypes)) {
			return;
		} else {
			if ($this->prettyTypes) {
				$key = $entityTypes[$key];
			}
		}
	
		if (isset($this->entities[$key])) {
			
			if (!in_array($val, $this->entities[$key])) {
				$this->entities[$key][] = $val;
			}
			
		} else {
			$this->entities[$key][] = $val;		
		}	
	
	}
	
	private function callAPI($content, $title = null) {
	
		$postdata['licenseID'] = $this->apikey;
	
		$postdata['paramsXML'] = 
			  '<c:params xmlns:c="http://s.opencalais.com/1/pred/"'
			. ' xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">'
			. '	<c:processingDirectives c:contentType="' . $this->contentType
			. '" c:outputFormat="' . $this->outputFormat . '"></c:processingDirectives>'
			. '	<c:userDirectives c:allowDistribution="' . $this->allowDistribution 
			. '" c:allowSearch="' . $this->allowSearch . '" c:externalID="' . $this->externalID 
			. '" c:submitter="' . $this->submitter . '"></c:userDirectives>'
			. '	<c:externalMetadata></c:externalMetadata>'
			. '</c:params>';
		
		if (!empty($content)) {
			$postdata['content'] = $content;
		} else {
			throw new OpenCalaisException("Content to analyze is empty.");
		}
		
		$poststring = $this->urlencodeArray($postdata);
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.opencalais.com/enlighten/calais.asmx/Enlighten");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $poststring);
		curl_setopt($ch, CURLOPT_POST, 1);
		$response = html_entity_decode(curl_exec($ch));
		
		if (strpos($response, "<Exception>") !== false) {
			$text = preg_match("/\<Exception\>(.*)\<\/Exception\>/mu", $response, $matches);
			throw new OpenCalaisException($matches[1]);
		}
		
		return $response;
		
	}
	
	private function urlencodeArray($array) {
		foreach ($array as $key => $val) {
			if (!isset($string)) {
				$string = $key . "=" . urlencode($val);
			} else {
				$string .= "&" . $key . "=" . urlencode($val);
			}
		}
		return $string;
	}

}

?>
