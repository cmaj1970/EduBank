<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;

/**
 * HelpText Helper
 *
 * Stellt zentral verwaltete Hilfetexte in Templates bereit.
 * Die Texte werden aus config/help_texts.php geladen.
 */
class HelpTextHelper extends Helper
{
    /**
     * Geladene Hilfetexte
     */
    protected $_texts = null;

    /**
     * Lädt die Hilfetexte beim ersten Zugriff
     */
    protected function _loadTexts()
    {
        if ($this->_texts === null) {
            $configFile = CONFIG . 'help_texts.php';
            if (file_exists($configFile)) {
                $this->_texts = require $configFile;
            } else {
                $this->_texts = [];
            }
        }
        return $this->_texts;
    }

    /**
     * Holt einen Inline-Hilfetext
     *
     * @param string $section Bereich (z.B. 'account', 'transfer')
     * @param string $key Schlüssel (z.B. 'iban', 'balance')
     * @return string Der Hilfetext oder leerer String
     */
    public function inline($section, $key)
    {
        $texts = $this->_loadTexts();
        return $texts['inline'][$section][$key] ?? '';
    }

    /**
     * Gibt ein data-help Attribut zurück (für einfache Verwendung in Templates)
     *
     * @param string $section Bereich
     * @param string $key Schlüssel
     * @return string Das komplette data-help="..." Attribut oder leerer String
     */
    public function attr($section, $key)
    {
        $text = $this->inline($section, $key);
        if ($text) {
            return 'data-help="' . h($text) . '"';
        }
        return '';
    }

    /**
     * Holt einen FAQ-Eintrag
     *
     * @param string $key Schlüssel (z.B. 'iban', 'tan')
     * @return array|null Array mit 'title' und 'content' oder null
     */
    public function faq($key)
    {
        $texts = $this->_loadTexts();
        return $texts['faq'][$key] ?? null;
    }

    /**
     * Holt alle FAQ-Einträge
     *
     * @return array Alle FAQ-Einträge
     */
    public function allFaq()
    {
        $texts = $this->_loadTexts();
        return $texts['faq'] ?? [];
    }

    /**
     * Holt einen allgemeinen Text
     *
     * @param string $key Schlüssel
     * @return string Der Text oder leerer String
     */
    public function general($key)
    {
        $texts = $this->_loadTexts();
        return $texts['general'][$key] ?? '';
    }

    /**
     * Rendert den Inhalt eines FAQ-Eintrags als HTML
     *
     * @param array $content Content-Array aus der Config
     * @return string HTML-String
     */
    public function renderFaqContent($content)
    {
        $html = '';
        foreach ($content as $item) {
            if (is_array($item) && isset($item['type']) && $item['type'] === 'list') {
                $html .= '<ul class="mb-3">';
                foreach ($item['items'] as $listItem) {
                    $html .= '<li>' . $listItem . '</li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<p>' . $item . '</p>';
            }
        }
        // Letztes </p> ohne mb-Klasse
        $html = preg_replace('/<p>([^<]*)<\/p>$/', '<p class="mb-0">$1</p>', $html);
        return $html;
    }
}
