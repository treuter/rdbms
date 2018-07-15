<?php namespace rdbms\unittest;

use lang\FormatException;
use rdbms\DSN;

/**
 * Tests the DSN class
 *
 * @see  xp://rdbms.DSN
 */
class DSNTest extends \unittest\TestCase {

  #[@test]
  public function stringRepresentationWithPassword() {
    $this->assertEquals(
      'rdbms.DSN@(sybase://sa:********@localhost:1999/CAFFEINE?autoconnect=1)',
      (new DSN('sybase://sa:password@localhost:1999/CAFFEINE?autoconnect=1'))->toString()
    );
  }
  
  #[@test]
  public function stringRepresentationWithoutPassword() {
    $this->assertEquals(
      'rdbms.DSN@(mysql://root@localhost/?log=default)',
      (new DSN('mysql://root@localhost/?log=default'))->toString()
    );
  }

  #[@test]
  public function asStringRemovesPassword() {
    $this->assertEquals(
      'mysql://user:********@localhost/?log=default',
      (new DSN('mysql://user:foobar@localhost/?log=default'))->asString()
    );
  }

  #[@test]
  public function asStringKeepsPasswordIfRequested() {
    $this->assertEquals(
      'mysql://user:foobar@localhost/?log=default',
      (new DSN('mysql://user:foobar@localhost/?log=default'))->asString(true)
    );
  }

  #[@test]
  public function asStringSkipsUserEvenWithRaw() {
    $this->assertEquals(
      'mysql://localhost/?log=default',
      (new DSN('mysql://localhost/?log=default'))->asString(true)
    );
  }

  #[@test]
  public function driver() {
    $this->assertEquals(
      'sybase', 
      (new DSN('sybase://TEST/'))->getDriver()
    );
  }

  #[@test, @expect(FormatException::class)]
  public function noDriver() {
    new DSN('');
  }

  #[@test]
  public function host() {
    $this->assertEquals(
      'TEST', 
      (new DSN('sybase://TEST/'))->getHost()
    );
  }
  
  #[@test]
  public function port() {
    $this->assertEquals(
      1999, 
      (new DSN('sybase://TEST:1999/'))->getPort()
    );
  }

  #[@test]
  public function portDefault() {
    $this->assertEquals(
      1999, 
      (new DSN('sybase://TEST:1999/'))->getPort(5000)
    );
  }

  #[@test]
  public function noPort() {
    $this->assertNull((new DSN('sybase://TEST/'))->getPort());
  }

  #[@test]
  public function noPortDefault() {
    $this->assertEquals(
      1999, 
      (new DSN('sybase://TEST/'))->getPort(1999)
    );
  }

  #[@test]
  public function database() {
    $this->assertEquals(
      'CAFFEINE', 
      (new DSN('sybase://TEST/CAFFEINE'))->getDatabase()
    );
  }

  #[@test]
  public function databaseDefault() {
    $this->assertEquals(
      'CAFFEINE', 
      (new DSN('sybase://TEST/CAFFEINE'))->getDatabase('master')
    );
  }

  #[@test]
  public function noDatabase() {
    $this->assertNull((new DSN('mysql://root@localhost'))->getDatabase());
  }

  #[@test]
  public function noDatabaseDefault() {
    $this->assertEquals(
      'master', 
      (new DSN('mysql://root@localhost'))->getDatabase('master')
    );
  }

  #[@test]
  public function slashDatabase() {
    $this->assertNull((new DSN('mysql://root@localhost/'))->getDatabase());
  }

  #[@test]
  public function slashDatabaseDefault() {
    $this->assertEquals(
      'master', 
      (new DSN('mysql://root@localhost/'))->getDatabase('master')
    );
  }

  #[@test]
  public function fileDatabase() {
    $this->assertEquals(
      '/usr/local/fb/jobs.fdb', 
      (new DSN('ibase://localhost//usr/local/fb/jobs.fdb'))->getDatabase()
    );
  }

  #[@test]
  public function user() {
    $this->assertEquals(
      'sa', 
      (new DSN('sybase://sa@TEST'))->getUser()
    );
  }

  #[@test]
  public function userDefault() {
    $this->assertEquals(
      'sa', 
      (new DSN('sybase://sa@TEST'))->getUser('reader')
    );
  }

