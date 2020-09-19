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
/* Import package form parent dir https://gist.github.com/JungeAlexander/6ce0a5213f3af56d7369*/
require_once( 'os.php');
$current_dir = os::path::dirname(os::path::abspath($inspect->getfile($inspect->currentframe())));
$parent_dir = os::path::dirname($current_dir);
$sys->path->insert(0, $parent_dir);
/* Now it can be imported :)*/
require_once( 'andaluh.php');
/* Basic tests*/
function test1() {
    assert((andaluh::epa('Todo Xenomorfo dice: [haber], que el Ãxito y el Ã©xtasis asfixian, si no eres un xilÃ³fono Chungo.') == 'TÃ³ Ãenomorfo diÃ§e: [abÃªh], que el ÃÃ§Ã§ito y el Ã©ttaÃ§Ã® Ã¢ffÃ®Ã§Ã§ian, Ã§i no erÃª un Ã§ilÃ³fono Xungo.'));
    assert((andaluh::epa('Lleva un Guijarrito el ABuelo, Â¡QuÃ© Bueno! Â¡para la VERGÃENZA!') == 'Yeba un Giharrito el AGuelo, Â¡QuÃ© Gueno! Â¡pa la BERGUENÃA!'));
    assert((andaluh::epa('VALLA valla, si vas toda de ENVIDIA') == 'BAYA baya, Ã§i bÃ¢ toa de EMBIDIA'));
    assert((andaluh::epa('Alrededor de la Alpaca habÃ­a un ALfabeto ALTIVO de valkirias malnacidas') == 'ArrededÃ´h de la Arpaca abÃ­a un ARfabeto ARTIBO de barkiriÃ¢ mÃ¢nnaÃ§idÃ¢'));
    assert((andaluh::epa('En la Zaragoza y el JapÃ³n asexual se SabÃ­a SÃriamente sILBAR con el COxis') == 'En la ÃaragoÃ§a y er HapÃ³n aÃ§ÃªÃ§Ã§uÃ¢h Ã§e ÃabÃ­a ÃÃriamente Ã§IRBÃH con er CÃÃ§Ã§Ã®'));
    assert((andaluh::epa('Transportandonos a la connotaciÃ³n perspicaz del abstracto solsticio de Alaska, el aislante plÃ¡stico adsorvente asfixiÃ³ al aMnÃ©sico pseudoescritor granadino de constituciones, para ConMemorar broncas adscritas') == 'TrÃ¢pportandonÃ´ a la cÃ´nnotaÃ§iÃ³n perppicÃ¢h del Ã¢ttrÃ¢tto Ã§orttiÃ§io de AlÃ¢kka, el aÃ®l-lante plÃ¡ttico Ã¢Ã§Ã§orbente Ã¢ffÃ®Ã§Ã§iÃ³ al Ã¢nnÃ©Ã§ico Ã§eudoÃªccritÃ´h granadino de cÃ´ttituÃ§ionÃª, pa CÃ´MMemorÃ¢h broncÃ¢ Ã¢ccritÃ¢'));
    assert((andaluh::epa('En la postmodernidad, el transcurso de los transportes y translados en postoperatorios transcienden a la postre unas postillas postpalatales apostilladas se transfieren') == 'En la pÃ´mmodÃªnnidÃ¡, er trÃ¢ccurÃ§o de lÃ´ trÃ¢pportÃª y trÃ¢l-lÃ¡Ã´ en pÃ´ttoperatoriÃ´ trÃ¢Ã§Ã§ienden a la pÃ´ttre unÃ¢ pÃ´ttiyÃ¢ pÃ´ppalatalÃª apÃ´ttiyÃ¢h Ã§e trÃ¢ffieren'));
    assert((andaluh::epa('Venid todos a correr en anorak de visÃ³n a CÃ¡diz con actitud y maldad, para escuchar el trÃ­ceps de AlbÃ©niz tocar Ã¡pud con virtud de laÃºd.') == 'BenÃ®h tÃ´h a corrÃªh en anorÃ¢h de biÃ§Ã³n a CÃ¡dÃ® con Ã¢ttitÃ»h y mardÃ¡, pa ÃªccuxÃ¢h er trÃ­Ã§Ãª de ArbÃ©nÃ® tocÃ¢h Ã¡pÃ» con birtÃ»h de laÃ»h.'));
    assert((andaluh::epa('Una comida fabada con fado, y sin descuido serÃ¡ casada y amarrada al acolchado roido.') == 'Una comida fabada con fado, y Ã§in dÃªccuido Ã§erÃ¡ caÃ§Ã¡ y amarrÃ¡ al acorxao roÃ­o.'));
    assert((andaluh::epa('Los SABuesos ChiHuaHUA comÃ­an cacaHuETes, FramBuESas y Heno, Â¡y HABLAN con hÃ¡lito de ESPANGLISH!') == 'LÃ´ ÃAGueÃ§Ã´ XiGuaGUA comÃ­an cacaGuETÃª, FramBuEÃÃ¢ y Eno, Â¡y ABLAN con Ã¡lito de ÃPPANGLÃ!'));
    assert((py2php_kwargs_function_call('andaluh::epa', ['Oye sexy @miguel, la web HTTPS://andaluh.es no sale en google.es pero si en http://google.com #porqueseÃ±or'], ["escape_links" => true]) == 'Oye Ã§ÃªÃ§Ã§y @miguel, la wÃªh HTTPS://andaluh.es no Ã§ale en google.es pero Ã§i en http://google.com #porqueseÃ±or'));
}
/* Lemario test*/
function test2($report_all=false) {
    require_once( 'csv.php');
    $file = './tests/lemario_cas_and.csv';
    $transcriptions = [];
    $transcription_errors = [];
    $stats = ['total' => 0, 'ok' => 0, 'fail' => 0];
// py2php.fixme "with" unsupported.
    if ($report_all) {
        foreach( pyjslib_list($transcription_errors) as $error ) {
            pyjslib_printnl($error[0] . ' => ' . $error[1] . ', ' . $error[2]);
        }
    }
    require_once( 'pprint.php');
    $pprint->pprint($stats);
}


