<?php

/*
 * test.php
 *
 * @(#) $Id: test.php,v 1.6 2008/03/24 21:12:13 mlemos Exp $
 *
 */

	$__tests=array(
		'singleclienterror'=>array(
			'script'=>'../test_form.php',
			'generatedfile'=>'generated/test_form.php.html',
			'expectedfile'=>'expect/test_form.php.html',
			'options'=>array(
				'ShowAllErrors'=>0,
				'ErrorMessagePrefix'=>''
			),
			'clear'=>array(
				'doit'
			)
		),
		'allclienterrors'=>array(
			'script'=>'../test_form.php',
			'generatedfile'=>'generated/all_client_errors_test_form.php.html',
			'expectedfile'=>'expect/all_client_errors_test_form.php.html',
			'options'=>array(
				'ShowAllErrors'=>1,
				'ErrorMessagePrefix'=>''
			),
			'clear'=>array(
				'doit'
			)
		),
		'singleservererror'=>array(
			'script'=>'../test_form.php',
			'generatedfile'=>'generated/server_test_form.php.html',
			'expectedfile'=>'expect/server_test_form.php.html',
			'options'=>array(
				'ShowAllErrors'=>0,
				'ErrorMessagePrefix'=>''
			),
			'post'=>array(
				'doit'=>'1'
			)
		),
		'allservererrors'=>array(
			'script'=>'../test_form.php',
			'generatedfile'=>'generated/all_server_errors_test_form.php.html',
			'expectedfile'=>'expect/all_server_errors_test_form.php.html',
			'options'=>array(
				'ShowAllErrors'=>1,
				'ErrorMessagePrefix'=>''
			),
			'post'=>array(
				'doit'=>'1'
			)
		),
		'singleclienterrordate'=>array(
			'script'=>'../test_date_input.php',
			'generatedfile'=>'generated/test_date_input.php.html',
			'expectedfile'=>'expect/test_date_input.php.html',
			'options'=>array(
				'ShowAllErrors'=>0,
				'today'=>'2000-01-01',
				'start_date'=>'2000-01-02',
				'end_date'=>'2000-01-08'
			),
			'clear'=>array(
				'doit'
			)
		),
		'allclienterrorsdate'=>array(
			'script'=>'../test_date_input.php',
			'generatedfile'=>'generated/all_client_errors_test_date_input.php.html',
			'expectedfile'=>'expect/all_client_errors_test_date_input.php.html',
			'options'=>array(
				'ShowAllErrors'=>1,
				'today'=>'2000-01-01',
				'start_date'=>'2000-01-02',
				'end_date'=>'2000-01-08'
			),
			'clear'=>array(
				'doit'
			)
		),
		'singleclienterrorage'=>array(
			'script'=>'../test_age_date_input.php',
			'generatedfile'=>'generated/test_age_date_input.php.html',
			'expectedfile'=>'expect/test_age_date_input.php.html',
			'options'=>array(
				'ShowAllErrors'=>0,
				'today'=>'2000-01-01',
				'start_date'=>'2000-01-02',
				'end_date'=>'2000-01-08'
			),
			'clear'=>array(
				'doit'
			)
		),
		'allclienterrorsage'=>array(
			'script'=>'../test_date_input.php',
			'generatedfile'=>'generated/all_client_errors_test_age_date_input.php.html',
			'expectedfile'=>'expect/all_client_errors_test_age_date_input.php.html',
			'options'=>array(
				'ShowAllErrors'=>1,
				'today'=>'2000-01-01',
				'start_date'=>'2000-01-02',
				'end_date'=>'2000-01-08'
			),
			'clear'=>array(
				'doit'
			)
		),
		'singleclienterrorcustomvalidation'=>array(
			'script'=>'../test_custom_validation.php',
			'generatedfile'=>'generated/test_custom_validation.php.html',
			'expectedfile'=>'expect/test_custom_validation.php.html',
			'options'=>array(
				'ShowAllErrors'=>0,
			),
			'clear'=>array(
				'doit'
			)
		),
		'allclienterrorscustomvalidation'=>array(
			'script'=>'../test_custom_validation.php',
			'generatedfile'=>'generated/all_client_errors_test_custom_validation.php.html',
			'expectedfile'=>'expect/all_client_errors_test_custom_validation.php.html',
			'options'=>array(
				'ShowAllErrors'=>1,
			),
			'clear'=>array(
				'doit'
			)
		),
		'javascriptstringescaping'=>array(
			'script'=>'../test_javascript_string_escaping.php',
			'generatedfile'=>'generated/test_javascript_string_escaping.php.txt',
			'expectedfile'=>'expect/test_javascript_string_escaping.php.txt',
		),
	);

	define('__TEST',1);
	for($__different=$__test=$__checked=0, Reset($__tests); $__test<count($__tests); Next($__tests), $__test++)
	{
		$__name=Key($__tests);
		$__script=$__tests[$__name]['script'];
		if(!file_exists($__script))
		{
			echo "\n".'Test script '.$__script.' does not exist.'."\n".str_repeat('_',80)."\n";
			continue;
		}
		echo 'Test "'.$__name.'": ... ';
		flush();
		if(IsSet($__tests[$__name]['options']))
			$__test_options=$__tests[$__name]['options'];
		else
			$__test_options=array();
		if(IsSet($__tests[$__name]['clear']))
		{
			for($__p=0; $__p<count($__tests[$__name]['clear']); $__p++)
			{
				$__k=$__tests[$__name]['clear'][$__p];
				Unset($_POST[$__k]);
				Unset($HTTP_POST_VARS[$__k]);
				Unset($GLOBALS[$__k]);
				Unset($$__k);
			}
		}
		if(IsSet($__tests[$__name]['post']))
		{
			$_POST=$HTTP_POST_VARS=$__tests[$__name]['post'];
			$_GET=$HTTP_GET_VARS=array();
			$_SERVER['REQUEST_METHOD']='POST';
		}
		else
		{
			$_POST=$HTTP_POST_VARS=$_GET=$HTTP_GET_VARS=array();
			$_SERVER['REQUEST_METHOD']='GET';
		}
		ob_start();
		require($__script);
		$output=ob_get_contents();
		ob_end_clean();
		$generated=$__tests[$__name]['generatedfile'];
		if(!($file = fopen($generated, 'wb')))
			die('Could not create the generated output file '.$generated."\n");
		if(!fputs($file, $output)
		|| !fclose($file))
			die('Could not save the generated output to the file '.$generated."\n");
		$expected=$__tests[$__name]['expectedfile'];
		if(!file_exists($expected))
		{
			echo "\n".'Expected output file '.$expected.' does not exist.'."\n".str_repeat('_',80)."\n";
			continue;
		}
		$diff=array();
		exec('diff '.$expected.' '.$generated, $diff);
		if(count($diff))
		{
			echo "FAILED\n".'Output of script '.$__script.' is different from the expected file '.$expected." .\n".str_repeat('_',80)."\n";
			for($line=0; $line<count($diff); $line++)
				echo $diff[$line]."\n";
			echo str_repeat('_',80)."\n";
			flush();
			$__different++;
		}
		else
			echo "OK\n";
		$__checked++;
	}
	echo $__checked.' test '.($__checked==1 ? 'was' : 'were').' performed, '.($__checked!=$__test ? (($__test-$__checked==1) ? ' 1 test was skipped, ' : ($__test-$__checked).' tests were skipped, ') : '').($__different ? $__different.' failed' : 'none has failed').'.'."\n";

?>