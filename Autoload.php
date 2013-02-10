<?php

namespace FlexibleORMDemoWebsite;

use FlexibleORMDemoWebsite\Exceptions\WebsiteConfigurationException;
use ORM\AutoLoader;
use ORM\Utilities\Configuration;

require_once 'flexible-orm/AutoLoader.php';

Autoload::Load();

/**
 * The generic Autoloader class for this website.
 *
 * @todo Review the other suho website to get better CLI retrieval.
 */
class Autoload {
    /**
     * Load in the general configurations for this website.
     */
    public static function Load() {
        // Register the flexible-orm autoloader (automatically loads php files to find class files as they are requested)
        $flexibleORMAutoloader = new AutoLoader();
        $flexibleORMAutoloader->register();

        // Include the packages locations for this project's name-space to enable the exception class to be found.
        // by declaring that any fully qualified class beginning with 'RoboraterAuditing\' should be found in this directory.
        $packageLocations = array_merge($flexibleORMAutoloader->getPackageLocations(), array('FlexibleORMDemoWebsite' => __DIR__));
        $flexibleORMAutoloader->setPackageLocations($packageLocations);

        // Read in configurations from the site-config-???.ini file.
        $siteConfigFilename = self::GetSiteConfigFilename();
        Configuration::Load($siteConfigFilename);

        // Set PHP Package locations within the Autoloader based on the [packages] section in the site-config-???.ini file.
        $flexibleORMAutoloader->setPackageLocations(array_merge($packageLocations, Configuration::packages()->toArray()));

        if (php_sapi_name() !== 'cli') {
            error_reporting(E_ALL);
            // Turn on DEBUG settings if enabled
            if (Configuration::application('debug')) {
                ini_set('display_errors', 'on');
            } else {
                // Otherwise hide error messages (they will only be logged in the log file)
                ini_set('display_errors', 'off');
            }
        }
    }

    /**
     * This function is used to get the SiteConfig filename for this website.
     *
     * return @string
     *     The name of the file to retrieve the file.
     */
    public static function GetSiteConfigFilename() {
        if (php_sapi_name() === 'cli') {
            $applicationConfigFilename = self::GetSiteConfigFilenameForCLI();
        } else {
            $applicationConfigFilename = self::GetSiteConfigFilenameFromServerVariable();
        }
        if (!file_exists($applicationConfigFilename)) {
            throw new WebsiteConfigurationException("The configuration file, `$applicationConfigFilename` could not be found!");
        }
        return $applicationConfigFilename;
    }

    /**
     * This function is used to get the SiteConfig filename using the SERVER variable, SITE_CONFIG.
     *
     * return @string
     *     The name of the file to retrieve the file.
     * @throws WebsiteConfigurationException
     */
    public static function GetSiteConfigFilenameFromServerVariable() {
        if (array_key_exists('SITE_CONFIG', $_SERVER)) {
            return __DIR__."/".$_SERVER['SITE_CONFIG'];
        }
        throw new WebsiteConfigurationException("
The configuration file has not been specified in the apache config
file!  Please ensure add:

`SetEnv SITE_CONFIG \"site-config-???.ini\"`

in the apache configuration file and create a configuration file
accordingly.
");
    }

    /**
     * This function is used to get the SiteConfig filename for command line scripts.
     *
     * return @string
     *     The name of the file to retrieve the file.
     * @throws WebsiteConfigurationException
     */
    public static function GetSiteConfigFilenameForCLI() {
        $basePath = realpath(__DIR__);
        $applicationINIKey = gethostname().":".$basePath;
        foreach(explode("\n", file_get_contents($basePath.'/CLISiteConfigINISelector')) as $line) {
            if (preg_match("/^([^\s]*)\s*([^\s]*)$/", $line, $m)) {
                if ($m[1] === $applicationINIKey) {
                    return __DIR__."/".$m[2];
                }
            }
        }
        throw new WebsiteConfigurationException("
The application config file has not been specified in the CLIApplicationINISelector file.
Please append a line as follows:

$applicationINIKey site-config-???.ini

to the file so that the CLI tools know which application ini to use.");
    }

}
