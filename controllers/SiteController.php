<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\FbfContactForm;
use app\models\Campain;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    public $defaultAction = 'contact';
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
    public function actionIndex($id)
    {
        $campain = Campain::findOne($id);
        
        if ($campain === null) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'A campaign with this ID could not be found'));
        }
        
        $model = new ContactForm();
        $contactForm = $this->render('contact', [
            'model' => $model,
        ]);
        
        return $this->render('index', [
            'campain' => $campain,
            'contactForm' => $contactForm,
        ]);
    }
     */

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'main_admin';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/campain/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/campain/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $this->layout = 'main_admin';
        Yii::$app->user->logout();

        return $this->redirect(['/campain/index']);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact($id)
    {
        $request = Yii::$app->request;
        $campain = Campain::findOne($id);
        $jobCode = $request->get('jobcode');
        
        
        if ($campain === null) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'A campaign with this ID could not be found'));
        }
        
        $now = time();
        if ($campain->start_date_int > $now || (isset($campain->end_date_int) && $campain->end_date_int < $now)) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'The campaign is not active!'));
        }

        if ($campain->fbf === 0) {
            $model = new ContactForm();
            if ($campain->show_licanse) $model->scenario = 'showLicanse';
            $model->supplierId = Yii::$app->request->get('sid', (empty($campain->sid) ? Yii::$app->params['supplierId'] : $campain->sid));
        } else {
            $model =  new FbfContactForm();
            $model->supplierId = Yii::$app->request->get('sid', (empty($campain->sid) ? Yii::$app->params['supplierIdFbf'] : $campain->sid));
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->cvfile = UploadedFile::getInstance($model, 'cvfile');
            if ($model->cvfile) $model->upload();
            if ($model->contact(Yii::$app->params['cvWebMail'], $this->renderPartial('_cvView', ['model' => $model]))) {
                Yii::$app->session->setFlash('contactFormSubmitted');
            }
        }
        
        if ($jobCode) {
            $model->searchArea = $model->jobsMapping()[$jobCode]['area'];
        }
        if ($campain->fbf === 0) {
            return $this->render('contact', [
                'campain' => $campain,
                'model' => $model,
            ]);
        } else {
            return $this->render('contactFbf', [
                'campain' => $campain,
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContactFbf($id)
    {
        $campain = Campain::findOne($id);
        
        
        if ($campain === null) {
            throw new \yii\web\NotFoundHttpException(Yii::t('app', 'A campaign with this ID could not be found'));
        }
        
        
        $modelFbf = new FbfContactForm();
        if ($modelFbf->load(Yii::$app->request->post())) {
            if (is_array($modelFbf->name)) {
                for ($i = 0; $i < count($modelFbf->name); $i++) {
                    $model = $this->contactModel($modelFbf, $i);
                    if (!$model->validate()) continue;
                    $model->cvfile = UploadedFile::getInstance($model, "cvfile[$i]");
                    if ($model->cvfile) $model->upload();
                    if ($model->contact(Yii::$app->params['cvWebMail'], $this->renderPartial('_cvFbfView', ['model' => $model]))) {
                        $sent = true;
                    }
                }
                
                if ($sent) {
                    Yii::$app->session->setFlash('contactFormSubmitted');
                    return $this->refresh();
                }
            }
        }
        
        return $this->render('contactFbf', [
            'campain' => $campain,
            'model' => $modelFbf,
        ]);
    }
    
    private function contactModel($modelFbf, $i) {
        $model = new FbfContactForm();
        foreach ($model->attributes as $attribute => $value) {
            $model->$attribute = is_array($modelFbf->attributes[$attribute]) ? $modelFbf->attributes[$attribute][$i] : $modelFbf->attributes[$attribute];
        }
        return $model;
    }
    
    public function actionApplicant() {
        $request = Yii::$app->request;
        $id = $request->post('id', '');
        $del = $request->post('del', false);
        $model = new FbfContactForm();
        return $this->renderAjax('applicant', [
            'model' => $model,
            'id' => $id,
            'del' => $del,
        ]);
    }
}
