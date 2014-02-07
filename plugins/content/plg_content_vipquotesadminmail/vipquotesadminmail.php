<?php
/**
 * @package		 VipQuotes
 * @subpackage	 Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('vipquotes.init');

/**
 * This plugin send notification mails to the administrator. 
 *
 * @package		VipQuotes
 * @subpackage	Plugins
 */
class plgContentVipQuotesAdminMail extends JPlugin {
    
    protected $autoloadLanguage = true;
    
    /**
     * This method is executed when someone post a quote.
     * 
     * @param string            $context
     * @param stdObject         $item
     * @param boolean           $isNew
     * 
     * @return boolean
     */
    public function onContentAfterSave($context, $item, $isNew) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }

        if(strcmp("com_vipquotes.quote", $context) != 0){
            return;
        }
        
        // Check for enabled option for sending mail 
        // when user post a quote.
        $emailId = $this->params->get("send_when_post", 0);
        if(!$emailId) {
            return true;
        }
        
        if(!empty($item->id) AND $isNew) {
            
            // Load class VipQuotesEmail.
            jimport("vipquotes.email");
            
            // Send email to the administrator.
            $return = $this->sendMails($item, $emailId);
            
            // Check for error.
            if ($return !== true) {
                Jlog::add(JText::_("PLG_CONTENT_VIPQUOTESADMINMAIL_ERROR_MAIL_SENDING_USER"));
                return false;
            }
            
        }
        
        return true;
        
    }
    
    protected function sendMails($item, $emailId) {
    
        $app = JFactory::getApplication();
        /** @var $app JSite **/
    
        $result = false;
        
        // Send mail to the administrator
        if(!empty($emailId)) {
            
            // Get website
            $uri        = JUri::getInstance();
            $website    = $uri->toString(array("scheme", "host"));
            
            $emailMode  = $this->params->get("email_mode", 0);
            
            jimport("vipquotes.quote");
            $quote      = new VipQuotesQuote(JFactory::getDbo());
            $quote->load($item->id);
            
            $subject    = JText::sprintf("PLG_CONTENT_VIPQUOTESADMINMAIL_DEFAULT_SUBJECT", $quote->getAuthor(), $quote->getCategory());
            
            // Prepare default data that will be parsed.
            $data = array(
                "site_name"         => $app->getCfg("sitename"),
                "site_url"          => JUri::root(),
                "item_title"        => $subject,
                "item_url"          => $website.JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid)),
                "category_name"     => $quote->getCategory(),
                "category_url"      => $website.JRoute::_(VipQuotesHelperRoute::getCategoryRoute($quote->getCategorySlug())),
                "author_name"       => $quote->getAuthor(),
                "author_url"        => $website.JRoute::_(VipQuotesHelperRoute::getAuthorRoute($quote->getAuthorSlug())),
            );
            
            // Get the e-mail.
            $email    = new VipQuotesEmail();
            $email->setDb(JFactory::getDbo());
            $email->load($emailId);
            
            // Check for valid predefined e-mail.
            if(!$email->getId()) {
                return false;
            }
    
            if(!$email->getSenderName()) {
                $email->setSenderName($app->getCfg("fromname"));
            }
            if(!$email->getSenderEmail()) {
                $email->setSenderEmail($app->getCfg("mailfrom"));
            }
    
            $recipientName = $email->getSenderName();
            $recipientMail = $email->getSenderEmail();
    
            // Prepare data for parsing
            $data["sender_name"]     =  $email->getSenderName();
            $data["sender_email"]    =  $email->getSenderEmail();
            $data["recipient_name"]  =  $recipientName;
            $data["recipient_email"] =  $recipientMail;
    
            $email->parse($data);
            $subject    = $email->getSubject();
    
            $mailer  = JFactory::getMailer();
            if(strcmp("html", $emailMode) == 0) { // Send as HTML message
                
                $body    = $email->getBody(VipQuotesEmail::MAIL_MODE_HTML);
                $result  = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, VipQuotesEmail::MAIL_MODE_HTML);
    
            } else { // Send as plain text.
                
                $body    = $email->getBody(VipQuotesEmail::MAIL_MODE_PLAIN);
                $result  = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, VipQuotesEmail::MAIL_MODE_PLAIN);
    
            }
            
        }
    
        return ($result !== true) ? false : true;
    }
}