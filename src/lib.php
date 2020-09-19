<?php set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DIRECTORY_SEPARATOR . 'libpy2php');
require_once('libpy2php.php');
/* -*- coding: utf-8 -*-*/
/* vim: ts=4*/
/*##*/
/**/
/* Copyright (c) 2018-2020 Andalugeeks*/
/* Authors:*/
/* - Ksar Feui <a.moreno.losana@gmail.com>*/
/* - J. FÃ©lix OntaÃ±Ã³n <felixonta@gmail.com>*/
/* - Sergio Soto <scots4ever@gmail.com>*/
require_once( 're.php');
require_once( 'random.php');
/* Regex compilation.*/
/* Words to ignore in the translitaration in escapeLinks mode.*/
$to_ignore_re = re::compile(join('|', ['s, i.e. andaluh.es, www.andaluh.es, https://www.andaluh.es(?:[h|H][t|T][t|T][p|P][s|S]?://)?(?:www\.)?(?:[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z|A-Z]{2,6})', '(?:@\w+)', '(?:#\w+)', '(?=[MCDXLVI]{1,8})M{0,4}(?:CM|CD|D?C{0,3})(?:XC|XL|L?X{0,3})(?:IX|IV|V?I{0,3})']), re::UNICODE);
/* Auxiliary functions*/
/**
 * no tilde, replace with circumflex
 */
function get_vowel_circumflex($vowel) {
    if ($vowel && in_array($vowel, VOWELS_ALL_NOTILDE)) {
        $i = (VOWELS_ALL_NOTILDE::find($vowel) + 5);
        return array_slice(VOWELS_ALL_NOTILDE, $i, ($i + 1) - $i)[0];
    }
    else if ($vowel && in_array($vowel, VOWELS_ALL_TILDE)) {
        /*l with tilde, leave it as it is*/
        return $vowel;
    }
    else {
        /*uldn\'t call this method with a non vowel*/
        throw new new AndaluhError('Not a vowel', $vowel);
    }
}
/**
 * no tilde, replace with circumflex
 */
function get_vowel_tilde($vowel) {
    if ($vowel && in_array($vowel, VOWELS_ALL_NOTILDE)) {
        $i = VOWELS_ALL_NOTILDE::find($vowel);
        return VOWELS_ALL_TILDE[$i];
    }
    else if ($vowel && in_array($vowel, VOWELS_ALL_TILDE)) {
        /*l with tilde, leave it as it is*/
        return $vowel;
    }
    else {
        /*uldn\'t call this method with a non vowel*/
        throw new new AndaluhError('Not a vowel', $vowel);
    }
}
/* TODO: This can be improved to perform replacement in a per character basis*/
/* NOTE: It assumes replacement_word to be already lowercase*/
function keep_case($word,$replacement_word) {
    if (ctype_lower($word)) {
        return $replacement_word;
    }
    else if (ctype_upper($word)) {
        return strtoupper($replacement_word);
    }
    else if ($word->istitle()) {
        return $replacement_word->title();
    }
    else {
        return $replacement_word;
    }
}
/**
 * Supress mute /h/
 */
