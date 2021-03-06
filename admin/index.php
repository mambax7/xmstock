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

use Xmf\Module\Admin;

require __DIR__ . '/admin_header.php';

$moduleAdmin = Admin::getInstance();
$moduleAdmin->displayNavigation('index.php');
$moduleAdmin->addConfigModuleVersion('system', 212);
// xmarticle
if (is_dir(XOOPS_ROOT_PATH . '/modules/xmarticle')) {
    $moduleAdmin->addConfigModuleVersion('xmarticle', 10);
} else {
    $moduleAdmin->addConfigError(_MA_XMSTOCK_INDEXCONFIG_XMARTICLE_ERROR);
}
$moduleAdmin->displayIndex();

require __DIR__ . '/admin_footer.php';
