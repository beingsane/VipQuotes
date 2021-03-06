<?php
/**
 * @package      VipQuotes
 * @subpackage   Filters
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Filter;

use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manages filter options.
 *
 * @package      VipQuotes
 * @subpackage   Filters
 */
class Options
{
    protected $options = array();

    /**
     * Database driver.
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    protected static $instance;

    /**
     * Initialize the object.
     *
     * <code>
     * $filters = new VipQuotes\Filter\Options(\JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver $db Database object.
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Initialize the object.
     *
     * <code>
     * $filters = VipQuotes\Filter\Options::getInstance(\JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver $db Database object.
     *
     * @return null|Options
     */
    public static function getInstance(\JDatabaseDriver $db)
    {
        if (is_null(self::$instance)) {
            self::$instance = new Options($db);
        }

        return self::$instance;
    }

    /**
     * Return a list with authors.
     *
     * <code>
     * $options = array(
     *     "state" => 1 // 1 for published authors, 0 for not published authors, null for both.
     * );
     * $filters = new VipQuotes\Filter\Options(\JFactory::getDbo());
     *
     * $authors = $options->getAuthors($options);
     * </code>
     *
     * @param array $options
     *
     * @return array()
     */
    public function getAuthors($options = array())
    {
        if (!isset($this->options["authors"])) {

            $query = $this->db->getQuery(true);

            $query
                ->select("a.id AS value, a.name AS text")
                ->from($this->db->quoteName("#__vq_authors", "a"))
                ->order("a.name");

            // Filter by state.
            $state = ArrayHelper::getValue($options, "state");
            if (is_null($state)) { // All
                $query->where("a.published IN (0, 1)");
            } elseif ($state == 0) { // Unpublished
                $query->where("a.published = 0");
            } elseif ($state == 1) { // Published
                $query->where("a.published = 1");
            }

            $this->db->setQuery($query);
            $rows = $this->db->loadAssocList();

            if (!$rows) {
                $rows = array();
            }

            $this->options["authors"] = $rows;

        }

        return $this->options["authors"];
    }

    /**
     * Return a list with options used for ordering quotes.
     *
     * <code>
     * $filters = new VipQuotes\Filter\Options(\JFactory::getDbo());
     *
     * $options = $options->getQuotesOrdering();
     * </code>
     *
     * @return array()
     */
    public function getQuotesOrdering()
    {
        return array(
            array("value" => '0', "text" => \JText::_("LIB_VIPQUOTES_ORDERING")),
            array("value" => '1', "text" => \JText::_("LIB_VIPQUOTES_ADDED_ASC")),
            array("value" => '2', "text" => \JText::_("LIB_VIPQUOTES_ADDED_DESC")),
            array("value" => '3', "text" => \JText::_("LIB_VIPQUOTES_AUTHOR_NAME")),
            array("value" => '4', "text" => \JText::_("LIB_VIPQUOTES_POPULAR_QUOTES")),
            array("value" => '5', "text" => \JText::_("LIB_VIPQUOTES_POPULAR_AUTHORS")),
        );
    }

    /**
     * Return a list with options used for ordering authors.
     *
     * <code>
     * $filters = new VipQuotes\Filter\Options(\JFactory::getDbo());
     *
     * $options = $options->getAuthorsOrdering();
     * </code>
     *
     * @return array()
     */
    public function getAuthorsOrdering()
    {
        return array(
            array("value" => '0', "text" => \JText::_("LIB_VIPQUOTES_ORDERING")),
            array("value" => '1', "text" => \JText::_("LIB_VIPQUOTES_AUTHOR_NAME")),
            array("value" => '2', "text" => \JText::_("LIB_VIPQUOTES_POPULAR_AUTHORS")),
        );
    }
}
