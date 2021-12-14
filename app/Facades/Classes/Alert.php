<?php


namespace App\Facades\Classes;


use Illuminate\Support\Str;

class Alert
{
    public function primary(string $message = 'saved', string $url = null)
    {
        return $this->make('primary', $message, $url);
    }

    public function secondary(string $message = 'saved', string $url = null)
    {
        return $this->make('secondary', $message, $url);
    }

    public function success(string $message = 'saved', string $url = null)
    {
        return $this->make('success', $message, $url);
    }

    public function danger(string $message = 'saved', string $url = null)
    {
        return $this->make('danger', $message, $url);
    }

    public function warning(string $message = 'saved', string $url = null)
    {
        return $this->make('warning', $message, $url);
    }

    public function info(string $message = 'saved', string $url = null)
    {
        return $this->make('info', $message, $url);
    }

    public function light(string $message = 'saved', string $url = null)
    {
        return $this->make('light', $message, $url);
    }

    public function dark(string $message = 'saved', string $url = null)
    {
        return $this->make('dark', $message, $url);
    }

    private function make(string $type, string $key, string $url = null)
    {
        $replace = [];
        if (Str::contains($key, '.')) {
            $parts = explode('.', $key);

            $key = $parts[1];
            $replace['resource'] = trans('alerts.' . $parts[0]);
        }

        return [
            'alerts' => [[
                'type' => $type,
                'message' => trans('alerts.' . $key, $replace),
                'url' => $url,
            ]]
        ];
    }
}
