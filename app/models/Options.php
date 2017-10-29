<?php

class Options extends \Phalcon\Mvc\Model
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
    public $option;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_image;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $image_path;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_answer;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_multi_answer;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $questions_id;

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
        $this->hasMany('id', 'Answers', 'options_id', ['alias' => 'Answers']);
        $this->belongsTo('questions_id', '\Questions', 'id', ['alias' => 'Questions']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'options';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Options[]|Options|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Options|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
