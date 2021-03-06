<?php namespace rdbms\unittest\sqlite3;

use lang\IllegalStateException;
use rdbms\SQLStateException;
use rdbms\SQLStatementFailedException;
use rdbms\sqlite3\SQLite3Connection;
use rdbms\sqlite3\SQLite3ResultSet;

/**
 * Testcase for rdbms.sqlite3.SQLite3Connection
 *
 * @see      xp://rdbms.sqlite3.SQLite3Connection
 */
#[@action(new \unittest\actions\ExtensionAvailable('sqlite3'))]
class SQLite3ConnectionTest extends \unittest\TestCase {
  protected $conn= null;

  /**
   * Set up this test
   */
  public function setUp() {
    $this->conn= new SQLite3Connection(new \rdbms\DSN('sqlite+3:///:memory:?autoconnect=1'));
  }

  #[@test]
  public function close() {
    $this->assertFalse($this->conn->close());
  }

  #[@test]
  public function connect_then_close_both_return_true() {
    $this->assertTrue($this->conn->connect());
    $this->assertTrue($this->conn->close());
  }

  #[@test]
  public function second_close_call_returns_false() {
    $this->assertTrue($this->conn->connect());
    $this->assertTrue($this->conn->close());
    $this->assertFalse($this->conn->close());
  }

  #[@test, @expect(SQLStatementFailedException::class)]
  public function selectdb() {
    $this->conn->selectdb('foo');
  }

  #[@test]
  public function query() {
    $this->conn->connect();
    $result= $this->conn->query('select 1 as one');
    
    $this->assertInstanceOf(SQLite3ResultSet::class, $result);
    $this->assertEquals(['one' => 1], $result->next());
  }

  #[@test]
  public function query_returns_success_on_empty_resultset() {
    $this->conn->connect();
    $this->assertTrue($this->conn->query('pragma user_version = 1')->isSuccess());
  }

  #[@test, @expect(SQLStatementFailedException::class)]
  public function query_throws_exception_for_broken_statement() {
    $this->conn->connect();
    $this->conn->query('select something with wrong syntax');
  }

  #[@test]
  public function query_returns_result_for_empty_resultset() {
    $this->conn->connect();
    $result= $this->conn->query('select 1 where 1 = 0');

    $this->assertInstanceOf(SQLite3ResultSet::class, $result);
    $this->assertNull($result->next());
  }

  #[@test]
  public function create_table_and_fill() {
    $this->conn->query('create temp table testthewest (
      col1 integer primary key asc,
      str2 text,
      col3 real,
      col4 numeric
    )');

    $q= $this->conn->insert('into testthewest (str2, col3, col4) values (%s, %f, %f)',
      "Hello World",
      1.5,
      12345.67
    );
    $this->assertEquals(1, $q); // 1 Row inserted
    $this->assertEquals(1, $this->conn->identity());
  }

  #[@test]
  public function select_from_prefilled_table_yields_correct_column_types() {
    $this->create_table_and_fill();
    $this->assertEquals([[
      'col1' => 1,
      'str2' => 'Hello World',
      'col3' => 1.5,
      'col4' => 12345.67
    ]], $this->conn->select('* from testthewest'));
  }

  #[@test]
  public function unbuffered_queries_simulated() {
    $this->conn->connect();
    $this->assertEquals([1 => 1], $this->conn->query('select 1', false)->next());
  }

  #[@test, @expect(SQLStateException::class)]
  public function identity_throws_exception_when_not_connected() {
    $this->conn->identity();
  }
 }
