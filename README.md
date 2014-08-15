<a href="https://travis-ci.org/dmitrirussu/RJSON-php" target="_balank">
    <img src="https://travis-ci.org/dmitrirussu/RJSON-php.png" data-bindattr-130="130" title="Build Status Images">
</a>
<a href="http://badge.fury.io/gh/dmitrirussu%2FRJSON-php">
<img src="https://badge.fury.io/gh/dmitrirussu%2FRJSON-php.svg" alt="GitHub version" height="18">
</a>

PHP5 RJson Version 1.0.2
===
Copyright (c) 2013, Dmitri Russu <dmitri.russu@gmail.com>
RJson return compact recursive data Array or Object Array by Algorithm Dmytro Dogadailo <entropyhacker@gmail.com>.

===
Compress Array data and Json data until 60%! 
===
RJSON-php VS RJSON-js
=========

How begin to use PHP RJSON example:
```php
    $data = //Your recursive Array data;
    $pack = RJson::pack($data);
    $unpack = RJson::unpack($pack);
```   
How begin to use JavaScript RJSON example: 
```java
    data = //jason packedge from php;
    unpack = RJSON.unpack(data);
    packAndSendToSerevr = RJSON.pack(unpack);
```
...

    ---------------------------------------------------------------------------------------
    JavaScrip RJSON release https://github.com/dogada/RJSON from Dmytro Dogadailo.
    ---------------------------------------------------------------------------------------

RJson converts any Array data collection into more compact recursive
form. Compressed data is still JSON and can be parsed with `JSON.parse`. RJson
can compress not only homogeneous collections, but any data sets with free
structure.

Below you can see initial form!
```php
    Array:
	$data['data_process'] = array(
	'template' => array('layers' => array(
	'layer_id_one' => array('age' => 23,'name' => 'Robert',  'height' => 187),
	'layer_id_two' => array('name' => 'Andre', 'age' => 24, 'height' => 188),
	),
	'themes_one' => array(
	'theme_id_one' => array('name' => 'Green', 'width' => 11),
	'theme_id_two' => array('name' => 'Yellow', 'width' => 12),
	),
	'themes_two' => array(
	'theme_id_one' => array('name' => 'Green', 'width' => 11),
	'theme_id_two' => array('name' => 'Yellow', 'width' => 12),
	),
	'designs' => array(
	array('title' => 'Design_1', 'width' => 23, 'height' => 187),
	array('width' => 24, 'title' => 'Design_2','height' => 181),
	)
	),
	'id' => 7,
	'tags' => array('php', 'javascript', 2013, null, false, true),
	'users' => array(
	array('first' => 'Homer', 'last' => 'Simpson'),
	array('first' => 'Hank', 'last' => 'Hill'),
	),
	'library' => array(
	array('title' => 'RJSON-php', 'author' => 'Dmitri Russu', 'year' => 2013),
	array('title' => 'JavaScrip RJSON', 'author' => 'Dmytro Dogadailo', 'year' => 2012))
	);
```
RJson result compact json or one compact php array():


    RJson ENCODED Packedge
```php
	{"id":7,
		"library":
			[{"author":"Dmitri Russu","title":"RJSON-php","year":2013},
			[3,"Dmytro Dogadailo","JavaScrip RJSON",2012]],
		"tags":
			["php","javascript",2013,null,false,true],
		"template":{
			"designs":
				[{"height":187,"title":"Design_1","width":23},
				[5,181,"Design_2",24]],
			"layers":{
				"layer_id_one":{"age":23,"height":187,"name":"Robert"},
				"layer_id_two":[7,24,188,"Andre"]},
			"themes_one":
				{"theme_id_one":{"name":"Green","width":11},
				"theme_id_two":[9,"Yellow",12]},
			"themes_two":
				[8,[9,"Green",11],
				[9,"Yellow",12]]},
			"users":
				[{"first":"Homer","last":"Simpson"},
				[10,"Hank","Hill"]]}
```
EXAMPLE OF USE RJson:

You make a call class with one simple single tone request
```php
    $data = array(
    'projects' => Db_Model_Projects::findAllProjects($returnArrayRows),
    'settings' => Db_Model_Settings::findAllSettings($returnArrayRows),
    'pages' => Db_Model_Pages::findAllPages($returnArrayRows)
    );

    $compactArrayPackedge = RJson::pack($data);
    
    $compactJsonFormatPackedge = RJson::pack($data, $json = true);
```
```html
    $compactJsonFormatPackedge - this packedge you can send to Ajax request Where can make unpack with Js library
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
    
    //Send packedge to server
    packedge = RJSON.pack(data);
    
    $.ajax( {
    "dataType": 'json',
    "type": "POST",
    "data": data,
    "url": 'index.php?action=saveData',
    "success": function(result) {
    console.log(result);
    }
    });
    
    <script>

RJson is a good practice to use on your Applications which make requests at server for obtains a big data
to client Application.
