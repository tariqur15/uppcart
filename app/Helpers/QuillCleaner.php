<?php

namespace App\Helpers;

use DOMDocument;
use DOMXPath;

class QuillCleaner
{
    private const STRIP_ATTRS = [
        'class', 'cellpadding', 'cellspacing',
        'data-table-id', 'data-row-id', 'data-col-id',
        'data-wrap-tag', 'data-tag', 'data-style', 'data-align',
        'contenteditable',
    ];

    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $html = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, ~0], 'UTF-8');

        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);

        foreach ($xpath->query('//div[@class="ql-table-wrapper"]') as $wrapper) {
            $table = null;

            foreach ($wrapper->childNodes as $child) {
                if ($child->nodeName === 'table') {
                    $table = $child;
                    break;
                }
            }

            if (! $table) {
                continue;
            }

            $parent = $wrapper->parentNode;
            $parent->replaceChild($table, $wrapper);
        }

        foreach ($xpath->query('//colgroup') as $colgroup) {
            $colgroup->parentNode->removeChild($colgroup);
        }

        foreach ($xpath->query('//div[@class="ql-table-cell-inner"]') as $inner) {
            $parent = $inner->parentNode;

            while ($inner->firstChild) {
                $parent->insertBefore($inner->firstChild, $inner);
            }

            $parent->removeChild($inner);
        }

        foreach ($xpath->query('//*[contains(@class, "ql-align-")]') as $el) {
            $classes = explode(' ', $el->getAttribute('class'));

            foreach ($classes as $class) {
                if (str_starts_with($class, 'ql-align-')) {
                    $alignment = substr($class, strlen('ql-align-'));
                    $existingStyle = trim((string) $el->getAttribute('style'), '; ');

                    if ($existingStyle && ! preg_match('/text-align\s*:/', $existingStyle)) {
                        $el->setAttribute('style', $existingStyle.'; text-align: '.$alignment);
                    } elseif (! $existingStyle) {
                        $el->setAttribute('style', 'text-align: '.$alignment);
                    }

                    break;
                }
            }
        }

        foreach ($xpath->query('//table | //tr | //td | //th') as $el) {
            foreach (self::STRIP_ATTRS as $attr) {
                $el->removeAttribute($attr);
            }
        }

        foreach ($xpath->query('//*[starts-with(@data-, "")]') as $el) {
            $toRemove = [];

            for ($i = 0; $i < $el->attributes->length; $i++) {
                $attr = $el->attributes->item($i);

                if (str_starts_with($attr->name, 'data-')) {
                    $toRemove[] = $attr->name;
                }
            }

            foreach ($toRemove as $attr) {
                $el->removeAttribute($attr);
            }
        }

        foreach ($xpath->query('//p') as $p) {
            $text = trim($p->textContent);

            if ($text === '' && ! $p->hasChildNodes()) {
                $p->parentNode->removeChild($p);
            } elseif ($text === '' && $p->childNodes->length === 1 && $p->firstChild->nodeName === 'br') {
                $p->parentNode->removeChild($p);
            }
        }

        $output = $doc->saveHTML();
        $output = preg_replace('#^<div>(.*)</div>$#s', '$1', $output);

        return trim($output);
    }
}
