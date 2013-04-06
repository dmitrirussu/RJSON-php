RJSON-php
=========

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

OR convert object

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


RJson result compact json or one compact php array():


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

EXAMPLE OF USE RJson:

You make a call class with one simple single tone request

    $data = array(
    'projects' => Db_Model_Projects::findAllProjects($returnArrayRows),
    'settings' => Db_Model_Settings::findAllSettings($returnArrayRows),
    'pages' => Db_Model_Pages::findAllPages($returnArrayRows)
    );

    $compactArrayPackedge = RJson::pack($data);
    
    $compactJsonFormatPackedge = RJson::pack($data, $json = true);
    
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
