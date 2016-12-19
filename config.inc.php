<?php
	$sparql_endpoint = "http://localhost:9999/blazegraph/namespace/dicom/sparql";
	$default_view = "view_default.php";
	$renderExternalUri = true;
	$rootClass = "http://semantic-dicom.org/dcm#Patient";
	
	$views = array(
		"default"=>"defaultView.inc.php",
		"http://semantic-dicom.org/dcm#Patient"=>"sediPatientView.inc.php",
		"http://semantic-dicom.org/dcm#Study"=>"sediView.inc.php",
		"http://semantic-dicom.org/dcm#Series"=>"sediView.inc.php",
		"http://semantic-dicom.org/dcm#Image"=>"sediView.inc.php",
		"http://semantic-dicom.org/dcm#StructureSet"=>"sediView.inc.php"
		);
?>
