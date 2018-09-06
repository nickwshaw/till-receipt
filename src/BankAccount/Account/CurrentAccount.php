<?php
namespace Willshaw\WorldFirst\BankAccount\Account;

use Willshaw\WorldFirst\BankAccount\AccountInterface;
use Willshaw\WorldFirst\BankAccount\Exception\AccountClosedException;
use Willshaw\WorldFirst\BankAccount\Exception\InsufficientFundsException;
use Willshaw\WorldFirst\BankAccount\Exception\InvalidAmountException;


class CurrentAccount implements AccountInterface {

	private $account_status = false;
	private $balance = 0;
	private $overdraft = 0;
	private $currency_formatter;
	private $currency_code = 'GBP';
	private $scale = 4;

	public function __construct() {
		$this->currency_formatter = new \NumberFormatter('en_GB', \NumberFormatter::CURRENCY);
	}

	public function openAccount() {
		$this->account_status = true;
	}

	public function closeAccount() {
		$this->account_status = false;
	}

	public function getAccountStatus() {
		return $this->account_status;
	}

	/**
	 * Deposit funds into account
	 * @param float $amount
	 * @throws AccountClosedException
	 * @throws InvalidAmountException
	 */
	public function depositFunds($amount) {
		if (!$this->account_status) {
			throw new AccountClosedException();
		}
		if (!is_numeric($amount) || $amount <= 0) {
			throw new InvalidAmountException();
		}
		$this->balance = (float)bcadd((string)$this->balance, (string)$amount, $this->scale);
	}

	/**
	 * Withdraw funds from account
	 * @param float $amount
	 * @throws AccountClosedException
	 * @throws InsufficientFundsException
	 * @throws InvalidAmountException
	 */
	public function withDrawFunds($amount) {
		if (!$this->account_status) {
			throw new AccountClosedException();
		}
		if (!is_numeric($amount) || $amount <= 0) {
			throw new InvalidAmountException();
		}
		$calculated_balance = bcadd((string)$this->balance, (string)$this->overdraft, $this->scale);
		$balance = (float)bcsub((string)$calculated_balance, (string)$amount, $this->scale);
		if ($balance < 0) {
			throw new InsufficientFundsException();
		}
		$this->balance = (float)bcsub((string)$this->balance, (string)$amount, $this->scale);
	}

	/**
	 * Set agreed overdraft
	 * @param float $amount
	 * @throws InvalidAmountException
	 */
	public function setOverdraft($amount) {
		if (!is_numeric($amount) || $amount <= 0) {
			throw new InvalidAmountException();
		}
		$this->overdraft = $amount;
	}

	public function getOverdraft() {
		return $this->overdraft;
	}

	public function getBalance() {
		return $this->balance;
	}

	public function displayBalance() {
		return 'Current balance : ' . $this->currency_formatter->formatCurrency($this->balance, $this->currency_code);
	}
}