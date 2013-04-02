<?php
/**

PHP5 RJson Version 0.0.1

Copyright (c) 2013, Dmitri Russu <dmitri.russu@gmail.com>
RJson return compact recursive data Array or Object Array by Algorithm Dmytro Dogadailo <entropyhacker@gmail.com>.

RJson converts any Array data collection into more compact recursive
form. Compressed data is still JSON and can be parsed with `JSON.parse`. RJson
can compress not only homogeneous collections, but any data sets with free
structure.

Below you can see initial form!

Array:
array('id' => 7,
'tags' => array('programming', 'javascript'),
'users' => array(
	array('first' => 'Homer', 'last' => 'Simpson'),
	array('first' => 'Hank', 'last' => 'Hill'),
	array('first' => 'Peter', 'last' => 'Griffin'),
),
'books' => array(
	array('title' => 'Php', 'author' => 'Grim', 'year' => 2000),
	array('title' => 'JavaScrip', 'author' => 'Flanagan', 'year' => 2006))
);

 * OR convert object

array('id' => 7,
'tags' => stdObj('programming', 'javascript'),
'users' => array(
	stdObj('first' => 'Homer', 'last' => 'Simpson'),
	stdObj('first' => 'Hank', 'last' => 'Hill'),
	stdObj('first' => 'Peter', 'last' => 'Griffin'),
),
'books' => array(
	stdObj('title' => 'Php', 'author' => 'Grim', 'year' => 2000),
	stdObj('title' => 'JavaScrip', 'author' => 'Flanagan', 'year' => 2006))
);

+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
RJson result compact json or one compact php array():
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				{ "id": 7,
				  "tags": ["programming", "javascript"],
				  "users": [
				{ "first": "Homer", "last": "Simpson"},
				[2, "Hank", "Hill", "Peter", "Griffin"]
				],
				"books": [
				{"title": "JavaScript", "author": "Flanagan", "year": 2006},
				[3, "Cascading Style Sheets", "Meyer", 2004]]
				}
 * TODO EXAMPLE OF USE RJson:
 * You make a call class with one simple single tone request
 *
 * $data = array(
 *			'projects' => Db_Model_Projects::findAllProjects($returnArrayRows),
 * 			'settings' => Db_Model_Settings::findAllSettings($returnArrayRows),
 * 			'pages' => Db_Model_Pages::findAllPages($returnArrayRows)
 * );
 *
 * $compactArrayPackedge = RJson::pack($data);
 * $compactJsonFormatPackedge = RJson::pack($data, $json = true);
 *
 * $compactJsonFormatPackedge - this packedge you can send to Ajax request Where can make unpack with Js library
 *
 * <scrip language="JavaScript" type="text/javascript" >
		  $.ajax( {
			"dataType": 'json',
			"type": "POST",
			"url": 'index.php?action=getData',
			"success": function(data) {
				packedge = RJSON.unpack(data);
 			console.dir(packedge);
		}
	});
 *
 *
 *  TODO send packedge to server
 *		packedge = RJSON.pack(data);
 *
       $.ajax( {
		"dataType": 'json',
		"type": "POST",
		"data": data,
		"url": 'index.php?action=saveData',
		"success": function(result) {
			console.log(result);
		}
	});
 *
 *<script>
 *
 * RJson is a good practice to use on your Applications which make requests at server for obtains a big data
 * to client Application.
 *
 */


class RJson {

	private static $schemas = array();
	private static $decodedSchemas = array();
	private static $maxDecodedSchemaIndex = 0;
	private static $maxSchemaIndex = 0;

	/**
	 * Method RJson::pack($data), return compact array
	 *
	 * @param $data
	 * @param bool $returnJsonEncode
	 * @return array|bool|string
	 */
	public static function pack($data, $returnJsonEncode = false) {
		if (!$returnJsonEncode) {
			return self::encode($data);
		}
		return json_encode(self::encode($data));
	}

	/**
	 * @param $data
	 * @return array|bool|string
	 */
	private static function encode($data) {
		if (isset($data[0]) && !is_string($data)) {
			return self::encodeArrayChild($data);
		} else if (is_string($data) || is_null($data) || is_int($data)){
			return $data;
		} else {
			return self::encodeObjectArray($data);
		}
	}

