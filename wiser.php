<?php
require_once __DIR__.'/classes/brodcast.class.php';
$db=new DB();
$Brodecast =new Brodecast();
$birtdayPerson = $db->select("SELECT *, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age FROM tbl_contacts WHERE DATE_FORMAT(birthday, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d');");

if(!empty($birtdayPerson))
{
    foreach($birtdayPerson as $person)
    {
        $Brodecast->sendEmail("
        Hello,
        
        Wishing you the happiest of birthdays filled with joy, laughter, and wonderful memories! May this special day bring you everything your heart desires and more. Cheers to another amazing year ahead! 🎈🥳
        
        Best wishes,
        Vishal Baste",'🎉 Happy Birthday,',$person->email_id ?? null);
        $Brodecast->sendSMS("",$person->contact_no ?? null);
        $Brodecast->sendWhatsapp("",$person->contact_no ?? null);
        $Brodecast->sendTelegram("",$person->contact_no ?? null);
    }
}