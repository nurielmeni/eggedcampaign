<?php
namespace app\components;

use SoapClient;
use yii\helpers\Url;
use SoapHeader;
use SoapVar;


/**
 * Description of NlsSoapClient
 *
 * @author nurielmeni
 */
class NlsSoapClient extends SoapClient
{

    function __doRequest($request, $location, $action, $version, $one_way = null) 
    {
        set_exception_handler([$this, 'handleException']);
        if (strpos($location, 'SecurityService')) { 
            $namespace = "http://NilooSoft.com";

            $request = preg_replace('/<ns1:(\w+)/', '<$1 xmlns="' . $namespace . '"', $request, 1);
            $request = preg_replace('/<ns1:(\w+)/', '<$1', $request);
            $request = str_replace(array('/ns1:', 'xmlns:ns1="' . $namespace . '"'), array('/', ''), $request);
        }
        // parent call
        return parent::__doRequest($request, $location, $action, $version);
    }
    public function handleException($e)
    {
        if (strpos($e->getMessage(), 'Security Service Evaluate Failed') !== false) {
            if ($e->getTrace()[0]['args'][1][0]->enc_value[0]->enc_value == 'seevprod\List') {
                echo "Could not connect to Hunter Services! check youer credentials.";
                die;
            }
            \Yii::$app->getSession()->setFlash('error', '<strong>Authentication Failed:</strong> username or password incorrect.');
            header('Location: ' . Url::to('@web/site/login'));
            die();
        }  
        
        restore_exception_handler();
        throw $e;
    }
}
