<?php

namespace Excellence\Trello\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface {

    protected $orderFactory;
    protected $addressRenderer;

    public function __construct(
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
            \Excellence\Trello\Model\Trello $trelloFactory,
            \Excellence\Trello\Model\CardFactory $cardFactory,
            \Excellence\Trello\Model\SetupwizardFactory $setupwizardFactory,
            \Magento\Sales\Model\OrderFactory $orderFactory, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Magento\Framework\Locale\CurrencyInterface $localeCurrency, 
            \Magento\Sales\Model\Order\Address\Renderer $addressRenderer, 
            \Magento\Catalog\Model\ProductFactory $productFactory, 
            \Magento\Customer\Model\CustomerFactory $customerFactory, 
            \Magento\Customer\Model\GroupFactory $groupFactory, 
             \Magento\Downloadable\Model\LinkFactory $linkFactory, 
            \Magento\Framework\UrlInterface $url, 
            \Magento\Payment\Helper\Data $paymentHelper, 
            \Excellence\Trello\Model\RulesCheck $rulescheck,
            \Magento\Customer\Model\Session $customerSession,
            \Excellence\Trello\Model\RulesFactory $rulesFactory,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->scopeConfigObject = $scopeConfigObject;
        $this->trelloFactory = $trelloFactory;
        $this->setupwizardFactory = $setupwizardFactory;
        $this->cardFactory = $cardFactory;
        $this->orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
        $this->_localeCurrency = $localeCurrency;
        $this->addressRenderer = $addressRenderer;
        $this->_productFactory = $productFactory;
        $this->_customerFactory = $customerFactory;
        $this->groupFactory = $groupFactory;
        $this->linkFactory = $linkFactory;
        $this->url = $url;
        $this->timezoneTime = $timezone;
        $this->paymentHelper = $paymentHelper;
        $this->rulesCheck = $rulescheck;
        $this->trelloRulesModel = $rulesFactory;
        $this->customerSession = $customerSession;
    }

    /*
     * check module is enable and active (setupdone)
     * return boolean true and false
     */

    public function checkActiveEnable() {
        $is_enable = $this->scopeConfigObject->getValue('trello/advance/enable');
        $isActive = $this->scopeConfigObject->getValue('trello/wizard/setupdone');
        $return = (($isActive == 'Yes') && ($is_enable == 1)) ? true : false;
        return $return;
    }

    public function execute(Observer $observer) {
        $active = $this->checkActiveEnable();
        $orderData = $observer->getEvent()->getOrder();

        if ($active == true) {
            $items = $orderData->getAllVisibleItems();
            $quote = $observer->getEvent()->getQuote();
         
             /*
             * check all rules
             */
            $rules_data = $this->trelloRulesModel->create()->getCollection()->addFieldToFilter('is_active', 1)->setOrder('priority', 'ASC')->getData();
           
            $unserialized_array = array();
            $rule_id = '';
            $store_id = $this->_storeManager->getStore()->getStoreId();

            $store_ids = array();

            foreach ($rules_data as $sdata) {
               $condition_data = unserialize($sdata['conditions_serialized']);
               $store_ids = explode(',', $sdata['rule_website_ids']);
             
                if (in_array($store_id, $store_ids) || in_array(0, $store_ids)) {
                  $returl_status = $this->rulesCheck->baseRule($condition_data, $orderData , $quote);
                    if ($returl_status) {
                        $rule_id = $sdata['id'];
                        break;
                    }
                }
            }


            /*
             * if condition of action are true then apply new conditions
             */
            $list_id = Null;
            if ($rule_id != '') {
                $rules_actiontab =  $this->trelloRulesModel->create()->load($rule_id);
                $label_id_array = explode(',', $rules_actiontab['label']);
                $member_id_array = explode(',', $rules_actiontab['member']);
                if (!empty($rules_actiontab['list'])) {
                    $list_id = $rules_actiontab['list'];
                }
            }
           
           
            /*
             * get order details like address , item , total etc
             */
            $arguments = $this->orderDescription($orderData);
            $order_sales = $arguments['order_object'];
            $order_id = $order_sales->getEntityId();

            $modelid = $this->setupwizardFactory->create()->getCollection();
            $setupwizard_data = $modelid->getData();

            $idList = '';
            $list_name = '';
            $Newstatus = $orderData->getData('status');
            foreach ($setupwizard_data as $setwizd) {
                if ($setwizd['order_state'] == $Newstatus) {
                    $idList = $setwizd['trello_list_id'];
                    $list_name = $setwizd['order_state'];
                }
            }
        


            if ($idList) {

                $value = array();
                /*
                 * check list_id is isset in rule then give the first priority to this
                 */
                
                $value['url'] = '';
                $value['due'] = '';
                $value['pos'] = 'top';
                $value['desc'] = $arguments['desc'];
                $value['name'] = $arguments['name'];
                $value['idList']=  $list_id ?  $list_id : $idList;
              
               /*
                 * added the card to trello by using createPost Api
                 */
                $values['value'] = $value;
                $values['type'] = 'post';
                $response = $this->trelloFactory->createPost($values);
                $result = json_decode($response);
                $status = $order_sales->getStatus();


                if (!empty($result)) {

                     /*
                     * add label or member to card 
                     */
                    if (!empty($list_id)) {
                        $card_id = $result->id;
                        foreach ($label_id_array as $lb_arr) {
                            $value['value'] = $lb_arr;
                            $label_resutl = $this->trelloFactory->postAddLabels($value, $card_id);
                        }
                        foreach ($member_id_array as $mb_arr) {
                            $value['value'] = $mb_arr;
                            $this->trelloFactory->postAddMemberToCard($value, $card_id);
                        }
                    }
                   
                    /*
                     * change unix time stamp to  date-time format
                     */
                    $date = $this->timezoneTime->date();
                    $timedatefomate = $this->timezoneTime->formatDateTime($date);
                    $timeformat = date("d M Y H:i:s", strtotime($timedatefomate));

                    $boardName = $modelid->getFirstItem()->getOrderState();
                    $comment = __("Your order was successfully added on Trello - '" . $boardName . "'  to list '" . $list_name . "' at " . $timeformat . "<br/> View Order here " . "<a href='" . $result->url . "'>" . $result->name . "</a>.");

                    $orderId = $orderData->getId();
                    /*
                     * added order_id and card_id to excellence_trello_card table
                     */
                    $card_update_data = array();
                    $card_update_data['card_id'] = $result->id;
                    $card_update_data['order_id'] = $orderId;
                    $this->saveCardData($card_update_data);
                    $this->saveHistoryComments($order_sales, $comment, $status);
                } else {
                    $comment = __(" Unable to create card :" . $response);
                    $this->saveHistoryComments($order_sales, $comment, $status);
                }
            }
        }
    }

