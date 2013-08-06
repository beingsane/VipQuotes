<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipQuotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class VipQuotesViewCategories extends JView {
    
    protected $state      = null;
    protected $items      = null;
    protected $pagination = null;
    
    protected $option     = null;
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }
    
    public function display($tpl = null) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Initialise variables
        $this->state          = $this->get("State");
        $this->items          = $this->get('Items');
        $this->pagination     = $this->get('Pagination');
        
        $this->params         = $this->state->get("params");
        $this->displayNumber  = $this->params->get("categories_display_counter", 0);

        // HTML Helpers
        JHtml::addIncludePath(VIPQUOTES_PATH_COMPONENT_SITE.'/helpers/html');
        
        $this->version        = new VipQuotesVersion();
        
        $this->prepareDocument();
                
        // Prepare TMPL variable
        $tmpl = $app->input->get->get("tmpl", "");
        $this->tmplValue = "";
        if(strcmp("component", $tmpl) == 0) {
            $this->tmplValue = "&tmpl=component";
        }
        
        parent::display($tpl);
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){
        
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();
        
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if($menu){
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }else{
            $this->params->def('page_heading', JText::_('COM_VIPQUOTES_CATEGORIES_DEFAULT_PAGE_TITLE'));
        }
        
        // Set page title
        $title = $this->params->get('page_title', '');
        if(empty($title)){
            $title = $app->getCfg('sitename');
        }elseif($app->getCfg('sitename_pagetitles', 0)){
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        $this->document->setTitle($title);
        
        // Meta Description
        if($this->params->get('menu-meta_description')){
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }
        
        // Meta keywords
        if($this->params->get('menu-meta_keywords')){
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }
        
        // Head styles
        $this->document->addStyleSheet( JURI::root() . 'media/'.$this->option.'/css/site/style.css');
        
    }
    
}