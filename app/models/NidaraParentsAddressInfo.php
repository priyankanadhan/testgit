<?php

class NidaraParentsAddressInfo extends \Phalcon\Mvc\Model
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
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $address_type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $nidara_parents_profile_id;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $address1;

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
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $pincode;

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
        $this->belongsTo('nidara_parents_profile_id', '\NidaraParentsProfile', 'id', ['alias' => 'NidaraParentsProfile']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'nidara_parents_address_info';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraParentsAddressInfo[]|NidaraParentsAddressInfo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraParentsAddressInfo|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
