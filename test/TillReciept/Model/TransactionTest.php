<?php
namespace Willshaw\WorldFirst\Test\TillReceipt\Model;

use Willshaw\WorldFirst\TillReceipt\Model\Transaction;


class TransactionTest extends \PHPUnit_Framework_TestCase {

	private $products = array();

	public function setup() {
		// Setup transaction with products
		//$currency = 'GBP';
		$this->products = array(
			array('Baked Beans', 0.50, 0),
			array('Washing Up Liquid', 0.72, 0.10),
			array('Rubber Gloves', 1.50, 0.35)
		);
	}


	public function testAddProduct() {
		$mock_product = $this->getMockBuilder('Willshaw\WorldFirst\TillReceipt\Model\Product')
		->disableOriginalConstructor()->getMock();
		$transaction = new Transaction();
		$transaction->addProduct($mock_product);
		$this->assertSame($mock_product, $transaction->getProducts()[0]);
	}

	public function testGetGrandTotal() {
		$transaction = new Transaction();
		foreach ($this->products as $product) {
			$mock_product = $this->getMockBuilder('Willshaw\WorldFirst\TillReceipt\Model\Product')
				->disableOriginalConstructor()
				->getMock();
			$mock_product->expects($this->once())
				->method('getPrice')->willReturn($product[1]);
			$transaction->addProduct($mock_product);
		}
		$result = $transaction->getGrandTotal();
		$this->assertTrue(is_float($result));
		$this->assertTrue((string)$result === '2.72');
	}

	public function testGetDiscounts() {
		$transaction = new Transaction();
		foreach ($this->products as $product) {
			$mock_product = $this->getMockBuilder('Willshaw\WorldFirst\TillReceipt\Model\Product')
				->disableOriginalConstructor()
				->getMock();
			$mock_product->expects($this->once())
				->method('getDiscount')->willReturn($product[2]);
			$transaction->addProduct($mock_product);
		}
		$result = $transaction->getDiscounts();
		$this->assertTrue(is_float($result));
		$this->assertTrue((string)$result === '0.45');
	}

	public function testGetSubTotal() {
		$transaction = new Transaction();
		foreach ($this->products as $product) {
			$mock_product = $this->getMockBuilder('Willshaw\WorldFirst\TillReceipt\Model\Product')
				->disableOriginalConstructor()
				->getMock();
			$mock_product->expects($this->once())
				->method('getPrice')->willReturn($product[1]);
			$mock_product->expects($this->once())
				->method('getDiscount')->willReturn($product[2]);
			$transaction->addProduct($mock_product);
		}
		$result = $transaction->getSubTotal();
		$this->assertTrue(is_float($result));
		$this->assertTrue((string)$result === '2.27');
	}
}

