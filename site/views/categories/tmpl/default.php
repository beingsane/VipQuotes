<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="vq-categories<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php foreach($this->items as $item) {?>
    	<div class="q_category">
    		<?php $categoryParams = json_decode($item->params, true);
    		    $categoryImage = JArrayHelper::getValue($categoryParams, "image");
    		    if($categoryImage) {
    		?>
    		<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug).$this->tmplValue); ?>">
    		<img src="<?php echo $categoryImage;?>" alt="<?php echo $item->title;?>" />
    		</a><br />
    		<?php } ?>
    		<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug).$this->tmplValue); ?>">
    		<?php echo $item->title;?>
    		<?php echo JHtml::_("vipquotes.categoryQuotesNumber", $item->id, $this->displayNumber);?>
    		</a>
    	</div>
    <?php }?>
    
    <div class="clearfix">&nbsp;</div>
    <div class="pagination">
    
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>
    
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <div class="clearfix">&nbsp;</div>
</div>
<div class="clearfix"></div>
<?php echo $this->version->backlink; ?>