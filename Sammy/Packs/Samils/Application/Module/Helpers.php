<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Application\Module
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
namespace Sammy\Packs\Samils\Application\Module {
  use SamiController;
  use Samils\Handler\Error;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Application\Module\Helpers')) {
  /**
   * @trait Helpers
   * Base internal trait for the
   * Samils\Application\Module module.
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
  trait Helpers {
    /**
     * @method boolean verify if is given module name is a valid name
     */
    public static function IsModuleRightName (string $name = null) {
      return (boolean)(is_right_var_name ($name));
    }

    /**
     * @method boolean verify if a given object is a module or controller object
     */
    public static function IsModule ($mod = null) {
      return is_object ($mod) && ($mod instanceof SamiController);
    }

    /**
     * @method boolean verify if a given string is a valid controller reference
     */
    public static function IsController (string $class = null) {
      $class = preg_replace ('/(Controller|Ctrl)$/i', '', $class);

      $controllerClassRef = join ('', [
        $class, 'Controller'
      ]);

      return (boolean)(
        class_exists ($controllerClassRef) &&
        in_array (SamiController::class,
          class_parents ($controllerClassRef)
        )
      );
    }

    /**
     * @method boolean verify if a given object is a controller object
     */
    public static function IsControllerObject ($object = null) {
      return (boolean)(
        is_object ($object) &&
        self::IsController (get_class ($object))
      );
    }

    /**
     * @method void NoValidControllerErr
     */
    private static function NoValidControllerErr ($controller, array $backTrace = null) {
      $error = new Error;

      $error->message = "Invalid controller object";
      $error->title = "Sammy\Packs\Samils\Application\Module::Error";

      $TraceDatas = requires ('trace-datas');

      $traceDatas = $TraceDatas ($backTrace);

      $error->paragraphes = [
        "File => {$traceDatas->file}"
      ];

      $error->handle ([
        'title' => $error->title,
        'paragraphes' => $traceDatas,
        'source' => $traceDatas
      ]);
    }

    /**
     * @method void NoValidControllerErr
     */
    private static function NoFoundControllerErr ($controller, array $backTrace = null) {
      $error = new Error;

      $error->message = "Undefined controller $controller";
      $error->title = "Sammy\Packs\Samils\Application\Module::Error::Undefined Controller";

      $TraceDatas = requires ('trace-datas');

      $traceDatas = $TraceDatas ($backTrace);

      $error->handle ([
        'title' => $error->title,
        'paragraphes' => $traceDatas,
        'source' => $traceDatas
      ]);
    }
  }}
}
