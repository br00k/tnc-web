<?php
//
require_once('functions.php');

$steps=array(
		array(
			// Step name
			'name' => 'Server requirements',

			// Items we're going to display
			'fields' => array(

				// Simple text
				array(
					'type' => 'info',
					'value' => 'Before proceeding with the full installation, we will carry out some tests on your server configuration to ensure that you are able to install and run our software. Please ensure you read through the results thoroughly and do not proceed until all the required tests are passed.',
					),

				// Check PHP configuration
				array(
					'type' => 'php-config',
					'label' => 'Required PHP settings',
					'items' => array(
						'php_version' => array('>=5.3', 'PHP 5.3 or newer'), // PHP version must be at least 4.0
						'short_open_tag' => true, // Display the value for "short_open_tag" setting
						'register_globals' => false, // "register_globals" must be disabled
						'safe_mode' => false, // "safe_mode" must be disabled
						'upload_max_filesize' => '>=2mb', // "upload_max_filesize" must be at least 2mb
						),
					),

				// Check loaded PHP modules
				array(
						'type' => 'php-modules',
						'label' => 'Required PHP modules',
						'items' => array(
							'pdo_pgsql'	=> array(true, 'PDO PostgreSQL functions (pdo_pgsql)'),
							'apc'		=> array(true, 'Alternative PHP Cache (apc)'),
							'fileinfo'	=> array(true, 'File Information (fileinfo)'),
							'gd'		=> array(true, 'GD Graphics (gd)'),
							),
					 ),

				// Verify folder/file permissions
				array(
						'type' => 'file-permissions',
						'label' => 'Folders and files',
						'items' => array(
							CORE_DIR.'/cache/' => 'write',
							CORE_DIR.'/uploads/' => 'write',
							CORE_DIR.'/logs/'	=> 'write',
							CORE_DIR.'/data/mails/'    => 'write',
							CORE_DIR.'/public/.htaccess' => 'write',
							CORE_DIR.'/application/configs/application.ini' => 'write',
							),
					 ),
				),
				),


				// STEP: Zend
				array(
						'name' => 'Zend Framework',
						'fields' => array(
							array(
								'type'=>	'info',
								'value'=>	'Where is the Zend Framework library located?<br />
								This is directory that contains the "Zend" directory.<br />
								For the demo try one of these:<br /><strong>/usr/share/php/libzend-framework-php<br />/opt/tmp/ZendFramework-1.11.10-minimal/library<br />/opt/tmp/ZendFramework-1.8.2-minimal/library</strong>',
								),
							array(
								'label' => 'Location',
								'type' => 'text',
								'name' => 'zend_location',
								'default' => ($z = file_path('Zend/Version.php')) ? $z : '',
								'validate' => array(
									array('rule' => 'required'), // make it "required"
									),

								),
							),
						'callbacks' => array(
							array(
								'name'	=> 'zend_check',
								'execute' => 'after',
								'params' => array(
									array(
										'version' => '1.11',
										'location' => isset($_REQUEST['zend_location']) ? $_REQUEST['zend_location'] : null,
										),
									),
								),
							),
						),


						// STEP: SimpleSAMLphp
						array(
								'name' => 'SimpleSAMLphp',
								'fields' => array(
									array(
										'type'=>	'info',
										'value'=>	'<p>Where is SimpleSAMLphp library located? For demo use <strong>/opt/tmp/simplesamlphp</strong></p>'
										),
									array(
										'label' => 'Location',
										'type' => 'text',
										'name' => 'ssp_location',
										'default' => ($s = file_path('lib/SimpleSAML/Utilities.php')) ? $s : '',
										'validate' => array(
											array('rule' => 'required'), // make it "required"
											),

										),
									),

									'callbacks' => array(
											array(
												'name'  => 'simplesamlphp_check',
												'execute' => 'after',
												'params' => array(
														// No 'version' here because not possible to check....
														'location' => isset($_REQUEST['ssp_location']) ? $_REQUEST['ssp_location'] : null,
													),
												),
											),
									),

							array(
								'name' => 'Authenticate',
								'fields' => array(
									array(
										   'type' =>    'info',
                                        'value'=>   '<p>Select the SimpleSAMLphp authentication source that will be used for CORE.</p><p>The next step will try to authenticate you. If this succeeds, the installer will make you the first administrative user.</p><p>Please make sure that the selected authsource actually <strong>WORKS</strong>, otherwise you will be <strong>lost</strong> after the redirect with no way back, and you have to restart your browser to try again...</p>',
                                        ),

                                    array(
                                        'label' =>  'authsource',
                                        'type' =>   'select',
                                        'name' =>   'ssp_authsource',
                                        'items' => get_authsources(),
                                        'validate' => array(
                                            array('rule' => 'required'),
                                            ),
                                        ),

								),
							),



									// Check SimpleSAMLphp authentication
									array(
											'name' => 'Attribute mappings',
											'fields' => array(
												array(
													'type' =>	'info',
													'value'=>	'Choose which SAML atttributes should be used for:',
													),
												array(
													'label' => 'Unique user ID',
													'type' => 'select',
													'name' => 'ssp_uid_attribute',
													'items' => get_attributes(),
													),
												array(
													'label' => 'First name',
													'type' => 'select',
													'name' => 'ssp_fname_attribute',
													'items' => get_attributes(),
													),
												array(
													'label' => 'Last name',
													'type' => 'select',
													'name' => 'ssp_lname_attribute',
													'items' => get_attributes(),
													),
												array(
														'label' => 'E-mail',
														'type' => 'select',
														'name' => 'ssp_email_attribute',
														'items' => get_attributes(),
													 ),
												array(
														'label' => 'Organisation',
														'type' => 'select',
														'name' => 'ssp_organisation_attribute',
														'items' => get_attributes(),
													 ),
												array(
														'label' => 'Country',
														'type' => 'select',
														'name' => 'ssp_country_attribute',
														'items' => get_attributes(),
													 ),

												),
												),

/*
										array(
											'name' => 'Admin account',
											'fields' => array(
												array(
													'type' =>	'Your account will be made 

*/
												// STEP: PostgreSQL
												array(
														'name' => 'PostgreSQL',

														// Items we're going to display
														'fields' => array(

															// Simple text
															array(
																'type' => 'info',
																'value' => 'Please note that the database must be created prior to this step. If you have not created one yet, do so now.',
																),

															// Text box
															array(
																'label' => 'hostname',
																'name' => 'db_hostname',
																'type' => 'text',
																'default' => 'localhost',
																'validate' => array(
																	array('rule' => 'required'), // make it "required"
																	),
																),

															// Text box
															array(
																'label' => 'database',
																'name' => 'db_name',
																'type' => 'text',
																'default' => 'core_test_db',
																'highlight_on_error' => false,
																'validate' => array(
																	array('rule' => 'required'), // make it "required"
																	array(
																		'rule' => 'database', // system will automatically verify database connection details based on the provided values
																		'params' => array(
																			'db_host' => 'db_hostname',
																			'db_user' => 'db_username',
																			'db_pass' => 'db_password',
																			'db_name' => 'db_name'
																			),
																		),
																	),
																),


															// Text box
															array(
																	'label' => 'username',
																	'name' => 'db_username',
																	'type' => 'text',
																	'default' => 'core_user',
																	'validate' => array(
																		array('rule' => 'required'), // make it "required"
																		),
																 ),

															// Text box
															array(
																	'label' => 'password',
																	'name' => 'db_password',
																	'type' => 'text',
																	'default' => 'hackme',
																	'validate' => array(
																		array('rule' => 'required'), // make it "required"
																		),
																 ),

															),
															),

															// Mail setup
															array(
																	'name' => 'Mail options',
																	'fields' => array(
																		array('type' => 'info', 'value'=>'CORE needs to send various e-mail messages'),
																		array(
																			'label'	=> 'SMTP host',
																			'type' => 'text',
																			'name' => 'mail_transport_host',
																			'default' => 'localhost',
																			'validate' => array(array('rule' => 'required')),
																			),
																		array(
																			'label' => 'SMTP port',
																			'type' => 'text',
																			'name' => 'mail_transport_port',
																			'default' => '25',
																			'validate' => array(array('rule' => 'required'), array('rule'=>'numeric')),
																			),

																		array(
																			'label' => 'Default "From" mail address',
																			'type' => 'text',
																			'name' => 'default_from_email',
																			'default' => 'webmaster@'.mydomain(),
																			'validate' => array(array('rule' => 'required')),
																			),

																		array(
																				'label' => 'Default "From" name',
																				'type' => 'text',
																				'name' => 'default_from_name',
																				'default' => 'CORE',
																				'validate' => array(array('rule' => 'required')),
																			 ),

																		array(
																				'label' => 'Default "Reply-To" address',
																				'type' => 'text',
																				'name' => 'default_replyto_email',
																				'default' => 'webmaster+core@'.mydomain(),
																				'validate' => array(array('rule' => 'required')),
																			 ),

																		array(
																				'label' => 'Default "Reply-To" name',
																				'type' => 'text',
																				'name' => 'default_replyto_name',
																				'default' => 'CORE admins',
																				'validate' => array(array('rule' => 'required')),
																			 ),

																		array(
																				'label' => 'Send critical errors to',
																				'type' => 'text',
																				'name' => 'debug_mailto',
																				'default' => 'webmaster@'.mydomain(),
																				'validate' => array(array('rule' => 'required')),
																			 ),
																		),
																		),



																		// my own step
																		array(
																				'name' => 'Conference details',
																				'fields' => array(
																					array(
																						'type' => 'info',
																						'value' => 'Here you can configure the initial conference. This will also be used to manage things',
																						),

																					array(
																						'label'	=>	'Conference name',
																						'type'	=>	'text',
																						'name'	=>	'conf_name',
																						'default' =>	($bits = preg_split('/\./', $_SERVER['HTTP_HOST'])) ? strtoupper($bits[0]).' Conference ' : '',
																						'validate' => array(
																							array('rule' => 'required'),
																							),
																						),

																					array(
																						'label' =>      'Conference abbreviation',
																						'type'  =>      'text',
																						'name'  =>      'conf_abbr',
																						'default' =>    ($bits = preg_split('/\./', $_SERVER['HTTP_HOST'])) ? $bits[0] : 'niets',
																						'validate' => array(
																							array('rule' => 'required'),
																							),
																						),

																					array(
																							'label' =>      'hostname',
																							'type'  =>      'text',
																							'name'  =>      'conf_hostname',
																							'default' =>    $_SERVER['HTTP_HOST'],
																							'validate' => array(
																								array('rule' => 'required'),
																								),
																						 ),
																					),
																					),





																					array(
																							// Step name
																							'name' => 'Save and install',

																							// Items we're going to display
																							'fields' => array(

																								// Simple text
																								array(
																									'type' => 'info',
																									'value' => '<p>All the information needed to bootstrap the installation is collected. We will now:
																									<ul>
																									<li>Install an empty database template</li>
																									<li>Give administrative permission to your account with:
																										<ul>
																											<li>SAML uid attribute: <strong>'.issetweb('ssp_uid_attribute').'</strong></li>
																											<li>value: <strong>'.get_admin().'</strong></li>
																										</ul>
																									</li>
																									</ul></p><p>This will take some seconds to complete. If everything goes well, you will be redirected to CORE</p>',
																									),
																								),

																							// Callback functions that will be executed
																							'callbacks' => array(
																								array(
																									'name' => 'install',
																									'params' => array(
																										get_attributes()
																									),
																								),
																							),
																						),
																						// Dummy step
//																						array( 'name' => 'Finish', 'fields' => array('type'=>'info', 'value'=> 'This is the final step, that you should not see because the redirect...')),
);

