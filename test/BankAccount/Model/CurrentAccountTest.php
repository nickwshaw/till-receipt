<?php
namespace Willshaw\WorldFirst\Test\BankAccount\Model;

use Willshaw\WorldFirst\BankAccount\Account\CurrentAccount;

class CurrentAccountTest extends \PHPUnit_Framework_TestCase {

	private $deposit_amount;
	private $withdraw_amount;
	private $overdraft;
	private $balance_after_withdrawal;
	private $balance_after_withdraw_empty_account;
	private $withdraw_exceed_overdraft;
	/** @var  $account CurrentAccount */
	private $account;

	public function setUp() {
		$this->withdraw_amount = 10;
		$this->withdraw_exceed_overdraft = 500;
		$this->deposit_amount = 100.50;
		$this->overdraft = 50;
		$this->balance_after_withdrawal = 90.50;
		$this->balance_after_withdraw_empty_account = -10;
		$this->account = new CurrentAccount();
	}

	public function testOpenAccountIfClosed() {
		$this->account->openAccount();
		$this->assertTrue($this->account->getAccountStatus());
	}

	public function testCloseAccount() {
		$this->account->openAccount();
		$this->account->closeAccount();
		$this->assertFalse($this->account->getAccountStatus());
	}

	public function testOverdraftInvalidAmount() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\InvalidAmountException');
		$this->account->openAccount();
		$this->account->setOverdraft(-10);
	}

	public function testSetOverdraft() {
		$this->account->openAccount();
		$this->account->setOverdraft($this->overdraft);
		$this->assertEquals($this->account->getOverdraft(), $this->overdraft);
	}

	public function testDepositClosedAccount() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\AccountClosedException');
		$this->account->depositFunds($this->deposit_amount);
	}

	public function testDepositInvalidAmount() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\InvalidAmountException');
		$this->account->openAccount();
		$this->account->depositFunds(-10);
	}

	public function testDepositOpenAccount() {
		$this->account->openAccount();
		$this->account->depositFunds($this->deposit_amount);
		$this->assertEquals($this->account->getBalance(), $this->deposit_amount);
	}

	public function testWithdrawFromClosedAccount() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\AccountClosedException');
		$this->account->withDrawFunds($this->withdraw_amount);
	}

	public function testWithdrawInvalidAmount() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\InvalidAmountException');
		$this->account->openAccount();
		$this->account->withDrawFunds(-10);
	}

	public function testWithdrawFromInsufficientFunds() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\InsufficientFundsException');
		$this->account->openAccount();
		$this->account->withDrawFunds($this->withdraw_amount);
	}

	public function testWithdrawFunds() {
		$this->account->openAccount();
		$this->account->depositFunds($this->deposit_amount);
		$this->account->withDrawFunds($this->withdraw_amount);
		$this->assertEquals($this->account->getBalance(), $this->balance_after_withdrawal);
	}

	public function testWithdrawOverBalanceWithinOverdraft() {
		$this->account->openAccount();
		$this->account->setOverdraft($this->overdraft);
		$this->account->withDrawFunds($this->withdraw_amount);
		$this->assertEquals($this->account->getBalance(), $this->balance_after_withdraw_empty_account);
	}

	public function testWithDrawExceedOverdraft() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\InsufficientFundsException');
		$this->account->openAccount();
		$this->account->setOverdraft($this->overdraft);
		$this->account->withDrawFunds($this->withdraw_exceed_overdraft);
	}

	public function testWithDrawExceedOverdraftAfterDeposit() {
		$this->setExpectedException('Willshaw\WorldFirst\BankAccount\Exception\InsufficientFundsException');
		$this->account->openAccount();
		$this->account->setOverdraft($this->overdraft);
		$this->account->depositFunds($this->deposit_amount);
		$this->account->withDrawFunds($this->withdraw_exceed_overdraft);
	}

	public function testDisplayBalance() {
		$this->assertEquals($this->account->displayBalance(), 'Current balance : £0.00');
	}
}