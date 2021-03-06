<?php

namespace Sabre\DAV\Browser;

use
    Sabre\DAV,
    Sabre\HTTP\RequestInterface,
    Sabre\HTTP\ResponseInterface;

/**
 * This is a simple plugin that will map any GET request for non-files to
 * PROPFIND allprops-requests.
 *
 * This should allow easy debugging of PROPFIND
 *
 * @copyright Copyright (C) 2007-2014 fruux GmbH (https://fruux.com/).
 * @author Evert Pot (http://evertpot.com/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class MapGetToPropFind extends DAV\ServerPlugin {

    /**
     * reference to server class
     *
     * @var Sabre\DAV\Server
     */
    protected $server;

    /**
     * Initializes the plugin and subscribes to events
     *
     * @param DAV\Server $server
     * @return void
     */
    public function initialize(DAV\Server $server) {

        $this->server = $server;
        $this->server->on('method:GET', [$this,'httpGet'], 90);
    }

    /**
     * This method intercepts GET requests to non-files, and changes it into an HTTP PROPFIND request
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function httpGet(RequestInterface $request, ResponseInterface $response) {

        $node = $this->server->tree->getNodeForPath($request->getPath());
        if ($node instanceof DAV\IFile) return;

        $subRequest = clone $request;
        $subRequest->setMethod('PROPFIND');

        $this->server->invokeMethod($subRequest,$response);
        return false;

    }

}
