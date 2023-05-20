<?php

/*
 * Created by: S. Delgado
 * Date: 14-01-2020
 *
 */
class UtilsAutoloader
{
    private $baseDir;

    /**
     * Arreglo que guarda las instancias del autoloader. La key es el directorio base y el valor
     * es la instancia
     *
     * @var array
     */
    private static $instances;

    /**
     * Método Constructor.
     *
     */
    public function __construct()
    {
        $this->baseDir = __DIR__ . '/../';
    }

    /**
     * Registra una nueva instancia del autoloader en el SPL
     *
     * @return UtilsAutoloader Instancia del autoloader registrado
     */
    public static function register()
    {
        $key = __DIR__ . "\/..\/";

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self();
        }

        $loader = self::$instances[$key];
        spl_autoload_register(array($loader, 'autoload'));

        return $loader;
    }

    /**
     * Método de autoload de las clases en utils
     *
     * @param string $className
     */
    public function autoload($className)
    {
        $file = sprintf('%s/%s.class.php', $this->baseDir, str_replace("//", "/", str_replace("\\", DIRECTORY_SEPARATOR, $className)));
        //print_r($file);
        if (is_file($file)) {
            require_once $file;
        }
    }
}
