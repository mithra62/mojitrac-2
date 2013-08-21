<?php
/**
 * The IP module model
 * @author Eric
 *
 */
class PM_Model_Ical extends Model_Abstract
{
	
	public $event_id = null;
	
	public $filename = null;
	
	public $desc = null;
	
	public $summary = null;
	
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db = new PM_Model_DbTable_Ips;
	}
	
	public function setDates($start, $stop)
	{
		$this->start_date = date("Ymd\THi00", $start);
		$this->end_date = date("Ymd\THi00", $stop);		
	}
	
	public function download()
	{
		$Filename = "SSPCAEvent" . $this->event_id . ".vcs";
		header("Content-Type: text/x-vCalendar");
		header("Content-Disposition: inline; filename=".$this->filename);
		
		    /** Put mysql connection and query statements here **/
		
		$DescDump = str_replace("\r", "=0D=0A=", $this->desc);
		
		$vCalendar = '
BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN
VERSION:2.0
METHOD:PUBLISH
BEGIN:VEVENT
ORGANIZER:MAILTO:eric@ericlamb.net
DTSTART:20110215T160000Z
DTEND:20110215T163000Z
LOCATION:Test Location
TRANSP:OPAQUE
SEQUENCE:0
UID:040000008200E00074C5B7101A82E00800000000608FFECBE8CBCB010000000000000000100
 0000054D362E83BB36045854E97C57480B3F6
DTSTAMP:20110214T094511Z
DESCRIPTION:Here is where the description goes\n\nAnd here are some
  new\n\nLines\n\n\n
SUMMARY:Test Subject
PRIORITY:5
X-MICROSOFT-CDO-IMPORTANCE:1
CLASS:PUBLIC
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR';	

		echo $vCalendar;
	}
	
}