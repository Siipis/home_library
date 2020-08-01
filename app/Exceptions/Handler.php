<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Don't show detailed errors unless debug is on.
        $message = config('app.debug')
            ? $exception->getMessage()
            : trans('alert.error');

        // Serve JSON to Ajax requests
        if ($request->expectsJson()) {
            return response()->json([
                'errors' => array($message)
            ]);
        }

        // Otherwise flash the error through the session
        if ($request->acceptsHtml()) {
            // Redirect forms back, or...
            if (!empty($request->input())) {
                return redirect()->back()->withErrors($message);
            }

            // ...create a message bag manually
            $errors = $request->session()->get('errors', new ViewErrorBag);

            if (!$errors instanceof ViewErrorBag) {
                $errors = new ViewErrorBag;
            }

            $request->session()->flash('errors',
                $errors->put('default', new MessageBag([
                    'exception' => $message
                ]))
            );
        }

        // Fallback to Laravel's default behaviour
        return parent::render($request, $exception);
    }
}
