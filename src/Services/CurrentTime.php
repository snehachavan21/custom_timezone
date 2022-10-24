<?php
namespace Drupal\custom_timezone\Services;

use Drupal\Core\Datetime\DateFormatter;

/**
 * CurrentTime class.
 */
class CurrentTime
{
    /**
     * @var \Drupal\Core\Datetime\DateFormatter
     */
    protected $date_formatter;

    /**
     * CurrentTime constructor.
     * 
     * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
     * DateFormatter Service
     */
    public function __construct(DateFormatter $date_formatter)
    {
        $this->date_formatter = $date_formatter;
    }

    /**
     * Get current date and time according to timezone.
     *
     * @param $selected_timezone
     * timezone to get current time
     * 
     * @return string $current_time
     *   current data and time.
     */
    public function getCurrentTime($selected_timezone)
    {
        $current_time = strtotime('now');
        $current_time_str = $this->date_formatter->format($current_time, 'custom', 'jS M Y - g:i A', $selected_timezone);
        return $current_time_str;
    }

    /**
     * Get current date and time according to timezone for the display.
     *
     * @param $selected_timezone
     * timezone to get current time
     * 
     * @return array $current_time_details
     *   current data and time details in array format.
     */
    public function displayCurrentTimeDetails($selected_timezone)
    {
        $current_time = strtotime('now');
        $current_time_details['day'] = $this->date_formatter->format($current_time, 'custom', 'l', $selected_timezone);
        $current_time_details['date'] = $this->date_formatter->format($current_time, 'custom', 'j F Y', $selected_timezone);
        $current_time_details['time'] = $this->date_formatter->format($current_time, 'custom', 'g:i a', $selected_timezone);
        return $current_time_details;
    }


}
