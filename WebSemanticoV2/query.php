<?php

class query_sparql{
	

	function __construct(){
		
	}
	
//QUERY CHE RESTITUISCE TUTTI I TITOLI
function getTitle(){
$query="
SELECT ?titleValue WHERE{ 
  ?prod crm:P108_has_produced ?doc. 
  ?doc crm:P102_has_title ?title. 
  ?title sd:Title_value ?titleValue. 
 	
}";

return $query;
}

/*PER PRENDERE I TOPIC DI UN ARTICOLO A PARTIRE DAL TITOLO*/
function getTopicByTitle($title,$tn){

$query="

SELECT ".$tn."
   WHERE{
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$title."'.
  ?doc crm:P56_bears_feature ?topic.
  ?topic sd:Topic_value ".$tn."
}";

return $query;
}


/*PER PRENDERE KEYWORDs DI UN ARTICOLO A PARTIRE DAL TITOLO*/
function getKeywordsByTitle($title,$tn1){
	
$query="

SELECT ".$tn1."
 
  WHERE{
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$title."'.
  ?doc crm:P149_is_identified_by ?key.
  ?key sd:Text ".$tn1.".
}";

return $query;
}

/*A PARTIRE DAI TOPIC DI UN ARTICOLO RESTITUISCE LA LISTA DEGLI ARTICOLI CON IL NUMERO DI TOPIC IN COMUNE*/

function getArticlesByTopics($topics,$title,$title_value,$topic_value,$count){
$i=0;
$size = sizeof($topics);

$query="
SELECT   ".$title_value." (COUNT(DISTINCT ".$topic_value.") AS ".$count.") 
 
  WHERE{
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P56_bears_feature ?topic.";
	for($i; $i<$size-1;$i++){
		$query = $query."
		{?topic sd:Topic_value '".$topics[$i]."'.} UNION";
	}
  $query=$query.
  "{?topic sd:Topic_value '".$topics[$size-1]."'.}.
   ?topic sd:Topic_value ".$topic_value.".
   ?doc crm:P102_has_title ?title.
   ?title sd:Title_value ".$title_value.".
 
  FILTER (".$title_value." != '".$title."').
}
GROUP BY ".$title_value."
ORDER BY ".$title_value."
";
return $query;

}

/*RESTITUISCE LA SOMMA DELLE RELEVANCE PER OGNI ARTICOLO*/

function getSumOfRelForEachTitles($title_value,$sum,$totalRel){

$query="
	SELECT  ".$title_value."  (SUM(xsd:double(".$sum.")) as ".$totalRel.")
	WHERE
	{
		?prod crm:P108_has_produced ?doc.
		?doc crm:P102_has_title ?title.
		?title sd:Title_value ".$title_value.".
		?doc crm:P149_is_identified_by ?key.
		?key sd:Relevance ".$sum.".
		?key sd:Text ?t
	}
	group by ".$title_value."
	";
	return $query;
	}
	
	/* DATE DELLE KEYWORD RESTITUISCE LA LISTA DI TUTTI GLI ARTICOLI CHE CONTENGONO UN SOTTOINSIEME DI TALI KEYWORD CON LA SOMMA DELLE RELEVANCE ESCLUDENDO L'ARTICOLO DI PARTENZA DAL QUALE SI SONO OTTENUTE LE KEYWORD INIZIALI*/
	function getArticlesByKeywords($keywords, $title){
	$size = sizeof($keywords);
       
	$query="
		SELECT   ?title_value  (SUM(xsd:double(?r)) as ?totalR) (COUNT(DISTINCT ?t) AS ?NumKeyword) 
		WHERE
		{ ";
		
		
		for($i=0;$i<$size-1;$i++){
			$query=$query."{?prod crm:P108_has_produced ?doc.
		?doc crm:P102_has_title ?title.
		?title sd:Title_value ?title_value.
		?doc crm:P149_is_identified_by ?key.
		?key sd:Relevance ?r.
		?key sd:Text ?t.
		FILTER(?t='".$keywords[$i]."')} UNION " ;"
		}";
		}
		
