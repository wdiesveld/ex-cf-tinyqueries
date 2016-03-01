<?php
namespace TinyQueries;

/**
 * This script is meant to be called by the TinyQueries IDE
 *
 */

/**
 * - This functions checks if there is a config file for TinyQueries
 *   If not, it creates one based on the VCAP_SERVICES env var
 * - Creates a project folder for the queries
 *
 */
function setup()
{
	$configFile 	= dirname(__FILE__) . '/config.xml';
	$queriesFolder 	= dirname(__FILE__) . '/../queries';
	
	// If there is a configfile we are ready
	if (file_exists($configFile))
		return array(
			'message' => 'Nothing to do - Config file already exists'
		);

	// Get credentials from Bluemix env var and app var
	$services 		= getEnvJson("VCAP_SERVICES");
	$application 	= getEnvJson("VCAP_APPLICATION");
	
	$specs = getDBspecs($services);
	
	// Create TQ config file
	$template = configTemplate();
	$TQapiKey = HttpTools::getRequestVar('_api_key', '/^\w+$/');
	
	if (!$TQapiKey)
		throw new \Exception('API key TinyQueries not sent');
	
	// Currently only 1 project per client is supported
	$projectLabel = HttpTools::getRequestVar('_project', '/^[\w\-\.]+$/');
	
	if (!$projectLabel)
		throw new \Exception('Project label not sent');

	$template = str_replace('{projectLabel}', 	$projectLabel, 		$template);	
	$template = str_replace('{driver}', 		$specs['driver'], 	$template);	
	$template = str_replace('{host}', 			$specs['hostname'], $template);	
	$template = str_replace('{name}', 			$specs['name'], 	$template);	
	$template = str_replace('{user}', 			$specs['username'], $template);	
	$template = str_replace('{password}', 		$specs['password'], $template);	
	$template = str_replace('{queries_folder}', $queriesFolder, 	$template);
	$template = str_replace('{api_key}', 		$TQapiKey, 			$template);
	
	$r = @file_put_contents( $configFile, $template );
	
	if (!$r)
		throw new \Exception("Cannot create configfile $configFile");
	
	if (file_exists($queriesFolder))
		return array(
			'message' => 'Nothing to do - Queries folder already exists'
		);;
		
	createFolder( $queriesFolder );
	createFolder( $queriesFolder . "/interface" );
	createFolder( $queriesFolder . "/sql" );
	createFolder( $queriesFolder . "/tiny" );
	
	return array(
		'message' => 'TinyQueries setup complete'
	);
}

function getDBspecs(&$services)
{
	foreach ($services as $id => $service)
	{
		switch ($id)
		{
			case 'cleardb': 
				$specs = $service[0]['credentials'];
				$specs['driver'] = 'mysql';
				return $specs;
		}
	}
	
	throw new \Exception("Cannot find an (appropriate) SQL database service in the VCAP_SERVICES");
}

/**
 * Creates a folder on the filesystem
 *
 */
function createFolder($folder)
{
	$r = @mkdir( $folder );
		
	if (!$r)
		throw new \Exception("Cannot create folder");
}

/**
 *
 *
 */
function getEnvJson($varname)
{
	$value = getenv($varname);
	
	if (!$value)
		throw new \Exception("No $varname found");
	
	return json_decode($value, true);
}

/**
 * Returns a template for a TinyQueries config file
 *
 */
function configTemplate()
{
	return '<?xml version="1.0" encoding="UTF-8"?>
<config>

	<project
		label		= "{projectLabel}"
	/>

	<database
		driver		= "{driver}"
		host 		= "{host}"
		name		= "{name}"
		user		= "{user}"
		password	= "{password}"
	/>

	<compiler
		enable		= "true"
		api_key 	= "{api_key}"
		input		= "{queries_folder}/tiny"
		output		= "{queries_folder}"
	/>
	
	<postprocessor
		nest_fields	= "true"
	/>

</config>';
}


