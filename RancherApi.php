<?php

/**
 * Rancher
 *
 * @link https://github.com/WeeSee/yii2-rancher
 * @copyright Copyright (c) 2018 WeeSee
 * @license  https://github.com/WeeSee/yii2-rancher/blob/master/LICENSE
 */

namespace weesee\Rancher;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigyii\base\InvalidConfigExceptionException;
use yii\httpclient\Client as HttpClient;

class RancherApi extends BaseObject
{
    public $apiEndpointUrl;
    public $apiUsername;
    public $apiPassword;

    public function __construct($config = [])
    {
        $this->apiEndpointUrl = isset($config['apiEndpointUrl'])
            ?$config['apiEndpointUrl']:null;
        $this->apiUsername = isset($config['apiUsername'])
            ?$config['apiUsername']:null;    
        $this->apiPassword = isset($config['apiPassword'])
            ?$config['apiPassword']:null;  
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        if (!isset($this->apiEndpointUrl,$this->apiUsername,$this->apiPassword))
            throw new yii\base\InvalidConfigException("Rancher: ".
                "invalid configuration");
    }

    /*
     * prepare Rancher request‚ add Basic Authentification
     */
    protected function prepareRancherApiRequest($apiResource=null)
    {
        $client = new HttpClient([
            'baseUrl'=>$this->apiEndpointUrl,
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
        $response = $client->createRequest()
            ->addHeaders(['Authorization' => 'Basic ' . base64_encode(
                $this->apiUsername . ":". $this->apiPassword
            )]);
        if ($apiResource && $response)
            $response->setUrl($apiResource);
        return $response;
    }

    /*
     * Get stacks from Rancher
     * 
     * https://rancher.com/docs/rancher/v1.6/en/api/v2-beta/api-resources/stack/
     * 
     * @param Array $info ... List of elements to copy from a stack
     *                        If empty, all elements from a stack are copied
     * 
     * @return  
     * - Array with rancher stacks or
     * - String with error message on error 
     */
    const STACK_INFO = ['id','name','state','healthState','system'];
    public function getStacks($info = self::STACK_INFO)
    {
        $error = null;
        $stacks = [];
        try {
            $response = $this->prepareRancherApiRequest('/stacks')
                ->setMethod('GET')
                ->send();
            if ($response->isOk) {
                $responseData = $response->getData();
                foreach($responseData['data'] as $data) {
                    if (count($info)) {
                        foreach($info as $key)
                            $stack[$key] = $data[$key];
                        $stacks[] = $stack;
                    } else { 
                        $stacks[] = $data;
                    }
                }
            } else 
                $error = $response->getContent();
        } catch (\Exception $e) {
            $error = $e->getMessage();        
        } 
        return $error ?: $stacks;
    }

    /*
     * Deactivate stack ("service")
     * 
     * https://rancher.com/docs/rancher/v1.6/en/api/v2-beta/api-resources/stack/
     * 
     * @param Array $info ... List of elements to copy from a stack
     *                        If empty, all elements from a stack are copied
     * 
     * @return true or String with error message on error 
     */
    public function deactivateStack($id,$name="")
    {
        $error = null;
        try {
            $response = $this->getClientResponse()
                ->setMethod('POST')
                ->setUrl("/stacks/$id?action=deactivateservices")
                ->send();
            Yii::info("response=".print_r($response,true));
            if (!$response->isOk)
                $error = $response->getContent();
        } catch (\Exception $e) {
            $error = $e->getMessage();        
        } 
        return $error ?: true;
    }
}
