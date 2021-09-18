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
function array_to_xml(array $arr, SimpleXMLElement $xml) {
	foreach ($arr as $k => $v) {

		$attrArr = array();
		$kArray = explode(' ',$k);
		$tag = array_shift($kArray);

		if (count($kArray) > 0) {
			foreach($kArray as $attrValue) {
				$attrArr[] = explode('=',$attrValue);                   
			}
		}

		if (is_array($v)) {
			if (is_numeric($k)) {
				array_to_xml($v, $xml);
			} else {
				$child = $xml->addChild($tag);
				if (isset($attrArr)) {
					foreach($attrArr as $attrArrV) {
						$child->addAttribute($attrArrV[0],$attrArrV[1]);
					}
				}                   
				array_to_xml($v, $child);
			}
		} else {
			$child = $xml->addChild($tag, $v);
			if (isset($attrArr)) {
				foreach($attrArr as $attrArrV) {
					$child->addAttribute($attrArrV[0],$attrArrV[1]);
				}
			}
		}               
	}

	return $xml;
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


$xml = array_to_xml($a, new SimpleXMLElement('<root/>'))->asXML();

echo "$xml\n";
$xml_parser = new DOMDocument();
$xml_parser->preserveWhiteSpace = false;
$xml_parser->formatOutput=true;
$xml_parser->loadXML($xml);
echo $xml_parser->saveXML();

function array_to_xml2($array, $root, $element) {
    $xml = new SimpleXMLElement("<{$root}/>");
    foreach ($array as $value) {
        $elem = $xml->addChild($element);
        xml_recurse_child($elem, $value);
    }
    return $xml;
}

function xml_recurse_child(&$node, $child) {
    foreach ($child as $key=>$value) {
        if(is_array($value)) {
            foreach ($value as $k => $v) {
                if(is_numeric($k)){
                    xml_recurse_child($node, array($key => $v));
                }
                else {
                    $subnode = $node->addChild($key);
                    xml_recurse_child($subnode, $value);
                }
            }
        }
        else {
            $node->addChild($key, $value);
        }
    }   
} 

echo array_to_xml2($a, $root='root', $element='child');
function array2Xml($data, $xml = null)
{
    if (is_null($xml)) {
        $xml = simplexml_load_string('<' . key($data) . '/>');
        $data = current($data);
        $return = true;
    }
    if (is_array($data)) {
        foreach ($data as $name => $value) {
            array2Xml($value, is_numeric($name) ? $xml : $xml->addChild($name));
        }
    } else {
        $xml->{0} = $data;
    }
    if (!empty($return)) {
        return $xml->asXML();
    }
}
echo array2Xml($a);
?>