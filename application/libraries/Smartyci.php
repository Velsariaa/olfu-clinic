<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smartyci extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        
        $this->caching = 0;
        
        $this->setTemplateDir( VIEWPATH );
        
        $this->setCompileDir( APPPATH.'third_party/smarty/templates_c' );
        
        $this->setConfigDir( APPPATH.'third_party/smarty/configs' );

        $this->addPluginsDir( APPPATH.'third_party/smarty/plugins' );
        
        #$this->setCacheDir( APPPATH . 'cache' );

        $this->setCaching(Smarty::CACHING_OFF);

        $this->clearAllCache(3600);

        $this->clearCompiledTemplate(null, null, 10);
        
        $this->clearCompiledTemplate();

        #

        $this->configLoad('assets.conf');

        #

        $this->registerPlugin('function', 'flashdata', 'flashdata');

        $this->registerPlugin('modifier', 'base_url', 'base_url');

        $this->registerPlugin('modifier', 'strProperCase', 'strProperCase');

        $this->registerPlugin('function', 'uuid4', 'uuid4');
    }
    public function _()
    {
        return new self();
    }
    public function dumpTplVars()
    {
        dj( $this->getTemplateVars() );
    }
}