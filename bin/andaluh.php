<?php set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DIRECTORY_SEPARATOR . 'libpy2php');
require_once('libpy2php.php');
/* -*- coding: utf-8 -*-*/
/* vim: ts=4*/
/*##*/
/**/
/* Copyright (c) 2018-2020 Andalugeeks*/
/* Authors:*/
/* - J. FÃ©lix OntaÃ±Ã³n <felixonta@gmail.com>*/
/* - Sergio Soto <scots4ever@gmail.com>*/
require_once( 'os_path.php');
function is_valid_file($parser,$arg) {
    if (!(os_path::exists($arg))) {
        $parser->error(sprintf('The file %s does not exist!', $arg));
    }
    else {
        return $arg;
    }
}
if (($__name__ == '__main__')) {
    require_once( 'sys.php');
    require_once( 'argparse.php');
    $parser = py2php_kwargs_method_call($argparse, null, 'ArgumentParser', [], ["description" => 'Transliterate espaÃ±ol (spanish) spelling to AndalÃ»h EPA.']);
    py2php_kwargs_method_call($parser, null, 'add_argument', ['text'], ["type" => $str,"help" => 'Text to transliterate. Enclosed in quotes for multiple words.',"nargs" => '?',"default" => '']);
    py2php_kwargs_method_call($parser, null, 'add_argument', ['-e'], ["type" => $str,"choices" => [s, z, h],"help" => 'Enforce seseo, zezeo or heheo instead of cedilla (standard).']);
    py2php_kwargs_method_call($parser, null, 'add_argument', ['-j'], ["help" => 'Keep /x/ sounds as J instead of /h/',"action" => 'store_true']);
    py2php_kwargs_method_call($parser, null, 'add_argument', ['-i'], ["dest" => 'filename',"help" => 'Transliterates the plain text input file to stdout',"metavar" => 'FILE',"type" => function ($x) {return is_valid_file($parser, $x);}]);
    $args = $parser->parse_args();
    if ((strlen($sys->argv) == 1)) {
        $parser->print_help($sys->stderr);
        $sys->exit(1);
    }
    if ($args->e) {
        $vaf = $args->e;
    }
    else {
        $vaf = Ã§;
    }
    if ($args->j) {
        $vvf = j;
    }
    else {
        $vvf = h;
    }
    if ($args->filename) {
        require_once( 'io.php');
        $file_in = py2php_kwargs_method_call($io, null, 'open', [$args->filename], ["mode" => 'r',"encoding" => 'utf-8']);
        foreach( pyjslib_list($file_in->readlines()) as $line ) {
            pyjslib_printnl(py2php_kwargs_function_call('new epa', [$line], ["vaf" => $vaf,"vvf" => $vvf,"escape_links" => true]));
        }
    }
    else {
        pyjslib_printnl(py2php_kwargs_function_call('new epa', [$args->text], ["vaf" => $vaf,"vvf" => $vvf,"escape_links" => true]));
    }
    $sys->exit(0);
}


