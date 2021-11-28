<?php

namespace app\components;

use yii\base\Controller;
use yii\helpers\Url;
use SoapHeader;
use SoapVar;
use app\helpers\Helper;
use app\components\NlsSoapClient;
use SimpleXMLElement;


/**
 * 
 */
class Niloos
{
    private $format = 'd-m-Y H:i:s';
    private $cache;
    private $settings;
    private $settingsFile = '@app/config/settings.xml';
    private $nlsSecurityWsdlUrl = 'https://hunterdirectory.hunterhrms.com/SecurityService.svc?wsdl';
    private $nlsCardsWsdlUrl = 'https://huntercards.hunterhrms.com/HunterCards.svc?wsdl';
    private $nlsDirectoryWsdlUrl = 'https://directoryservicetestbasic.hunterhrms.com/DirectoryManagementService.svc?wsdl';
    private $nsoftApplicationId = '037685f2-9adc-4d65-954b-7212be692d85';
    private $nlsSecurityDomain = 'elbitsys';
    private $siteId = '037685f2-9adc-4d65-954b-7212be692d85';
    private $username = 'campaign';
    private $password = 'pass2019';
    private $languageCode = '1037';
    private $client;
    private $auth;

    public function __construct()
    {
        $this->cache = \Yii::$app->cache;

        // Flush cache
        if (array_key_exists('flushCache', \Yii::$app->params) && \Yii::$app->params['flushCache']) $this->cache->flush();

        $this->loadSettings();
        $this->authenticate();
    }

    private function loadSettings()
    {
        // custom initialization code goes here
        $url = Url::to($this->settingsFile);
        if (file_exists($url)) {
            $this->settings = simplexml_load_file($url);
            if (!$this->settings) {
                \Yii::error('Could not load settings file on console command', 'Niloos Get Service');
                die;
            }
            $this->setProperties();
        } else {
            \Yii::error('Settings file not exist console command', 'Niloos Get Service');
        }
    }

    private function setProperties()
    {
        $settingsArr = Helper::xml2array($this->settings);
        $this->nlsSecurityWsdlUrl = $settingsArr['nlsSecurityWsdlUrl'];
        $this->nlsCardsWsdlUrl = $settingsArr['nlsCardsWsdlUrl'];
        $this->nlsDirectoryWsdlUrl = $settingsArr['nlsDirectoryWsdlUrl'];
        $this->nsoftApplicationId = $settingsArr['nsoftApplicationId'];
        $this->nlsSecurityDomain = $settingsArr['nlsSecurityDomain'];
        $this->siteId = $settingsArr['nsoftSiteId'];
        $this->username = $settingsArr['nlsSecurityUsername'];
        $this->password = $settingsArr['nlsSecurityPassword'];
        $this->languageCode = $settingsArr['languageCode'];
    }

    private function setClient($service)
    {
        switch ($service) {
            case 'security':
                /** Define SOAP headers for token authentication **/
                $soap_headers = [
                    new SoapHeader('_', 'NiloosoftCred0', $this->nsoftApplicationId),
                    new SoapHeader('_', 'NiloosoftCred1', $this->nlsSecurityDomain . '\\' . $this->username),
                    new SoapHeader('_', 'NiloosoftCred2', $this->password)
                ];
                $url = $this->nlsSecurityWsdlUrl;
                break;
            case 'sec':
                $soap_headers = [
                    new SoapHeader('_', 'NiloosoftCred1', isset($this->auth) ? $this->auth->UsernameToken : null),
                    new SoapHeader('_', 'NiloosoftCred2', isset($this->auth) ? $this->auth->PasswordToken : null)
                ];
                $url = $this->nlsSecurityWsdlUrl;
                break;
            case 'cards':
                $soap_headers = [
                    new SoapHeader('_', 'NiloosoftCred1', isset($this->auth) ? $this->auth->UsernameToken : null),
                    new SoapHeader('_', 'NiloosoftCred2', isset($this->auth) ? $this->auth->PasswordToken : null)
                ];
                $url = $this->nlsCardsWsdlUrl;
                break;
            case 'directory':
                $soap_headers = [
                    new SoapHeader('_', 'NiloosoftCred1', isset($this->auth) ? $this->auth->UsernameToken : null),
                    new SoapHeader('_', 'NiloosoftCred2', isset($this->auth) ? $this->auth->PasswordToken : null)
                ];
                $url = $this->nlsDirectoryWsdlUrl;
                break;
            default:
                $this->client = null;
                return null;
                break;
        }

        $this->client = new NlsSoapClient($url, array(
            'trace' => 1,
            'exceptions' => 1,
            'cache_wsdl' => WSDL_CACHE_BOTH,
            'location' => explode('?', $url)[0],
            'encoding' => 'UTF-8'
        ));

        $this->client->__setSoapHeaders($soap_headers);
    }

