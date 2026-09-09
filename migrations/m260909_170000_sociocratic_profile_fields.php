<?php
// SPDX-License-Identifier: AGPL-3.0-only

use humhub\modules\user\models\ProfileField;
use humhub\modules\user\models\ProfileFieldCategory;
use humhub\modules\user\models\fieldtype\Select;
use yii\db\Migration;

/** Adds the profile-manager fields that describe a person's form of participation. */
class m260909_170000_sociocratic_profile_fields extends Migration
{
    private const CATEGORY_TITLE = 'Soziokratisches Modell';
    private const FIELD_NAME = 'sg_participation_type';

    public function safeUp()
    {
        $category = ProfileFieldCategory::findOne(['title' => self::CATEGORY_TITLE]);
        if (!$category) {
            $category = new ProfileFieldCategory([
                'title' => self::CATEGORY_TITLE,
                'description' => 'Angaben zur Form der Mitarbeit in der soziokratischen Organisation.',
                'sort_order' => 2000,
                'visibility' => 1,
            ]);
            if (!$category->save()) {
                throw new \RuntimeException('Die Profilkategorie für das soziokratische Modell konnte nicht angelegt werden.');
            }
        }

        // Never overwrite a field which already belongs to a manual or another module setup.
        if (ProfileField::findOne(['internal_name' => self::FIELD_NAME])) {
            return;
        }

        $field = new ProfileField([
            'profile_field_category_id' => $category->id,
            'field_type_class' => Select::class,
            'internal_name' => self::FIELD_NAME,
            'title' => 'Form der Mitarbeit',
            'description' => 'Hauptamtlich bezeichnet eine regelmäßige Mitarbeit mit erheblichem wöchentlichem Umfang. Ehrenamtlich bezeichnet freiwillige Mitarbeit mit typischerweise geringerem Umfang. „Hauptamtlich (unentgeltlich)“ beschreibt einen erheblichen Umfang ohne Vergütung.',
            'sort_order' => 100,
            'editable' => 1,
            'visible' => 1,
            'searchable' => 1,
            'directory_filter' => 1,
        ]);
        if (!$field->save()) {
            throw new \RuntimeException('Das Profilfeld für die Form der Mitarbeit konnte nicht angelegt werden.');
        }

        $field->fieldType->options = "employed=>Hauptamtlich\nunpaid=>Hauptamtlich (unentgeltlich)\nvoluntary=>Ehrenamtlich";
        $field->fieldType->save();
    }

    public function safeDown()
    {
        echo "Profilkategorie und -feld bleiben erhalten. Für einen Rückbau das HumHub-Profilfeld-Management verwenden.\n";
        return false;
    }
}
