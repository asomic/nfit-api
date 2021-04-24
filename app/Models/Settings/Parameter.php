<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

/**
 *  Parameters of the system
 *
 * 	calendar_start		   time      Start time of the calendar clases
 *  calendar_end   		   time      Finish time of the calendar clases
 *  check_confirm_clases   boolean   Check true if it's necessary confirm the assistance to the class.
 *  mins_confirm_clases    integer   Mins before the class start needs to be confirm, or reservation will be deleted.
 *  check_quite_alumnos    boolean   Check if the user will be remove from a class if he doesn't confirm it.
 *  mins_quite_alumnos     integer   Mins before the class start, the user will be removed if he doesn't confirm her assistance.
 *  user_convertion_days   integer   Max time that in a user end his test plan to take a bill plan
 *  default_password       string    Default password that can be assigned to the users
 *  box_name			   string	 Name of the box
 *  box_email 			   string	 Email given for the box
 *  box_image 			   string	 Image for the box (in emails, headbar, "others")
 *  box_web 			   string	 Box Website url
 *  box_address 		   string	 Box Address
 *  box_country 		   string	 Box Country sport center
 *  box_facebook 		   string	 Box Facebook page url
 *  box_instagram 		   string	 Box Instagram page url
 *  box_prefix 		       string	 Box Prefix phone number (+1) (depends of the country)
 *  box_whatsapp 		   string	 WhatsApp number
 *  box_schedule 		   string	 Times for shedule box (plain text with info about schedule of the sport center)
 *  vimeo_folder           integer   Folder ID in the Vimeo Account to separate sport centers
 *  vimeo_connection       integer   We have Purasangre and global connection to vimeo
 *  default_connection     string    name of the connection to get credentials in config/vimeo
 *  timezone               string    Time zone of the box (ex. 'America/Santiago') by default 'UTC'
 *
 *  THE NEXT VALUES ARE NOT MASSIVE ASSIGNMENT
 *  flow_apiKey            string    Key of the flow account for students clients can buy his sport center (contractable) plans
 *  flow_secret            string    Key secret of the flow account for students clients can buy his sport center (contractable) plans
 *
 *  @var  array
 */
class Parameter extends Model
{
    
}
