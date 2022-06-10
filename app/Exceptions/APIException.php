<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class APIException extends Exception
{

    public function __construct(Throwable $error, int $code = null)
    {
        parent::__construct();
        $this->error = $error;
        $this->code = $this->getValidCode($code);
        $this->message = $this->getValidMessage();
    }

    private function getValidMessage(): string
    {
        // default message
        if ($this->error->getMessage() == '')
            return 'Erro desconhecido';

        // validation
        if ($this->error instanceof ValidationException)
            return $this->error->validator->errors()->first();

        // dont found register
        if ($this->error instanceof ModelNotFoundException)
            return 'Registro não encontrado';

        // system message
        if ($this->error instanceof Throwable)
            return $this->error->getMessage();
    }

    private function getValidCode(int $code): int
    {
        // from constructor
        if ($code)
            return $code;

        // from system
        if ($this->getCode() != 0)
            return $this->getCode();

        // default
        return 500;
    }

    public function render()
    {
        return response()->json(["error" => true, "message" => $this->message], $this->code);
    }
}
