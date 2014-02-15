<?php
/**
* PHP5 RJson Version 0.1.2
* Copyright (c) 2013, Dmitri Russu <dmitri.russu@gmail.com>
* RJson return compact recursive data Array or Object Array by Algorithm Dmytro Dogadailo <entropyhacker@gmail.com>.
* RJson converts any Array data collection into more compact recursive
* form. Compressed data is still JSON and can be parsed with `JSON.parse`. RJson
* can compress not only homogeneous collections, but any data sets with free
* structure.
*
* RJson is a good practice to use on your Applications which make requests at server for obtains a big data
* to client Application.
*/

class RJson {

	private static $schemas = array();
	private static $maxSchemaIndex = 0;
	private static $decodedSchemas = array();
	private static $maxDecodedSchemaIndex = 0;

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
		if (isset($data[0]) && !is_string($data) && !is_int($data)) {
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
		$data = (isset($data[0]) ? $data[0] : $data);
		if (is_bool($data) || is_null($data)) {
			return $data;
		}
		ksort($data);
		ksort(static::$schemas);
		$schemaKeys = array_keys((isset($data[0]) ? $data[0] : $data));
		$numberOfSchemaKeys = count($schemaKeys);

		if ($numberOfSchemaKeys === 0) {
			return false;
		}

		$encoded = array();
		$schema = $numberOfSchemaKeys . ':' . join('|', $schemaKeys);
		$schemaIndex = (isset(static::$schemas[$schema]) ? static::$schemas[$schema] : false);
		if ($schemaIndex) {
			$encoded[] = $schemaIndex;
			for ($i = 0; $i < $numberOfSchemaKeys; $i++) {
				$encoded[] = self::encode($data[$schemaKeys[$i]]);
			}
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
			$arrayKey = null;
			if(!is_string($current) && !is_int($current) && !is_null($current) && !is_bool($current)) {
				$arrayKey = array_keys($current);
				$arrayKey = $arrayKey[0];
			}

			if($i === 0 && !is_int($arrayKey)) {
				array_push($encoded, $current);
			} else if (!is_string($current) && !is_int($current) && !is_null($current) && !is_bool($current)) {
				$arrayKeys = (isset($encoded[0]) ? array_keys($encoded[0]) : null);
				$positionId = $current[0];
				$currentData = array_slice($current, 1, count($current));

				if(isset($encoded[0]) && isset($arrayKeys[0]) && is_string($arrayKeys[0])) {
					$encoded[1][0] = $positionId;
					foreach($currentData as $value) {
						$encoded[1][] = $value;
					}
				} else {
					$encoded[0][0] = $current[0];
					foreach($currentData as $value) {
						$encoded[0][] = $value;
					}
				}
			} else if (is_string($current) || is_int($current) || is_null($current) || is_bool($current)) {
				$encoded[] = $current;
			}
		}
		return $encoded;
	}

	/**
	 * RJson Unpack
	 * Example of use RJson::unpack($data);
	 *
	 * @param $data
	 * @param bool $isJson
	 * @return array|bool|string
	 */
	public static function unpack($data, $isJson = false) {
		if ( $isJson ) {
			$data = json_decode($data);
		}
		return self::decode($data);
	}

	/**
	 * Decode Array collection
	 * @param $data
	 * @return array|bool
	 */
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
	private static function parseArray($data) {
		if(count($data) === 0) {
			return array();
		} else if ((isset($data[0]) && $data[0] === 0) || (isset($data[0]) && !is_numeric($data[0]))) {
			return self::decodeArray($data);
		} else {
			return self::decodeObject($data);
		}
	}

	/**
	 * @param $data
	 * @return array|bool
	 */
	private static function decodeNewObject($data) {
		if(is_bool($data) || is_null($data)) {
			return $data;
		}
		$schemaKeys = array_keys($data);
		if (count($schemaKeys)  === 0) {
			return false;
		}
		$decoded = array();
		static::$decodedSchemas[++static::$maxDecodedSchemaIndex] = $schemaKeys;

		for($i = 0; $i < count($schemaKeys); $i++) {
			$decoded[$schemaKeys[$i]] = self::decode($data[$schemaKeys[$i]]);
		}
		return $decoded;
	}

	/**
	 * @param $data
	 * @return array
	 */
	private static function decodeArray($data) {
		$numberOfSchema = count($data);
		$decoded = array();
		for($i = (isset($data[0]) && is_int($data[0]) ? 1 : 0); $i < $numberOfSchema; $i++) {
			$object = self::decode($data[$i]);
			if (!is_string($object) && !is_int($object) && !is_null($object) && !is_bool($object)) {
				$objectKey = array_keys($object);
				if(!is_string($objectKey[0])) {
					foreach($object as $value) {
						$decoded[] = $value;
					}
				} else {
					array_push($decoded, $object);
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
	private static function decodeObject($data) {
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
				$decoded[] = $object;
			}
		} else if ($total == 1) {
			for($i = 0; $i < $total; $i++) {
				$object = null;
				for($j = 0; $j <= $schemaLength; $j++) {
					if(isset($data[$i * $schemaLength + $j+1]) && isset($schemaKeys[$j])) {
						$object[$schemaKeys[$j]] = self::decode($data[$i * $schemaLength + $j+1]);
					}
				}
				$decoded = $object;
			}
		}
		return $decoded;
	}
}