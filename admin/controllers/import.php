<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Vip Quotes import controller
 *
 * @package     VipQuotes
 * @subpackage  Components
 */
class VipQuotesControllerImport extends Prism\Controller\Form\Backend
{
    public function getModel($name = 'Import', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function quotes()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $data = array_merge($data, $file);

        $redirectOptions = array(
            "view" => "import",
            "task" => $this->getTask()
        );

        $model = $this->getModel();
        /** @var $model VipQuotesModelImport */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_FORM_CANNOT_BE_LOADED"), 500);
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        $fileData = Joomla\Utilities\ArrayHelper::getValue($data, "data");

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.archive');

        try {

            $filePath = $model->uploadFile($fileData);

            $model->validateFileType($filePath);

            $resetId = Joomla\Utilities\ArrayHelper::getValue($data, "reset_id", false, "bool");
            $model->importQuotes($filePath, $resetId);

        } catch (RuntimeException $e) {
            $this->displayWarning($e->getMessage(), $redirectOptions);
            return;
        } catch (Exception $e) {

            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));

        }

        $this->displayMessage(JText::_("COM_VIPQUOTES_DATA_IMPORTED"), $redirectOptions);
    }

    public function cancel($key = null)
    {
        $link = $this->defaultLink . "&view=quotes";
        $this->setRedirect(JRoute::_($link, false));
    }
}