    /**
     * Authenticate the user against the service and gets an Auth object
     * @return auth object with user data and expiration time
     * @throws \app\modules\niloos\models\Exception
     */
    private function authenticate()
    {
        $transactionCode = Helper::newGuid();
        try {
            $param[] = new SoapVar($this->nlsSecurityDomain . '\\' . $this->username, XSD_STRING, null, null, 'userName', null);
            $param[] = new SoapVar($this->password, XSD_STRING, null, null, 'password', null);
            $param[] = new SoapVar($transactionCode, XSD_STRING, null, null, 'transactionCode', null);
            $param[] = new SoapVar($this->siteId, XSD_STRING, null, null, 'applicationSecret', null);
            $options = new SoapVar($param, SOAP_ENC_OBJECT, null, null);

            $this->auth = \Yii::$app->cache->getOrSet('consoleAuth', function () use ($options) {
                $this->setClient('security');
                return $this->client->__soapCall("Authenticate2", array($options));
            }, 60 * 60 * 24);
        } catch (\Exception $ex) {
            $ex->transactionCode = $transactionCode;
            throw $ex;
        }
        //echo '[' . date("Y-m-d H:i:s") . '] UserNameToken: ' . $this->auth->UsernameToken . "\n";
    }

    public function testService()
    {
        $return = false;
        $this->setClient('cards');
        $res = $this->client->isServiceReachable();
        $return = $res->isServiceReachableResult;

        $this->setClient('directory');
        $res = $this->client->isServiceReachable();
        return $return && $res->isServiceReachableResult;
    }

    /**
     * @return categories array
     */
    public function categories($parentId = null)
    {
        $languageCode = $this->languageCode;

        $res = \Yii::$app->cache->getOrSet('categories', function () use ($parentId, $languageCode) {
            $this->setClient('directory');
            $list = [];

            try {
                $params = [
                    'transactionCode' => Helper::newGuid(),
                    'parentItemId' => $parentId,
                    'languageId' => $languageCode,
                    'listName' => $parentId === null ? 'ProfessionalCategories' : 'ProfessionalFields',
                ];

                $res = $this->client->GetListItems($params)->GetListItemsResult;

                if (!property_exists($res, 'ListItemInfo'))
                    return $list;

                $res = $res->ListItemInfo;

                foreach ($res as $cat) {
                    $list[] = [
                        'id' => $cat->ListItemId,
                        'text' => $cat->ValueTranslated
                    ];
                }

                return $list;
            } catch (\Exception $ex) {
                var_dump($ex);
                echo 'Request ' . $this->client->__getLastRequest();
                echo 'Response ' . $this->client->__getLastResponse();
                die;
            }
        }, 60 * 20);

        return $res;
    }

    /**
     * @return List By Name array
     */
    public function getListByName($name)
    {
        $languageCode = $this->languageCode;
        \Yii::$app->cache->flush();

        $res = \Yii::$app->cache->getOrSet($name, function () use ($name, $languageCode) {
            $this->setClient('directory');
            $list = [];

            try {
                $params = [
                    'transactionCode' => Helper::newGuid(),
                    'parentItemId' => null,
                    'languageId' => $languageCode,
                    'listName' => $name,
                ];

                $res = $this->client->GetListItems($params)->GetListItemsResult;

                if (!property_exists($res, 'ListItemInfo'))
                    return $list;

                $res = $res->ListItemInfo;

                foreach ($res as $item) {
                    $list[$item->ListItemId] = $item->ValueTranslated;
                }

                return $list;
            } catch (\Exception $ex) {
                var_dump($ex);
                echo 'Request ' . $this->client->__getLastRequest();
                echo 'Response ' . $this->client->__getLastResponse();
                die;
            }
        }, 60 * 30);

        return $res;
    }

