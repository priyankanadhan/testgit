<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class NidaraParentsProfile extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=45, nullable=true)
     */
    public $parent_type;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $last_name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $email;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $mobile;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $occupation;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $company_name;

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
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("nidara_dev");
        $this->hasMany('id', 'NidaraParentsAddressInfo', 'nidara_parents_profile_id', ['alias' => 'NidaraParentsAddressInfo']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'nidara_parents_profile';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraParentsProfile[]|NidaraParentsProfile|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraParentsProfile|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
