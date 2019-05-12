<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\Environment;

class TextExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('truncate', [$this, 'twig_truncate_filter'], array('needs_environment' => true)),
            new TwigFilter('wordwrap', [$this, 'twig_wordwrap_filter'], array('needs_environment' => true)),
        );
    }

    function twig_truncate_filter(Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
                    return $value;
                }
                $length = $breakpoint;
            }
            return rtrim(mb_substr($value, 0, $length, $env->getCharset())).$separator;
        }
        return $value;
    }
    function twig_wordwrap_filter(Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        $sentences = array();
        $previous = mb_regex_encoding();
        mb_regex_encoding($env->getCharset());
        $pieces = mb_split($separator, $value);
        mb_regex_encoding($previous);
        foreach ($pieces as $piece) {
            while (!$preserve && mb_strlen($piece, $env->getCharset()) > $length) {
                $sentences[] = mb_substr($piece, 0, $length, $env->getCharset());
                $piece = mb_substr($piece, $length, 2048, $env->getCharset());
            }
            $sentences[] = $piece;
        }
        return implode($separator, $sentences);
    }
}