<?php namespace rdbms\criterion;

/**
 * Represents a query criterion to be used in a Criteria query
 *
 * @see      xp://rdbms.Criteria#add
 * @purpose  Interface
 */
interface Criterion {
  /**
   * Returns the fragment SQL
   *
   * @param   rdbms.DBConnection conn
   * @param   rdbms.Peer peer
   * @return  string
   * @throws  rdbms.SQLStateException
   */
  public function asSql(\rdbms\DBConnection $conn, \rdbms\Peer $peer);
}
