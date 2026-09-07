<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\controllers;

use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\sociocraticGovernance\models\{Circle, WorkItem};
use humhub\modules\sociocraticGovernance\services\{Access, WorkAccess, WorkService};

class WorkController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];
    public function behaviors()
    {
        return array_merge(parent::behaviors(), ['verbs' => [
            'class' => \yii\filters\VerbFilter::class, 'actions' => ['create' => ['POST'], 'change' => ['POST']],
        ]]);
    }
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) { return false; }
        if (!WorkAccess::activeUser() || !Access::read($this->contentContainer) || !Circle::findOne($this->contentContainer->id)) {
            throw new \yii\web\NotFoundHttpException();
        }
        return true;
    }
    public function actionIndex()
    {
        return $this->render('index', ['space' => $this->contentContainer, 'items' => $this->boardItems(), 'error' => '', 'draft' => new WorkItem(), 'draftTopics' => '']);
    }
    private function boardItems(): array
    {
        $items = [];
        foreach (WorkItem::find()->where(['space_id' => $this->contentContainer->id, 'archived_at' => null])->with(['events', 'space', 'circle', 'assignee', 'topics', 'resources.contributions.user'])->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])->all() as $item) {
            if (WorkAccess::read($item)) { $items[] = $item; }
        }
        return $items;
    }

    public function actionView($id)
    {
        $item = $this->findItem($id);
        return $this->render('view', ['space' => $this->contentContainer, 'item' => $item, 'error' => '']);
    }
    public function actionCreate()
    {
        $draft = new WorkItem();
        $input = Yii::$app->request->post('WorkItem', []);
        try {
            if (!is_array($input) || !is_string($input['title'] ?? null) || !is_string($input['description'] ?? null)
                || !in_array($input['kind'] ?? 'idea', ['idea', 'task'], true)) { throw new \DomainException('Ungültige Eingabe.'); }
            $draft->title = $input['title']; $draft->description = $input['description']; $draft->kind = $input['kind'] ?? 'idea';
            $topics = $input['topics'] ?? '';
            if (!is_string($topics)) { throw new \DomainException('Ungültige Themen.'); }
            $item = (new WorkService())->create($this->contentContainer, $draft->title, $draft->description, $input['kind'] ?? 'idea', $topics);
            return $this->redirect($this->contentContainer->createUrl('/sociocratic-governance/work/view', ['id' => $item->id]));
        } catch (\DomainException $e) {
            Yii::$app->response->statusCode = 422;
            return $this->render('index', ['space' => $this->contentContainer, 'items' => $this->boardItems(), 'error' => $e->getMessage(), 'draft' => $draft, 'draftTopics' => is_string($input['topics'] ?? null) ? $input['topics'] : '']);
        }
    }
    public function actionChange($id)
    {
        $item = $this->findItem($id);
        $revision = Yii::$app->request->post('revision');
        $action = Yii::$app->request->post('action');
        if (!is_scalar($revision) || filter_var($revision, FILTER_VALIDATE_INT) === false || (int) $revision < 0 || !is_string($action)) {
            throw new \yii\web\BadRequestHttpException('Ungültige Aktion oder Revision.');
        }
        try {
            $changed = (new WorkService())->change((int) $item->id, (int) $revision, $action, Yii::$app->request->post());
            return $this->redirect(Space::findOne($changed->space_id)->createUrl('/sociocratic-governance/work/view', ['id' => $changed->id]));
        } catch (\DomainException $e) {
            Yii::$app->response->statusCode = 422;
            return $this->render('view', ['space' => $this->contentContainer, 'item' => $this->findItem($id), 'error' => $e->getMessage(), 'submitted' => Yii::$app->request->post()]);
        }
    }
    private function findItem($id): WorkItem
    {
        if (!is_scalar($id) || filter_var($id, FILTER_VALIDATE_INT) === false || (int) $id < 1) { throw new \yii\web\NotFoundHttpException(); }
        $item = WorkItem::findOne(['id' => (int) $id, 'space_id' => $this->contentContainer->id]);
        if (!$item || !WorkAccess::read($item)) { throw new \yii\web\NotFoundHttpException(); }
        return $item;
    }
}
