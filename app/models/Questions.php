<?php

class Questions extends \Phalcon\Mvc\Model
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
    public $complexity_level;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $desc;

    /**
     *
     * @var string
     * @Column(type="string", length=128, nullable=true)
     */
    public $tags;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $lessons_id;

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
     * @Column(type="integer", length=11, nullable=false)
     */
    public $questions_category_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("nidara_dev");
        $this->hasMany('id', 'Answers', 'questions_id', ['alias' => 'Answers']);
        $this->hasMany('id', 'Options', 'questions_id', ['alias' => 'Options']);
        $this->hasMany('id', 'QuestionsTagging', 'questions_id', ['alias' => 'QuestionsTagging']);
        $this->belongsTo('questions_category_id', '\QuestionsCategory', 'id', ['alias' => 'QuestionsCategory']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'questions';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Questions[]|Questions|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Questions|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
