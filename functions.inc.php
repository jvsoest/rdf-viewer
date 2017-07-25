<?php	
	function getBaseUrl(){
		return sprintf(
			"%s://%s/",
			isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
			$_SERVER['SERVER_NAME']
		);
	}
	
	function getViewForUri($resourceUri, $views, $sparql_endpoint, $renderExternalUri) {
		$viewToUse = $views["default"];
		
		$prefixes = "PREFIX rdfs:<http://www.w3.org/2000/01/rdf-schema#> "
			."PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#> ";
		$resourceInfoQuery = "$prefixes SELECT ?resource ?resourceLabel ?resourceClass ?resourceClassLabel WHERE { BIND(<$resourceUri> AS ?resource). OPTIONAL { ?resource rdf:type ?resourceClass. }. }";
		$resourceInfo = executeQuery($resourceInfoQuery, $sparql_endpoint);
		
		if(isset($resourceInfo[0]->{"resourceClass"})) {
			$resourceClass = $resourceInfo[0]->{"resourceClass"}->{"value"};
			if(isset($views[$resourceClass])) {
				$viewToUse = $views[$resourceClass];
			}
		}
		
		return $viewToUse;
	}
	
	function executeQuery($query, $endpointUrl) {
		$curl = curl_init($endpointUrl);
		$curl_post_data = array(
			"query" => $query
		);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept:application/sparql-results+json'
		));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
		$curl_response = curl_exec($curl);
		curl_close($curl);    
					   
		file_put_contents("jsonOutput.json", $curl_response);
					   
		$jsonObj = json_decode($curl_response);
		$results = $jsonObj->{'results'};
		$bindings = $results->{'bindings'};
		
		return $bindings;
	}
	
	function parseMyElement($bindingObj, $renderExternalUri=false, $labelObj=NULL) {
		if($bindingObj->{"type"}=="uri") {
			$uri = $bindingObj->{"value"};
			$uriLabel = $bindingObj->{"value"};
			if($renderExternalUri && strpos($uri, $_SERVER['SERVER_NAME'])===false) {
				$uriRedirect = getBaseUrl()."?uri=$uri";
				$uriRedirect = str_replace("#", "HASH", $uriRedirect);
			} else {
				$uriRedirect = $uri;
			}
			
			if(isset($labelObj)) {
				$uriLabel = $labelObj->{"value"};
			}
			
			return "<a href=\"$uriRedirect\">$uriLabel</a>";	
		} else {
			return $bindingObj->{"value"};
		}
		
		return "Resource type unknown";
	}
	
	function getValueForColumn($row, $colName, $renderExternalUri=false, $colLabel=NULL) {
		//only set label column/resource when column label has been given
		$labelRes = NULL;
		if(isset($colLabel)) {
			if(isset($row->{$colLabel})) {
				$labelRes = $row->{$colLabel};
			}
		}
		
		return parseMyElement($row->{$colName}, $renderExternalUri, $labelRes);
	}
?>
