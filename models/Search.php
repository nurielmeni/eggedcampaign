<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\components\Niloos;
use app\helpers\Helper;
use stdClass;

/**
 * ContactForm is the model behind the contact form.
 */
class Search extends Model
{

    const LANG_HEB = '1037';
    const LANG_ENG = '1033';

    private $niloos;
    private $supplierId;

    public function __construct($supplierId = null)
    {
        $this->niloos = new Niloos();
        $this->supplierId = $supplierId === null ? Yii::$app->request->get('sid', Yii::$app->params['supplierId']) : $supplierId;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        ];
    }

    public function formName() {
        return 'Search';
    }

    public function getSupplierId() {
        return $this->supplierId;
    }

    /**
     * This function will add a where filter to the $obj
     * @author Meni Nuriel
     * @version 1.0 this version tag is parsed
     */
    private function addWhereFilter($condition, $field, $searchPhrase, $values)
    {
        $JobFilterFields = [];
        is_array($values) ?: $values = [$values];

        foreach ($values as $value) {
            $JobFilterField = [
                'Field' => $field,
                'SearchPhrase' => $searchPhrase,
                'Value' => $value,
            ];
            $JobFilterFields[] = $JobFilterField;
        }

        // Then: build the FilterWhere
        $JobFilterWhere = [
            'Filters' => $JobFilterFields,
            'Condition' => $condition
        ];

        // Last: add the FilterWhere object above to WhereFilters array of your filter
        return $JobFilterWhere;
    }

    public function searchJobById($id) {
        $filter = [
            'transactionCode' => Helper::newGuid(),
            'LanguageId' => self::LANG_HEB,
            'jobFilter' => [
                'FromView' => 'Jobs',
                'NumberOfRows' => key_exists('maxNumberOfJobs', Yii::$app->params) ? Yii::$app->params['maxNumberOfJobs'] : 1000,
                'OffsetIndex' => 0,
                'SelectFilterFields' => [
                    'JobFilterFields' => [
                        'JobId',
                        'JobTitle',
                        'JobCode',
                        'CategoryId',
                        'RegionText',
                        'EmploymentType',
                    ],
                ],
                'OrderByFilterSort' => [
                    'JobFilterSort' => [
                        [
                            'Field' => 'JobTitle',
                            'Direction' => 'Ascending',
                        ],
                    ],
                ],
                'WhereFilters' => [
                    'JobFilterWhere' => [
                        $this->addWhereFilter('AND', 'SupplierId', 'Exact', $this->supplierId),
                        $this->addWhereFilter('AND', 'JobId', 'Exact', $id),
                    ],
                ],
            ],
        ];

        $cacheKey = 'job_id_' . $id;
        $jobs = $this->niloos->jobsGetByFilter($filter, $cacheKey);
        return is_array($jobs) && count($jobs) > 0 ? $jobs[0] : [];
    }

    public function getJobById($id)
    {
        return $this->niloos->jobGetConsideringIsDiscreetFiled($id);
    }

    public function jobs()
    {
        $filter = [
            'transactionCode' => Helper::newGuid(),
            'LanguageId' => self::LANG_HEB,
            'jobFilter' => [
                'FromView' => 'Jobs',
                'NumberOfRows' => key_exists('maxNumberOfJobs', Yii::$app->params) ? Yii::$app->params['maxNumberOfJobs'] : 1000,
                'OffsetIndex' => 0,
                'SelectFilterFields' => [
                    'JobFilterFields' => [
                        'CityId',
                        //                        'CountryCodeFIPS', 
                        'Description',
                        'JobId',
                        //                        'JobSeniority', 
                        'JobTitle',
                        'JobCode',
                        //                        'OpenDate',
                        'CategoryId',
                        //                        'OpenPositions', 
                        'Rank',
                        'RegionValue',
                        'Requiremets',
                        'Skills',
                        //                        'YearsOfExperience',
                        //                        'EmployerName',
                        'JobScope',
                        //                        'EmployerId',
                        'RegionText',
                        'EmploymentType',
                        'UpdateDate',
                        //                        'ExpertiseId',
                        'ProfessionalFieldId'
                    ],
                ],
                'OrderByFilterSort' => [
                    'JobFilterSort' => [
                        [
                            'Field' => 'JobTitle',
                            'Direction' => 'Ascending',
                        ],
                    ],
                ],
                'WhereFilters' => [
                    'JobFilterWhere' => [
                        $this->addWhereFilter('AND', 'SupplierId', 'Exact', $this->supplierId),
                    ],
                ],
            ],
        ];

        $cacheKey = $this->supplierId;
        $jobs = $this->niloos->jobsGetByFilter($filter, $cacheKey);

        return $jobs;
    }
}
