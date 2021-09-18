<?php
//clasa cu care dintr-un array de raspuns se creeaza un xml
class ResponseXML extends DOMDocument {
	function add_dets($array, &$oparent) {
		//se face apelare recursiva a functiei
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				if($key == 'attributes'){
					foreach ($val as $name => $value){
						$oparent->setAttribute($name,$value);
					}
				}
				elseif($key == 'multi'){
					for($k=0;$k<count($val);$k++){
						$this->add_dets($val[$k], $oparent);
					}
				}
				else{
					if(array_key_exists(0,$val)){
						$otmp = $this->createElement($key);
						$otmp = $oparent->appendChild($otmp);
						for($k=0;$k<count($val);$k++){
							$this->add_dets($val[$k], $otmp);
						}
					}
					else{
						$otmp = $this->createElement($key);
						$otmp = $oparent->appendChild($otmp);
						$this->add_dets($val, $otmp);
					}
				}
			}
			else{
				if($key == 'nodevalue'){
					$oparent->nodeValue = $val;
				}
				elseif($key == 'cdata'){
					$otmp = $this->createCDATASection($val);
					$oparent->appendChild($otmp);
				}
				else{
					$otmp = $this->createElement($key,$val);
					$oparent->appendChild($otmp);
				}
			}
		}
	}
	function createXML($array){
		$this->add_dets($array, $this);
	}
}

//header('Content-Type: text/xml');
$a['root']['hotels'][0]['hotel']['name'] = 'test';
$a['root']['hotels'][0]['description']['cdata'] = 'test';
$a['root']['hotels'][0]['test']['attributes']['code'] = 'test';
$a['root']['hotels'][0]['test']['nodevalue'] = 'test';
$a['root']['hotels'][1]['hotel']['name'] = 'test';
$a['root']['hotels'][1]['description']['cdata'] = 'test';
$a['root']['hotels'][1]['test']['attributes']['code'] = 'test';
$a['root']['hotels'][1]['test']['nodevalue'] = 'test';

$oResponseXml = new ResponseXML("1.0","UTF-8");
$oResponseXml->formatOutput = true;
$oResponseXml->createXML($a);
//header('Content-Type: text/xml');
header('Content-Type: text/plain');
echo $oResponseXml->saveXML();
?>