    public function saveCardData($card_update_data) {
        $card_model = $this->cardFactory->create();
        $card_model->setData($card_update_data);
        $card_model->save();
    }

    public function orderDescription($orderData) {
        $incre_id = $orderData->getIncrementId();
        $grand_total = $orderData->getGrandTotal();
        $order = $this->orderFactory->create();
        $order_sales = $order->loadByIncrementId($incre_id);
        $customer_name = $orderData->getCustomerName();

        $currency_code = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        $currency_symbol = $this->_localeCurrency->getCurrency($currency_code)->getSymbol();

        /*
         * order detail url 
         */
        $orderId = $orderData->getId();
        $encodedId = base64_encode($orderId);
        $baseUrl = $this->url->getUrl("oms/order/print/", ['order_id' => $encodedId]);

        /*
         * get the billing and shipping address
         */
        $date = $this->timezoneTime->date();
        $timedatefomate = $this->timezoneTime->formatDateTime($date);
        $time = date("h:i A", strtotime($timedatefomate));
        $order_description_address = __("Order [#" . $incre_id . "](" . $baseUrl . ") placed by *" . $customer_name . "* at *" . $time . "* for " . $currency_symbol . '' . $grand_total . '<br>');
        $order_description_address.= $this->getOrderAddress($orderData);
        $arguments = array();
        
        $arguments['name'] = "#" . $incre_id . " placed by " . $customer_name . " for " . $currency_symbol . '' . $grand_total;
        /**
         * get the ordered product collection  (set as string )
         */
        $productItems = $this->getOrderItems($orderData);

        /*
         * order detail link (show order for non login user)
         */
        $productItems.= __("<br><br>[View Full Order Details](" . $baseUrl . ")");
        $order_description_address.="<br>" . $productItems;

        $breaks = array("<br />", "<br>", "<br/>");
        $arguments['desc'] = str_ireplace($breaks, "\r\n", $order_description_address);
        $arguments['order_object'] = $order_sales;
        return $arguments;
    }

     public function getOrderAddress($order_sales) {
        
        $order_description_address = '';
        if ($order_sales->getShippingAddress()) {
           $address = $order_sales->getShippingAddress();
            $shipping_address = $this->addressRenderer->format($address, 'html');
            $shipping_address = str_replace(array("\r", "\n"), '', $shipping_address);
            $order_description_address .= __("*Shipping Address*:\n" . $shipping_address . "\n\n");
         }
      
         if ($order_sales->getBillingAddress()) { 
           $billingAddress = $order_sales->getBillingAddress();
            $billing_address = $this->addressRenderer->format($billingAddress, 'html');
            $billing_address = str_replace(array("\r", "\n"), '', $billing_address);
             $order_description_address .= __("*Billing Address*:\n" . $billing_address . "\n\n");
        }
 
        return $order_description_address;
        
    }

