<?php
class System {
    private static $classSettings = null;
    private $settings;

    public function __construct() {
        if (self::$classSettings === null) {
            $environment = ini_get('display_errors') ? 'Development' : 'Production';

            $httpHost = $_SERVER['HTTP_HOST'];
    
            // Load environment variables based on HTTP_HOST and environment
            $this->loadEnvironment($httpHost, $environment);
    
            self::$classSettings = [
                'DBConnection' => "pgsql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_DATABASE') . ";user=" . getenv('DB_USERNAME') . ";password=" . getenv('DB_PASSWORD'),
                'FTPConnection' => (object) [
                    'host' => getenv('FTP_HOST'),
                    'port' => getenv('FTP_PORT'),
                    'username' => getenv('FTP_USERNAME'),
                    'password' => getenv('FTP_PASSWORD'),
                    'path' => getenv('FTP_PATH'),
                ],
                'App' => (object) [
                    'url'   => $environment == 'Development' ? getenv('DEV_APP_URL') : getenv('APP_URL'),
                    'ec_url'   => getenv('EC_URL'),
                    'secret'   => getenv('SECRET_KEY'),
                    'tenant'   => getenv('APP_TENANT'),
                    'title' => getenv('APP_TITLE'),
                    'company' => getenv('COMP_NAME'),
                    'state' => getenv('APP_STATE'),
                    'botToken' => getenv('TG_BOT_TOKEN'),
                    'botName' => getenv('TG_BOT_NAME'),
                    'geoserver' => getenv('GEO_TYPE_NAME'),
                ]
            ];
            $this->settings = self::$classSettings;
        } else {
            $this->settings = self::$classSettings;
        }
        
    }

    private function loadEnvironment($httpHost, $environment) {
        // Define the filename based on the HTTP_HOST
        // REVIEW - why only declaring __DIR__."/.ENV" will work? shouldn't calling .ENV directly also work? 
        $envFilename = __DIR__."/.ENVDEV"; // Default value
        if ($environment == 'Production') {
            switch ($httpHost) {
                case 'dev.admin.kup.my':
                    $envFilename = __DIR__.'/.KUPENV';
                    break;
                case 'dev.admin.kutt.my':
                    $envFilename = __DIR__.'/.KUTTENV';
                    break;
                case 'dev.admin.kudr.my':
                    $envFilename = __DIR__.'/.KUDRATENV';
                    break;
                case 'ucidos.asiadebut.tech':
                    $envFilename = __DIR__.'/.KUPENV';
                    break;
                case 'kiter.asiadebut.tech':
                    $envFilename = __DIR__.'/.KUTTENV';
                    break;
                case 'kudrat.asiadebut.tech':
                    $envFilename = __DIR__.'/.KUDRATENV';
                    break;
                case 'admin.kup.my':
                    $envFilename = __DIR__.'/.KUPENV';
                    break;
                case 'kutt.ddns.net':
                    $envFilename = __DIR__.'/.KUTTENV';
                    break;
                case 'admin.kutt.my':
                $envFilename = __DIR__. '/.KUTTENV';
                    break;
                case 'admin.kudr.my':
                    $envFilename = __DIR__.'/.kudrat';
                    break;
                default:
                    break;
                // Add more cases for other hosts as needed
            }
        }

        // Load environment variables from the selected file
        $fileExist = file_exists($envFilename);
        if ($fileExist) {
            $env = parse_ini_file($envFilename);
            foreach ($env as $key => $value) {
                putenv("$key=$value");
            }
        }
    }

    public function __get($property) {
        if (array_key_exists($property, $this->settings)) {
            return $this->settings[$property];
        }

        // Check if the property is within 'AppSetting'
        if (array_key_exists('App', $this->settings)) {
            return $this->settings['App'][$property];
        }
        // Handle the case where an undefined property is accessed
        throw new Exception("Undefined property: $property");
    }
}

?>
