<?php

class GuidedLearningGamesMap extends \Phalcon\Mvc\Model
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
    public $guided_learning_schedule_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $games_tagging_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("nidara_dev");
        $this->hasMany('id', 'GameHistory', 'guided_learning_games_map_id', ['alias' => 'GameHistory']);
        $this->hasMany('id', 'KidsGamesStatus', 'guided_learning_games_map_id', ['alias' => 'KidsGamesStatus']);
        $this->belongsTo('games_tagging_id', '\GamesTagging', 'id', ['alias' => 'GamesTagging']);
        $this->belongsTo('guided_learning_schedule_id', '\GuidedLearningSchedule', 'id', ['alias' => 'GuidedLearningSchedule']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GuidedLearningGamesMap[]|GuidedLearningGamesMap|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GuidedLearningGamesMap|\Phalcon\Mvc\Model\ResultInterface
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
        return 'guided_learning_games_map';
    }

}
