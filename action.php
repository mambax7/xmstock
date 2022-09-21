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

use \Xmf\Request;

include_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'xmstock_action.tpl';
include_once XOOPS_ROOT_PATH . '/header.php';

$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname', 'n') . '/assets/css/styles.css', null);

$op = Request::getCmd('op', '');
$xoopsTpl->assign('index_module', $helper->getModule()->getVar('name'));
$xoopsTpl->assign('op', $op);

if ($op == 'next' || $op == 'edit' || $op == 'del' || $op == 'save') {
    switch ($op) {

        // next
        case 'next':
			$order_id = Request::getInt('order_id', 0);
			if ($order_id == 0) {
                $xoopsTpl->assign('error_message', _MA_XMSTOCK_ERROR_NOORDER);
			} else {
				$obj  = $orderHandler->get($order_id);
				if (empty($obj)) {
					$xoopsTpl->assign('error_message', _MA_XMSTOCK_ERROR_NOORDER);
				} else {
					$permHelper->checkPermissionRedirect('xmstock_manage', $obj->getVar('order_areaid'), 'index.php', 2, _NOPERM);
					$xoopsTpl->assign('orderid', $order_id);
					$xoopsTpl->assign('description', XmstockUtility::generateDescriptionTagSafe($obj->getVar('order_description', 'show'), 50));
					$xoopsTpl->assign('ddesired', formatTimestamp($obj->getVar('order_ddesired'), 's'));
					$xoopsTpl->assign('dorder', formatTimestamp($obj->getVar('order_dorder'), 'm'));
					$xoopsTpl->assign('user', XoopsUser::getUnameFromId($obj->getVar('order_userid')));
					$xoopsTpl->assign('delivery', $obj->getVar('order_delivery'));
					$xoopsTpl->assign('status', $obj->getVar('order_status'));
					$xoopsTpl->assign('ddelivery', formatTimestamp($obj->getVar('order_ddelivery'), 's'));
					switch ($obj->getVar('order_status')) {
						case 1:
							$xoopsTpl->assign('status_text', _MA_XMSTOCK_ORDER_STATUS_1);
							$xoopsTpl->assign('status_icon', '<span class="fa fa-hourglass-start fa-fw" aria-hidden="true"></span>');
							break;
						case 2:
							$xoopsTpl->assign('status_text', _MA_XMSTOCK_ORDER_STATUS_2);
							$xoopsTpl->assign('status_icon', '<span class="fa fa-hourglass-half fa-fw" aria-hidden="true"></span>');
							break;
						case 3:
							$xoopsTpl->assign('status_text', _MA_XMSTOCK_ORDER_STATUS_3);
							$xoopsTpl->assign('status_icon', '<span class="fa fa-thumbs-o-up fa-fw" aria-hidden="true"></span>');
							break;
						case 4:
							$xoopsTpl->assign('status_text', _MA_XMSTOCK_ORDER_STATUS_4);
							$xoopsTpl->assign('status_icon', '<span class="fa fa-check fa-fw" aria-hidden="true"></span>');
							break;
						case 0:
							$xoopsTpl->assign('status_text', _MA_XMSTOCK_ORDER_STATUS_0);
							$xoopsTpl->assign('status_icon', '<span class="fa fa-ban fa-fw" aria-hidden="true"></span>');
							break;
					}					
					$form = $obj->getFormNext();
					$xoopsTpl->assign('form', $form->render());
				}
            }
            break;

        // Edit
        case 'edit':
			$order_id = Request::getInt('order_id', 0);
			if ($order_id == 0) {
                $xoopsTpl->assign('error_message', _MA_XMSTOCK_ERROR_NOORDER);
            } else {
				$obj  = $orderHandler->get($order_id);
				if (empty($obj)) {
					$xoopsTpl->assign('error_message', _MA_XMSTOCK_ERROR_NOORDER);
				} else {
					$permHelper->checkPermissionRedirect('xmstock_manage', $obj->getVar('order_areaid'), 'index.php', 2, _NOPERM);
					$form = $obj->getFormEdit();
					$xoopsTpl->assign('form', $form->render());
				}
            }
            break;

        // Save
        case 'save':
			$order_areaid = Request::getInt('order_areaid', 0);
			// Get Permission to submit in category
			$permHelper->checkPermissionRedirect('xmstock_manage', $order_areaid, 'index.php', 2, _NOPERM);
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('index.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $order_id = Request::getInt('order_id', 0);
            if ($order_id == 0) {
                $obj = $orderHandler->create();
            } else {
                $obj = $orderHandler->get($order_id);
            }
            $error_message = $obj->saveOrderEdit($orderHandler, 'management.php');
            if ($error_message != '') {
                $xoopsTpl->assign('error_message', $error_message);
				$form = $obj->getFormEdit();
                $xoopsTpl->assign('form', $form->render());
            }
            break;

		// del
		case 'del':
			// a faire si besoin
			break;
    }
} else {
    redirect_header('index.php', 2, _NOPERM);
}
include XOOPS_ROOT_PATH . '/footer.php';
