<?php

/**
 * Переводит все строки во всех файлах плагина на английский язык...
 * Работа этого модуля необратима, поэтому нужно сделать дамп всех файлов плагина...
 */

// DEMOC_PATH - пусть до всех файлов плагина
// DEM_LANG_DIRNAME - название папки с файлами перевода

function get_files_list( $folder, & $all_files ){
	$folder = rtrim( $folder, '/');
	$fp = opendir($folder);

	while( $cv_file = readdir($fp) ){
		$_file = "$folder/$cv_file";

		if( is_file($_file) ){
			if( preg_match('/\.php$/', $_file) )
				$all_files[] = $_file;
		}
		elseif( $cv_file != "." && $cv_file != ".." && is_dir($_file) ){
			if( $cv_file !== '.svn' )
				get_files_list( $_file, $all_files );
		}
	}

	closedir($fp);
}

$all_files = array();

get_files_list( DEMOC_PATH, $all_files );


// замена ----
// отпарсим английский перевод из файла
$mofile = DEMOC_PATH . DEM_LANG_DIRNAME . '/en_US.mo';
$en_US = new MO();
$en_US->import_from_file( $mofile );
$transl = $en_US->entries;



$loclines = $notransl = $doit_array = [];

foreach( $all_files as $file ){
	$filecont = file_get_contents( $file );
	$original_filecont = $filecont;

	//preg_match_all('/(?:__|_e|__dem)\((.*?)\)(?:[;,]|\s*[.:])/', $filecont, $mm );
	preg_match_all('/(?:__|_e|__dem)\( *([\'"])(.+?)(?<!\\\)\1/s', $filecont, $mm );

	if( $mm[0] ){
		foreach( $mm[2] as $key => $transl_from ){
			if( isset($transl[$transl_from]) ){
				$loclines[] = $transl_from;

				$quote = $mm[1][$key];

				// добавим экранированные слэши в перевод, чтобы не поломать PHP строку...
				$transl_to = str_replace( $quote, '\\'. $quote, $transl[$transl_from]->translations[0] );

				$transl_from = $quote . $transl_from . $quote;
				$transl_to   = $quote . $transl_to . $quote;

				$doit_array[ $transl_from ] = $transl_to;

				// добавим слэши для " или '
				$filecont = str_replace( $transl_from, $transl_to, $filecont, $count );
			}
			else
				$notransl[] = $transl_from;

		}

	}

	// заменим файл если он изменился...
	if( @ $_GET['hard_translate_files_to_eng'] === 'doit' )
		if( $original_filecont !== $filecont ) file_put_contents( $file, $filecont );
}

print_r($doit_array);
print_r($loclines);
print_r($notransl);

exit;
