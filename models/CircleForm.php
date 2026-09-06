<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;

class CircleForm extends \yii\base\Model
{
    public $purpose = '';
    public $mandate = '';
    public $parent_space_id;
    public $revision = -1;
    public $leader;
    public $delegate;
    public $facilitator;
    public $secretary;

    public function rules()
    {
        return [
            [['purpose', 'mandate'], 'trim'],
            [['purpose', 'mandate'], 'string', 'max' => 20000],
            [['parent_space_id', 'leader', 'delegate', 'facilitator', 'secretary'], 'default', 'value' => null],
            [['parent_space_id', 'leader', 'delegate', 'facilitator', 'secretary'], 'integer', 'min' => 1],
            ['revision', 'required'], ['revision', 'integer', 'min' => -1],
        ];
    }
    public function attributeLabels()
    {
        return array_merge(Role::LABELS, [
            'purpose' => 'Zweck – warum gibt es diesen Kreis?',
            'mandate' => 'Mandat – Verantwortung, Befugnisse und Grenzen',
            'parent_space_id' => 'Oberkreis',
        ]);
    }
    public static function forCircle(?Circle $circle): self
    {
        $form = new self();
        if ($circle) {
            $form->purpose = $circle->purpose;
            $form->mandate = $circle->mandate;
            $form->parent_space_id = $circle->parent_space_id;
            $form->revision = $circle->revision;
            foreach ($circle->roles as $role) { $form->{$role->role_key} = $role->user_id; }
        }
        return $form;
    }
    public function roleValues(): array
    {
        $result = [];
        foreach (Role::LABELS as $key => $label) {
            $result[$key] = $this->$key === null || $this->$key === '' ? null : (int) $this->$key;
        }
        return $result;
    }
}