    public function suppliersGetByFilter2($filter)
    {
        $this->setClient('cards');
        $languageCode = $this->languageCode;

        $res = \Yii::$app->cache->getOrSet('suppliersGetByFilter2', function () use ($languageCode, $filter) {
            $this->setClient('cards');

            $params = [
                'cardFilter' => $filter,
                'languageId' => $languageCode,
                'transactionCode' => Helper::newGuid(),
            ];

            try {
                $response = $this->client->SuppliersGetByFilter2($params);
                $xmlRes = $response->SuppliersGetByFilter2Result->any;
                $xmlRes = substr($xmlRes, strpos($xmlRes, '<diffgr:'));
                $xmlObj = simplexml_load_string($xmlRes);
                $json = json_encode($xmlObj);

                // If the Cards do not returned by the function
                if (!preg_match('/DocumentElement.*Cards/', $json)) {
                    return [];
                }
                $resarray = json_decode($json, TRUE);

                return $resarray['DocumentElement']['Cards'];
            } catch (\Exception $ex) {
                var_dump($ex);
                echo 'Request ' . $this->client->__getLastRequest();
                echo 'Response ' . $this->client->__getLastResponse();
                die;
            }
        }, 60 * 10);

        return $res;
    }

    public function jobGetConsideringIsDiscreetFiled($jobId)
    {
        $res = \Yii::$app->cache->getOrSet('jobGetCDF' . $jobId, function () use ($jobId) {
            $this->setClient('cards');

            $params = [
                'JobId' => $jobId,
                'transactionCode' => Helper::newGuid(),
            ];

            try {
                $response = $this->client->JobGetConsideringIsDiscreetFiled($params);
                return $response->JobGetConsideringIsDiscreetFiledResult;
            } catch (\Exception $ex) {
                var_dump($ex);
                echo 'Request ' . $this->client->__getLastRequest();
                echo 'Response ' . $this->client->__getLastResponse();
                die;
            }
        }, 60 * 10);

        return $res;
    }

    public function getJobFields($jobId)
    {
        try {
            $params = array(
                "JobId" => $jobId,
                'transactionCode' => Helper::newGuid(),
            );
            $this->setClient('cards');
            $response = $this->client->JobProfessionalFieldsGet($params);

            return $response->JobProfessionalFieldsGetResult;
        } catch (\Exception $e) {
            /**
             * var_dump($ex);
             * echo "Request " . $this->client->__getLastRequest();
             * echo "Response " . $this->client->__getLastResponse();
             * die;
             **/
            throw new \Exception('Error: Niloos services are not availiable, try later.');
        }
    }

    /*
     * @return Jobs by filter
     */
    public function jobsGetByFilter($filter, $cacheKey = 'niloos_search_result')
    {
        return $this->cache->getOrSet(
            $cacheKey,
            function () use ($filter) {
                try {
                    $this->setClient('cards');
                    $jobsXml =  $this->client->JobsGetByFilter($filter);
                    $jobsXml =  $jobsXml->JobsGetByFilterResult->any;
                    $jobsXml = substr($jobsXml, strpos($jobsXml, '<diffgr:'));
                    $jobsObj = simplexml_load_string($jobsXml);
                    $jobs = json_decode(json_encode($jobsObj), TRUE);
                    if (key_exists('DocumentElement', $jobs) && key_exists('Jobs', $jobs['DocumentElement'])) {
                        $jobsArray = $jobs['DocumentElement']['Jobs'];

                        // If only one item place it in an array
                        return key_exists('JobId', $jobsArray) ? [$jobsArray] : $jobsArray;
                    }
                } catch (\SoapFault $e) {
                    $test = $e;
                }

                // No results or bad result from search
                return [];
            },
            60 * 20
        );
    }
}
