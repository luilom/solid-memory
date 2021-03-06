<?php
require_once( 'http_API.php' );
require_once( 'query.php' );

  $http= new http_API();

$author = "John Darlington";
$year = null;
$topics = array();
$article1 = array();
$article2 = array();
$article3 = array();
$control=0;
$title = "A decomposition theorem for finite persistent transition systems";
//$title="Fault diagnosis for nonlinear systems using a bank of neural estimators";
//$title="Parallel Integer Sorting and Simulation Amongst CRCW Models";

$sparql = new query_sparql();
$prefixs = "
PREFIX crm: <http://www.cidoc-crm.org/cidoc-crm/>
PREFIX cs: <http://purl.org/vocab/changeset/schema#>
prefix owl: <http://www.w3.org/2002/07/owl#>
Prefix sd: <http://www.semanticweb.org/francesco/ontologies/2016/docs#>
prefix xsd: <http://www.w3.org/2001/XMLSchema#>
";



//DATO UN AUTORE RESTITUISCE GLI ARTICOLI
/*$query=$prefixs.$sparql->getArticlesByAuthors("Norbert Blum");
$response = $http->sparqlQuery($query);
$json = json_decode($response,true);
$temp= $json["results"]["bindings"];

foreach($temp as $t){
        echo $t["name_author"]["value"]."<br>";
}*/


//Dato un journal restituire tutti gli articoli
$query=$prefixs.$sparql->getArticlesByJournal("Acta Inf.");
$response = $http->sparqlQuery($query);
$json = json_decode($response,true);
$temp= $json["results"]["bindings"];


foreach($temp as $t){
        echo $t["title_value"]["value"]."<br>";
}



?>
