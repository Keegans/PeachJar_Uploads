 Project PeachJar Automation
 The purpose is to take in two files and combine them into one needed for 
 PeachJar. Then open up a SFTP connection and upload the file to PeachJar
 
 Input Files:
 1) A CSV with all Parent Emails and the schools thier student(s) belong to
      Name: PeachJar1.csv
 2) A CSV with parent emails and the contact preferences (filtered down to those 
      who have requested General. 
      Name: PeachJar2.csv
 
 Output File:
 1) A CSV with School and Email address
       Name: PeachJarOut.csv
  
 The input file 2 filters who want to recieve the General message from Camapus. 
 File 1 email needs to be compared to the list of emails in file2 and added
 to the output iff it exists in file 2.
 
 Once the output file is created, we then need to SFTP is over to PeachJar.