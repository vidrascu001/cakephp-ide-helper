<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\DatabaseTableColumnNameTask;

class DatabaseTableColumnNameTaskTest extends TestCase {

	/**
	 * @var string[]
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Generator\Task\DatabaseTableColumnNameTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->getTableLocator()->get('Cars');
		$this->getTableLocator()->get('Wheels');

		$this->task = new DatabaseTableColumnNameTask();
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->task);
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(6, $result);

		/** @var \IdeHelper\Generator\Directive\RegisterArgumentsSet $directive */
		$directive = array_shift($result);
		$this->assertSame(DatabaseTableColumnNameTask::SET_TABLE_NAMES, $directive->toArray()['set']);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::addColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expectedList = [
			'argumentsSet(\'tableNames\')',
		];
		$this->assertSame($expectedList, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::changeColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);
		$this->assertSame($expectedList, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::removeColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);
		$this->assertSame($expectedList, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::renameColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);
		$this->assertSame($expectedList, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::hasColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);
		$this->assertSame($expectedList, $list);
	}

}
