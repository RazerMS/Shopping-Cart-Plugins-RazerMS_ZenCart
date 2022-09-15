WARNING!
========

<h3>Please be informed that this library has been obsoleted and marked as End-of-Life product. 


------------------------------------------------------


Razer Merchant Services (RMS) ZenCart Plugin
===============

Razer Merchant Services Plugin for ZenCart Shopping Cart developed by Razer Merchant Services R&D team.


Supported version
-----------------

ZenCart version 1.3.x<br>
ZenCart version 1.4.x<br>
ZenCart version 1.5.x<br>


Notes
-----

Razer Merchant Services is not responsible for any problems that might arise from the use of this module. 
Use at your own risk. Please backup any critical data before proceeding. For any query or 
assistance, please email support-sa@razer.com 


Installations
-------------

- Download this plugin, Extract/Unzip the files. 

- Upload or copy those file and folder into your cart root folder

- (Skip this if your cart is not hosted in UNIX environment). 
Please ensure the file permission is correct. It's recommended to CHMOD to 644

- Login as ZenCart store admin, go to `Modules` -> `Payment (Payment Modules)`
    You'll see there is a Razer Merchant Services payment option, click on it and press `[Install]` button.

- Please provide all the necessary details into the respective fields. Please refer below :

    1.RMS Merchant ID : Merchant ID provided by RMS

    2.RMS Verify Key : Please refer your RMS merchant profile for this key.

    3.RMS Multi Return URL : Define return url for this module to update order after payment has been made.
     Otherwise you may need to define on your RMS merchant profile.

    4.Sort order of display : set to 0
    
    5.Set Order Status : set to Pending [1]

- Click on `[Update]` button to save your setting.

- Now, access your RMS merchant account using the loginID and password provided to you.

- Click Transaction > Transaction Settings > Endpoint setting.

    `E.g :`

    `Return URL : http://www.yourdomain.com.my/process.php`

    `Callback URL : http://www.yourdomain.com.my/process_callback.php`

- Click on `[Submit]`

- Now you can try to use RMS at the shop front by going thru a complete purchase procedure.
 


Contribution
------------

You can contribute to this plugin by sending the pull request to this repository.


## Resources

- GitHub: https://github.com/RazerMS
- Website: https://merchant.razer.com/
- Twitter: https://twitter.com/Razer_MS
- YouTube: https://www.youtube.com/c/RazerMerchantServices
- Facebook: https://www.facebook.com/RazerMerchantServices/
- Instagram: https://www.instagram.com/RazerMerchantServices/

Issues
------------

Submit issue to this repository or email to our support-sa@razer.com


Support
-------

Merchant Technical Support / Customer Care : support-sa@razer.com
Sales/Reseller Enquiry : sales-sa@razer.com
Marketing Campaign : marketing-sa@razer.com
Channel/Partner Enquiry : channel-sa@razer.com
Media Contact : media-sa@razer.com
R&D and Tech-related Suggestion : technical-sa@razer.com
Abuse Reporting : abuse-sa@razer.com
