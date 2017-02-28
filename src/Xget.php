<?php

namespace eznio\xget;


use eznio\ar\Ar;
use GuzzleHttp\Client;
use XPathSelector\Selector;
use XPathSelector\Node;


class Xget
{
    const XML_TEMPLATE = '<?xml version="1.0" encoding="UTF-8"?><body>%s</body>';

    /** @var Client */
    protected $httpClient;

    /** @var array */
    protected $config = [];

    /** @var string */
    protected $url;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param Client $httpClient
     * @return $this
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param array $config
     * @return array
     */
    public function parse($config = [])
    {
        if (count($config) > 0) {
            $this->config = $config;
        }

        $result = [];
        foreach ($config as $key => $item) {
            if (!is_array($item)) {
                $result[$key] = $this->parseSingleElement($item);
            } else {
                $result[$key] = $this->parseNestedElements($item);
            }
        }
        return $result;
    }

    /**
     * @param $itemXpath
     * @return array
     */
    protected function parseSingleElement($itemXpath)
    {
        $pageBody = $this->loadPage();
        $elements = $this->findRootElements($pageBody, $itemXpath);

        return Ar::map($elements, function($element) {
            /** @var $element Node */
            return $element->innerHTML();
        });
    }

    /**
     * @param $elementsDescription
     * @return array
     */
    protected function parseNestedElements($elementsDescription)
    {
        $pageBody = $this->loadPage();
        $elements = $this->findRootElements($pageBody, Ar::get($elementsDescription, '@'));
        unset($elementsDescription['@']);

        $result = [];
        foreach ($elements as $key => $element) {
            /** @var Node $element */
            $innerXs = Selector::loadHTML('<?xml version="1.0" encoding="UTF-8"?><body>' . $element->innerHTML() . '</body>');

            foreach ($elementsDescription as $nodeKey => $nodeValue) {
                $result[$key][$nodeKey] = $innerXs->find($nodeValue)->extract();
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    protected function loadPage()
    {
        $page = $this->httpClient->get($this->url);
        return $page->getBody()->getContents();
    }

    /**
     * @param $pageBody
     * @param $selector
     * @return array
     */
    public function findRootElements($pageBody, $selector)
    {
        $xs = Selector::loadHTML($pageBody);
        return $xs->findAll($selector);
    }
}