function h_rules($text) {
    function replace_with_case($match) {
        $word = $match->group(0);
        if (in_array(strtolower($word), pyjslib_list(H_RULES_EXCEPT::keys()))) {
            return keep_case($word, H_RULES_EXCEPT[strtolower($word)]);
        }
        else {
            function replace_with_case($match) {
                $h_char = $match->group(1);
                $next_char = $match->group(2);
                if ($next_char && ctype_upper($h_char)) {
                    return strtoupper($next_char);
                }
                else if ($next_char && ctype_lower($h_char)) {
                    return strtolower($next_char);
                }
                else {
                    return '';
                }
            }
            return py2php_kwargs_function_call('re::sub', ['(?<!c)(h)(\w?)',$replace_with_case,$word], ["flags" => re::IGNORECASE]);
        }
    }
    /*huahua => chiguagua*/
    $text = py2php_kwargs_function_call('re::sub', ['(?<!c)(h)(ua)',function ($match) {return ctype_lower($match->group(1)) ? 'g' . $match->group(2) : 'G' . $match->group(2);},$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    /*ahuete => cacagÃ»ete*/
    $text = py2php_kwargs_function_call('re::sub', ['(?<!c)(h)(u)(e)',function ($match) {return ctype_lower($match->group(1)) ? 'g' . keep_case($match->group(2), 'Ã¼') . $match->group(3) : 'G' . keep_case($match->group(2), 'Ã¼') . $match->group(3);},$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    /*eral /h/ replacements*/
    $text = py2php_kwargs_function_call('re::sub', ['(\w*?)(h)(\w*?)',$replace_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
/**
 * Replacement rules for /ks/ with EPA VAF
 */
function x_rules($text,$vaf=VAF) {
    function replace_with_case($match) {
        $x_char = $match->group(1);
        if (ctype_lower($x_char)) {
            return $vaf;
        }
        else {
            return strtoupper($vaf);
        }
    }
    function replace_intervowel_with_case($match) {
        $prev_char = $match->group(1);
        $x_char = $match->group(2);
        $next_char = $match->group(3);
        $prev_char = get_vowel_circumflex($prev_char);
        if (ctype_upper($x_char)) {
            return (($prev_char + (strtoupper($vaf) * 2)) + $next_char);
        }
        else {
            return (($prev_char + ($vaf * 2)) + $next_char);
        }
    }
    /*the text begins with /ks/*/
    /*Ã³fono roto => ÃilÃ³fono roto*/
    if (($text[0] == 'X')) {
        $text = (strtoupper($vaf) + array_slice($text, 1, null));
    }
    if (($text[0] == 'x')) {
        $text = ($vaf + array_slice($text, 1, null));
    }
    /*the /ks/ sound is between vowels*/
    /*la => AÃ§Ã§ila | Ãxito => ÃÃ§Ã§ito | Sexy => ÃeÃ§Ã§y*/
    $text = py2php_kwargs_function_call('re::sub', ['(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(x)(a|e|i|o|u|y|Ã¡|Ã©|Ã­|Ã³|Ãº)',$replace_intervowel_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    /*ry word starting with /ks/*/
    $text = py2php_kwargs_function_call('re::sub', ['(x)',$replace_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
/**
 * Replacement rules for // (voiceless postalveolar fricative)
 */
function ch_rules($text) {
    $text = py2php_kwargs_function_call('re::sub', ['(c)(h)',function ($match) {return ctype_lower($match->group(1)) ? 'x' : 'X';},$text], ["flags" => re::IGNORECASE]);
    return $text;
}
/**
 * Replacing /x/ (voiceless postalveolar fricative) with /h/
 */
function gj_rules($text,$vvf=VVF) {
    function replace_h_with_case($match) {
        $word = $match->group(0);
        if (in_array(strtolower($word), pyjslib_list(GJ_RULES_EXCEPT::keys()))) {
            return keep_case($word, GJ_RULES_EXCEPT[strtolower($word)]);
        }
        else {
            /*is an AWFUL way of implementing replacement rules with*/
            /* To be fixed.*/
            $word = py2php_kwargs_function_call('re::sub', ['(g|j)(e|i|Ã©|Ã­)',function ($match) {return ctype_lower($match->group(1)) ? ($vvf + $match->group(2)) : (strtoupper($vvf) + $match->group(2));},$word], ["flags" => re::IGNORECASE | re::UNICODE]);
            $word = py2php_kwargs_function_call('re::sub', ['(j)(a|o|u|Ã¡|Ã³|Ãº)',function ($match) {return ctype_lower($match->group(1)) ? ($vvf + $match->group(2)) : (strtoupper($vvf) + $match->group(2));},$word], ["flags" => re::IGNORECASE | re::UNICODE]);
            return $word;
        }
    }
    function replace_g_with_case($match) {
        $s = $match->group('s');
        $a = $match->group('a');
        $b = $match->group('b');
        $ue = $match->group('ue');
        $const = $match->group('const');
        return (((($s + $a) + keep_case($b, 'g')) + $ue) + $const);
    }
    $text = py2php_kwargs_function_call('re::sub', ['(\w*?)(g|j)(e|i|Ã©|Ã­)(\w*?)',$replace_h_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    $text = py2php_kwargs_function_call('re::sub', ['(\w*?)(j)(a|o|u|Ã¡|Ã³|Ãº)(\w*?)',$replace_h_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    /*,GUI replacement*/
    $text = re::sub('(gu|gU)(e|i|Ã©|Ã­|E|I|Ã|Ã)', 'g', $text);
    $text = re::sub('(Gu|GU)(e|i|Ã©|Ã­|E|I|Ã|Ã)', 'G', $text);
    /*E,GÃI replacement*/
    $text = re::sub('(g|G)(Ã¼)(e|i|Ã©|Ã­|E|I|Ã|Ã)', 'u', $text);
    $text = re::sub('(g|G)(Ã)(e|i|Ã©|Ã­|E|I|Ã|Ã)', 'U', $text);
    /*n / abuel / sabues => guen / aguel / sagues*/
    /*O: I\'ve the gut feeling the following two regex can be merged into*/
    /*.*/
    $text = py2php_kwargs_function_call('re::sub', ['(b)(uen)',function ($match) {return ctype_lower($match->group(1)) ? 'g' . $match->group(2) : 'G' . $match->group(2);},$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    $text = py2php_kwargs_function_call('re::sub', ['(?P<s>s?)(?P<a>a?)(?<!m)(?P<b>b)(?P<ue>ue)(?P<const>l|s)',$replace_g_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
/**
 * Replacing all /v/ (Voiced labiodental fricative) with /b/
 */
function v_rules($text) {
    function replace_with_case($match) {
        $word = $match->group(0);
        if (in_array(strtolower($word), pyjslib_list(V_RULES_EXCEPT::keys()))) {
            return keep_case($word, V_RULES_EXCEPT[strtolower($word)]);
        }
        else {
            /* MB (i.e.: envidia -> embidia)*/
            $word = py2php_kwargs_function_call('re::sub', ['nv',function ($match) {return keep_case($match->group(0), 'mb');},$word], ["flags" => re::IGNORECASE | re::UNICODE]);
            $word = re::sub('v', 'b', $word);
            $word = re::sub('V', 'B', $word);
            return $word;
        }
    }
    $text = py2php_kwargs_function_call('re::sub', ['(\w*?)(v)(\w*?)',$replace_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
/**
 * Replace ll digraph.
 *
 * Replacing // (digraph ll) with Greek Y for // sound (voiced
 * postalveolar affricate)
 */
function ll_rules($text) {
    function replace_with_case($match) {
        $word = $match->group(0);
        if (in_array(strtolower($word), pyjslib_list(LL_RULES_EXCEPT::keys()))) {
            return keep_case($word, LL_RULES_EXCEPT[strtolower($word)]);
        }
        else {
            return py2php_kwargs_function_call('re::sub', ['(l)(l)',function ($match) {return ctype_upper($match->group(1)) ? 'Y' : 'y';},$word], ["flags" => re::IGNORECASE]);
        }
    }
    $text = py2php_kwargs_function_call('re::sub', ['(\w*?)(l)(l)(\w*?)',$replace_with_case,$text], ["flags" => re::IGNORECASE]);
    return $text;
}
/**
 * Rotating /l/ with /r/
 */
function l_rules($text) {
    $text = py2php_kwargs_function_call('re::sub', ['(l)(b|c|Ã§|Ã|g|s|d|f|g|h|k|m|p|q|r|t|x|z)',function ($match) {return ctype_lower($match->group(1)) ? 'r' . $match->group(2) : 'R' . $match->group(2);},$text], ["flags" => re::IGNORECASE]);
    return $text;
}
/**
 * Drops /p/ for pseudo- or psico- prefixes
 */
function psico_pseudo_rules($text) {
    function replace_psicpseud_with_case($match) {
        $ps_syllable = $match->group(1);
        if (($ps_syllable[0] == 'p')) {
            return array_slice($ps_syllable, 1, null);
        }
        else {
            return (strtoupper($ps_syllable[1]) + array_slice($ps_syllable, 2, null));
        }
    }
    $text = py2php_kwargs_function_call('re::sub', ['(psic|pseud)',$replace_psicpseud_with_case,$text], ["flags" => re::IGNORECASE]);
    return $text;
}
function vaf_rules($text,$vaf=VAF) {
    function replace_with_case($match) {
        $l_char = $match->group(1);
        $next_char = $match->group(2);
        if (ctype_lower($l_char)) {
            return ($vaf + $next_char);
        }
        else {
            return (strtoupper($vaf) + $next_char);
        }
    }
    $text = py2php_kwargs_function_call('re::sub', ['(z|s)(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº|Ã¢|Ãª|Ã®|Ã´|Ã»)',$replace_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    $text = py2php_kwargs_function_call('re::sub', ['(c)(e|i|Ã©|Ã­|Ãª|Ã®)',$replace_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
/**
 * Replacement of consecutive consonant with EPA VAF
 */
function digraph_rules($text) {
    function replace_lstrst_with_case($match) {
        $vowel_char = $match->group(1);
        $lr_char = $match->group(2);
        $t_char = $match->group(4);
        if (($lr_char == 'l')) {
            ($lr_char == 'r');
        }
        else if (($lr_char == 'L')) {
            ($lr_char == 'R');
        }
        else {
        }
        return (($vowel_char + $lr_char) + ($t_char * 2));
    }
    function replace_bdnr_s_with_case($match) {
        $vowel_char = $match->group(1);
        $cons_char = $match->group(2);
        $s_char = $match->group(3);
        $digraph_char = $match->group(4);
        if (((strtolower($cons_char) + strtolower($s_char)) == 'rs')) {
            return (($vowel_char + $cons_char) + ($digraph_char * 2));
        }
        else {
            return (get_vowel_circumflex($vowel_char) + ($digraph_char * 2));
        }
    }
    function replace_transpost_with_case($match) {
        $init_char = $match->group(1);
        $vowel_char = $match->group(2);
        $cons_char = $match->group(4);
        if ((strtolower($cons_char) == 'l')) {
            return (($init_char + get_vowel_circumflex($vowel_char)) + $cons_char) . '-' . $cons_char;
        }
        else {
            return (($init_char + get_vowel_circumflex($vowel_char)) + ($cons_char * 2));
        }
    }
    function replace_l_with_case($match) {
        $vowel_char = $match->group(1);
        $digraph_char = $match->group(3);
        return (get_vowel_circumflex($vowel_char) + $digraph_char) . '-' . $digraph_char;
    }
    function replace_digraph_with_case($match) {
        $vowel_char = $match->group(1);
        list($to_drop_char, $digraph_char) = $match->group(2);
        return (get_vowel_circumflex($vowel_char) + ($digraph_char * 2));
    }
    /*ersticial / solsticio / supersticiÃ³n / cÃ¡rstico => interttiÃ§iÃ¢h /*/
    /*rttiÃ§io / Ã§uperttiÃ§iÃ³n / cÃ¡rttico*/
    $text = py2php_kwargs_function_call('re::sub', ['(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(l|r)(s)(t)',$replace_lstrst_with_case,$text], ["flags" => re::IGNORECASE]);
    /*otransporte => aerotrÃ¢pporte | translado => trÃ¢l-lado | transcendente*/
    /*trÃ¢Ã§Ã§endente | postoperatorio => pÃ´ttoperatorio | postpalatal =>*/
    /*ppalatal*/
    $text = py2php_kwargs_function_call('re::sub', ['(tr|p)(a|o)(ns|st)(b|c|Ã§|Ã|d|f|g|h|j|k|l|m|n|p|q|s|t|v|w|x|y|z)',$replace_transpost_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    /*tracto => Ã¢ttrÃ¢tto | adscrito => Ã¢ccrito | perspectiva => pÃªrppÃªttiba*/
    $text = py2php_kwargs_function_call('re::sub', ['(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(b|d|n|r)(s)(b|c|Ã§|Ã|d|f|g|h|j|k|l|m|n|p|q|s|t|v|w|x|y|z)',$replace_bdnr_s_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    /*Ã¡ntico => Ã¢l-lÃ¡ntico | orla => Ã´l-la | adlÃ¡tere => Ã¢l-lÃ¡tere | tesla*/
    /*tÃªl-la ...*/
    $text = py2php_kwargs_function_call('re::sub', ['(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(d|j|r|s|t|x|z)(l)',$replace_l_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    /*eral digraph rules.*/
    $text = py2php_kwargs_function_call('re::sub', ['(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(' . join('|', DIGRAPHS) . ')',$replace_digraph_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
function word_ending_rules($text) {
    function replace_d_end_with_case($match) {
        $unstressed_rules = ['a' => 'Ã¢', 'A' => 'Ã', 'Ã¡' => 'Ã¢', 'Ã' => 'Ã', 'e' => 'Ãª', 'E' => 'Ã', 'Ã©' => 'Ãª', 'Ã' => 'Ã', 'i' => 'Ã®', 'I' => 'Ã', 'Ã­' => 'Ã®', 'Ã' => 'Ã', 'o' => 'Ã´', 'O' => 'Ã', 'Ã³' => 'Ã´', 'Ã' => 'Ã', 'u' => 'Ã»', 'U' => 'Ã', 'Ãº' => 'Ã»', 'Ã' => 'Ã'];
        $stressed_rules = ['a' => 'Ã¡', 'A' => 'Ã', 'Ã¡' => 'Ã¡', 'Ã' => 'Ã', 'e' => 'Ã©', 'E' => 'Ã', 'Ã©' => 'Ã©', 'Ã' => 'Ã', 'i' => 'Ã®', 'I' => 'Ã', 'Ã­' => 'Ã®', 'Ã' => 'Ã', 'o' => 'Ã´', 'O' => 'Ã', 'Ã³' => 'Ã´', 'Ã' => 'Ã', 'u' => 'Ã»', 'U' => 'Ã', 'Ãº' => 'Ã»', 'Ã' => 'Ã'];
        $word = $match->group(0);
        $prefix = $match->group(1);
        $suffix_vowel = $match->group(2);
        $suffix_const = $match->group(3);
        if (in_array(strtolower($word), pyjslib_list(WORDEND_D_RULES_EXCEPT::keys()))) {
            return keep_case($word, WORDEND_D_RULES_EXCEPT[strtolower($word)]);
        }
        if (any(pyjslib_genexpr( function($__vars) { extract($__vars); foreach( pyjslib_list(['Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã', 'Ã', 'Ã', 'Ã', 'Ã']) as $s ) {yield in_array($s, $prefix);}}, get_defined_vars() ))) {
            return ($prefix + $unstressed_rules[$suffix_vowel]);
        }
        else {
            if (in_array($suffix_vowel, ['a', 'e', 'A', 'E', 'Ã¡', 'Ã©', 'Ã', 'Ã'])) {
                return ($prefix + $stressed_rules[$suffix_vowel]);
            }
            else {
                if (ctype_upper($suffix_const)) {
                    return ($prefix + $stressed_rules[$suffix_vowel]) . 'H';
                }
                else {
                    return ($prefix + $stressed_rules[$suffix_vowel]) . 'h';
                }
            }
        }
    }
    function replace_s_end_with_case($match) {
        $repl_rules = ['a' => 'Ã¢', 'A' => 'Ã', 'Ã¡' => 'Ã¢', 'Ã' => 'Ã', 'e' => 'Ãª', 'E' => 'Ã', 'Ã©' => 'Ãª', 'Ã' => 'Ã', 'i' => 'Ã®', 'I' => 'Ã', 'Ã­' => 'Ã®', 'Ã' => 'Ã', 'o' => 'Ã´', 'O' => 'Ã', 'Ã³' => 'Ã´', 'Ã' => 'Ã', 'u' => 'Ã»', 'U' => 'Ã', 'Ãº' => 'Ã»', 'Ã' => 'Ã'];
        $prefix = $match->group(1);
        $suffix_vowel = $match->group(2);
        $suffix_const = $match->group(3);
        $word = (($prefix + $suffix_vowel) + $suffix_const);
        if (in_array(strtolower($word), pyjslib_list(WORDEND_S_RULES_EXCEPT::keys()))) {
            return keep_case($word, WORDEND_S_RULES_EXCEPT[strtolower($word)]);
        }
        else if (in_array($suffix_vowel, ['Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã', 'Ã', 'Ã', 'Ã', 'Ã'])) {
            if (ctype_upper($suffix_const)) {
                return ($prefix + $repl_rules[$suffix_vowel]) . 'H';
            }
            else {
                return ($prefix + $repl_rules[$suffix_vowel]) . 'h';
            }
        }
        else {
            return ($prefix + $repl_rules[$suffix_vowel]);
        }
    }
    function replace_const_end_with_case($match) {
        $repl_rules = ['a' => 'Ã¢', 'A' => 'Ã', 'Ã¡' => 'Ã¢', 'Ã' => 'Ã', 'e' => 'Ãª', 'E' => 'Ã', 'Ã©' => 'Ãª', 'Ã' => 'Ã', 'i' => 'Ã®', 'I' => 'Ã', 'Ã­' => 'Ã®', 'Ã' => 'Ã', 'o' => 'Ã´', 'O' => 'Ã', 'Ã³' => 'Ã´', 'Ã' => 'Ã', 'u' => 'Ã»', 'U' => 'Ã', 'Ãº' => 'Ã»', 'Ã' => 'Ã'];
        $word = $match->group(0);
        $prefix = $match->group(1);
        $suffix_vowel = $match->group(2);
        $suffix_const = $match->group(3);
        $else_cond = any(pyjslib_genexpr( function($__vars) { extract($__vars); foreach( pyjslib_list(['Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã', 'Ã', 'Ã', 'Ã', 'Ã']) as $s ) {yield in_array($s, $prefix);}}, get_defined_vars() ));
        if (in_array(strtolower($word), pyjslib_list(WORDEND_CONST_RULES_EXCEPT::keys()))) {
            return keep_case($word, WORDEND_CONST_RULES_EXCEPT[strtolower($word)]);
        }
        else if ($else_cond) {
            return ($prefix + $repl_rules[$suffix_vowel]);
        }
        else {
            if (ctype_upper($suffix_const)) {
                return ($prefix + $repl_rules[$suffix_vowel]) . 'H';
            }
            else {
                return ($prefix + $repl_rules[$suffix_vowel]) . 'h';
            }
        }
    }
    function replace_eps_end_with_case($match) {
        $prefix = $match->group(1);
        $suffix_vowel = $match->group(2);
        $suffix_const = $match->group(3);
        if (any(pyjslib_genexpr( function($__vars) { extract($__vars); foreach( pyjslib_list(['Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã', 'Ã', 'Ã', 'Ã', 'Ã']) as $s ) {yield in_array($s, $prefix);}}, get_defined_vars() ))) {
            if (ctype_upper($suffix_vowel)) {
                return $prefix . 'Ã';
            }
            else {
                return $prefix . 'Ãª';
            }
        }
        else {
            /* is. There shouldn\'t be any word with -eps ending*/
            /*cent.*/
            return (($prefix + $suffix_vowel) + $suffix_const);
        }
    }
    function replace_intervowel_d_end_with_case($match) {
        $prefix = $match->group(1);
        $suffix_vowel_a = $match->group(2);
        $suffix_d_char = $match->group(3);
        $suffix_vowel_b = $match->group(4);
        $ending_s = $match->group('s');
        $suffix = ((($suffix_vowel_a + $suffix_d_char) + $suffix_vowel_b) + $ending_s);
        $word = ($prefix + $suffix);
        $else_cond = any(pyjslib_genexpr( function($__vars) { extract($__vars); foreach( pyjslib_list(['Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã', 'Ã', 'Ã', 'Ã', 'Ã']) as $s ) {yield in_array($s, $prefix);}}, get_defined_vars() ));
        if (in_array(strtolower($word), pyjslib_list(WORDEND_D_INTERVOWEL_RULES_EXCEPT::keys()))) {
            return keep_case($word, WORDEND_D_INTERVOWEL_RULES_EXCEPT[strtolower($word)]);
        }
        else if (!($else_cond)) {
            /* -ada rules*/
            if ((strtolower($suffix) == 'ada')) {
                if (ctype_upper($suffix_vowel_b)) {
                    return $prefix . 'Ã';
                }
                else {
                    return $prefix . 'Ã¡';
                }
            }
            if ((strtolower($suffix) == 'adas')) {
                /*a rules*/
                return ($prefix + keep_case(array_slice($suffix, null, 2), get_vowel_circumflex($suffix[0]) . 'h'));
            }
            else if ((strtolower($suffix) == 'ado')) {
                /*o rules*/
                return (($prefix + $suffix_vowel_a) + $suffix_vowel_b);
            }
            else if (in_array(strtolower($suffix), ['ados', 'idos', 'Ã­dos'])) {
                /*os -idos -Ã­dos rules*/
                return (($prefix + get_vowel_tilde($suffix_vowel_a)) + get_vowel_circumflex($suffix_vowel_b));
            }
            else if (in_array(strtolower($suffix), ['ido', 'Ã­do'])) {
                /*o -Ã­do rules*/
                if (ctype_upper($suffix_vowel_a)) {
                    return $prefix . 'Ã' . $suffix_vowel_b;
                }
                else {
                    return $prefix . 'Ã­' . $suffix_vowel_b;
                }
            }
            else {
                return $word;
            }
        }
        else {
            return $word;
        }
    }
    /*ervowel /d/ replacements*/
    $text = py2php_kwargs_function_call('re::sub', ['(\w*?)(a|i|Ã­|Ã)(d)(o|a)(?P<s>s?)',$replace_intervowel_d_end_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    $text = py2php_kwargs_function_call('re::sub', ['(\w+?)(e)(ps)',$replace_eps_end_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    $text = py2php_kwargs_function_call('re::sub', ['(\w+?)(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(d)',$replace_d_end_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    $text = py2php_kwargs_function_call('re::sub', ['(\w+?)(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(s)',$replace_s_end_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    $text = py2php_kwargs_function_call('re::sub', ['(\w+?)(a|e|i|o|u|Ã¡|Ã©|Ã­|Ã³|Ãº)(b|c|f|g|j|k|l|p|r|t|x|z)',$replace_const_end_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
/**
 * Set of exceptions to the replacement algorithm
 */
function exception_rules($text) {
    function replace_with_case($match) {
        $word = $match->group(1);
        $replacement_word = ENDING_RULES_EXCEPTION[strtolower($word)];
        return keep_case($word, $replacement_word);
    }
    $text = py2php_kwargs_function_call('re::sub', ['(' . join('|', pyjslib_list(ENDING_RULES_EXCEPTION::keys())) . ')',$replace_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}
/**
 * Contractions and other word interaction rules
 */
function word_interaction_rules($text) {
    function replace_with_case($match) {
        $prefix = $match->group(1);
        $l_char = $match->group(2);
        $whitespace_char = $match->group(3);
        $next_word_char = $match->group(4);
        $r_char = keep_case($l_char, 'r');
        return ((($prefix + $r_char) + $whitespace_char) + $next_word_char);
    }
    /*ating word ending /l/ with /r/ if first next word char is non-r*/
    /*sonant*/
    $text = py2php_kwargs_function_call('re::sub', ['(\w*?)(l)(\s)(b|c|Ã§|d|f|g|h|j|k|l|m|n|Ã±|p|q|s|t|v|w|x|y|z)',$replace_with_case,$text], ["flags" => re::IGNORECASE | re::UNICODE]);
    return $text;
}


/* Main function*/

function epa($text,$vaf=VAF,$vvf=VVF,$escape_links=false,$debug=false) {
    $rules = [$h_rules, $x_rules, $ch_rules, $gj_rules, $v_rules, $ll_rules, $l_rules, $psico_pseudo_rules, $vaf_rules, $word_ending_rules, $digraph_rules, $exception_rules, $word_interaction_rules];
    if (!(isinstance($text, $str))) {
        $text = pyjslib_str($text, 'utf-8');
    }
    /*not start transcription if the input is empty*/
    if (!($text)) {
        return $text;
    }
    function transliterate($text) {
        foreach( pyjslib_list($rules) as $rule ) {
            if (in_array($rule, [$x_rules, $vaf_rules])) {
                $text = $rule($text, $vaf);
            }
            else if (($rule == $gj_rules)) {
                $text = $rule($text, $vvf);
            }
            else {
                $text = $rule($text);
            }
            if ($debug) {
                pyjslib_printnl($rule->__name__ . ' => ' . $text);
            }
        }
        return $text;
    }
    if ($escape_links) {
        /*n the message not to transliterate*/
        $ignore = $to_ignore_re->findall($text);
        /* words in the message to transliterate*/
        $words = explode($text, $to_ignore_re);
        if (!($ignore)) {
            $tags = [];
            $text = $text;
        }
        else {
            /*ds to ignore in the transliteration with randints*/
            $tags = pyjslib_list(pyjslib_zip(pyjslib_listcomp( function($__vars) { extract($__vars); foreach( pyjslib_list($ignore) as $x ) {yield pyjslib_str(random::randint(1, 999999999));}}, get_defined_vars() ), $ignore));
            $text = join('', new reduce(function ($x,$y) {return (join('', $x) + join('', $y));}, pyjslib_list(pyjslib_zip($words, pyjslib_listcomp( function($__vars) { extract($__vars); foreach( pyjslib_list($tags) as $x ) {yield $x[0];}}, get_defined_vars() )))));
            if ((strlen($words) > strlen($ignore))) {
                $text += $words[-1];
            }
        }
        if ($debug) {
            pyjslib_printnl('escapeLinks => ' . $text);
        }
        $text_and = $transliterate($text);
        foreach( pyjslib_list($tags) as $tag ) {
            $text_and = str_replace($tag[0], $tag[1], $text_and);
        }
        if ($debug) {
            pyjslib_printnl('unEscapeLinks => ' . $text_and);
        }
        return $text_and;
    }
    else {
        return $transliterate($text);
    }
}

class AndaluhError extends Exception {
    /**
     * e base class constructor with the parameters it needs
     */
    function __construct($message,$errors) {
        parent->__construct($message);
        /* your custom code...*/
        $this->errors = $errors;
    }
}
