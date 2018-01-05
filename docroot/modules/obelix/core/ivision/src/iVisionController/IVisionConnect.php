<?php
/**
 * User: MoeVoe
 * Date: 25.05.16
 * Time: 23:17
 */

namespace Drupal\ivision\iVisionController;


class IVisionConnect
{
    /**
     * @var array
     *  The base uri to Set up a Connection to the World Vision Webservice.
     */
    private $uri;

    /**
     * @var array
     *  The language to Set up a Connection to the World Vision Webservice.
     */
    private $language;

    /**
     * @var array
     *  The siteID to Set up a Connection to the World Vision Webservice.
     */
    private $siteID;

    /**
     * IVisionBase constructor.
     * @param null $uri
     * @param null $language
     * @param null $siteID
     * @throws IVisionException
     */
    public function __construct($uri = NULL, $language = NULL, $siteID = NULL)
    {
        if ($uri !== NULL && $language !== NULL && $siteID !== NULL) {
            $this->setConnection($uri, $language, $siteID);
        }
    }

    public function setConnection($uri, $language, $siteID)
    {
        if (!isset($uri)) {
            throw new IVisionException('API URI is missing');
        }
        if (!isset($language)) {
            throw new IVisionException('API language is missing');
        }
        if (!isset($siteID)) {
            throw new IVisionException('siteID ist missing');
        }
        $this->uri = $uri;
        $this->language = $language;
        $this->siteID = $siteID;
    }

    /**
     * @return array
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public function getSiteID()
    {
        return $this->siteID;
    }

}
