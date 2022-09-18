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
  if (!trait_exists('Sammy\Packs\Samils\Application\Module\Imports')){
  /**
   * @trait Imports
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
  trait Imports {

    private static function modImportRequire ($mod = null, $require = null, $as = null) {

      $mod = is_string($mod) ? requires('@'.$mod) : $mod;

      if (is_module ($mod) && is_string($require) && isset(self::$Modules[ $require ])) {
        if(is_string($as) && is_module_right_name($as)){
          $mod->def($as, self::$Modules[ $require ]);
        }else{
          $mod->def($require, self::$Modules[ $require ]);
        }
      }

    }

    private static function modImportRequires ($mod = null, $mod_requires = null) {

      $mod = is_string($mod) ? requires('@'.$mod) : $mod;

      if (is_module ($mod)) {

        foreach ($mod_requires as $key => $value) {

          if (isset(self::$Modules[ $key ])){

            if (!$mod->_isset ($value) && is_module_right_name($value)){
              self::modImportRequire ($mod, $key, $value);
            }
            else{
              /**
               ** Handle Error
               **
               **/
              //exit('Error: can not import ' . $key . ' module as ' . $value);
            }

          } elseif (is_int ($key) && isset (self::$Modules [ $value ])) {

            if (!$mod->_isset ($value) && is_module_right_name ($value)){
              self::modImportRequire ($mod, $value, $value);
            } else {
              exit ('Error: can not import ' . $value . ' module as ' . $value);
            }

          } else {

            if (is_module_right_name ($key)) {

              $sml_mod_location = (__modules__. "/$key.sami-module");

              if(Saml::ModCreateFromFile($sml_mod_location)){
                $v = is_module_right_name($value) ? $value : $key;
                self::modImportRequire($mod, $key, $v);

              }

            } else if (is_int ($key) && is_module_right_name ($value)) {

              $sml_mod_location = (__modules__ . "/$value.sami-module");

              if (Saml::ModCreateFromFile($sml_mod_location)) {
                self::modImportRequire ($mod, $value, $value);
              }
            }

          }

        }

      }

    }

  }}
}
