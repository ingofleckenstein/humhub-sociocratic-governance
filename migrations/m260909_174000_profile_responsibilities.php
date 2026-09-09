<?php
// SPDX-License-Identifier: AGPL-3.0-only

use humhub\modules\sociocraticGovernance\models\fieldtype\CircleResponsibilities;
use humhub\modules\user\models\ProfileField;
use humhub\modules\user\models\ProfileFieldCategory;
use yii\db\Migration;

/** Renames the profile category and adds the dynamic list of circles and roles. */
class m260909_174000_profile_responsibilities extends Migration
{
    private const CATEGORY_TITLE = 'Rollen & Zuständigkeiten';
    private const LEGACY_CATEGORY_TITLE = 'Soziokratisches Modell';
    private const FIELD_NAME = 'sg_circle_responsibilities';

    public function safeUp()
    {
        $category = ProfileFieldCategory::findOne(['title' => self::CATEGORY_TITLE])
            ?? ProfileFieldCategory::findOne(['title' => self::LEGACY_CATEGORY_TITLE]);
        if (!$category) {
            throw new \RuntimeException('Die Profilkategorie für das soziokratische Modell wurde nicht gefunden.');
        }
        if ($category->title !== self::CATEGORY_TITLE) {
            $category->title = self::CATEGORY_TITLE;
            $category->description = 'Kreise, Rollen und Form der Mitarbeit in der soziokratischen Organisation.';
            if (!$category->save()) {
                throw new \RuntimeException('Die Profilkategorie konnte nicht umbenannt werden.');
            }
        }
        if (ProfileField::findOne(['internal_name' => self::FIELD_NAME])) {
            return;
        }
        $field = new ProfileField([
            'profile_field_category_id' => $category->id,
            'field_type_class' => CircleResponsibilities::class,
            'internal_name' => self::FIELD_NAME,
            'title' => 'Kreise und Rollen',
            'description' => 'Aktuelle Mitgliedschaften in sichtbaren Projekt- und Kompetenzkreisen sowie die darin übernommenen Rollen.',
            'sort_order' => 200,
            'visible' => 1,
            'editable' => 0,
            'searchable' => 0,
            'required' => 0,
            'show_at_registration' => 0,
            'directory_filter' => 0,
        ]);
        // Module are not loaded as profile-field-type providers in every Yii
        // console migration run. The class itself is the supported virtual type
        // and needs no field configuration or profile-table column.
        if (!$field->save(false)) {
            throw new \RuntimeException('Das dynamische Profilfeld für Kreise und Rollen konnte nicht angelegt werden.');
        }
    }

    public function safeDown()
    {
        echo "Profilkategorie und Rollenübersicht bleiben erhalten. Für einen Rückbau das HumHub-Profilfeld-Management verwenden.\n";
        return false;
    }
}
