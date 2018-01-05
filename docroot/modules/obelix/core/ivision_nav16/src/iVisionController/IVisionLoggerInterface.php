<?php
/**
 * User: MoeVoe
 * Date: 23.03.17
 * Time: 11:44
 */

namespace Drupal\ivision_nav16\iVisionController;

use Psr\Log\LoggerInterface;

interface IVisionLoggerInterface extends LoggerInterface {

  /**
   * @param bool $level
   *  Log level (debug, error, notice etc.)
   * @return array
   *  All log messages.
   */
  public function getLogs($level = FALSE);

  /**
   * Render the logs in html format.
   * @return string
   */
  public function renderLogs();
}
