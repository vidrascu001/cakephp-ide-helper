<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\Core\Plugin;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\DatabaseTableColumnTypeTask;
use IdeHelper\Test\test_app\src\Generator\Task\TestDatabaseTableColumnTypeTask;

class DatabaseTableColumnTypeTaskTest extends TestCase {

	/**
	 * @var string[]
	 */
	protected $fixtures = [
		'plugin.IdeHelper.Cars',
		'plugin.IdeHelper.Wheels',
	];

	/**
	 * @var \IdeHelper\Generator\Task\DatabaseTableColumnTypeTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->getTableLocator()->get('Cars');
		$this->getTableLocator()->get('Wheels');

		$this->task = new DatabaseTableColumnTypeTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::addColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];

		$expectedList = [
			'text' => "'text'",
			'integer' => "'integer'",
		];
		foreach ($expectedList as $key => $value) {
			$this->assertArrayHasKey($key, $list);
			$this->assertSame($list[$key], $list[$key]);
		}

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::changeColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		foreach ($expectedList as $key => $value) {
			$this->assertArrayHasKey($key, $list);
			$this->assertSame($list[$key], $list[$key]);
		}
	}

	/**
	 * @return void
	 */
	public function testCollectPluginLoaded() {
		$driver = ConnectionManager::get('test')->getDriver();
		$this->skipIf(!($driver instanceof Mysql || $driver instanceof Postgres), 'Only for Postgres/Mysql');

		$this->assertFalse(Plugin::isLoaded('Migrations'));

		$plugin = Plugin::getCollection()->create('Migrations');
		Plugin::getCollection()->add($plugin);

		$this->assertTrue(Plugin::isLoaded('Migrations'));

		$this->task = new TestDatabaseTableColumnTypeTask();
		$result = $this->task->collect();

		$this->assertCount(2, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\Migrations\Table::addColumn()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expectedList = [
			'text' => "'text'",
			'integer' => "'integer'",
		];
		foreach ($expectedList as $key => $value) {
			$this->assertArrayHasKey($key, $list);
			$this->assertSame($list[$key], $list[$key]);
		}

		Plugin::getCollection()->remove('Migrations');
		$this->assertFalse(Plugin::isLoaded('Migrations'));
	}

}