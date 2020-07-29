<?php


namespace App\Http\Api\Providers\Covers;


use Storage;
use App\Http\Api\Providers\ApiProvider;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;

class ImageCast extends ApiProvider
{
    public function cast(Model $model, $key)
    {
        $value = $model->getAttribute($key);

        if (\Str::startsWith($value, 'http') && !\Str::contains($value, config('app.url'))) {
            $response = $this->request($value);

            $folder = 'images/' . $model->getTable() . '/' . $key;
            $filename = $model->getKey() . '.png';
            $path = $folder . '/' .  $filename;

            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder);
            }

            imagepng(imagescale(
                    imagecreatefromstring($response), 500, -1, IMG_BICUBIC),
            Storage::path($path));

            return $filename;
        }

        return $value;
    }

    protected function parseResponse($response)
    {
        return $response;
    }

    protected function handleException(RequestException $exception)
    {
        throw $exception;
    }

    protected function getTimeout()
    {
        return 60;
    }

    protected function getCacheConfig()
    {
        return false;
    }

    protected function getUrl(array $options = [])
    {
        return false;
    }
}
