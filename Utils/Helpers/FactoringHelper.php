<?php

namespace App\Utils\Helpers;

use App\Models\Executor;
use Encore\Admin\Auth\Database\Administrator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FactoringHelper
{

    # todo: needs to change this, what if id = 1 doesn't exist
    const DEFAULT_EXECUTOR_ID = 1;

    /**
     * @param $text
     * @param int $limit
     * @return string
     */
    public static function makeShort($text, $limit = 50)
    {
        $cleanHtml = strip_tags($text);
        if (strlen($cleanHtml) > $limit) {
            $cleanHtml = str_limit($cleanHtml, $limit);
        }

        return $cleanHtml;
    }

    /**
     * @param $date
     * @param bool $short
     * @return string
     */
    public static function localizeDate($date, $short = true)
    {
        $parser = Carbon::parse($date);

        return "{$parser->day} " . trans('months.' . ($short ? $parser->shortLocaleMonth : $parser->localeMonth)) . " {$parser->year}";
    }

    /**
     * @return int
     */
    public static function getDefaultExecutorId()
    {
        if ($officeManagerExecutor = Executor::get()->where('description', 'office-manager')->first()) {

            return $officeManagerExecutor->id;
        }

        return self::DEFAULT_EXECUTOR_ID;
    }

    /**
     * Get errors messages from session
     *
     * @param \Illuminate\Validation\Validator|null $validator
     *
     * @return array
     */
    public static function getErrorsMessages($validator = null)
    {
        $errors = [];

        if (!is_null($validator)) {
            $errorBag = $validator->messages();
        }

        if (session()->has('errors')) {
            /** @var \Illuminate\Support\ViewErrorBag $errorViewBag */
            $errorViewBag = session()->get('errors');
            if ($errorViewBag->hasBag('default')) {
                /** @var \Illuminate\Support\MessageBag $errorBag */
                $errorBag = $errorViewBag->getBag('default');
            }
        }

        if (isset($errorBag) && $errorBag instanceof \Illuminate\Support\MessageBag) {
            $errors = $errorBag->getMessages();
        }

        return $errors;
    }

}
