<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "campain".
 *
 * @property int $id
 * @property int $fbf
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property string $campain
 * @property string $image
 * @property string $logo
 * @property string $sid
 * @property int $show_licanse
 * @property int $show_cv
 * @property string $button_color
 * @property string $contact
 */
class Campain extends \yii\db\ActiveRecord
{
    public $start_date_int;
    public $end_date_int;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campain';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            ['start_date', 'date', 'format' => 'php:d/m/Y', 'timestampAttribute' => 'start_date'],
            ['start_date', 'default', 'value' => date('d/m/Y', time())],
            ['end_date', 'date', 'format' => 'php:d/m/Y', 'timestampAttribute' => 'end_date'],
            ['end_date', 'default', 'value' => null],
            [['show_licanse', 'show_cv', 'fbf'], 'integer'],
            [['name', 'sid'], 'string', 'max' => 64],
            [['contact'], 'string', 'max' => 30],
            [['campain', 'image', 'logo'], 'string', 'max' => 1024],
            [['button_color'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fbf' => Yii::t('app', 'Friend Brings Friend'),
            'name' => Yii::t('app', 'Name'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'campain' => Yii::t('app', 'Campain'),
            'image' => Yii::t('app', 'Image'),
            'logo' => Yii::t('app', 'Logo'),
            'sid' => Yii::t('app', 'Sid'),
            'show_licanse' => Yii::t('app', 'Show Licanse'),
            'show_cv' => Yii::t('app', 'Show Cv'),
            'button_color' => Yii::t('app', 'Button Color'),
            'contact' => Yii::t('app', 'Contact Text'),
        ];
    }

    public function afterFind() {
        parent::afterFind();
        $this->start_date_int = $this->start_date;
        $this->end_date_int = $this->end_date;
        $this->start_date = empty($this->start_date) ? $this->start_date : date('d/m/Y', $this->start_date);
        $this->end_date = empty($this->end_date) ? $this->end_date : date('d/m/Y', $this->end_date);
    }
}
