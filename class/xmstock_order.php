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
 * @copyright       XOOPS Project (http://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author          Mage Gregory (AKA Mage)
 */
use Xmf\Request;
use Xmf\Module\Helper;

if (!defined('XOOPS_ROOT_PATH')) {
    die('XOOPS root path not defined');
}

/**
 * Class xmstock_order
 */
class xmstock_order extends XoopsObject
{
    // constructor
    /**
     * xmstock_order constructor.
     */
    public function __construct()
    {
        $this->initVar('order_id', XOBJ_DTYPE_INT, null, false, 11);
        $this->initVar('order_description', XOBJ_DTYPE_TXTAREA, null, false);
        // use html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('order_userid', XOBJ_DTYPE_INT, null, false, 8);
		$this->initVar('order_areaid', XOBJ_DTYPE_INT, null, false, 8);
		$this->initVar('order_ddesired', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_dorder', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_dvalidation', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_ddelivery', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_dwithdrawal', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_dready', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_ddelivery_r', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_dwithdrawal_r', XOBJ_DTYPE_INT, null, false, 10);
		$this->initVar('order_dcancellation', XOBJ_DTYPE_INT, null, false, 10);;
		$this->initVar('order_delivery', XOBJ_DTYPE_INT, null, false, 2);
        $this->initVar('order_status', XOBJ_DTYPE_INT, null, false, 1);
    }

    /**
     * @return mixed
     */
    public function get_new_enreg()
    {
        global $xoopsDB;
        $new_enreg = $xoopsDB->getInsertId();
        return $new_enreg;
    }

    /**
     * @return mixed
     */
    public function saveOrder($orderHandler, $action = false)
    {
        global $xoopsUser;
		$session_name = 'caddy';
        include __DIR__ . '/../include/common.php';

        $error_message = '';
        $this->setVar('order_description',  Request::getText('order_description', ''));
		$this->setVar('order_userid', !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0);
		$this->setVar('order_ddesired', strtotime(Request::getString('order_ddesired', '')));
		$this->setVar('order_dorder', time());
		$this->setVar('order_delivery',  Request::getInt('order_delivery', 0));
        $this->setVar('order_status', Request::getInt('order_status', 1));
		// temporaire pour les tests
		$this->setVar('order_dvalidation', strtotime(Request::getString('order_ddesired', ''))+(1*86400));
		$this->setVar('order_ddelivery', strtotime(Request::getString('order_ddesired', ''))+(2*86400));
		$this->setVar('order_dready', strtotime(Request::getString('order_ddesired', ''))+(3*86400));
		$this->setVar('order_ddelivery_r', strtotime(Request::getString('order_ddesired', ''))+(4*86400));
		$this->setVar('order_dcancellation', strtotime(Request::getString('order_ddesired', ''))+(5*86400));

		//
        if ($error_message == '') {
			$sessionHelper = new \Xmf\Module\Helper\Session();
			$arr_selectionArticles = $sessionHelper->get($session_name);
			$areaid = 0;
			if (is_array($arr_selectionArticles) == true){
				$areaid = $arr_selectionArticles[0]['area'];
			}
			$this->setVar('order_areaid', $areaid);
            if ($orderHandler->insert($this)) {
				if ($this->get_new_enreg() == 0){
					$order_id = $this->getVar('order_id');
				} else {
					$order_id = $this->get_new_enreg();
				}
				if (is_array($arr_selectionArticles) == true){
					foreach ($arr_selectionArticles as $datas) {
						$obj = $itemorderHandler->create();
						$obj->setVar('itemorder_orderid', $order_id);
						$obj->setVar('itemorder_articleid', $datas['id']);
						$obj->setVar('itemorder_areaid', $datas['area']);
						$obj->setVar('itemorder_amount', $datas['qty']);
						$obj->setVar('itemorder_status', 1);
						if (!$itemorderHandler->insert($obj)) {
							$error_message = $obj->getHtmlErrors();
						}
					}
					if ($error_message == '') {
						if ($action === false) {
							$action = $_SERVER['REQUEST_URI'] . '?op=confirm&order_id=' . $order_id;
						}
						redirect_header($action, 2, _MA_XMSTOCK_CHECKOUT_SEND);
					}
				} else {
					redirect_header('index.php', 5, _MA_XMSTOCK_CADDY_ERROR_EMPTY);
				}
            } else {
                $error_message =  $this->getHtmlErrors();
            }
        }
        return $error_message;
    }

    /**
     * @param bool $action
     * @return XoopsThemeForm
     */
    /*public function getForm($action = false)
    {
        $helper = Helper::getHelper('xmstock');
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        include __DIR__ . '/../include/common.php';

        //form title
        $title = $this->isNew() ? sprintf(_MA_XMSTOCK_ADD) : sprintf(_MA_XMSTOCK_EDIT);

        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);

        if (!$this->isNew()) {
            $form->addElement(new XoopsFormHidden('order_id', $this->getVar('order_id')));
            $status = $this->getVar('order_status');
        } else {
            $status = 1;
        }

        // description
        $editor_configs           =array();
        $editor_configs['name']   = 'order_description';
        $editor_configs['value']  = $this->getVar('order_description', 'e');
        $editor_configs['rows']   = 20;
        $editor_configs['cols']   = 160;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';
        $editor_configs['editor'] = $helper->getConfig('general_editor', 'Plain Text');
        $form->addElement(new XoopsFormEditor(_MA_XMSTOCK_AREA_DESC, 'order_description', $editor_configs), false);

		// A faire (pour la gestion des commandes admin et user)
		// status
        $form_status = new XoopsFormRadio(_MA_XMSTOCK_STATUS, 'order_status', $status);
        $options = array(1 => _MA_XMSTOCK_STATUS_A, 0 =>_MA_XMSTOCK_STATUS_NA);
        $form_status->addOptionArray($options);
        $form->addElement($form_status);

        $form->addElement(new XoopsFormHidden('op', 'save'));
        // submit
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }*/

    /**
     * @return mixed
     */
    public function saveOrderEdit($orderHandler, $action = false)
    {
        global $xoopsUser;
        include __DIR__ . '/../include/common.php';

        $error_message = '';
        $this->setVar('order_description',  Request::getText('order_description', ''));
		$this->setVar('order_userid', Request::getInt('order_userid', 0));
		$this->setVar('order_ddesired', strtotime(Request::getString('order_ddesired', '')));
		$this->setVar('order_delivery',  Request::getInt('order_delivery', 0));
        $this->setVar('order_status', Request::getInt('order_status', 1));

        if ($error_message == '') {
            if ($orderHandler->insert($this)) {
				$order_id = $this->getVar('order_id');
				$count = Request::getInt('count', 0);
				if ($count > 0){
					for ($i = 1; $i <= $count; $i++) {
						$amount = Request::getInt('amount' . $i, 0);
						$itemorder = Request::getInt('itemorder' . $i, 0);
						$obj = $itemorderHandler->get($itemorder);
						if ($amount == 0){
							if ($count > 1){
								if (!$itemorderHandler->delete($obj)) {
									$error_message = $obj->getHtmlErrors();
								}
							} else {
								$error_message = _MA_XMSTOCK_ERROR_ONEARTICLE;
							}
						} else {
							$obj->setVar('itemorder_amount', $amount);
							if (!$itemorderHandler->insert($obj)) {
								$error_message = $obj->getHtmlErrors();
							}
						}
					}
					if ($error_message == '') {
						redirect_header($action, 2, _MA_XMSTOCK_REDIRECT_SAVE);
					}
				} else {
					$error_message = _MA_XMSTOCK_ERROR_NOARTICLE;
				}
            } else {
                $error_message =  $this->getHtmlErrors();
            }
        }
        return $error_message;
    }

	/**
     * @param bool $action
     * @return XoopsThemeForm
     */
    public function getFormEdit($action = false)
    {
        $helper = Helper::getHelper('xmstock');
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        include __DIR__ . '/../include/common.php';

        //form title
        $title = sprintf(_MA_XMSTOCK_EDIT);
        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
		$form->addElement(new XoopsFormHidden('order_id', $this->getVar('order_id')));
		if ($this->getVar('order_status') > 0){
			$status = 1;
		} else {
			$status = 0;
		}

        // description
        $editor_configs           =array();
        $editor_configs['name']   = 'order_description';
        $editor_configs['value']  = $this->getVar('order_description', 'e');
        $editor_configs['rows']   = 3;
        $editor_configs['cols']   = 40;
        $editor_configs['width']  = '50%';
        $editor_configs['height'] = '100px';
        $editor_configs['editor'] = $helper->getConfig('general_editor', 'Plain Text');
        $form->addElement(new XoopsFormEditor(_MA_XMSTOCK_AREA_DESC, 'order_description', $editor_configs), false);

		$form->addElement(new XoopsFormTextDateSelect(_MA_XMSTOCK_CHECKOUT_DORDER, 'order_ddesired', 2, $this->getVar('order_ddesired')), false);

		$delivery = new XoopsFormRadio(_MA_XMSTOCK_CHECKOUT_DELIVERY, 'order_delivery', $this->getVar('order_delivery'));
		$options        = [0 => _MA_XMSTOCK_CHECKOUT_DELIVERY_WITHDRAWAL, 1 => _MA_XMSTOCK_CHECKOUT_DELIVERY_DELIVERY];
		$delivery->addOptionArray($options);
		$form->addElement($delivery);

		// articles
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('stock_areaid', $this->getVar('order_areaid')));
		$stock_arr = $stockHandler->getall($criteria);
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('itemorder_orderid', $this->getVar('order_id')));
		$itemorderHandler->table_link = $itemorderHandler->db->prefix("xmarticle_article");
		$itemorderHandler->field_link = "article_id";
		$itemorderHandler->field_object = "itemorder_articleid";
		$itemorder_arr = $itemorderHandler->getByLink($criteria);
		$count = 0;
		$articles = "<table  class='table table-bordered'><thead class='table-primary'><tr><th scope='col'>" . _MA_XMSTOCK_ACTION_ARTICLES . "</th><th scope='col'>" . _MA_XMSTOCK_VIEWORDER_AMOUNT . "</th><th scope='col'>" . _MA_XMSTOCK_STOCK_AMOUNT . "</th></tr></thead>";
		$articles .= "<tbody>";
		foreach (array_keys($itemorder_arr) as $i) {
			$count++;
			$articles .= "<tr><th scope='row'>" . $itemorder_arr[$i]->getVar('article_name') . "</th>";
			$articles .= "<td><input class='form-control' type='text' name='amount" . $count . "' id='amount" . $count . "' value='"  . $itemorder_arr[$i]->getVar('itemorder_amount') .  "'></td>";
			$articles .= "<td class='text-center'><span class='badge badge-primary badge-pill'>" . XmstockUtility::articleAmountPerArea($this->getVar('order_areaid'), $itemorder_arr[$i]->getVar('itemorder_articleid'), $stock_arr) . "</span></td></tr>";
			$form->addElement(new XoopsFormHidden('itemorder' . $count, $i));
		}
		$articles .= "</tbody></table>";
		$articles .= "<small class='form-text text-muted'>" . _MA_XMSTOCK_ACTION_INFODELARTICLE . "</small>";
		$form->addElement(new XoopsFormLabel(_MA_XMSTOCK_ORDER_ARTICLES, $articles), true);
		$form->addElement(new XoopsFormHidden('count', $count));

		// user
        $form->addElement(new XoopsFormSelectUser(_MA_XMSTOCK_MANAGEMENT_CUSTOMER, 'order_userid', true, $this->getVar('order_userid')), true);

		// status
        $form_status = new XoopsFormRadio(_MA_XMSTOCK_STATUS, 'order_status', $status);
        $options = array(1 => _MA_XMSTOCK_STATUS_A, 0 =>_MA_XMSTOCK_ORDER_STATUS_0);
        $form_status->addOptionArray($options);
        $form->addElement($form_status);

		$form->addElement(new XoopsFormHidden('op', 'save'));
        // submit
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }

	/**
     * @param bool $action
     * @return XoopsThemeForm
     */
    public function getFormNext($action = false)
    {
        $helper = Helper::getHelper('xmstock');
        if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
        include __DIR__ . '/../include/common.php';

        //form title
        $title = sprintf(_MA_XMSTOCK_ACTION_NEXT);
        $form = new XoopsThemeForm($title, 'form', $action, 'post', true);
		$form->addElement(new XoopsFormHidden('order_id', $this->getVar('order_id')));

		//Date de livraison
		$form->addElement(new XoopsFormTextDateSelect(_MA_XMSTOCK_ORDER_DATEDELIVERY, 'order_ddelivery', 2, time()), false);

		//Livraison
		$delivery = new XoopsFormRadio(_MA_XMSTOCK_CHECKOUT_DELIVERY, 'order_delivery', $this->getVar('order_delivery'));
		$options        = [0 => _MA_XMSTOCK_CHECKOUT_DELIVERY_WITHDRAWAL, 1 => _MA_XMSTOCK_CHECKOUT_DELIVERY_DELIVERY];
		$delivery->addOptionArray($options);
		$form->addElement($delivery);

		// articles
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('stock_areaid', $this->getVar('order_areaid')));
		$stock_arr = $stockHandler->getall($criteria);
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('itemorder_orderid', $this->getVar('order_id')));
		$itemorderHandler->table_link = $itemorderHandler->db->prefix("xmarticle_article");
		$itemorderHandler->field_link = "article_id";
		$itemorderHandler->field_object = "itemorder_articleid";
		$itemorder_arr = $itemorderHandler->getByLink($criteria);
		$count = 0;
		$articles = "<table  class='table table-bordered'><thead class='table-primary'><tr><th scope='col'>" . _MA_XMSTOCK_ACTION_ARTICLES . "</th><th scope='col'>" . _MA_XMSTOCK_VIEWORDER_AMOUNT . "</th><th scope='col'>" . _MA_XMSTOCK_STOCK_AMOUNT . "</th><th scope='col'>" . _MA_XMSTOCK_STOCK_SPLIT . "</th></tr></thead>";
		$articles .= "<tbody>";
		foreach (array_keys($itemorder_arr) as $i) {
			$count++;
			$articles .= "<tr><th scope='row'>" . $itemorder_arr[$i]->getVar('article_name') . "</th>";
			$articles .= "<td><input class='form-control' type='text' name='amount" . $count . "' id='amount" . $count . "' value='"  . $itemorder_arr[$i]->getVar('itemorder_amount') .  "'></td>";
			$articles .= "<td class='text-center'><span class='badge badge-primary badge-pill'>" . XmstockUtility::articleAmountPerArea($this->getVar('order_areaid'), $itemorder_arr[$i]->getVar('itemorder_articleid'), $stock_arr) . "</span></td>";
			$articles .= "<td class='text-center'><input type='checkbox' class='form-check-input' name='split" . $count . "' id='split" . $count . "'></td></tr>";
			$form->addElement(new XoopsFormHidden('itemorder' . $count, $i));
		}
		$articles .= "</tbody></table>";
		$articles .= "<small class='form-text text-muted'>" . _MA_XMSTOCK_STOCK_SPLIT_DESC . "</small>";
		$form->addElement(new XoopsFormLabel(_MA_XMSTOCK_ORDER_ARTICLES, $articles), true);
		$form->addElement(new XoopsFormHidden('count', $count));

		$form->addElement(new XoopsFormHidden('op', 'saveNext'));
		$form->addElement(new XoopsFormHidden('status', $this->getVar('order_status')));
        // submit
        $form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        return $form;
    }

	/**
     * @return mixed
     */
    public function delOrder($orderHandler, $order_id, $action = false)
    {
		if ($action === false) {
            $action = $_SERVER['REQUEST_URI'];
        }
		$error_message = '';
		include __DIR__ . '/../include/common.php';
		if ($orderHandler->delete($this)) {
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('itemorder_orderid', $order_id));
			$itemorder_count = $itemorderHandler->getCount($criteria);
			if ($itemorder_count > 0) {
				$itemorderHandler->deleteAll($criteria);
			}
			redirect_header($action, 2, _MA_XMSTOCK_REDIRECT_SAVE);
		} else {
			$error_message .= $obj->getHtmlErrors();
		}
		return $error_message;
	}
}

/**
 * Classxmstockxmstock_orderHandler
 */
class xmstockxmstock_orderHandler extends XoopsPersistableObjectHandler
{
    /**
     * xmstockxmstock_orderHandler constructor.
     * @param null|XoopsDatabase $db
     */
    public function __construct($db)
    {
        parent::__construct($db, 'xmstock_order', 'xmstock_order', 'order_id', 'order_description');
    }
}
