<?php
	$prefixes = "PREFIX rdfs:<http://www.w3.org/2000/01/rdf-schema#> ".
		"PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#> ".
		"PREFIX owl:<http://www.w3.org/2002/07/owl#> ";
			
	$resourceInfoQuery = "$prefixes SELECT ?resource ?resourceLabel ?resourceClass ?resourceClassLabel ".
						"WHERE { BIND(<$resourceUri> AS ?resource). ".
							"OPTIONAL { ?resource rdfs:label ?resourceLabel. }. ".
							"OPTIONAL { ?resource rdf:type ?resourceClass. }. ".
							"OPTIONAL { ?resourceClass rdfs:label ?resourceClassLabel. } ".
						"}";
		
	$resourceDetailsQuery = "$prefixes SELECT ?property ?propertyLabel ?resource ?resourceLabel 
		WHERE { <$resourceUri> ?property ?resource. 
			OPTIONAL { ?resource rdfs:label ?resourceLabel }. 
			OPTIONAL { ?property rdfs:label ?propertyLabel }.
		}";
		
	$resourceReferencedQuery = "$prefixes SELECT ?property ?propertyLabel ?resource ?resourceLabel WHERE { ?resource ?property <$resourceUri>. OPTIONAL { ?resource rdfs:label ?resourceLabel }. OPTIONAL { ?property rdfs:label ?propertyLabel } }";
	
	$resourceInfo = executeQuery($resourceInfoQuery, $sparql_endpoint);
	$resourceDetails = executeQuery($resourceDetailsQuery, $sparql_endpoint);
	$resourceReferenced = executeQuery($resourceReferencedQuery, $sparql_endpoint);
	
	function getEquivalentPropLabel($row, $prefixes, $sparql_endpoint) {
		$propertyUri = $row->{"property"}->{"value"};
		$query = "$prefixes SELECT ?resourceLabel ".
			"WHERE { ".
				"<$propertyUri> owl:equivalentProperty ?eqProp. ".
				"?eqProp rdfs:label ?resourceLabel. ".
			"}";
		
		$labelInfo = executeQuery($query, $sparql_endpoint);
		
		if(isset($labelInfo[0]->{"resourceLabel"}->{"value"})) {
			return $labelInfo[0]->{"resourceLabel"}->{"value"}." ";
		} else {
			return " ";
		}
	}
?>

<h3>Patient: <?php echo(getValueForColumn($resourceInfo[0], "resource", $renderExternalUri, "resourceLabel")); ?></h3>
<h4>Properties:</h4>
<table>
		<tr>
	    <th>Property</th>
	    <th>Resource</th>
	</tr>
	<?php
		foreach($resourceDetails as $row) {
	    	echo("<tr>");
	    	echo("<td>".getEquivalentPropLabel($row, $prefixes, $sparql_endpoint).getValueForColumn($row, "property", $renderExternalUri, "propertyLabel").":</td>");
	    	echo("<td>".getValueForColumn($row, "resource", $renderExternalUri, "resourceLabel")."</td>");
	    	echo("</tr>");
		}
	?>
</table>

<h4>Resource used or linked at</h4>
<table>
		<tr>
	    <th>Resource</th>
	    <th>Property</th>
	</tr>
	<?php
		foreach($resourceReferenced as $row) {
	    	echo("<tr>");
	    	echo("<td>".getValueForColumn($row, "resource", $renderExternalUri, "resourceLabel")."</td>");
	    	echo("<td>".getValueForColumn($row, "property", $renderExternalUri, "propertyLabel")."</td>");
	    	echo("</tr>");
		}
	?>
</table>