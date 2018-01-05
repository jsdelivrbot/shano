<?php
/**
 * User: MoeVoe
 * Date: 29.07.16
 * Time: 11:14
 */

namespace Drupal\payone;


/**
 * Class PayOne
 *
 * Old class from old system....
 *
 * @package Drupal\payone
 */
class PayOne
{

  /**
   * @var
   */
  var $frontend_url;

  /**
   * @var
   */
  var $secret;

  /**
   * @var array
   */
  var $protected_keys = array("aid", "portalid", "mode", "request", "clearingtype", "reference", "customerid", "param",
    "narrative_text", "successurl", "errorurl", "backurl", "storecarddata",
    "encoding", "display_name", "display_address", "autosubmit", "targetwindow",
    "amount", "currency", "due_time", "invoiceid", "invoiceappendix", "invoice_deliverymode", "eci",
    "id", "pr", "no", "de", "ti", "va",
    "productid", "accessname", "accesscode",
    "access_expiretime", "access_canceltime", "access_starttime", "access_period", "access_aboperiod",
    "access_price", "access_aboprice", "access_vat",
    "settleperiod", "settletime", "vaccountname", "vreference");

  /**
   * @param $url
   */
  function setFrontendUrl($url)
  {
    $this->frontend_url = $url;
  }

  /**
   * @param $secret
   */
  function setSecret($secret)
  {
    $this->secret = $secret;
  }


  /**
   *
   */
  function financegateFrontend()
  {

  }

  /**
   * @param $data
   * @return string
   */
  function generateHash($data)
  {

    sort($this->protected_keys);

    $hashstr = '';

    foreach ($this->protected_keys as $key) {
      if (isset($data[$key])) {
        if (is_array($data[$key])) {
          ksort($data[$key]);
          foreach ($data[$key] as $id => $val) {
            $hashstr .= $val;
          }
        } else {
          $hashstr .= $data[$key];
        }
      }
    }
    $hashstr .= $this->secret;
    $hash = md5($hashstr);
    return $hash;
  }

  /**
   * @param $request_array
   * @return mixed
   */
  function generateUrlByArray($request_array)
  {

    if (!$request_array['aid']
      || !$request_array['portalid']
      || !$this->frontend_url
      || !$this->secret
    ) {
      $output['errormessage'] = "Payone Frontend Setup Data not complete (Frontend-URL, AId, PortalId, Key)";
      return $output;
    }

    $request_url = '';
    foreach ($request_array as $key => $val) {
      if (is_array($val)) foreach ($val as $i => $val1) {
        $request_url .= "&" . $key . "[" . $i . "]=" . urlencode($val1);
      }
      else {
        $request_url .= "&" . $key . "=" . urlencode($val);
      }
    }
    $request_url = $this->frontend_url . "?" . substr($request_url, 1);

    $hash = $this->generateHash($request_array);

    $request_url .= "&hash=" . $hash;

    $output['redirecturl'] = $request_url;

    return $output;

  }

}