	/**
	 * @param $data
	 * @return array|bool
	 */
	private static function encodeObjectArray($data) {
		$schemaKeys = array_keys((isset($data[0]) ? $data[0] : $data));

		usort($schemaKeys, function($a, $b){
			return strcasecmp($a[0], $b[0]);
		});

		$numberOfSchemaKeys = count($schemaKeys);

		if ($numberOfSchemaKeys === 0) {
			return false;
		}

		$encoded = array();
		$schema = $numberOfSchemaKeys . ':' . join('|', $schemaKeys);
		$schemaIndex = (isset(static::$schemas[$schema]) ? static::$schemas[$schema] : false);
		if($schemaIndex) {
			for ($i = 0; $i < $numberOfSchemaKeys; $i++) {
				$encoded[$schemaIndex][] = self::encode($data[$schemaKeys[$i]]);
			}
			return $encoded;
		} else {
			static::$schemas[$schema] = ++static::$maxSchemaIndex;
			$objectRow = null;
			if (!isset($data[0]) || !is_array($data[0])) {
				for ($i = 0; $i < $numberOfSchemaKeys; $i++) {
					$encoded[$schemaKeys[$i]] = self::encode($data[$schemaKeys[$i]]);
				}
			}
		}
		return $encoded;
	}

	/**
	 * @param $data
	 * @return array
	 */
	private static function encodeArrayChild($data) {
		$numberOfChild = count($data);
		$encoded = array();
		$last = null;

		if($numberOfChild === 0) {
			return array();
		}

		if(is_numeric($data[0])) {
			array_push($encoded, 0);
		}

		for($i = 0; $i < $numberOfChild; $i++) {
			if(is_object($data[$i])) {
				$data[$i] = get_object_vars($data[$i]);
			}
			$current = self::encode($data[$i]);
			$arrayKey = array_keys($current);
			$arrayKey = $arrayKey[0];

			if($i === 0 && !is_int($arrayKey)) {
				array_push($encoded, $current);
			} else {
				$arrayKeys = (isset($encoded[0]) ? array_keys($encoded[0]) : null);
				$currentData = array_slice($current,0, 1);
				$currentData = $currentData[0];

				if(isset($encoded[0]) && isset($arrayKeys[0]) && is_string($arrayKeys[0])) {
					$encoded[1][0] = key($current);
					foreach($currentData as $value) {
						$encoded[1][] = $value;
					}
				} else {
					$encoded[0][0] = key($current);
					foreach($currentData as $value) {
						$encoded[0][] = $value;
					}
				}
			}
		}
		return $encoded;
	}

	/**
	 * RJson Unpack
	 * Example of use RJson::unpack($data);
	 *
	 * @param $data
	 * @return array|bool|string
	 */
	public static function unpack($data) {
		return self::decode($data);
	}

	private static function decode($data) {
		if((is_array($data) && isset($data[0])) || (isset($data[0]) && !is_string($data[0]))) {
			return self::parseArray($data);
		} else if (is_string($data) || is_null($data) || is_int($data)) {
			return $data;
		} else {
			return self::decodeNewObject($data);
		}
	}

	/**
	 * @param $data
	 * @return array
	 */
	public static function parseArray($data) {
		if(count($data) === 0) {
			return [];
		} else if (isset($data[0]) && $data[0] === 0 || isset($data[0]) && !is_numeric($data[0])) {
			return self::decodeArray($data);
		} else {
			return self::decodeObject($data);
		}
	}

	/**
	 * @param $data
	 * @return array|bool
	 */
	public static function decodeNewObject($data) {
		$schemaKeys = array_keys($data);
		if (count($schemaKeys)  === 0) {
			return false;
		}

		usort($schemaKeys, function($a, $b){
			return strcasecmp($a[0], $b[0]);
		});

		static::$decodedSchemas[++static::$maxDecodedSchemaIndex] = $schemaKeys;

		$decoded = array();

		for($i = 0; $i < count($schemaKeys); $i++) {
			$decoded[$schemaKeys[$i]] = self::decode($data[$schemaKeys[$i]]);
		}
		return $decoded;
	}

	/**
	 * @param $data
	 * @return array
	 */
	public static function decodeArray($data) {
		$numberOfSchema = count($data);
		$decoded = [];
		for($i = (isset($data[0]) && is_int($data[0]) ? 1 : 0); $i < $numberOfSchema; $i++) {
			$object = self::decode($data[$i]);
			$objectKey = array_keys($object);
			if(!is_string($objectKey[0])) {
				foreach($object as $value) {
					$decoded[] = $value;
				}
			} else {
				array_push($decoded, $object);
			}
		}
		return $decoded;
	}

	/**
	 * @param $data
	 * @return array
	 */
	public static function decodeObject($data) {
		$schemaKeys = static::$decodedSchemas[$data[0]];
		$schemaLength = count($schemaKeys);
		$total = (count($data) - 1) / $schemaLength;
		$decoded = array();

		if ($total > 1) {
			for($i = 0; $i < $total; $i++) {
				$object = null;
				for($j = 0; $j <= $schemaLength; $j++) {
					if(isset($data[$i * $schemaLength + $j+1]) && isset($schemaKeys[$j])) {
						$object[$schemaKeys[$j]] = self::decode($data[$i * $schemaLength + $j+1]);
					}
				}
				array_push($decoded, $object);
			}
		}
		return $decoded;
	}
}