<?php
/* 
 * Project PeachJar Automation
 * The purpose is to take in two files and combine them into one needed for 
 * PeachJar. Then open up a SFTP connection and upload the file to PeachJar
 * 
 * Input Files:
 * 1) A CSV with all Parent Emails and the schools thier student(s) belong to
 *      Name: PeachJar1.csv
 *      Format: NameOfSchool,EmailAddress,**ContactType (Campus Fields: sch.name,contacts.email,contacts.messenger)
 * 2) A CSV with parent emails and the contact preferences (filtered down to those 
 *      who have requested General). 
 *      Name: PeachJar2.csv
 *      Format: EmailAddress,emailMessengerType (Campus fields: pcontact.email,pcontact.emailMessenger)
 * 
 * **ContactType is not used, but taken into consideration for my filter. I am going to only look at the first two anyways.
 * 
 * Output File:
 * 1) A CSV with School and Email address
 *      Name: PeachJarOut.csv
 * 
 * The input file 2 filters who want to recieve the General message from Camapus. 
 * File 1 email needs to be compared to the list of emails in file2 and added
 * to the output iff it exists in file 2.
 * 
 * Once the output file is created, we then need to SFTP is over to PeachJar.
 * 
 * SFTP Information
 * Host: uploads.peachjar.com 
 * Port: 22 
 * Username: columbus_sd 
 * Password: gv-6vZh^
 *
 */ 

//SFTP information
//https://stackoverflow.com/questions/14050231/php-function-ssh2-connect-is-not-working
/*
$host = "uploads.peachjar.com";
$port = 22;
$username = "columbus_sd";
$password = "gv-6vZh^";
$remotePath = "/home/columbus_sd/";
*/

include('Net/SFTP.php');

//Get the SFTP Info from the config file
$settingsArray = parse_ini_file("config.ini");
$host = $settingsArray["host"];
$port = $settingsArray["port"];
$username = $settingsArray["username"];
$password = $settingsArray["password"];
$remotePath = $settingsArray["remotePath"];

//File names - These need to be set up in Campus (or other SIS)
$input1 = "PeachJar1.csv";
$input2 = "PeachJar2.csv";
$output = "PeachJarOut.csv";

//global variables
$emailList = array();
$outputString = "";
$eol = "\r\n";

//Open input files
$handle1 = fopen($input1, "r");
$handle2 = fopen($input2, "r");

//make an array of email addresses from file 2 to compare to file 1
while ( ($lineData2 = fgetcsv($handle2, 1024, ",")) !== FALSE){

    $email = $lineData2[0];
    array_push($emailList, $email);
}

//Now we look at file 1 and treat it appropriately. 
while ( ($lineData = fgetcsv($handle1, 1024, ",")) !== FALSE){
    $schoolName = $lineData[0];
    $email = $lineData[1];
    
    if ( in_array( $email, $emailList ) ){
        $outputString .= $schoolName . "," . $email . $eol;
    }
}

$fhout = fopen($output, 'w') or die("Can't open the file" . $output);
fwrite($fhout, $outputString);
fclose($fhout); 

fclose($handle1);
fclose($handle2);


//Now we SFTP $output to PeachJar
//https://stackoverflow.com/questions/9572314/uploading-files-with-sftp 

//$resFile = fopen("ssh2.sftp://" . $username . ":" . $password . "@" . $host . ":" . $port .  "/home/columbus_sd/" . $output, 'w');
//$srcFile = fopen($output, 'r');
//$writtenBytes = stream_copy_to_stream($srcFile, $resFile);
//fclose($resFile);
//fclose($srcFile);

// Could not get the above to work in my testing environment.

// https://stackoverflow.com/questions/4689540/how-to-sftp-with-php
$sftp = new Net_SFTP($host);
if ( !$sftp->login($username,$password)){
    exit('Login Failed!');
}

// puts a three-byte file named filename.remote on the SFTP server
$sftp->put($output,'xxx');
// puts an x-byte file named filename.remote on the SFTP server,
// where x is the size of filename.local
$sftp->put($output, $output, NET_SFTP_LOCAL_FILE);


?>