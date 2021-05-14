<?php

namespace Excellence\Trello\Model;

abstract class AbstractModel extends \Magento\Framework\DataObject{

    protected $code = false;
    protected $section = false;
    protected $enablelog = false;

    public function __construct()
    {

        if(!$this->code){
            throw new \Exception(__('Code is not defined in extending class.')); 
        } else{
            $this->enablelog = $this->scopeConfigObject->getValue($this->section . '/' . $this->code . '/log');
        }
    }

    public function initCurl($url = '', $value = array(), $type = 'get')
    {
        $cSession = curl_init();
        $this->createlogFile($this->section, 'request', $url);
        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        if ((trim($type) == 'post') || (trim($type) == 'put')) {
            $access = $this->getKeyToken();
            $access = array_merge($access, $value);
            if (trim($type) == 'put') {
                curl_setopt($cSession, CURLOPT_CUSTOMREQUEST, "PUT");
            }
            curl_setopt($cSession, CURLOPT_POST, true);
            curl_setopt($cSession, CURLOPT_POSTFIELDS, http_build_query($access));
        }

        curl_setopt($cSession, CURLOPT_HEADER, false);
        $result = curl_exec($cSession);
        $this->createlogFile($this->section, 'response', $result);
        curl_close($cSession);

        return $result;
    }

    /*
     * get the all list members(user)
     */

    abstract public function getUserList($urlid);

    /*
     * get the list of boards/channel using parameters
     */

    abstract public function getBoardChannelList($array = array());

    /*
     * create Message/Card 
     */

    abstract public function createPost($array = array());

    public function createlogFile($dir, $filename, $message)
    {

        if ($this->enablelog) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/debug.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($message);
        }
    }
}
