<?php
namespace Willshaw\WorldFirst\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Willshaw\WorldFirst\TillReceipt\Model\Product;
use Willshaw\WorldFirst\TillReceipt\Model\Transaction;

class TillReceipt extends Command
{

	private $products = array();
	private $currencies = array();
	/** @var  $transaction Transaction */
	private $transaction;
	/** @var $currency_formatter \NumberFormatter */
	private $currency_formatter;
	private $currency_code;

	protected function configure () {
		$this->setName('till');
		$data = json_decode(file_get_contents('data/till_data.json'));
		$this->products = $data->products;
		$this->currencies = $data->currencies;
		$this->transaction = new Transaction();
		$this->currency_formatter = new \NumberFormatter('en_GB', \NumberFormatter::CURRENCY);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$question_helper = $this->getHelper('question');
		$currency_question = new ChoiceQuestion(
			'Please choose a currency:', $this->currencies, 'GBP'
		);

		$io = new SymfonyStyle($input, $output);
		$io->title('Till Receipt');
		$this->currency_code = $question_helper->ask($input, $output, $currency_question);
		$this->askProductQuestion($input, $output);
		$this->printReceipt($io);
	}

	/**
	 * Recursively ask user for product choice, price and discount
	 * Returns once user choose to finalise transaction
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return bool
	 */
	protected function askProductQuestion(InputInterface $input, OutputInterface $output) {
		$question_helper = $this->getHelper('question');
		$product_question = new  ChoiceQuestion(
			'Please choose a product or finalise transaction and print receipt:',
			array_merge(array('Finalise transaction'), $this->products)
		);
		$price_question = new Question('Please enter a price: ', 0);
		$discount_question = new Question('Please enter a discount: ', 0);
		$product_answer = $question_helper->ask($input, $output, $product_question);
		if ($product_answer == 'Finalise transaction') {
			return true;
		}
		$price_answer = $question_helper->ask($input, $output, $price_question);
		$discount_answer = $question_helper->ask($input, $output, $discount_question);
		$this->transaction->addProduct(new Product($product_answer, $price_answer, $discount_answer));
		$this->askProductQuestion($input, $output);
	}

	/**
	 * Output the receipt to end user
	 * @param SymfonyStyle $io
	 */
	protected function printReceipt(SymfonyStyle $io) {
		$rows = array();
		foreach ($this->transaction->getProducts() as $product) {
			$rows[] = array($product->getName(), $this->currency_formatter
			->formatCurrency($product->getPrice(), $this->currency_code));
		}

		$io->table(
			array('Item', 'Price'),
			$rows
		);
		$io->newLine();
		$io->table(
			array(),
			array(
				array('Sub-Total', $this->formatCurrency($this->transaction->getSubTotal())),
				array('Discounts', $this->formatCurrency($this->transaction->getDiscounts())),
				array('Grand Total', $this->formatCurrency($this->transaction->getGrandTotal())),
			)
		);
	}

	/**
	 * @param float $amount
	 * @return string
	 */
	protected function formatCurrency($amount) {
		return $this->currency_formatter->formatCurrency($amount, $this->currency_code);
	}

}