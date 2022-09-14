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
  use Closure;
  use Sammy\Packs\HTTP\Request;
  use Sammy\Packs\HTTP\Response;
  use Samils\ObjectBase as Obj;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists('Sammy\Packs\Samils\Application\Module\Runners')){
  /**
   * @trait Runners
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
  trait Runners {
    /**
     * @var run
     * Run an application
     */
    public function run ($module = null) {

      if (is_string ($module)) {
        $module = $this->module ($module);
      }

      if (!is_module ($module)) {
        return;
      }

      $middlewares = $module->ApplicationMiddlewares();
      $runner = requires ('sami/runner');

      $req = new Request ( $module );
      $res = new Response ( $module );

      #
      Obj::Methods2GlobalScope('\\Sammy\\Packs\\HTTP\\Response',
        '\\Sammy\\Packs\\HTTP\\Response::Instance()', array (
          'exlude' => array (
            'Instance',
            'getProperty',
            'setProperty',
            'def'
          )
        )
      );

      Obj::Methods2GlobalScope('\\Sammy\\Packs\\HTTP\\Request',
        '\\Sammy\\Packs\\HTTP\\Request::Instance()', array (
          'exlude' => array (
            'Instance',
            'getProperty',
            'setProperty',
            'def'
          )
        )
      );

      Obj::Helpers2GlobalScope ();

      # Run whole application
      # middlewares before initializing
      # the application runner module
      if (is_array($middlewares) && $middlewares) {
        # Map the '$middlewares' array on
        # condition that this is filled with a list of
        # module middlewares that should exetute before
        # the controller action executing.
        # Get the middleware core in an '$m' variable.
        foreach ($middlewares as $i => $m) {
          # Bind the middleware to the application
          # module class in order keeping the module
          # reference, but avoid it if the middleware
          # is not a Closure; make sure it is before
          # binding.
          if (!($m instanceof Closure) && is_callable($m)) {
            call_user_func_array ($m,
              [$req, $res]
            );
            continue;
          }

          # Bind the middleware closure to
          # the application module scope
          # in order getting access to the
          # application features
          $middlewareHandler = Closure::bind($m, $module,
            static::class
          );

          $middlewareHandler (
            $req, # Sammy\Packs\HTTP\Request
            $res  # Sammy\Packs\HTTP\Response
          );
        }
      }

      $middlewares = get_declared_classes_extending (
        'Middleware'
      );
      $middlewaresCount = count ( $middlewares );

      for ($i = 0; $i < $middlewaresCount; $i++) {
        $middleware = $middlewares [$i];

        $middlewareCore = new $middleware;

        if (method_exists($middlewareCore, 'handle')) {
          call_user_func_array ([$middlewareCore, 'handle'],
            [$req, $res]
          );
        }
      }

      exit ($runner->runApp ($module));
    }
  }}
}
