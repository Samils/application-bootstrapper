<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Application\Module\Boot
 * - Autoload, application dependencies
 *
 * MIT License
 *
 * Copyright (c) 2020 Ysare
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Sammy\Packs\Samils\Application\Module\Boot {
  use Sammy\Packs\Samils\Application\Module\Creator;
  use Sammy\Packs\Samils\Application\Module\Imports;
  use Sammy\Packs\Samils\Application\Module\Regists;
  use Sammy\Packs\Samils\Application\Module\Runners;
  use Sammy\Packs\Samils\Application\Module\Configs;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists('Sammy\Packs\Samils\Application\Module\Boot\Base')){
  /**
   * @trait Base
   * Base internal trait for the
   *\Samils\Application\Module\Boot module.
   * -
   * This is (in the ils environment)
   * an instance of the php module,
   * wich should contain the module
   * core functionalities that should
   * be extended.
   * -
   * For extending the module, just create
   * an 'exts' directory in the module directory
   * and boot it by using the ils directory boot.
   * -
   */
  trait Base {
    use Creator;
    use Imports;
    use Regists;
    use Runners;
    use Configs;

    /**
     * @var Modules
     * - As list of whole created ils modules
     * - it contains references to the original
     * - objects that have the module datas
     */
    private static $Modules = [];
    private static $Propers = [];

    /**
     * @var AppRunning
     * A bool value indicating if there's
     * an application running
     */
    private static $AppRunning = false;
    private static $ApplicationBootstrapperDepsOn = false;

    /**
     * @var module
     * Craete a new application with a given
     * name a requires
     */
    public function module ($mod_name = null, $mod_requires = null) {
      return self::Mod ($mod_name, $mod_requires);
    }

    /**
     * @method application_module
     * - Locate and instance the application
     * - main module
     */
    public function application_module () {
      $app = $this->module(__app__);

      if (!self::$ApplicationBootstrapperDepsOn) {
        $app->apply ( requires ('sami-bundler'), [
          'ApplicationObject' => $app
        ]);
        self::$ApplicationBootstrapperDepsOn = (true);
      }

      return ($app);
    }

    public static function ApplicationModule () {
      return call_user_func (
        [new static, 'application_module']
      );
    }

    public static function ModExists ($mod_name = null){
      if (is_string(\lower($mod_name)))
        return (isset(self::$Modules[ \lower($mod_name) ]));
    }

    public static function setProperty ($property, $value = null) {
      if (!!(is_string($property) && $property)) {
        self::$Propers[$property] = ( $value );
      }
    }

    public static function getProperty ($property) {
      if (is_string($property) && isset(self::$Propers[$property])) {
        return ( self::$Propers[ $property ] );
      }
    }
  }}
}
