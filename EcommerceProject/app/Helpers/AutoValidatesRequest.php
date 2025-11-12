<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use RuntimeException;

/**
 * @property \Illuminate\Http\Request|class-string<Request> $request The Request instance or the fully qualified Request class name associated with the current class.
 */
trait AutoValidatesRequest
{
    /**
     * The instantiated Request object resolved from $request or auto-detected.
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $requestObject;

    public function __construct()
    {
        $this->requestObject = isset($this->request)
            ? (($this->request instanceof Request) ? $this->request : (new $this->request))
            : (new ($this->detectRequestClass()));
    }

    /**
     * Detects the corresponding Request class based on the current class name.
     *
     * @return string The fully qualified Request class name.
     *
     * @throws \RuntimeException If no corresponding Request class file is found.
     */
    protected function detectRequestClass()
    {
        $className = basename(get_class($this));
        $requestBasePath = app_path("Http\\Requests");
        $entries = scandir($requestBasePath, SCANDIR_SORT_NONE);
        $detectedClass = null;

        foreach($entries as $entry){
            if($entry === "." || $entry === ".."){
                continue;
            }elseif(is_dir("{$requestBasePath}\\{$entry}")){
                $detectedClass = match(true){
                    file_exists("{$requestBasePath}\\{$entry}\\{$className}.php") => "\\App\\Http\\Requests\\{$entry}\\{$className}",
                    file_exists("{$requestBasePath}\\{$entry}\\{$className}Request.php") => "\\App\\Http\\Requests\\{$entry}\\{$className}Request",
                    default => $detectedClass
                };

                if($detectedClass) break;
            }
        }

        $requestClass = match(true){
            file_exists("{$requestBasePath}\\{$className}.php") => "\\App\\Http\\Requests\\{$className}",
            file_exists("{$requestBasePath}\\{$className}Request.php") => "\\App\\Http\\Requests\\{$className}Request",
            (bool) $detectedClass => $detectedClass,
            default => throw new RuntimeException("Request class not found for {$className}.")
        };
        return $requestClass;
    }

    /**
     * Retrieves the validation rules from the corresponding Request class.
     *
     * @param mixed ...$parameters Optional arguments to be passed to the Request::rules() method.
     * @return array<string, mixed> The validation rules.
     */
    protected function rules(...$parameters)
    {
        return $this->requestObject->rules(...$parameters);
    }

    /**
     * Retrieves the validation messages from the corresponding Request class.
     *
     * @param mixed ...$parameters Optional arguments to be passed to the Request::messages() method.
     * @return array<string, string> The validation messages.
     */
    protected function messages(...$parameters)
    {
        return $this->requestObject->messages(...$parameters);
    }
}
