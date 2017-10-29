<?php

class ParentsMappingProfiles extends \Phalcon\Mvc\Model
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
    public $primary_parents_id;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    public $primary_parent_type;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    public $secondary_parent_id;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    public $secondary_parent_type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $parent_photo;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("nidara_dev");
        $this->belongsTo('primary_parents_id', '\Users', 'id', ['alias' => 'Users']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'parents_mapping_profiles';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ParentsMappingProfiles[]|ParentsMappingProfiles|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ParentsMappingProfiles|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
