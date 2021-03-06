<?php

namespace app\models;

use Yii;
use app\components\Niloos;
use yii\base\Model;
use kartik\mpdf\Pdf;
use app\models\Search;
use Mpdf\Shaper\Sea;
use yii\helpers\ArrayHelper;
use yii\base\ErrorException;

/**
 * ContactForm is the model behind the contact form.
 */
class BaseContactForm extends Model
{
    public $name;
    public $phone;
    public $searchArea;
    public $supplierId;
    public $cvfile;
    public $education;
    public $experiance;

    public function __construct($config = array()) {
        parent::__construct($config);
        $this->experiance = Yii::t('app', 'Experiance');
        $this->education = Yii::t('app', 'Education');
    }
    
    protected $tmpFiles = [];
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'phone', 'searchArea'], 'required'],
            ['supplierId', 'match', 'pattern' => '/^[a-zA-Z\d-]+$/i'],
            [['name', 'phone', 'searchArea'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            ['phone', 'match', 'pattern' => '/^0[0-9]{1,2}[-\s]{0,1}[0-9]{3}[-\s]{0,1}[0-9]{4}/i'],
            ['cvfile', 'file', 'extensions' => ['doc', 'docx', 'pdf', 'rtf']],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'searchArea' => Yii::t('app', 'Search Area'),
            'cvfile' => Yii::t('app', 'Attach CV file (Not Mandatory)'),
            'supplierId' => Yii::t('app', 'Supplier Id'),
            'education' => Yii::t('app', 'Education'),
            'experiance' => Yii::t('app', 'Experiance'),
        ];
    }
    
    /**
     * @return array customized attribute labels
     */
    public function jobsMapping()
    {
        return [
            'jb-24' => ['area' => Yii::t('app', 'North'), 'licensed' => true],
            'jb-25' => ['area' => Yii::t('app', 'South'), 'licensed' => true],
            'jb-26' => ['area' => Yii::t('app', 'Jerusalem'), 'licensed' => true],
            'jb-27' => ['area' => Yii::t('app', 'North'), 'licensed' => false],
            'jb-28' => ['area' => Yii::t('app', 'South'), 'licensed' => false],
            'jb-29' => ['area' => Yii::t('app', 'Jerusalem'), 'licensed' => false],
            'jb-30' => ['area' => Yii::t('app', ''), 'licensed' => false],
            'jb-31' => ['area' => Yii::t('app', ''), 'licensed' => false],
            'jb-32' => ['area' => Yii::t('app', ''), 'licensed' => false],
        ];
    }

    public function getSearchAreaOptions() {
        $search = new Search('528e3917-7e8d-436d-9eb3-46570a63a42d');
        $jobs = $search->jobs();
        return ArrayHelper::map($jobs, 'JobCode', 'JobTitle');
    }

    public function getYesnoOptions() {
        return [
            Yii::t('app', 'Yes') => Yii::t('app', 'Yes'),
            Yii::t('app', 'No') => Yii::t('app', 'No'),
        ];
    }
    
    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
    public function contact($email, $content)
    {
        $res = 0;
        $subject = Yii::t('app', 'New request - Egged Jobs') . ' - ' . $this->jobCode;
        if (!$this->cvfile || empty($this->cvfile)) {
            $this->generateCv($content);
        }
        $this->generateNcai();
        
        try {
            $message = Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([$email => Yii::$app->params['cvWebMailName']])
                ->setSubject($subject)
                ->setHtmlBody($content)
                ->setTextBody(strip_tags($content));
            
            if (array_key_exists('bccMail', \Yii::$app->params) && !empty(\Yii::$app->params['bccMail'])) {
                $message->setBcc(\Yii::$app->params['bccMail']);
            }
            foreach ($this->tmpFiles as $tmpFile) {
                $message->attach($tmpFile);
            }
                    
            $res = $message->send();
        } catch (ErrorException $e) {
            echo ("Exp: $e");
        }
        
        $this->removeTmpFiles();

        return $res;
    }
        
    protected function removeTmpFiles() {
        foreach ($this->tmpFiles as $tmpFile) {
            unlink($tmpFile);
        }
    }
    
    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether email sent successfully
     */
    public function followUpMail($content)
    {
        $subject = '?????? ?????????? ?????? - ?????????? ????????????';
            return Yii::$app->mailer->compose()
                ->setTo($this->email)
                ->setFrom([Yii::$app->params['fromMail'] => Yii::$app->params['fromName']])
                ->setSubject($subject)
                ->setHtmlBody($content)
                ->setTextBody(strip_tags($content))
                ->send();
    }
    
    public function generateCv($content) {
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8, 
            // A4 paper format
            'format' => Pdf::FORMAT_A4, 
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            // stream to browser inline
            'destination' => Pdf::DEST_FILE, 
            // your html content input
            'content' => $content,  
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}', 
             // set mPDF properties on the fly
            'options' => ['title' => '?????? - ???????? ?????????? ???????? ??????????????'],
             // call mPDF methods on the fly
            'methods' => [ 
                'SetHeader'=>['?????? - ?????????? ???????? ????????????'], 
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);
        
        $tmpfile = Yii::getAlias('@webroot') . '/uploads/cvFile' . date('s', time()) . '.pdf';
        $pdf->output($content, $tmpfile, Pdf::DEST_FILE);
        $this->tmpFiles[] = $tmpfile;
        return true;
    }
    
    protected function sanitizeFileName($file, $ext = null) {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;[]().
        // If you don't need to handle multi-byte characters
        // you can use preg_replace rather than mb_ereg_replace
        // Thanks @??ukasz Rysiak!
        $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);
        // Remove any runs of periods (thanks falstro!)
        $file = mb_ereg_replace("([\.]{2,})", '', $file);
        return $ext ? ($file . '.' . $ext) : $file;
    }
    
    public function upload()
    {
        $tmpFile = 'uploads/' . $this->sanitizeFileName($this->cvfile->baseName, $this->cvfile->extension);
        if ($this->cvfile->saveAs($tmpFile)) {
            $this->tmpFiles[] = $tmpFile;
        }
        return true;
    }
    
    public function getJobCode() {
        $jobCode = "";
        $licanse = isset($this->licanse) && $this->licanse == Yii::t('app', 'Yes');
        switch ($this->searchArea) {
            case Yii::t('app', 'North'):
                $jobCode = $licanse ? 'JB-24' : 'JB-27';
                break;
            case Yii::t('app', 'Jerusalem'):
                $jobCode = $licanse ? 'JB-26' : 'JB-29';
                break;
            case Yii::t('app', 'South'):
                $jobCode = $licanse ? 'JB-25' : 'JB-28';
                break;
            case 'default':
                $jobCode = "";
                break;
        }    
        return $jobCode;
    }
}
