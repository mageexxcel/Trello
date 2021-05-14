<?php

namespace Excellence\Trello\Model;

class Trello extends \Excellence\Trello\Model\AbstractModel
{  
    const trelloUrl = 'https://api.trello.com/1/';
    protected $section = 'trellosections';
    protected $code = 'trelloadvance';
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
       \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,     
       \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        array $data = []
    ) {
         $this->_resourceConfig = $resourceConfig;
         $this->scopeConfigObject = $scopeConfigObject;
          parent::__construct($data);
    }
   
   
    
    /**
     *  get access key and token of Trello 
     */
    public function getKeyToken() {

        $access = array();
        $access['key'] = $this->scopeConfigObject->getValue('trello/advance/trellokey');
        $access['token'] = $this->scopeConfigObject->getValue('trello/setting/tokenkey');
        return $access;
    }

   /*
     * get the member details 
     */

    public function getMemberDetail() {
        $access = $this->getKeyToken();
        $url = self::trelloUrl . "member/me?key=" . $access["key"] . "&token=" . $access["token"];
        $result = $this->initCurl($url);
        return $result;
    }

    /*
     * get the board details using member name or memberId (use to get the all boards)//getBoardsDetail
     * $id  is array    $id['memberId']
     */

    public function getBoardChannelList($id=array()) {
        $access = $this->getKeyToken();
        $url = self::trelloUrl . "members/" . $id['memberId'] . "/boards?key=" . $access["key"] . "&token=" . $access["token"];
        $result = $this->initCurl($url);
        return $result;
    }
    
     /*
     * get the list of boards using boardId  /getBoardLists
     */

    public function getBoardLists($board_id) {
        $access = $this->getKeyToken();
        $url = self::trelloUrl . "boards/" . $board_id . "/lists?key=" . $access["key"] . "&token=" . $access["token"];
        $result = $this->initCurl($url);
        return $result;
    }

    
     /*
     * https://developers.trello.com/advanced-reference/board#get-1-boards-board-id-members
     * get the members of the board using board id 
     * @param array  - $value (requried) board id 
     */

    public function getUserList($boardid) {
        $access = $this->getKeyToken();
        $url = self::trelloUrl . "boards/" . $boardid . "/members?key=" . $access["key"] . "&token=" . $access["token"];
        $result = $this->initCurl($url);
        return $result;
    }
    /*
     * https://developers.trello.com/advanced-reference/board#get-1-boards-board-id-labels
     * get all the label using board id
     * 
     */

    public function getLabelsOfBoard($boardid) {
        $access = $this->getKeyToken();
        $url = self::trelloUrl . "boards/" . $boardid . "/labels?key=" . $access["key"] . "&token=" . $access["token"];
        $result = $this->initCurl($url);
        return $result;
     }
   
    /*
     * create Card  idList (required) Valid Values: id of the list that the card 
     * should be added to,urlSource (required)or null ,due (required)
     * Valid Values: A date, or null , name (optional) //postCreateCards
     * @param : array   $values[$value['idList'], $type='post']
     */

    public function createPost($values=array()) {
        $value=$values['value'];
        $type=$values['type'];
        $url = self::trelloUrl.'/cards';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }

    /*
     * $value is array type  $value['value']
     * PUT /1/cards/[card id or shortlink]/idList  
     * value (required) Valid Values: id of the list the card should be moved to
     */

    public function putCard($value, $card_id, $type = 'put') {
        $url = self::trelloUrl.'/cards/' . $card_id . '/idList';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }

    public function putCardTop($value, $card_id, $type = 'put') {
        $url = self::trelloUrl.'/cards/' . $card_id . '/pos';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }

    /*
     * create Card comment
     * $value is text in array
     *  text (required) a string
     */

    public function postCommentCards($value, $card_id, $type = 'post') {
        $url = self::trelloUrl.'/cards/' . $card_id . '/actions/comments';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }
    
    /*
     * https://developers.trello.com/advanced-reference/card#post-1-cards-card-id-or-shortlink-idlabels
     * add labels to card using card id
     * @param array  - $value (requried) idLabels
     */

    public function postAddLabels($value, $card_id, $type = 'post') {
        $url = self::trelloUrl.'cards/' . $card_id . '/idLabels';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }

    /*
     * https://developers.trello.com/advanced-reference/card#post-1-cards-card-id-or-shortlink-idmembers
     * add member to card
     * @param array -$value (requried) id of member
     */

    public function postAddMemberToCard($value, $card_id, $type = 'post') {
        $url = self::trelloUrl.'cards/' . $card_id . '/idMembers';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }

    /*
     * create webhook for card
     * POST /1/token/:token/webhooks
     * $value is array or idModel and callbackUrl requried
     * (requried) idModel: The id of the model (card, board, member, organization, list, etc.) that your webhook will trigger on updates for.
     * (requried) callbackURL: The url endpoint that the webhook should POST its payload to
     * (optional) :description
     */

    public function postCreateWebhook($value, $token, $type = 'post') {
        $url = self::trelloUrl.'tokens/' . $token . '/webhooks';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }

    /*
     * update the idModel of webhooks 
     * PUT /1/webhooks/[idWebhook]/idModel
     * @param array $value (requried) idModel  
     */

    public function putUpdateWebhook($value, $idWebhook, $type = 'put') {
        $url = self::trelloUrl.'tokens/' . $idWebhook . '/idModel';
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }

    
    /*
     * delete webhooks
     */

    public function deleteWebhook($token, $webhookid, $type = 'delete') {
        $url = self::trelloUrl."tokens/" . $token . "/webhooks/" . $webhookid;
        $result = $this->initCurl($url, $value, $type);
        return $result;
    }
    
    /*
     * File attchement in trello card POST /1/cards/[card id or shortlink]/attachments
     */
  public function postAttchment($card_id, $value, $type='post'){
      $url = self::trelloUrl."cards/".$card_id."/attachments";
        $result = $this->initCurl($url, $value, $type);
        return $result;
  }
}
