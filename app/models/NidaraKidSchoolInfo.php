<?php

class NidaraKidSchoolInfo extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $nidara_kid_profile_id;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $school_name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $school_type;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $address2;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $town_city;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $state;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $country;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created_at;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $created_by;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $modified_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("nidara_dev");
        $this->belongsTo('nidara_kid_profile_id', '\NidaraKidProfile', 'id', ['alias' => 'NidaraKidProfile']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraKidSchoolInfo[]|NidaraKidSchoolInfo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraKidSchoolInfo|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'nidara_kid_school_info';
    }

}
