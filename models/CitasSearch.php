<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CitasSearch represents the model behind the search form of `\app\models\Citas`.
 */
class CitasSearch extends Citas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'usuario_id', 'especialista_id'], 'integer'],
            [['instante',
                'especialista.especialidad.especialidad',
                'especialista.nombre', ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            [
            'especialidad',
            'especialista',
            'especialista.especialidad.especialidad',
            'especialista.nombre',
        ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Citas::find()
            ->joinWith(['especialista ea' => function ($q) {
                $q->joinWith('especialidad ed');
            }]);

        $query->where(['usuario_id' => Yii::$app->user->id])
            ->andWhere('instante > LOCALTIMESTAMP');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['especialista.especialidad.especialidad'] = [
            'asc' => ['ed.especialidad' => SORT_ASC],
            'desc' => ['ed.especialidad' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['especialista.nombre'] = [
            'asc' => ['ea.nombre' => SORT_ASC],
            'desc' => ['ea.nombre' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            // 'id' => $this->id,
            // 'usuario_id' => $this->usuario_id,
            // 'especialista_id' => $this->especialista_id,
            'instante' => $this->instante,
        ]);

        return $dataProvider;
    }
}
