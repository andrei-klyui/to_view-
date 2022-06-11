<?php


namespace App\Traits;

trait CheckDateWithTimeTrait
{
    /**
     * Return object DateTime without null
     *
     * @param string $value
     * @return \DateTime
     */
    public function getDateWithTime($value)
    {
        $dueDate = new \DateTime($value);
        $dueTime = $dueDate->format('H:i:s');

        $date = $dueDate->format('Y-m-d');
        $time = $dueTime == '00:00:00' ? now()->format('H:i:s') : $dueTime;

        return new \DateTime($date . ' ' . $time);
    }
}
