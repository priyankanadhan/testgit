<?php

class NidaraKidProfile extends \Phalcon\Mvc\Model
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
    public $first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $middle_name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $last_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $date_of_birth;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $age;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $gender;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $grade;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $child_photo;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $child_avatar;

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
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $board_of_education;
    
    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=true)
     */
    public $status;
    
    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $cancel_subscription;
  	/**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    
    public $relationship_to_child;
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("nidara_dev");
        $this->hasMany('id', 'Answers', 'nidara_kid_profile_id', ['alias' => 'Answers']);
        $this->hasMany('id', 'GameHistory', 'nidara_kid_profile_id', ['alias' => 'GameHistory']);
        $this->hasMany('id', 'KidsGamesStatus', 'nidara_kid_profile_id', ['alias' => 'KidsGamesStatus']);
        $this->hasMany('id', 'NidaraKidCaregiverInfo', 'nidara_kid_profile_id', ['alias' => 'NidaraKidCaregiverInfo']);
        $this->hasMany('id', 'NidaraKidFamilyInfo', 'nidara_kid_profile_id', ['alias' => 'NidaraKidFamilyInfo']);
        $this->hasMany('id', 'NidaraKidFriendsInfo', 'nidara_kid_profile_id', ['alias' => 'NidaraKidFriendsInfo']);
        $this->hasMany('id', 'NidaraKidLanguageInfo', 'nidara_kid_profile_id', ['alias' => 'NidaraKidLanguageInfo']);
        $this->hasMany('id', 'NidaraKidPhysicalInfo', 'nidara_kid_profile_id', ['alias' => 'NidaraKidPhysicalInfo']);
        $this->hasMany('id', 'NidaraKidSchoolInfo', 'nidara_kid_profile_id', ['alias' => 'NidaraKidSchoolInfo']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraKidProfile[]|NidaraKidProfile|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return NidaraKidProfile|\Phalcon\Mvc\Model\ResultInterface
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
        return 'nidara_kid_profile';
    }

}
