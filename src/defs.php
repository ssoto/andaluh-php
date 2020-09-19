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
/* Useful for calculate the circumflex equivalents.*/
$VOWELS_ALL_NOTILDE = 'aeiouÃ¢ÃªÃ®Ã´Ã»AEIOUÃÃÃÃÃ';
$VOWELS_ALL_TILDE = 'Ã¡Ã©Ã­Ã³ÃºÃ¢ÃªÃ®Ã´Ã»ÃÃÃÃÃÃÃÃÃÃ';
/* EPA character for Voiceless alveolar fricative /s/*/
/* https://en.wikipedia.org/wiki/Voiceless_alveolar_fricative*/
$VAF = 'Ã§';
/* EPA character for Voiceless velar fricative /x/*/
/* https://en.wikipedia.org/wiki/Voiceless_velar_fricative*/
$VVF = 'h';
/* Digraphs producers. (vowel)(const)(const) that triggers the general*/
/* digraph rule*/
$DIGRAPHS = ['bb', 'bc', 'bÃ§', 'bÃ', 'bd', 'bf', 'bg', 'bh', 'bm', 'bn', 'bp', 'bq', 'bt', 'bx', 'by', 'cb', 'cc', 'cÃ§', 'cÃ', 'cd', 'cf', 'cg', 'ch', 'cm', 'cn', 'cp', 'cq', 'ct', 'cx', 'cy', 'db', 'dc', 'dÃ§', 'dÃ', 'dd', 'df', 'dg', 'dh', 'dl', 'dm', 'dn', 'dp', 'dq', 'dt', 'dx', 'dy', 'fb', 'fc', 'fÃ§', 'fÃ', 'fd', 'ff', 'fg', 'fh', 'fm', 'fn', 'fp', 'fq', 'ft', 'fx', 'fy', 'gb', 'gc', 'gÃ§', 'gÃ', 'gd', 'gf', 'gg', 'gh', 'gm', 'gn', 'gp', 'gq', 'gt', 'gx', 'gy', 'jb', 'jc', 'jÃ§', 'jÃ', 'jd', 'jf', 'jg', 'jh', 'jl', 'jm', 'jn', 'jp', 'jq', 'jr', 'jt', 'jx', 'jy', 'lb', 'lc', 'lÃ§', 'lÃ', 'ld', 'lf', 'lg', 'lh', 'll', 'lm', 'ln', 'lp', 'lq', 'lr', 'lt', 'lx', 'ly', 'mm', 'mn', 'nm', 'nn', 'pb', 'pc', 'pÃ§', 'pÃ', 'pd', 'pf', 'pg', 'ph', 'pm', 'pn', 'pp', 'pq', 'pt', 'px', 'py', 'rn', 'sb', 'sc', 'sÃ§', 'sÃ', 'sd', 'sf', 'sg', 'sh', 'sk', 'sl', 'sm', 'sn', 'sÃ±', 'sp', 'sq', 'sr', 'st', 'sx', 'sy', 'tb', 'tc', 'tÃ§', 'tÃ', 'td', 'tf', 'tg', 'th', 'tl', 'tm', 'tn', 'tp', 'tq', 'tt', 'tx', 'ty', 'xb', 'xc', 'xÃ§', 'xÃ', 'xd', 'xf', 'xg', 'xh', 'xl', 'xm', 'xn', 'xp', 'xq', 'xr', 'xt', 'xx', 'xy', 'zb', 'zc', 'zÃ§', 'zÃ', 'zd', 'zf', 'zg', 'zh', 'zl', 'zm', 'zn', 'zp', 'zq', 'zr', 'zt', 'zx', 'zy'];
$H_RULES_EXCEPT = ['haz' => 'Ã¢h', 'hez' => 'Ãªh', 'hoz' => 'Ã´h', 'oh' => 'Ã´h', 'yihad' => 'yihÃ¡', 'h' => 'h'];
$GJ_RULES_EXCEPT = ['gin' => 'yin', 'jazz' => 'yÃ¢h', 'jet' => 'yÃªh'];
$V_RULES_EXCEPT = ['vis' => 'bÃ®', 'ves' => 'bÃªh'];
$LL_RULES_EXCEPT = ['grill' => 'grÃ®h'];
$WORDEND_D_RULES_EXCEPT = ['Ã§ed' => 'Ã§Ãªh'];
$WORDEND_S_RULES_EXCEPT = ['bies' => 'biÃªh', 'bis' => 'bÃ®h', 'blues' => 'blÃ»', 'bus' => 'bÃ»h', 'dios' => 'diÃ´h', 'dos' => 'dÃ´h', 'gas' => 'gÃ¢h', 'gres' => 'grÃªh', 'gris' => 'grÃ®h', 'luis' => 'luÃ®h', 'mies' => 'miÃªh', 'mus' => 'mÃ»h', 'os' => 'Ã´', 'pis' => 'pÃ®h', 'plus' => 'plÃ»h', 'pus' => 'pÃ»h', 'ras' => 'rÃ¢h', 'res' => 'rÃªh', 'tos' => 'tÃ´h', 'tres' => 'trÃªh', 'tris' => 'trÃ®h'];
$WORDEND_CONST_RULES_EXCEPT = ['al' => 'al', 'cual' => 'cuÃ¢', 'del' => 'del', 'dÃ©l' => 'dÃ©l', 'el' => 'el', 'Ã©l' => 'Ã¨l', 'tal' => 'tal', 'bil' => 'bÃ®l', 'O: uir = huir. Maybe better to add the exceptions on h_rules?por' => 'por', 'uir' => 'huÃ®h', ', tacÃ§ic' => 'Ã§ic', 'tac' => 'tac', 'yak' => 'yak', 'stop' => 'ÃªttÃ´h', 'bip' => 'bip'];
$WORDEND_D_INTERVOWEL_RULES_EXCEPT = ['ing with -adofado' => 'fado', 'cado' => 'cado', 'nado' => 'nado', 'priado' => 'priado', 'ing with -adafabada' => 'fabada', 'fabadas' => 'fabadas', 'fada' => 'fada', 'ada' => 'ada', 'lada' => 'lada', 'rada' => 'rada', 'ing with -adasadas' => 'adas', 'radas' => 'radas', 'nadas' => 'nadas', 'ing with -idoaikido' => 'aikido', 'bÃ»Ã§Ã§ido' => 'bÃ»Ã§Ã§ido', 'Ã§ido' => 'Ã§ido', 'cuido' => 'cuido', 'cupido' => 'cupido', 'descuido' => 'descuido', 'despido' => 'despido', 'eido' => 'eido', 'embido' => 'embido', 'fido' => 'fido', 'hido' => 'hido', 'ido' => 'ido', 'infido' => 'infido', 'laido' => 'laido', 'libido' => 'libido', 'nido' => 'nido', 'nucleido' => 'nucleido', 'Ã§onido' => 'Ã§onido', 'Ã§uido' => 'Ã§uido'];
$ENDING_RULES_EXCEPTION = ['eptions to digraph rules with nmbiÃªmmandao' => 'bienmandao', 'biÃªmmeÃ§abe' => 'bienmeÃ§abe', 'buÃªmmoÃ§o' => 'buenmoÃ§o', 'Ã§iÃªmmilÃ©Ã§ima' => 'Ã§ienmilÃ©Ã§ima', 'Ã§iÃªmmilÃ©Ã§imo' => 'Ã§ienmilÃ©Ã§imo', 'Ã§iÃªmmilÃ­metro' => 'Ã§ienmilÃ­metro', 'Ã§iÃªmmiyonÃ©Ã§ima' => 'Ã§ienmiyonÃ©Ã§ima', 'Ã§iÃªmmiyonÃ©Ã§imo' => 'Ã§ienmiyonÃ©Ã§imo', 'Ã§iÃªmmirmiyonÃ©Ã§ima' => 'Ã§ienmirmiyonÃ©Ã§ima', 'Ã§iÃªmmirmiyonÃ©Ã§imo' => 'Ã§ienmirmiyonÃ©Ã§imo', 'eptions to l rulesmarrotadÃ´h' => 'mÃ¢rrotadÃ´h', 'marrotÃ¢h' => 'mÃ¢rrotÃ¢h', 'mirrayÃ¢' => 'mÃ®rrayÃ¢', 'eptions to psico pseudo rulesherÃ´Ã§Ã§iquiatrÃ­a' => 'heroÃ§iquiatrÃ­a', 'herÃ´Ã§Ã§iquiÃ¡trico' => 'heroÃ§iquiÃ¡trico', 'farmacÃ´Ã§Ã§iquiatrÃ­a' => 'farmacoÃ§iquiatrÃ­a', 'metempÃ§Ã­coÃ§Ã®' => 'metemÃ§Ã­coÃ§Ã®', 'necrÃ³Ã§ico' => 'necrÃ³Ã§Ã§ico', 'pampÃ§iquÃ®mmo' => 'pamÃ§iquÃ®mmo', 'er exceptionsantÃ®Ã§Ã§erÃ´ttÃ¡rmico' => 'antiÃ§erÃ´ttÃ¡rmico', 'eclampÃ§ia' => 'eclampÃ§ia', 'pÃ´ttoperatorio' => 'pÃ´Ã§Ã§operatorio', 'Ã§Ã¡ccrito' => 'Ã§Ã¡nccrito', 'manbÃ®h' => 'mambÃ®h', 'cÃ´mmelinÃ¡Ã§eo' => 'commelinÃ¡Ã§eo', 'dÃ®mmneÃ§ia' => 'dÃ®nneÃ§ia', 'todo' => 'tÃ³', 'todÃ´' => 'tÃ´h', 'toda' => 'toa', 'todÃ¢' => 'toÃ¢', 'er exceptions monosyllablesas' => 'Ã¢h', 'clown' => 'claun', 'crack' => 'crÃ¢h', 'down' => 'daun', 'es' => 'Ãªh', 'ex' => 'Ãªh', 'ir' => 'Ã®h', 'miss' => 'mÃ®h', 'muy' => 'mu', 'Ã´ff' => 'off', 'os' => 'Ã´', 'para' => 'pa', 'ring' => 'rin', 'rock' => 'rÃ´h', 'spray' => 'Ãªppray', 'sprint' => 'ÃªpprÃ­n', 'wau' => 'guau'];


