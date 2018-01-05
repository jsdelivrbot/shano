<?php

/*
 *
 */

namespace Drupal\ivision_nav16\iVisionController;

use LogicException;

class IVisionLogger implements IVisionLoggerInterface {
  protected $logs = [];

  /**
   * @param bool $level
   *  Log level (debug, error, notice etc.)
   * @return array
   *  All log messages.
   */
  public function getLogs($level = FALSE) {
    return FALSE === $level ? $this->logs : $this->logs[$level];
  }

  /**
   * @inheritdoc
   */
  public function log($level, $message, array $context = array()) {
    if (!empty($context)) {
      $this->logs[$level][][$message] = $context;
    }
    else {
      $this->logs[$level][] = $message;
    }
  }

  /**
   * @inheritdoc
   */
  public function emergency($message, array $context = array()) {
    $this->log('emergency', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function alert($message, array $context = array()) {
    $this->log('alert', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function critical($message, array $context = array()) {
    $this->log('critical', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function error($message, array $context = array()) {
    $this->log('error', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function warning($message, array $context = array()) {
    $this->log('warning', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function notice($message, array $context = array()) {
    $this->log('notice', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function info($message, array $context = array()) {
    $this->log('info', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function debug($message, array $context = array()) {
    $this->log('debug', $message, $context);
  }

  /**
   * @inheritdoc
   */
  public function renderLogs() {
    $log_rendered = '';
    foreach ($this->getLogs() as $level => $logs) {
      foreach ($logs as $log_entry) {
        if (is_array($log_entry)) {
          foreach ($log_entry as $log_key => $log_value) {
            if (is_array($log_value)) {
              try {
                strlen(serialize($log_value));
              } catch (LogicException $exception) {
                echo $log_key;
                exit;
              }

              $log_value = '<pre>' . var_export($log_value, TRUE) . '</pre>';
            }
            $log_rendered .= '<p><strong>' . $level . ':</strong><br>' . $log_key . ': ' . $log_value . '</p>';
          }
        }
        else {
          $log_rendered .= '<p><strong>' . $level . ':</strong> ' . $log_entry . '</p>';
        }
      }
    }
    return $log_rendered;
  }

}
