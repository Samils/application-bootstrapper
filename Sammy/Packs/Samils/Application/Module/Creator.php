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
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Application\Module\Creator')) {
  /**
   * @trait Creator
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
  trait Creator {
    public static function Mod (string $moduleName = null){
      $moduleName = strtolower ($moduleName);

      $trace_ = debug_backtrace ();

      $trace = $trace_ [0];

      if (isset ($trace_ [0]['file'])
        && $trace_ [0]['file'] === __FILE__) {
        $trace = $trace_ [1];
      }

      if (!self::IsModuleRightName ($moduleName)) {
        return error_bad_module_name ($moduleName, $trace);
      }

      if (isset (self::$Modules [strtolower ($moduleName)])) {
        return self::$Modules [strtolower ($moduleName)];
      }

      if (class_exists ($moduleName . 'Ctrl')) {
        $controllerName = ($moduleName . 'Ctrl');
      } elseif (class_exists ($moduleName . 'Controller')) {
        $controllerName = ($moduleName . 'Controller');
      } else {
        return self::NoFoundControllerErr ($moduleName, $trace);
      }

      if (!self::IsController ($controllerName)) {
        return self::NoValidControllerErr ($moduleName, $trace);
      }

      # $ControllerSerial = date('dymihs');
      # $module_class_name = (
      #   $moduleName . 'ExtendedInternalClass' . (
      #     $ControllerSerial
      #   )
      # );

      # if (!class_exists ($module_class_name)) {
      #   eval('final class '.$module_class_name.' extends '.$controllerName.' {}');
      # }

      $mod = new $controllerName ($moduleName);
      # self::modImportRequires ($mod, $mod_requires);

      return self::$Modules [strtolower ($moduleName)] = $mod;
    }
  }}
}
