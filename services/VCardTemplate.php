<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

/** Sandboxed scalar data instead of unrestricted model property access. */
final class VCardTemplate
{
    public static function render(string $template, array $data): string
    {
        // Compatibility with the originally requested single-brace shortcodes.
        $template = preg_replace('/(?<!\{)\{\s*(user\.rolls|space\.purpose|space\.mandate)\s*\}(?!\})/', '{{ $1 }}', $template);
        $twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader(), ['autoescape' => 'html']);
        $twig->addExtension(new \Twig\Extension\SandboxExtension(
            new \Twig\Sandbox\SecurityPolicy(['if', 'for'], ['escape', 'e'], [], []), true
        ));
        try {
            return \yii\helpers\HtmlPurifier::process($twig->createTemplate($template)->render($data));
        } catch (\Twig\Error\Error $e) {
            // A broken admin template must not take down the whole profile card.
            return 'Die Kartenbeschreibung enthält eine ungültige Vorlage. Bitte die VCard-Konfiguration prüfen.';
        }
    }
}
