<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class AnnouncementBodySanitizer
{
    /**
     * @var array<string, string[]>
     */
    private array $allowedAttributes = [
        'a' => ['href', 'target', 'rel'],
        'div' => ['style'],
        'p' => ['style'],
        'span' => ['style'],
        'h2' => ['style'],
        'h3' => ['style'],
        'h4' => ['style'],
        'blockquote' => ['style'],
        'ul' => ['style'],
        'ol' => ['style'],
        'li' => ['style'],
        'img' => ['src', 'alt', 'title'],
    ];

    /**
     * @var string[]
     */
    private array $allowedTags = [
        'a',
        'blockquote',
        'br',
        'div',
        'em',
        'h2',
        'h3',
        'h4',
        'img',
        'li',
        'ol',
        'p',
        's',
        'span',
        'strong',
        'u',
        'ul',
    ];

    /**
     * @var string[]
     */
    private array $allowedStyleProperties = [
        'color',
        'font-size',
        'font-style',
        'font-weight',
        'text-align',
        'text-decoration',
    ];

    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $dom->loadHTML(
            mb_convert_encoding('<div>'.$html.'</div>', 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $root = $dom->documentElement;

        if (! $root instanceof DOMElement) {
            return '';
        }

        $this->sanitizeNode($root);

        return trim($this->innerHtml($root));
    }

    private function sanitizeNode(DOMNode $node): void
    {
        if ($node instanceof DOMText) {
            return;
        }

        if (! $node instanceof DOMElement) {
            $node->parentNode?->removeChild($node);

            return;
        }

        if (! in_array($node->tagName, $this->allowedTags, true)) {
            $this->unwrapNode($node);

            return;
        }

        $this->sanitizeAttributes($node);

        foreach (iterator_to_array($node->childNodes) as $child) {
            $this->sanitizeNode($child);
        }
    }

    private function sanitizeAttributes(DOMElement $element): void
    {
        $tag = $element->tagName;
        $allowedAttributes = $this->allowedAttributes[$tag] ?? [];

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = $attribute->nodeName;

            if (! in_array($name, $allowedAttributes, true)) {
                $element->removeAttribute($name);

                continue;
            }

            if ($name === 'href') {
                $href = trim($attribute->nodeValue);

                if (! $this->isAllowedUrl($href, ['http', 'https', 'mailto'])) {
                    $element->removeAttribute('href');

                    continue;
                }

                if ($element->getAttribute('target') === '_blank') {
                    $element->setAttribute('rel', 'noopener noreferrer');
                }
            }

            if ($name === 'src') {
                $src = trim($attribute->nodeValue);

                if (! $this->isAllowedUrl($src, ['http', 'https'])) {
                    $element->removeAttribute('src');
                }
            }

            if ($name === 'style') {
                $sanitizedStyle = $this->sanitizeStyle($attribute->nodeValue);

                if ($sanitizedStyle === '') {
                    $element->removeAttribute('style');
                } else {
                    $element->setAttribute('style', $sanitizedStyle);
                }
            }
        }
    }

    /**
     * @param  string[]  $allowedSchemes
     */
    private function isAllowedUrl(string $url, array $allowedSchemes): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, $allowedSchemes, true);
    }

    private function sanitizeStyle(string $style): string
    {
        $declarations = array_filter(array_map('trim', explode(';', $style)));
        $sanitized = [];

        foreach ($declarations as $declaration) {
            [$property, $value] = array_pad(array_map('trim', explode(':', $declaration, 2)), 2, null);

            if (! $property || ! $value) {
                continue;
            }

            $property = strtolower($property);

            if (! in_array($property, $this->allowedStyleProperties, true)) {
                continue;
            }

            if (! preg_match('/^[#(),.%\-\sa-zA-Z0-9]+$/', $value)) {
                continue;
            }

            $sanitized[] = "{$property}: {$value}";
        }

        return implode('; ', $sanitized);
    }

    private function unwrapNode(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if (! $parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private function innerHtml(DOMElement $element): string
    {
        $html = '';

        foreach ($element->childNodes as $child) {
            $html .= $element->ownerDocument->saveHTML($child);
        }

        return $html;
    }
}
