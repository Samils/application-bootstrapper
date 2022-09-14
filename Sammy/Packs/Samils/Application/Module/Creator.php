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
  if (!trait_exists('Sammy\Packs\Samils\Application\Module\Creator')){
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
    public static function Mod($mod_name = null, $mod_requires = null){
      $mod_name = \lower ($mod_name);

      $trace_ = debug_backtrace();

      if(isset($trace_[0]['file']) && $trace_[0]['file']===__FILE__){
        $trace = $trace_[1];
      }else{
        $trace = $trace_[0];
      }

      if (!is_module_right_name($mod_name)) {
        return error_bad_module_name($mod_name, $trace);
      }

      $mod_requires = is_array($mod_requires) ? $mod_requires : [];

      if (isset(self::$Modules[\lower($mod_name)])){
        if (is_array(\lower($mod_requires))){
          self::modImportRequires(
            self::$Modules[ \lower($mod_name) ], (
              $mod_requires
            )
          );
        }
        return self::$Modules[
          \lower($mod_name)
        ];
      }

      if(class_exists($mod_name . 'Ctrl')){
        $ctrl_name = ($mod_name . 'Ctrl');
      }elseif(class_exists($mod_name . 'Controller')){
        $ctrl_name = ($mod_name . 'Controller');
      }else{
        return error_not_found_controller($mod_name, $trace);
      }

      if(!in_array('SamiController', class_parents($ctrl_name)))
        return error_not_novalid_controller($mod_name, $trace);

      $ControllerSerial = date('dymihs');
      $module_class_name = (
        $mod_name . 'ExtendedInternalClass' . (
          $ControllerSerial
        )
      );

      if (!class_exists($module_class_name)) {
        eval('final class '.$module_class_name.' extends '.$ctrl_name.' {}');
      }

      $mod = new $module_class_name($mod_name);
      self::modImportRequires($mod, $mod_requires);

      $SamiFiles = requires('sami-files');
      if (is_callable($SamiFiles))
        $mod->uses( $SamiFiles() );

      self::$Modules[ \lower($mod_name) ] = (
        $mod
      );
      return $mod;
    }
  }}
}
