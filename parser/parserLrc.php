<?php

$filename = "./caminando.lrc";

$inputFile = fopen($filename, "r") or die("Unable to open file!");

$songName = explode('.lrc', $filename)[0];

$lines = [];

if(!feof($inputFile)) {
	$txt = fgets($inputFile);
}

while(!feof($inputFile)) {
	$line = [];
	$aux = [];
	while(!(strpos($txt, "\\n") !== false) && !feof($inputFile)){
		$syllable = createSyllable($txt);
		array_push($aux, $syllable);
		$txt = fgets($inputFile);
	}

	$syllable = createSyllable($txt);
	$syllable['value']=substr($syllable['value'], 0, -2);
	array_push($aux, $syllable);

	$aux = getDurationSyllable($aux, $txt);

	if(count($aux)>0){
		$line['syllables'] = $aux;
		array_push($lines, $line);
	}
	$txt = fgets($inputFile);
}

$song = [];
$song['song'] = $songName;
$song['lines'] = $lines;

echo json_encode($song);

/*$outputFile = fopen($songName."_lrc.json", "w");

fwrite($outputFile, json_encode($song,JSON_PRETTY_PRINT));

fclose($inputFile);
fclose($outputFile);*/



function createSyllable($textLine){
	$aux = explode("-", $textLine);
	$syllable = [];
	$syllable['initTime']= $aux[0];
	$syllable['duration']=0;
	$syllable['value']=$aux[2];
	if(count($aux)==4){
		$syllable['value']= $syllable['value'] . '-';
	}else{
		$syllable['value']=substr($syllable['value'], 0, -1);
	}
	return $syllable;
}

function getDurationSyllable($syllables, $lastLine){
	$i = 1;
	while($i < count($syllables)){
		$end = floatval(explode(":", $syllables[$i]['initTime'])[1]);
		$start = floatval(explode(":", $syllables[$i-1]['initTime'])[1]);
		$syllables[$i -1]['duration'] = round($end - $start, 3);
		$i++;
	}
	$aux = explode("-", $lastLine);
	$end = floatval(explode(":", $aux[1])[1]);
	$start = floatval(explode(":", $aux[0])[1]);
	$syllables[$i -1]['duration'] = round($end - $start, 3);
	return $syllables;
}



?>