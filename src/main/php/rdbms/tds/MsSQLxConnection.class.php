<?php namespace rdbms\tds;

use rdbms\mssql\MsSQLDialect;


/**
 * Connection to MSSQL Databases via TDS 7.0
 *
 * @see   xp://rdbms.tds.TdsConnection
 */
class MsSQLxConnection extends TdsConnection {

  static function __static() {
    \rdbms\DriverManager::register('mssql+x', new \lang\XPClass(__CLASS__));
  }
  
  /**
   * Returns dialect
   *
   * @return  rdbms.SQLDialect
   */
  protected function getDialect() {
    return new MsSQLDialect();
  }
  
  /**
   * Returns protocol
   *
   * @param   peer.Socket sock
   * @return  rdbms.tds.TdsProtocol
   */
  protected function getProtocol($sock) {
    return new TdsV7Protocol($sock);
  }
}
