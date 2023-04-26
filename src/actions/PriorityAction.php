<?php
/**
 * @author: Sergey Mashkov (serge@asse.com)
 * Date: 4/24/23 10:06 AM
 * Project: asse-db-template
 */

namespace xpbl4\grid\actions;

class PriorityAction extends \yii\base\Action
{
	/** @var \yii\db\ActiveQuery */
	public $query;

	/** @var string */
	public $attribute = 'priority';

	/** @var string */
	public $primaryKey = 'id';

	public $pwr = 2;

	public function run()
	{
		$transaction = \Yii::$app->db->beginTransaction();
		$offset = \Yii::$app->request->post('offset');
		try {
			$pk = $this->primaryKey;
			$sorting = \Yii::$app->request->post('sorting');
			$models = $this->query
				->select([$pk, $this->attribute])
				->andWhere([$pk => $sorting])
				->indexBy('id')
				->orderBy([$this->attribute => SORT_DESC])
				->all();

			$maxOrder = 0;
			foreach ($sorting as $cnt => $id) {
				if (!isset($models[$id]))
					throw new \yii\web\BadRequestHttpException('Invalid request. Unknown row id.');

				if ($models[$id]->{$this->attribute} > 0) $maxOrder = $cnt + 1;
			}

			for ($i = 0; $i < $maxOrder; ++$i) {
				if (!isset($sorting[$i])) continue;

				$id = $sorting[$i];
				$models[$id]->{$this->attribute} = $this->pwr * ($offset + $maxOrder - $i);
				$models[$id]->update(false, [$this->attribute]);
			}

			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
		}

		return $this->controller->redirect(\Yii::$app->request->referrer);
	}

}