  #[@test]
  public function noUser() {
    $this->assertNull((new DSN('sybase://TEST'))->getUser());
  }

  #[@test]
  public function noUserDefault() {
    $this->assertEquals(
      'reader', 
      (new DSN('sybase://TEST'))->getUser('reader')
    );
  }

  #[@test]
  public function password() {
    $this->assertEquals(
      'password', 
      (new DSN('sybase://sa:password@TEST'))->getPassword()
    );
  }

  #[@test]
  public function passwordDefault() {
    $this->assertEquals(
      'password', 
      (new DSN('sybase://sa:password@TEST'))->getPassword('secret')
    );
  }

  #[@test]
  public function noPassword() {
    $this->assertNull((new DSN('sybase://sa@TEST'))->getPassword());
  }

  #[@test]
  public function noPasswordDefault() {
    $this->assertEquals(
      'secret', 
      (new DSN('sybase://sa@TEST'))->getPassword('secret')
    );
  }
  
  #[@test]
  public function stringPropertyValue() {
    $this->assertEquals(
      'default', 
      (new DSN('sybase://sa@TEST?log=default'))->getProperty('log')
    );
  }

  #[@test]
  public function arrayPropertyValue() {
    $this->assertEquals(
      ['util.log.LogObserver' => 'default'], 
      (new DSN('pgsql://postgres:1433/db?observer[util.log.LogObserver]=default'))->getProperty('observer')
    );
  }

  #[@test]
  public function twoDsnsCreatedFromSameStringAreEqual() {
    $string= 'scheme://user:password@host/DATABASE?log=default&autoconnect=1';
    $this->assertEquals(new DSN($string), new DSN($string));
  }

  #[@test]
  public function twoDsnsWithDifferingAutoconnectNotEqual() {
    $this->assertNotEquals(
      new DSN('scheme://user:password@host/DATABASE?log=default&autoconnect=0'), 
      new DSN('scheme://user:password@host/DATABASE?log=default&autoconnect=1')
    );
  }

  #[@test]
  public function twoDsnsWithDifferingParamsNotEqual() {
    $this->assertNotEquals(
      new DSN('scheme://user:password@host/DATABASE'), 
      new DSN('scheme://user:password@host/DATABASE?log=default')
    );
  }

  #[@test]
  public function twoDsnsWithDifferingFlagParamsNotEqual() {
    $this->assertNotEquals(
      new DSN('scheme://user:password@host/DATABASE'), 
      new DSN('scheme://user:password@host/DATABASE?autoconnect=1')
    );
  }

  #[@test]
  public function twoDsnsWithDifferingObserverParamsNotEqual() {
    $this->assertNotEquals(
      new DSN('scheme://user:password@host/DATABASE?observer[rdbms.sybase.SybaseShowplanObserver]=sql'), 
      new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default')
    );
  }

  #[@test]
  public function twoDsnsWithDifferingObserverParamValuesNotEqual() {
    $this->assertNotEquals(
      new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=sql'), 
      new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default')
    );
  }

  #[@test]
  public function twoDsnsWithSameObserverParamsEqual() {
    $this->assertEquals(
      new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default'), 
      new DSN('scheme://user:password@host/DATABASE?observer[util.log.LogObserver]=default')
    );
  }

  #[@test]
  public function twoDsnsWithDifferentlyOrderedParamsAreEqual() {
    $this->assertEquals(
      new DSN('scheme://host/DATABASE?autoconnect=1&observer[rdbms.sybase.SybaseShowplanObserver]=sql&log=default'), 
      new DSN('scheme://host/DATABASE?log=default&observer[rdbms.sybase.SybaseShowplanObserver]=sql&autoconnect=1')
    );
  }

  #[@test]
  public function cloning() {
    $dsn= new DSN('mysql://root:password@localhost/');
    $clone= clone $dsn;
    $clone->url->setPassword(null);
    $this->assertEquals('password', $dsn->getPassword());
  }

  #[@test]
  public function withoutPassword() {
    $dsn= new DSN('mysql://root:password@localhost/');
    $clean= $dsn->withoutPassword();
    $this->assertNull($clean->getPassword());
  }
}
