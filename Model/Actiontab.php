<?php
namespace Excellence\Trello\Model;
class Actiontab extends \Magento\Framework\Model\AbstractModel
{  
    const CACHE_TAG = 'excellence_trello_actiontab';
    
    public function __construct(
        \Excellence\Trello\Model\SetupwizardFactory $SetupwizardFactory,
        \Excellence\Trello\Model\Trello $trello,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Excellence\Trello\Model\ResourceModel\Actiontab $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject, 
        \Excellence\Trello\Model\ResourceModel\Actiontab\Collection $resourceCollection,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        array $data = []
        )
    {
        $this->setupwizard = $SetupwizardFactory;
        $this->trelloModel = $trello;
        $this->_resourceConfig = $resourceConfig;
        $this->scopeConfigObject = $scopeConfigObject;
        parent::__construct( $context, $registry, $resource, $resourceCollection, $data );

    }

    protected function _construct()
    {
        $this->_init('Excellence\Trello\Model\ResourceModel\Actiontab');
    }
 
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

     public function saveActionData() {
        $setup_data = $this->setupwizard->create()->getCollection();
        $board_id = $setup_data->getFirstItem()->getTrelloListId();
       
        $modelTrello = $this->trelloModel;

        $member_data = $modelTrello->getUserList($board_id);
        $labels_data = $modelTrello->getLabelsOfBoard($board_id);
        $boardLists = $modelTrello->getBoardLists($board_id);
        $data['label'] = $labels_data;
        $data['member'] = $member_data;
        $data['list'] = $boardLists;
      
        $firstItem = $this->getCollection()->getFirstItem()->getTabId();

        if ($firstItem) {
            $this->load($firstItem)->addData($data);
        } else { 
            $this->setData($data);
         }
          $this->save();
       
        $this->_resourceConfig->saveConfig('list/label/memberupdate', '', \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
     }

    /*
     * get all labels of board
     */

    public function getLabelList() {
      
        $field_data = $this->getCollection();
        $labels_data = $field_data->getFirstItem()->getLabel();
        $labels_decode_data = json_decode($labels_data);
        $option_labels = array();

        if (isset($labels_data)) {
            $j = 1;
            foreach ($labels_decode_data as $mdd) {
                $option_labels[$j]['label'] = $mdd->color;
                $option_labels[$j]['value'] = $mdd->id;
                $j++;
            }
        }
        return $option_labels;
    }

    /*
     * get all members of board
     */

    public function getBoardMember() {
        $field_data = $this->getCollection();
        $member_data = $field_data->getFirstItem()->getMember();
        $member_decode_data = json_decode($member_data);
        $option_members = array();
        if (isset($member_data)) {
            $i = 1;
            foreach ($member_decode_data as $mdd) {
                $option_members[$i]['label'] = $mdd->username;
                $option_members[$i]['value'] = $mdd->id;
                $i++;
            }
        }
        return $option_members;
    }

    /*
     * get all board's  list
     */

    public function getListBoard() {
        $field_data = $this->getCollection();
        $boardLists = $field_data->getFirstItem()->getList();
        $lists_decode_data = json_decode($boardLists);
        $option_lists = array();
        $option_lists[0]['label'] = 'Please Select..';
        $option_lists[0]['value'] = '';
        if (isset($boardLists)) {
            $k = 1;
            foreach ($lists_decode_data as $ldd) {
                $option_lists[$k]['label'] = $ldd->name;
                $option_lists[$k]['value'] = $ldd->id;
                $k++;
            }
        }
        return $option_lists;
    }
}
