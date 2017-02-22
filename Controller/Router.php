<?php

namespace Styla\Connect2\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     *
     * @var \Styla\Connect2\Helper\Config
     */
    protected $_configHelper;

    /**
     * @param \Magento\Framework\App\ActionFactory     $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Styla\Connect2\Helper\Config $configHelper
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_response     = $response;
        $this->_configHelper = $configHelper;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        if ($this->isMagazinePage($request)) {
            $request->setModuleName('stylaconnect2page')->setControllerName('page')->setActionName('view');
        } else {
            //There is no match
            return false;
        }

        //we want the part after the initial magazine uri, as it may point us to the user's intention
        $route = $this->_getRouteSettings($identifier);
        $request->setParam('path', $route);

        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward', ['request' => $request]
        );
    }
    
    /**
     * Are we currently on a magazine page?
     * 
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function isMagazinePage(\Magento\Framework\App\RequestInterface $request)
    {
        $stylaFrontendName = $this->_getFrontendName(); //my configured magazine uri
        
        return ($request->getFrontName() == $stylaFrontendName);
    }

    /**
     * Get only the last part of the route, leading up to a specific page
     *
     * @param string $path
     * 
     * @return string
     */
    protected function _getRouteSettings($path)
    {
        //whatever the pathinfo i'm getting, all i need is anything to the right of my current magazine name:
        $magazineName = $this->_getFrontendName();
        $route = false;
        if(false !== ($pos = strpos($path, $magazineName))) {
            $route = substr($path, stripos($path, $magazineName) + strlen($magazineName));
        }
        
        return $route;
    }
    
    /**
     * 
     * @param \Magento\Framework\App\RequestInterface $request
     * @return string
     */
    protected function _getRequestParamsString(\Magento\Framework\App\RequestInterface $request)
    {
        $allRequestParameters = $request->getQuery();
        
        return count($allRequestParameters) ? http_build_query($allRequestParameters) : '';
    }

    /**
     *
     * @return string
     */
    protected function _getFrontendName()
    {
        return $this->_configHelper->getFrontendName();
    }
}