    public function getOrderItems($orderData) {
        $orderitems = '';
        $items = $orderData->getAllVisibleItems();

        foreach ($items as $item) {
            $currency_code = $this->_storeManager->getStore()->getCurrentCurrencyCode();
            $currency_symbol = $this->_localeCurrency->getCurrency($currency_code)->getSymbol();
            $orderitems .=__("**Products** <br>");
            $orderitems .=__("*Name:* " . $item->getName() . "<br>");
            $orderitems .=__("*SKU:* " . $item->getSku() . "<br>");
            $orderitems .=__("*Qty:* " . $item->getQtyOrdered() . "<br>");
            $orderitems .=__("*Price:* " . $currency_symbol . $item->getPrice() . "<br><br>");

            $options = $item->getProductOptions();

            /*
             * 
             */
            if (!empty($options['options'])) {
                $orderitems.=$this->customOptions($options);
            }
            /*
             * add selected option product is configurable
             */
            if ($item->getProductType() == 'configurable') {
                $orderitems.=$this->configurableProduct($options);
            }
            /*
             * add selected option product is bundle
             */
            if ($item->getProductType() == 'bundle') {
                $orderitems.=$this->bundleProduct($options);
            }
            /*
             * add selected option product is downloadable
             */
            if ($item->getProductType() == 'downloadable') {
                $orderitems.=$this->downloadableProduct($item, $orderData);
            }


            /*
             * add product image
             */
            $productFactory = $this->_productFactory->create();
            $product = $productFactory->load($item->getProductId());
            $imageUrl = $product->getImage();
            if (is_string($imageUrl)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ) . 'catalog/product/' . $imageUrl;
                $orderitems.='![](' . $url . ')<br><br>';
            }
        }

        $paymentInfoBlock = $this->paymentHelper->getInfoBlock($orderData->getPayment())->toHtml();
       
        if($paymentInfoBlock){
         $paymentInfo = strip_tags($paymentInfoBlock);
         $paymentInfoDetails = str_replace(' ', '', $paymentInfo);
         $orderitems.="\n*Payment Method:* " . $paymentInfoDetails."\n";
        } else {
           $orderitems.=__("\n*Payment Method:* " . $orderData->getPayment()->getMethodInstance()->getTitle() . "\n\n");
        }

        $orderitems.=__("**Order Totals**");
         $orderitems.=__("\n Subtotal: " . $currency_symbol . '' . $orderData->getBaseSubtotal());
        if($orderData->getDiscountAmount()){
          $discountAmount = substr($orderData->getDiscountAmount(),1);
          if($orderData->getDiscountDescription()){
            $orderitems.=__("\n Discount ".$orderData->getDiscountDescription().": -" . $currency_symbol . '' .  $discountAmount);
          } else {
            $orderitems.=__("\n Discount : -" . $currency_symbol . '' .  $discountAmount);
          }
        }
       
        $orderitems.=__("\n Shipping : " . $currency_symbol . '' . $orderData->getShippingAmount());
        if($orderData->getTaxAmount()){
          $orderitems.=__("\n Tax: " . $currency_symbol . '' . $orderData->getTaxAmount());
        }
        
        $orderitems.=__("<br> GrandTotal: " . $currency_symbol . '' . $orderData->getGrandTotal());

        $customerSession = $this->customerSession;

        if ($customerSession->isLoggedIn()) {

           $customer_id = $orderData->getCustomerId();
           $customerData = $this->_customerFactory->create()->load($customer_id)->getData();
           $groupname = $this->groupFactory->create()->load($customerData['group_id'])->getCustomerGroupCode();
           $orderitems.=__("<br>**Customer**<br>");
           $orderitems.=__("Name: " . $customerData['firstname']);
           $orderitems.=__("\nEmail: " . $customerData['email']);
           $orderitems.=__("\nGroup: " . $groupname);
        }else {
            
          $orderitems.=__("\nName: " . $orderData->getBillingAddress()->getName());
          $orderitems.=__("\nEmail: " . $orderData->getBillingAddress()->getEmail());
             }
      
       return $orderitems;
    }

    public function customOptions($options) {
        $config = __("**Custom Options:** ");
        foreach ($options['options'] as $_option) {
            $config.=__("<br> " . $_option['label'] . ":");
            $config.= $_option['value'];
        }
        $config .=__("<br>");
        return $config;
    }

    public function configurableProduct($options) {
        $config = "";
        foreach ($options['attributes_info'] as $_option) {
            $config.=__("<br>" . $_option['label'] . ":");
            $config.= $_option['value'];
        }
        $config .=__("<br><br>");
        return $config;
    }

    public function bundleProduct($options) {
        $bundle = __("**Bundle Items:**  ");
        foreach ($options['bundle_options'] as $_option) {
            $bundle.=__("<br>" . $_option['label'] . "");
            foreach ($_option['value'] as $_optvalue) {
                $bundle.=__("<br>" . $_optvalue["qty"] . ' X ' . $_optvalue["title"] . ' $' . $_optvalue["price"]);
            }
        }
        $bundle .=__("<br><br>");
        return $bundle;
    }

    public function downloadableProduct($item, $orderData) {
        $downloadable = "";
        $modelLink = $this->linkFactory->create();
        $links = $modelLink->getCollection()->addTitleToResult()->addFieldToFilter('product_id', $item->getProductId());

        $downloadable.=__("<br>**Links**<br>");
        foreach ($links as $link) {
            $downloadable.=__($link->getDefault_title() . "<br>");
        }
        $downloadable.=__("<br>");
        return $downloadable;
    }

    /*
     * add order comment to order 
     */

    public function saveHistoryComments($order_sales, $comment, $status) {

        $order_sales->addStatusHistoryComment($comment, $status);
        $order_sales->save();
    }

}
