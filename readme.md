To Start using it add to composer.json repozitory

    "repositories": [
      {
      "type": "git",
       "url": "git@github.com:shivergard/smart-parse.git"
      }
    ],

and add requirements

	"require": {
		...
        "shivergard/smart-parse" : "dev-master" ,
    },

and add service provider

		'providers' => [
		...
      'Shivergard\SmartParse\SmartParseServiceProvider',
		...