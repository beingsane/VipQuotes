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
?>
<div class="vq-quote<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    <?php if($this->params->get("quote_display_hits", 0)) {?>
	<div class="vq-hits"><?php echo JText::sprintf("COM_VIPQUOTES_HITS", $this->item->hits);?></div>
    <?php }?>

    <?php echo $this->item->event->onContentBeforeDisplay;?>
	<blockquote>
		<?php echo $this->item->quote; ?>
	</blockquote>   
	<?php echo $this->item->event->onContentAfterDisplay;?>
</div>
<?php echo $this->version->backlink; ?>