<?php

declare(strict_types=1);
namespace oneX\Core;
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'em', 'u', 's', 'a', 'ul', 'ol', 'li',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'blockquote', 'code', 'pre',
        'table', 'thead', 'tbody', 'tr', 'td', 'th',
        'img', 'figure', 'figcaption',
        'div', 'span', 'hr',
        'iframe'
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'style'],
        'iframe' => ['src', 'width', 'height', 'frameborder', 'allow', 'allowfullscreen', 'title', 'style'],
        'table' => ['class', 'style'],
        'td' => ['colspan', 'rowspan', 'style'],
        'th' => ['colspan', 'rowspan', 'style'],
        'div' => ['class', 'style'],
        'span' => ['class', 'style'],
        'p' => ['class', 'style'],
        'h1' => ['class', 'style'],
        'h2' => ['class', 'style'],
        'h3' => ['class', 'style'],
        'h4' => ['class', 'style'],
        'h5' => ['class', 'style'],
        'h6' => ['class', 'style'],
        'ul' => ['class', 'style'],
        'ol' => ['class', 'style'],
        'li' => ['class', 'style'],
        'blockquote' => ['class', 'style'],
        'code' => ['class'],
        'pre' => ['class']
    ];

    private const ALLOWED_IFRAME_DOMAINS = [
        'youtube.com',
        'www.youtube.com',
        'player.vimeo.com',
        'vimeo.com',
        'tiktok.com',
        'www.tiktok.com'
    ];

    public static function sanitize(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $html = self::removeScripts($html);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        self::sanitizeNode($dom->documentElement);
        $sanitized = $dom->saveHTML();
        $sanitized = str_replace('<?xml encoding="UTF-8">', '', $sanitized);

        return $sanitized;
    }

    private static function removeScripts(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*[^\s>]*/i', '', $html);
        $html = preg_replace('/javascript:/i', '', $html);
        
        return $html;
    }

    private static function sanitizeNode(?\DOMNode $node): void
    {
        if (!$node) {
            return;
        }
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tagName = strtolower($child->nodeName);
                if (!in_array($tagName, self::ALLOWED_TAGS, true)) {
                    $node->removeChild($child);
                    continue;
                }
                if ($tagName === 'iframe') {
                    if (!self::isAllowedIframe($child)) {
                        $node->removeChild($child);
                        continue;
                    }
                }
                self::sanitizeAttributes($child);
                self::sanitizeNode($child);
            }
        }
    }
    private static function isAllowedIframe(\DOMElement $iframe): bool
    {
        $src = $iframe->getAttribute('src');
        if (empty($src)) {
            return false;
        }

        $parsedUrl = parse_url($src);
        if (!isset($parsedUrl['host'])) {
            return false;
        }

        $host = strtolower($parsedUrl['host']);

        foreach (self::ALLOWED_IFRAME_DOMAINS as $allowedDomain) {
            if ($host === $allowedDomain || str_ends_with($host, '.' . $allowedDomain)) {
                return true;
            }
        }

        return false;
    }

    private static function sanitizeAttributes(\DOMElement $element): void
    {
        $tagName = strtolower($element->nodeName);
        $allowedAttrs = self::ALLOWED_ATTRIBUTES[$tagName] ?? [];

        $attrs = [];
        foreach ($element->attributes as $attr) {
            $attrs[] = $attr->nodeName;
        }

        foreach ($attrs as $attrName) {
            if (!in_array($attrName, $allowedAttrs, true)) {
                $element->removeAttribute($attrName);
            }
        }

        if ($element->hasAttribute('href')) {
            $href = $element->getAttribute('href');
            if (preg_match('/^(javascript|data):/i', $href)) {
                $element->removeAttribute('href');
            }
        }

        if ($element->hasAttribute('src')) {
            $src = $element->getAttribute('src');
            if ($tagName === 'img') {
                if (!preg_match('/^(https?:|data:image\/)/i', $src)) {
                    $element->removeAttribute('src');
                }
            }
        }

        if ($element->hasAttribute('style')) {
            $style = $element->getAttribute('style');
            $style = self::sanitizeStyle($style);
            if (empty($style)) {
                $element->removeAttribute('style');
            } else {
                $element->setAttribute('style', $style);
            }
        }
    }

    private static function sanitizeStyle(string $style): string
    {
        $dangerous = [
            'expression',
            'behavior',
            'binding',
            '-moz-binding',
            'javascript:',
            'vbscript:',
            'import',
            '@import'
        ];

        $styleLower = strtolower($style);
        foreach ($dangerous as $danger) {
            if (str_contains($styleLower, $danger)) {
                return '';
            }
        }

        return $style;
    }

    public static function sanitizeText(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
