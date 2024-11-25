<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * xmstock module
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          Mage Gregory (AKA Mage)
 */

function xoops_module_update_xmstock(XoopsModule $module, $previousVersion = null) {
	// Passage de la version 0.2.0 à 0.3.0
    if ($previousVersion < '0.3.0') {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
		$sql = "ALTER TABLE `" . $db->prefix('xmstock_itemorder') . "` ADD `itemorder_needsyear` varchar(10) NOT NULL DEFAULT '' AFTER `itemorder_amount`;";
        $db->query($sql);
        $sql = "ALTER TABLE `" . $db->prefix('xmstock_transfer') . "` ADD `transfer_needsyear` varchar(10) NOT NULL DEFAULT '' AFTER `transfer_userid`;";
        $db->query($sql);
    }
    // Passage de la version 1.1.1 à 1.2.0
    if ($previousVersion < '1.2.0') {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "ALTER TABLE `" . $db->prefix('xmstock_stock') . "` ADD `stock_mini` smallint(5) unsigned NOT NULL DEFAULT '0' AFTER `stock_location`;";
        $db->query($sql);
    }

    // Passage de la version 1.2.0 à 1.3.0
    if ($previousVersion < '1.3.0') {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "ALTER TABLE `" . $db->prefix('xmstock_stock') . "` ADD `stock_order` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `stock_mini`;";
        $db->query($sql);

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "ALTER TABLE `" . $db->prefix('xmstock_itemorder') . "` ADD `itemorder_width` double(10,4) NOT NULL DEFAULT '0.0000' AFTER `itemorder_length`;";
        $db->query($sql);
    }
    return true;
}