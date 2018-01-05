<?php
/*
 * http://rabaix.net/en/articles/2008/03/13/using-soap-php-with-ntlm-authentication
 * Copyright (c) 2008 Invest-In-France Agency http://www.invest-in-france.org
 *
 * Author : Thomas Rabaix
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

namespace Drupal\ivision_nav16\iVisionController\ntlm\httpStream;

class NTLMStream {
  private $path;
  private $mode;
  private $options;
  private $opened_path;
  private $buffer;
  private $pos;
  private $ch;
  public static $user;
  public static $password;

  /**
   * Open the stream
   *
   * @param string $path
   * @param string $mode
   * @param string $options
   * @param string $opened_path
   * @return bool
   */
  public function stream_open($path, $mode, $options, $opened_path) {
    //echo "[NTLMStream::stream_open] $path , mode=$mode \n";
    $this->path = $path;
    $this->mode = $mode;
    $this->options = $options;
    $this->opened_path = $opened_path;
    $this->createBuffer($path);
    return TRUE;
  }

  /**
   * Close the stream
   *
   */
  public function stream_close() {
    curl_close($this->ch);
  }

  /**
   * Read the stream
   *
   * @param int $count number of bytes to read
   * @return string content from pos to count
   */
  public function stream_read($count) {
    if (strlen($this->buffer) == 0) {
      return FALSE;
    }
    $read = substr($this->buffer, $this->pos, $count);
    $this->pos += $count;
    return $read;
  }

  /**
   * Write to the stream (actually it does not happen at all...)
   *
   * @param string $data data to be written to the buffer
   * @return bool
   */
  public function stream_write($data) {
    if (strlen($this->buffer) == 0) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   *
   * @return true if eof else false
   */
  public function stream_eof() {
    if ($this->pos > strlen($this->buffer)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @return int the position of the current read pointer
   */
  public function stream_tell() {
    return $this->pos;
  }

  /**
   * Flush stream data
   */
  public function stream_flush() {
    $this->buffer = NULL;
    $this->pos = NULL;
  }

  /**
   * Stat the file, return only the size of the buffer
   *
   * @return array stat information
   */
  public function stream_stat() {
    $this->createBuffer($this->path);
    $stat = array(
      'size' => strlen($this->buffer),
    );
    return $stat;
  }

  /**
   * Stat the url, return only the size of the buffer
   *
   * @param string $path
   * @param string $flags
   * @return array stat information
   */
  public function url_stat($path, $flags) {
    $this->createBuffer($path);
    $stat = array(
      'size' => strlen($this->buffer),
    );
    return $stat;
  }

  /**
   * Create the buffer by requesting the url through cURL
   *
   * @param string $path
   */
  private function createBuffer($path) {
    if ($this->buffer) {
      return;
    }
    $this->ch = curl_init($path);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
    curl_setopt($this->ch, CURLOPT_USERPWD, self::$user . ':' . self::$password);
    $this->buffer = curl_exec($this->ch);
    $this->pos = 0;
  }
}