		$query=$query."{?prod crm:P108_has_produced ?doc.
		?doc crm:P102_has_title ?title.
		?title sd:Title_value ?title_value.
		?doc crm:P149_is_identified_by ?key.
		?key sd:Relevance ?r.
		?key sd:Text ?t.
		FILTER(?t='".$keywords[$size-1]."')}
		FILTER(?title_value!='".$title."').}

	group by ?title_value
	ORDER BY DESC(?totalR) 
		";
		
		
		
	return $query;
	}

//DATO UN AUTORE RESTITUISCE GLI ARTICOLI

function getArticlesByAuthors($authors){
	
	$query="
		SELECT   ?name_author 
		WHERE
		{   
  ?prod crm:P14_carried_out_by ?author.
  ?author sd:name '".$authors."'.  
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value ?name_author
}";


return $query;

}

//Dato un journal restituire tutti gli articoli
function getArticlesByJournal($journal){


$query="SELECT  ?title_value 
 
  WHERE{
  ?prod crm:P108_has_produced ?doc.
  ?doc sd:published_by '".$journal."'.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value ?title_value
}";

return $query;


}



//DATO UN ARTICOLO RESTUTUISCE LE KEYWORDS CHE MATCHANO CON LE KEYWORD DELL?ARTICOLO DI PARTENZA; CON LE RELEVANCE
function getEqualsKeywordByArticle($titolo,$keywords){
$query="SELECT ?t ?r WHERE{";
 
$size=sizeof($keywords);

if($size>1){
for($i=0;$i<$size-1;$i++){
 $query=$query."{?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$titolo."'.
  ?title sd:Title_value ?title_value.
  ?doc crm:P149_is_identified_by ?key.
  ?key sd:Text '".$keywords[$i]."'.
  ?key sd:Text ?t.
    ?key sd:Relevance ?r}

UNION";  
}
}
$query=$query."{
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$titolo."'.
  ?title sd:Title_value ?title_value.
  ?doc crm:P149_is_identified_by ?key.
  ?key sd:Text '".$keywords[$size-1]."'.
  ?key sd:Text ?t.
    ?key sd:Relevance ?r}
}";


return $query;
}


//DATO UN ARTICOLO RESTUTUISCE I TOPIC CHE MATCHANO CON I TOPIC DELL'ARTICOLO DI PARTENZA
function getEqualsTopicByArticle($titolo,$topics){
$query="SELECT ?topicValue WHERE{";
 
$size=sizeof($topics);

if($size>1){
for($i=0;$i<$size-1;$i++){
 $query=$query."{?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$titolo."'.
  ?doc crm:P56_bears_feature ?topic.
  ?topic sd:Topic_value '".$topics[$i]."'.
  ?topic sd:Topic_value ?topicValue }
UNION";  
}
}
$query=$query."{?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$titolo."'.
  ?doc crm:P56_bears_feature ?topic.
  ?topic sd:Topic_value '".$topics[$size-1]."'.
  ?topic sd:Topic_value ?topicValue }
}";

return $query;
}

//DATO UN ARTICOLO RESTITUISCE journal e anno 
function getJournalAndAnnoByArticle($titolo){
$query=$query."SELECT  ?journal ?anno
 
  WHERE{
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$titolo."'.
  ?doc sd:published_by ?journal.
  ?prod crm:P4_has_time-span ?date.
  ?date sd:Anno ?anno }";

return $query;
	
}

//DATO UN ARTICOLO RESTITUISCE autori
function getAuthorByArticle($titolo){
	
	$query="SELECT  ?author_name 
 
  WHERE{
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$titolo."'.
  ?prod crm:P14_carried_out_by ?author.
  ?author sd:name ?author_name }";

return $query;	
	}
	
//DAL TITOLO RESTITUISCE L'URL
function getUrlByTitle($titolo){
	$query="SELECT ?url_value
   WHERE{
  ?prod crm:P108_has_produced ?doc.
  ?doc crm:P102_has_title ?title.
  ?title sd:Title_value '".$titolo."'.
  ?doc crm:P67_refers_to ?url.
  ?url sd:Url_value ?url_value
}";

return $query;
}
}
?>
