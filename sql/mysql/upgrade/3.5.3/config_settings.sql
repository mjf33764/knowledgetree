CREATE TABLE `config_settings` (
  `id` int(255) unsigned NOT NULL,
  `group_name` varchar(255) NOT NULL default '0',
  `item` varchar(255) NOT NULL default '0',
  `type` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default 'default',
  `helptext` varchar(255) NOT NULL default '0',
  `default_value` varchar(255) NOT NULL default 'default',
  `can_edit` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `config_settings` WRITE;
/*!40000 ALTER TABLE `config_settings` DISABLE KEYS */;
INSERT INTO `config_settings`(`id`,`group_name`,`item`,`type`,`value`,`helptext`,`default_value`,`can_edit`) VALUES
(1,'ui','appName','','KnowledgeTree','OEM application name','KnowledgeTree',1),
(2,'KnowledgeTree','schedulerInterval','','30','','30',1),
(3,'dashboard','alwaysShowYCOD','boolean','default','Display the \"Your Checked-out Documents\" dashlet even when empty.','0',1),
(4,'urls','graphicsUrl','','${rootUrl}/graphics','','${rootUrl}/graphics',1),
(5,'urls','uiUrl','','${rootUrl}/presentation/lookAndFeel/knowledgeTree','','${rootUrl}/presentation/lookAndFeel/knowledgeTree',1),
(6,'tweaks','browseToUnitFolder','boolean','default','Whether to browse to the user\'s (first) unit when first going to the browse section.','0',1),
(7,'tweaks','genericMetaDataRequired','boolean','1','','1',1),
(8,'tweaks','developmentWindowLog','boolean','0','','0',1),
(9,'tweaks','noisyBulkOperations','boolean','default','Whether bulk operations should generate a transaction notice on each ; item, or only on the folder.  Default of \"false\" indicates that only the folder transaction should occur.','0',1),
(10,'email','emailServer','','none','','none',1),
(11,'email','emailPort','','default','','',1),
(12,'email','emailAuthentication','boolean','0','Do you need auth to connect to SMTP?\r\n','0',1),
(13,'email','emailUsername','','username','','username',1),
(14,'email','emailPassword','','password','','password',1),
(15,'email','emailFrom','','kt@example.org','','kt@example.org',1),
(16,'email','emailFromName','','KnowledgeTree Document Management System','','KnowledgeTree Document Management System',1),
(17,'email','allowAttachment','boolean','default','Set to true to allow users to send attachments from the document\r\n management system\r\n.','0',1),
(18,'email','allowEmailAddresses','boolean','default','Set to true to allow users to send to any email address, as opposed to\r\n only users of the system\r\n.','0',1),
(19,'email','sendAsSystem','boolean','default','Set to true to always send email from the emailFrom address listed above, even if there is an identifiable sending user\r\n.','0',1),
(20,'email','onlyOwnGroups','boolean','default','Set to true to only allow users to send emails to those in the same\r\n groups as them\r\n.','0',1),
(21,'user_prefs','passwordLength','','6','Minimum password length on password-setting\r\n','6',1),
(22,'user_prefs','restrictAdminPasswords','boolean','default','Apply the minimum password length to admin while creating / editing accounts?\r\n default is set to \"false\" meaning that admins can create users with shorter passwords.\r\n','0',1),
(23,'user_prefs','restrictPreferences','boolean','0','Restrict users from accessing their preferences menus?\r\n','0',1),
(24,'session','sessionTimeout','','1200','Session timeout (in seconds)\r\n','1200',1),
(25,'session','allowAnonymousLogin','boolean','0','By default, do not auto-login users as anonymous.\r\n Set this to true if you UNDERSTAND the security system that KT\r\n uses, and have sensibly applied the roles \"Everyone\" and \"Authenticated Users\".\r\n','0',1),
(26,'ui','companyLogo','','${rootUrl}/resources/companylogo.png','Add the logo of your company to the site\'s appearance. This logo MUST be 50px tall, and on a white background.\r\n','${rootUrl}/resources/companylogo.png',1),
(27,'ui','companyLogoWidth','','313px','The logo\'s width in pixels\r\n','313px',1),
(28,'ui','companyLogoTitle','','ACME Corporation','ALT text - for accessibility purposes.\r\n','ACME Corporation',1),
(29,'ui','alwaysShowAll','boolean','0','Do not restrict to searches (e.g. always show_all) on users and groups pages.\r\n','0',1),
(30,'ui','condensedAdminUI','boolean','0','Use a condensed admin ui\r\n?','0',1),
(31,'ui','fakeMimetype','boolean','0','Allow \"open\" from downloads.  Changing this to \"true\" will prevent (most)\r\n browsers from giving users the \"open\" option.\r\n','0',1),
(32,'ui','metadata_sort','boolean','0','Sort the metadata fields alphabetically\r\n','1',1),
(33,'i18n','useLike','boolean','default','If your language doesn\'t have distinguishable words (usually, doesn\'t\r\n have a space character), set useLike to true to use a search that can\r\n deal with this, but which is slower.\r\n','0',1),
(34,'import','unzip','','unzip','Unzip command - will use execSearchPath to find if the path to the binary is not given\r\n.','unzip',1),
(35,'export','zip','','zip','Zip command - will use execSearchPath to find if the path to the\r\n binary is not given\r\n.','zip',1),
(36,'externalBinary','xls2csv','','xls2csv','','xls2csv',1),
(37,'externalBinary','pdftotext','','pdftotext','','pdftotext',1),
(38,'externalBinary','catppt','','catppt','','catppt',1),
(39,'externalBinary','pstotext','','pstotext','','pstotext',1),
(40,'externalBinary','catdoc','','catdoc','','catdoc',1),
(41,'externalBinary','antiword','','antiword','','antiword',1),
(42,'externalBinary','python','','python','','python',1),
(43,'externalBinary','java','','java','','java',1),
(44,'externalBinary','php','','php','','php',1),
(45,'externalBinary','df','','df','','df',1),
(46,'cache','proxyCacheDirectory','','${varDirectory}/proxies','','${varDirectory}/proxies',1),
(47,'cache','proxyCacheEnabled','boolean','1','','1',1),
(48,'KTWebDAVSettings','debug','','off','This section is for KTWebDAV  only, _LOTS_ of debug info will be logged if the following is \"on\"\r\n','off',1),
(49,'KTWebDAVSettings','safemode','','on','To allow write access to WebDAV clients set safe mode to \"off\".','on',1),
(50,'BaobabSettings','debug','','off','This section is for Baobab only\r\n, _LOTS_ of debug info will be logged if the following is \"on\"\r\n.','off',1),
(51,'BaobabSettings','safemode','','on','To allow write access to WebDAV clients set safe mode to \"off\" below\r\n.','on',1),
(52,'search','searchBasePath','','${fileSystemRoot}/search2','','${fileSystemRoot}/search2',1),
(53,'search','fieldsPath','','${searchBasePath}/search/fields','','${searchBasePath}/search/fields',1),
(54,'search','resultsDisplayFormat','','searchengine','The format in which to display the results\r\n options are searchengine or browseview defaults to searchengine\r\n.','searchengine',1),
(55,'search','resultsPerPage','','50','The number of results per page\r\n, defaults to 25\r\n','25',1),
(56,'search','dateFormat','','Y-m-d','The date format used when making queries using widgets\r\n, defaults to Y-m-d\r\n','Y-m-d',1),
(57,'browse','previewActivation','','default','The document info box / preview is activated by mousing over or clicking on the icon\r\n. Options: onclick (default) or mouse-over\r\n.','onclick',1),
(58,'indexer','coreClass','','JavaXMLRPCLuceneIndexer','The core indexing class\r\n. Choices: JavaXMLRPCLuceneIndexer or PHPLuceneIndexer.','JavaXMLRPCLuceneIndexer',1),
(59,'indexer','batchDocuments','','20','The number of documents to be indexed in a cron session, defaults to 20\r\n.','20',1),
(60,'indexer','batchMigrateDocuments','','500','The number of documents to be migrated in a cron session, defaults to 500\r\n.','500',1),
(61,'indexer','indexingBasePath','','${searchBasePath}/indexing','','${searchBasePath}/indexing',1),
(62,'indexer','luceneDirectory','','${varDirectory}/indexes','The location of the lucene indexes\r\n.','${varDirectory}/indexes',1),
(63,'indexer','extractorPath','','${indexingBasePath}/extractors','','${indexingBasePath}/extractors',1),
(64,'indexer','extractorHookPath','','${indexingBasePath}/extractorHooks','','${indexingBasePath}/extractorHooks',1),
(65,'indexer','javaLuceneURL','','http://127.0.0.1:8875','The url for the Java Lucene Server. This should match up the the Lucene Server configuration. Defaults to http://127.0.0.1:8875\r\n','http://127.0.0.1:8875',1),
(66,'openoffice','host','','default','The host on which open office is installed\r\n. Defaults to 127.0.0.1\r\n','127.0.0.1',1),
(67,'openoffice','port','','default','The port on which open office is listening. Defaults to 8100\r\n','8100',1),
(68,'webservice','uploadDirectory','','${varDirectory}/uploads','Directory to which all uploads via webservices are persisted before moving into the repository\r\n.','${varDirectory}/uploads',1),
(69,'webservice','downloadUrl','','${rootUrl}/ktwebservice/download.php','Url which is sent to clients via web service calls so they can then download file via HTTP GET\r\n.','${rootUrl}/ktwebservice/download.php',1),
(70,'webservice','uploadExpiry','','30','Period indicating how long a file should be retained in the uploads directory.\r\n','30',1),
(71,'webservice','downloadExpiry','','30','Period indicating how long a download link will be available.','30',1),
(72,'webservice','randomKeyText','','bkdfjhg23yskjdhf2iu','Random text used to construct a hash. This can be customised on installations so there is less chance of overlap between installations.\r\n','bkdfjhg23yskjdhf2iu',1),
(73,'webservice','validateSessionCount','boolean','0','Validating session counts can interfere with access. It is best to leave this disabled, unless very strict access is required.\r\n','0',1),
(74,'webservice','useDefaultDocumentTypeIfInvalid','boolean','1','If the document type is invalid when adding a document, we can be tollerant and just default to the Default document type.\r\n','1',1),
(75,'webservice','debug','boolean','0','The web service debugging if the logLevel is set to DEBUG. We can set the value to 4 or 5 to get more verbose web service logging.\r\n Level 4 logs the name of functions being accessed. Level 5 logs the SOAP XML requests and responses.\r\n','0',1),
(76,'clientToolPolicies','explorerMetadataCapture','boolean','1','This setting is one of two which control whether or not the client is prompted for metadata when a\r\n document is added to knowledgetree via KTtools. It defaults to true.\r\n','1',1),
(77,'clientToolPolicies','officeMetadataCapture','boolean','1','This setting is one of two which control whether or not the client is prompted for metadata when a document is added to knowledgetree via KTtools. It defaults to true.','1',1),
(78,'clientToolPolicies','captureReasonsDelete','boolean','1','This setting is one of six which govern whether reasons are asked for in KTtools\r\n.','1',1),
(79,'clientToolPolicies','captureReasonsCheckin','boolean','1','This setting is one of six which govern whether reasons are asked for in KTtools\r\n.','1',1),
(80,'clientToolPolicies','captureReasonsCheckout','boolean','1','This setting is one of six which govern whether reasons are asked for in KTtools\r\n.','1',1),
(81,'clientToolPolicies','captureReasonsCancelCheckout','boolean','1','This setting is one of six which govern whether reasons are asked for in KTtools\r\n.','1',1),
(82,'clientToolPolicies','captureReasonsCopyInKT','boolean','1','This setting is one of six which govern whether reasons are asked for in KTtools\r\n.','1',1),
(83,'clientToolPolicies','captureReasonsMoveInKT','boolean','1','This setting is one of six which govern whether reasons are asked for in KTtools\r\n.','1',1),
(84,'clientToolPolicies','allowRememberPassword','boolean','1','This setting governs whether the password can be stored on the client or not.','1',1),
(85,'DiskUsage','warningThreshold','','10','When free space in a mount point is less than this percentage, the disk usage dashlet will highlight the mount in ORANGE\r\n.','10',1),
(86,'DiskUsage','urgentThreshold','','5','When free space in a mount point is less than this percentage, the disk usage dashlet will highlight the mount in RED\r\n.','5',1),
(87,'KnowledgeTree','useNewDashboard','','default','','1',1),
(88,'i18n','defaultLanguage','','en','Default language for the interface\r\n.','en',1),
(89,'CustomErrorMessages','customerrormessages','','off','Turn custom error messages on or off here','on',1),
(90,'CustomErrorMessages','customerrorpagepath','','customerrorpage.php','Name or url of custom error page\r\n.','customerrorpage.php',1),
(91,'CustomErrorMessages','customerrorhandler','','off','Turn custom error handler on or off','on',1),
(92,'ui','morphEnabled','boolean','0','Enable Morph','0',1),
(93,'ui','morphTo','','blue','Morph Theme\r\n','blue',1),
(94,'KnowledgeTree','logLevel','','default','Choice: INFO or DEBUG','INFO',1),
(95,'storage','manager','','default','','KTOnDiskHashedStorageManager',1),
(96,'ui','ieGIF','boolean','0','','1',1),
(97,'ui','automaticRefresh','boolean','0','','0',1),
(98,'ui','dot','','dot','','dot',1),
(99,'tweaks','phpErrorLogFile','boolean','default','If you want to enable PHP error logging to the log/php_error_log file, change this setting to true\r\n.','0',1),
(100,'urls','logDirectory','','default','','${varDirectory}/log',1),
(101,'urls','uiDirectory','','default','','${fileSystemRoot}/presentation/lookAndFeel/knowledgeTree',1),
(102,'urls','tmpDirectory','','default','','${varDirectory}/tmp',1),
(103,'urls','stopwordsFile','','default','','${fileSystemRoot}/config/stopwords.txt',1),
(104,'cache','cacheEnabled','boolean','default','','0',1),
(105,'cache','cacheDirectory','','default','','${varDirectory}/cache',1),
(106,'cache','cachePlugins','boolean','default','','1',1),
(107,'urls','varDirectory','','default','','${fileSystemRoot}/var',1),
(108,'urls','documentRoot','','default','','${varDirectory}/Documents',0),
(109,'KnowledgeTree','redirectToBrowse','boolean','default','set to true to redirect to browse screen ','false',1),
(110,'KnowledgeTree','redirectToBrowseExceptions','boolean','default','if redirectToBrowse is true, adding usernames to this list will force specific users to be redirected to dashboard e.g. \r\nredirectToBrowseExceptions = admin, joebloggs ','',1);
/*!40000 ALTER TABLE `config_settings` ENABLE KEYS */;
UNLOCK TABLES;