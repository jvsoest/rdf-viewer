<?php
	require("config.inc.php");
	require("functions.inc.php");	
	
	//Use rootClass URI or URI given in HTTP request
	$resourceUri = $rootClass;
	if($_GET["uri"]!==getBaseUrl() && $_GET["uri"]!=="http://localhost/") {
		$resourceUri = $_GET["uri"];
		$resourceUri = str_replace("HASH", "#", $resourceUri);
	}
	
	$viewToUse = getViewForUri($resourceUri, $views, $sparql_endpoint, $renderExternalUri);
?>

<html>
        <head>
                <title>RDF viewer</title>
        </head>

        <body>
                <h2>RDF viewer</h2>

		<?php require($viewToUse); ?>
        </body>
</html>
