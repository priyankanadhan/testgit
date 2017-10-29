<?php

class Files extends \Phalcon\Mvc\Model
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
    public $name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=true)
     */
    public $status;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $mime_type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $size;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("nidara_dev");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'files';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Files[]|Files|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Files|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
