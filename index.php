<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__.'/classes/google.class.php';
require_once __DIR__.'/classes/db.class.php';
require_once __DIR__.'/wiser.php';
$db=new DB();

$result = $db->rawQuery("SELECT COUNT(*) as tableCount FROM information_schema.tables WHERE table_schema = 'birthdaygreeter' AND table_name = 'tbl_contacts';");

if(!isset($result[0]['tableCount']) && $result[0]['tableCount'] == 0)
    $db->rawQuery("CREATE TABLE `birthdaygreeter`.`tbl_contacts` ( `id` BIGINT(16) NOT NULL AUTO_INCREMENT , `first_name` TEXT NULL DEFAULT NULL , `middle_name` TEXT NULL DEFAULT NULL , `last_name` TEXT NULL DEFAULT NULL , `contact_no` VARCHAR(255) NOT NULL , `email_id` TEXT NULL DEFAULT NULL , `birthday` DATE NULL DEFAULT NULL , `is_delete` ENUM('0','1') NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;");

$google =new Google(function($authUrl,$baseUrl)
{
    echo "<a href='{$authUrl}'><button>Google Auth</button></a>";die;
});

$connections = $google->getContact();

$query = "INSERT INTO `tbl_contacts`(`first_name`, `middle_name`, `last_name`, `contact_no`, `email_id`, `birthday`) VALUES ";

if(!empty($connections))
{
    $contactCount = count($connections);
    foreach($connections as $id=>$person)
    {
        // Get name details
        $names = $person->getNames();
        $firstName = $middleName = $lastName = '';
        foreach ($names as $name)
        {
            if ($name->getGivenName()) 
                $firstName = str_replace(["'"],'',$name->getGivenName());
            
            if ($name->getMiddleName()) 
                $middleName = str_replace(["'"],'',$name->getMiddleName());
            
            if ($name->getFamilyName())
                $lastName = str_replace(["'"],'',$name->getFamilyName());
        }

        // Get email details
        $emails = $person->getEmailAddresses();
        $email = [];
        foreach ($emails as $e)
            $email[] = $e->getValue();
        
        $email = implode(',',$email);

        // Get phone number details
        $phoneNumbers = $person->getPhoneNumbers();
        $phoneNumber = [];
        foreach ($phoneNumbers as $pn)
        {
            $number = str_replace(['+91 ','+91','-',' '],'',$pn->getValue());
            if(!in_array($number,$phoneNumber))
            $phoneNumber[] = $number;
        }

        $phoneNumber = implode(',',$phoneNumber);
        // Get birth date details
        $birthdays = $person->getBirthdays();
        $birthdate = '';
        foreach ($birthdays as $bd)
        {
            $birthdate = date("Y-m-d", strtotime($bd->getDate()->getDay() . '-' . $bd->getDate()->getMonth() . '-' . $bd->getDate()->getYear()));
            break; // Take only the first birthdate
        }

        $birthdate = (!empty($birthdate) && strlen($birthdate) > 6) ? "'".$birthdate."'" : 'NUll';
        if($contactCount == ($id + 1))
            $query .= " ('{$firstName}','{$middleName}','{$lastName}','{$phoneNumber}','{$email}',{$birthdate});";
        else
            $query .= " ('{$firstName}','{$middleName}','{$lastName}','{$phoneNumber}','{$email}',{$birthdate}), ";
    }

    $db->rawQuery("DELETE FROM  tbl_contacts");
    $status = $db->insert($query);
}
else
{
    echo 'Contact Not Updated';
}