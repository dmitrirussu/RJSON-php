<?php
/**
 * Created by Dumitru Russu.
 * Date: 08.02.2014
 * Time: 14:03
 * RJson Test Pack and Unpack Array Data
 */

class RJsonTest extends PHPUnit_Framework_TestCase {

	private $collectionOfData = array(
		'library_books' => array(
			array(
				'author' => 'Gramma',
				'name' => 'Design Patterns',
				'year' => 2009
			),
			array(
				'author' => 'Uliman',
				'name' => 'Advanced PHP',
				'year' => 2009
			)
		),
		'books_borrowed' => array(
			array(
				'author' => 'Gramma',
				'name' => 'Design Patterns',
				'year' => 2009
			)
		)
	);

	private static $compressedData = array();


	public function testCompressData() {

		static::$compressedData = RJson::pack($this->collectionOfData);


		print "Data Already Compressed!\n";
		print_r(static::$compressedData);

		$this->assertArrayNotHasKey('author', static::$compressedData);
	}

	public function testDecompressData() {
		$decompressedData = RJson::unpack(static::$compressedData);

		print "Data Already decompressed\n";
		print_r($decompressedData);


		$this->arrayHasKey('author', $decompressedData);
	}

	public function testCompressArrayDataToJson() {
		static::$compressedData = RJson::pack($this->collectionOfData, $toJson = true);

		print "Compressed to JSON";
		print "\n".static::$compressedData;

		$this->assertJson(static::$compressedData, 'Compressed Data to JSON');
	}

	public function testDecompressJsonData() {

		$decompressedData = RJson::unpack(static::$compressedData, $isJson = true);

		print "\n\nDecompressed JSON data\n";
		print_r($decompressedData);


		$this->assertTrue($this->collectionOfData == $decompressedData);
	}
} 