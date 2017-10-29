<?php

class GuidedLearningSchedule extends \Phalcon\Mvc\Model
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
    public $schedule_identified;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $order_by;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $guided_learning_id;

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
        $this->hasMany('id', 'GuidedLearningGamesMap', 'guided_learning_schedule_id', ['alias' => 'GuidedLearningGamesMap']);
        $this->belongsTo('guided_learning_id', '\GuidedLearning', 'id', ['alias' => 'GuidedLearning']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'guided_learning_schedule';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GuidedLearningSchedule[]|GuidedLearningSchedule|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GuidedLearningSchedule|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
