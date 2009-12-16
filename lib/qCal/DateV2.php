<?php
/**
 * Base date object. Stores date information only (without a time). Internally the date is stored as a
 * unix timestamp, but the time portion of it is not used. If you need a date with a time, use qCal_DateTime
 * @package qCal
 * @subpackage qCal_Date
 * @copyright Luke Visinoni (luke.visinoni@gmail.com)
 * @author Luke Visinoni (luke.visinoni@gmail.com)
 * @license GNU Lesser General Public License
 */
class qCal_DateV2 {

	/**
	 * @var unix timestamp
	 */
	protected $date;
	/**
	 * @var int The start day of the week (defaults to Monday)
	 */
	protected $wkst = 1;
	/**
	 * @var array The results of a getdate() call
	 */
	protected $dateArray;
	/**
	 * @var array The month in a two-dimensional array (picture a calendar)
	 */
	protected $monthMap = array();
	/**
	 * Class constructor
	 * @param int The year of this date
	 * @param int The month of this date
	 * @param int The day of this date
	 */
	public function __construct($year = null, $month = null, $day = null, $rollover = false) {
	
		$this->setDate($year, $month, $day, $rollover);
	
	}
	/**
	 * This is a factory method. It allows you to create a date by string or by another date object (to make a copy)
	 */
	public static function factory($date) {
	
		if (is_integer($date)) {
			// @todo Handle timestamps
		}
		if (is_string($date)) {
			if (!$timestamp = strtotime($date)) {
				// if unix timestamp can't be created throw an exception
				throw new qCal_Date_Exception_InvalidDate("Invalid or ambiguous date string passed to qCal_DateV2::factory()");
			}
		}
		
		$date = getdate($timestamp);
		$newdate = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
		$newdate = getdate($newdate);
		return new qCal_DateV2($newdate['year'], $newdate['mon'], $newdate['mday']);
	
	}
	/**
	 * Set the date of this class
	 * The date defaults to now. If any part of the date is missing, it will default to whatever "now"'s
	 * date portion is. For instance, if the year provided is 2006 and no other portion is given, it will
	 * default to today's month and day, but in the year 2006. If, for any reason the date defaults to a 
	 * nonsensical date, an exception will be thrown. For instance, if you specify the year as 2006, and
	 * the current date is february 29th, an exception will be thrown because the 29th of February does not
	 * exist in 2006. 
	 * @param int The year of this date
	 * @param int The month of this date
	 * @param int The day of this date
	 * @throws qCal_Date_Exception_InvalidDate
	 */
	public function setDate($year = null, $month = null, $day = null, $rollover = false) {
	
		$now = getdate();
		if (is_null($year)) {
			$year = $now['year'];
		}
		if (is_null($month)) {
			$month = $now['mon'];
		}
		if (is_null($day)) {
			$day = $now['mday'];
		}
		$this->date = mktime(0, 0, 0, $month, $day, $year);
		$this->dateArray = getdate($this->date);
		if (!$rollover) {
			if ($this->dateArray["mday"] != $day || $this->dateArray["mon"] != $month || $this->dateArray["year"] != $year) {
				throw new qCal_Date_Exception_InvalidDate("Invalid date specified for qCal_DateV2: \"{$month}/{$day}/{$year}\"");
			}
		}
		
		/**
		 * When a date is instantiated, it caches information such as which weekday of the month it is.
		 * For instance, it may be the 2nd Tuesday of the month and the 3rd to last Tuesday of the month.
		 * It also does this for the year. It will be the 23rd Tuesday of the year and the 29th from last
		 * Tuesday of the year (I have no idea if that's even possible, I'm just throwing random numbers
		 * out there).
		 */
		
		$this->dateArray["t"] = date("t", $this->date);
		// @todo This will not be accurate if the week start isn't monday
		$this->dateArray["W"] = date("W", $this->date);
		
		// $this->monthMap = $this->generateMonthMap();
		// pre($this->monthMap);
		
		// weekday of month (ie: 2nd Tuesday of the month)
		// $this->dateArray['wday_of_mon'] = "";
		
		// how many weekdays to the end of the month (ie: 2nd Tuesday from the end of the month)
		// $this->dateArray['wday_to_end_mon'] = ;
		
		// weekday of year (ie: 25th Tuesday of the year)
		// $this->dateArray['wday_of_year'] = ;
		
		// how many weekdays to the end of the year (ie: 35th Tuesday from the end of the year)
		// $this->dateArray['wday_to_end_year'] = ;
	
	}
	
	/**
	 * Getters
	 * The next dozen or so methods are just your standard getters for things such as month, day, year, week day, etc.
	 */
	
	/**
	 * Get the month (number) of this date
	 * @return integer A number between 1 and 12 inclusively
	 */
	public function getMonth() {
	
		return $this->dateArray["mon"];
	
	}
	/**
	 * Get the month of this date
	 * @return string The actual name of the month, capitalized
	 */
	public function getMonthName() {
	
		return $this->dateArray["month"];
	
	}
	/**
	 * Get the day of the month
	 * @return integer A number between 1 and 31 inclusively
	 */
	public function getDay() {
	
		return $this->dateArray["mday"];
	
	}
	/**
	 * Get the day of the year
	 * @return integer A number between 0 and 365 inclusively
	 */
	public function getYearDay() {
	
		return $this->dateArray["yday"];
	
	}
	/**
	 * Get the year
	 * @return integer The year of this date, for example 1999
	 */
	public function getYear() {
	
		return $this->dateArray["year"];
	
	}
	/**
	 * Get the day of the week 
	 * @return integer A number between 0 (for Sunday) and 6 (for Saturday).
	 */
	public function getWeekDay() {
	
		return $this->dateArray["wday"];
	
	}
	/**
	 * Get the day of the week
	 * @return string The actual name of the day of the week, capitalized
	 */
	public function getWeekDayName() {
	
		return $this->dateArray["weekday"];
	
	}
	/**
	 * Get the amount of days in the current month of this year
	 */
	public function getNumDaysInMonth() {
	
		return $this->dateArray["t"];
	
	}
	/**
	 * Get the week of the year
	 * @todo This is not accurate if the week start isn't monday. I need to adjust for that
	 */
	public function getWeekOfYear() {
	
		return $this->dateArray["W"];
	
	}
	/**
	 * Get a unix timestamp for the date
	 * @return integer The amount of seconds since unix epoch (January 1, 1970 UTC)
	 */
	public function getUnixTimestamp() {
	
		return $this->dateArray[0];
	
	}
	
	/**
	 * Date magic
	 * This component is capable of doing some really convenient things with dates.
	 * It is capable of determining things such as how many days until the end of the year,
	 * which monday of the month it is (ie: third monday in february), etc.
	 */
	
	public function isXthWeekdayOfMonth($weekday, $xth) {
	
		
	
	}
	
	/**
	 * Maps
	 * The following methods build maps of the month and year, respectively.
	 * What this means is it generates a multi-dimensional array with week days as one dimension
	 * and the week as the second (picture a calendar).
	 */
	
	/*public function generateMonthMap() {
		
		$map = array();
		$mday = 1;
		while ($mday <= $this->getDaysInMonth())
		return $map;
	
	}*/

}