/*
  AUTHOR:   Fahim Faisal (embeddedfahim)
  DATE:     October 28, 2020
  LICENSE:  Apache 2.0 (Public Domain)
  CONTACT:  embeddedfahim@gmail.com
*/

#include<SD.h>
#include<SPI.h>
#include<ESP8266WiFi.h>
#include<ESP8266HTTPClient.h>

// pins
int wifiLED = D0; // LED for indicating successful Wi-Fi connection..
int CSPin = D8; // chip select pin..

// global variables
short FTPresult;
String msg, newFileName;

// objects
HTTPClient http;

// Wi-Fi credentials
const char *ssid = "EMBEDDEDFAHIM";
const char *pass = "ubuntu11";

// HTTP server details
String serverURL = "http://remecg.embeddedfahim.xyz";

// FTP server details
char *xhost = "ftp.embeddedfahim.xyz";
char *xusername = "fahim@remecg.embeddedfahim.xyz";
char *xpassword = "heeyamoni30";
char *xfolder = "/";

void setup() {
  Serial.begin(9600);

  // pins initialization
  pinMode(CSPin, OUTPUT);
  pinMode(wifiLED, OUTPUT);

  // modules/protocols initialization
  if(!SD.begin(CSPin)) {
    Serial.println("SD card initialization failed!!");
  }

  // Wi-Fi initialization
  WiFi.mode(WIFI_STA); // static Wi-Fi mode..
  WiFi.begin(ssid, pass);
  WiFi.hostname("RemECG-0001");
  Serial.println("Welcome to RemECG..");
  Serial.print("Trying to connect to the saved Wi-Fi network");
  
  while(WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  
  Serial.println();
  Serial.print("Connected to ");
  Serial.print(ssid);
  Serial.println("..");
  digitalWrite(wifiLED, LOW);
}

void loop() {
  yield(); // for resetting the watchdog timer on NodeMCU..
  checkMode();
}

void checkMode() {
  int httpCode;
  String link, mode;
  
  link = serverURL + "/get_mode.php";
  http.begin(link);
  httpCode = http.GET();

  if(httpCode == 200) {
    mode = http.getString();
  }
  else {
    Serial.println("An error has occured!! Retrying..");
    delay(10);
    checkMode();
  }

  if(mode == "0") {
    return;
  }
  else if(mode == "1") {
    createECGReport();
    uploadECGReport();
    return;
  }
  else if(mode == "2") {
    deleteECGReport();
    return;
  }
}

void uploadMessage(String str) {
  String link;
  int httpCode;
  
  link = serverURL + "/set_msg.php?msg=" + str;
  link.replace(" ", "%20");
  http.begin(link);
  httpCode = http.GET();
  
  if(httpCode == 200) {
    Serial.println("Successfully uploaded message to the web server..");
  }
  else {
    Serial.println("An error has occurred!! Retrying..");
    delay(10);
    uploadMessage(msg);
  }

  delay(100);
}

String getReportID() {
  int httpCode;
  String link, newReportID;
  
  link = serverURL + "/get_reportid.php";
  http.begin(link);
  httpCode = http.GET();

  if(httpCode == 200) {
    newReportID = http.getString();
  }
  else {
    Serial.println("An error has occured!! Retrying..");
    delay(10);
    getReportID();
  }

  return newReportID;
}

void createECGReport() {
  int i;
  File reportFile;
  String reportID;
  
  msg = "Recording ECG, please wait..";
  uploadMessage(msg);
  while(!Serial.available()); // waiting for Arduino to send data over serial..
  reportID = getReportID();
  newFileName = reportID + ".csv";
  reportFile = SD.open(newFileName, FILE_WRITE);
  
  if(reportFile) {
    reportFile.println("time,ecg_reading");

    for(i = 0; i < 5000; i++) {
      reportFile.print(i);
      reportFile.print(",");
      reportFile.print(Serial.readStringUntil('\n'));
    }

    reportFile.close();
  }
  else {
    msg = "Couldn't create report file on SD card!!";
    uploadMessage(msg);
    return;
  }
}

void uploadECGReport() {
  msg = "Uploading ECG report file..";
  uploadMessage(msg);
  FTPresult = doFTP(xhost, xusername, xpassword, newFileName, xfolder);

  if(FTPresult == 226) {
    msg = "ECG report file uploaded successfully..";
    uploadMessage(msg);
  }
  else {
    msg = "An error has occurred!! Retrying..";
    uploadMessage(msg);
    delay(10);
    uploadECGReport();
  }
}

String getDeleteID() {
  int httpCode;
  String link, deleteID;
  
  link = serverURL + "/get_deleteid.php";
  http.begin(link);
  httpCode = http.GET();

  if(httpCode == 200) {
    deleteID = http.getString();
  }
  else {
    Serial.println("An error has occured!! Retrying..");
    delay(10);
    getDeleteID();
  }

  return deleteID;
}

void deleteECGReport() {
  String deleteID, fileName;

  deleteID = getDeleteID();
  fileName = deleteID + ".csv";
  
  if(SD.exists(fileName)) {
    SD.remove(fileName);
  }
}

short doFTP(char* host, char* uname, char* pwd, String fileName, char* folder) {
  File ftx;
  WiFiClient ftpclient;
  WiFiClient ftpdclient;

  const short FTPerrcode = 400;
  const byte Bufsize = 128;
  char outBuf[Bufsize];
  short FTPretcode = 0;
  const byte port = 21;
  ftx = SD.open(fileName, FILE_READ);
  
  if(ftpclient.connect(host, port)) {
    msg = "Connected to the FTP server..";
    uploadMessage(msg);
  } 
  else {
    ftx.close();
    msg = "Failed to connect to the FTP server!!";
    uploadMessage(msg);
    
    return 910;
  }
  
  FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
  
  if(FTPretcode >= 400) {
    return FTPretcode;
  }
  
  ftpclient.print("USER ");
  ftpclient.println(uname);
  FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
  
  if(FTPretcode >= 400) {
    return FTPretcode;
  }
  
  ftpclient.print("PASS ");
  ftpclient.println(pwd);  
  FTPretcode = eRcv(ftpclient,outBuf,Bufsize);
  
  if(FTPretcode >= 400) {
    return FTPretcode;
  }

  if(!(folder == "")) {
    ftpclient.print("CWD ");
    ftpclient.println(folder);
    FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
    
    if(FTPretcode >= 400) {
      return FTPretcode;
    }
  }
  
  ftpclient.println("SYST");
  FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
  
  if(FTPretcode >= 400) {
    return FTPretcode;
  }
  
  ftpclient.println("Type I");
  FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
  
  if(FTPretcode >= 400) {
    return FTPretcode;
  }
  
  ftpclient.println("PASV");
  FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
  
  if(FTPretcode >= 400) {
    return FTPretcode;
  }

  char *tStr = strtok(outBuf,"(,");
  int array_pasv[6];
  
  // break down port address into several integer type tokens..
  for(int i = 0; i < 6; i++) {
    tStr = strtok(NULL,"(,");
    array_pasv[i] = atoi(tStr); // convert to int, why atoi - because it ignores any non-numeric chars after the number..
    
    if(tStr == NULL) {
      msg = "Bad PASV answer!!";
      uploadMessage(msg);
    }
  }

  // extract port range..
  unsigned int hiPort, loPort;
  
  // determine 1's complement for subtraction..
  hiPort = array_pasv[4] << 8; // bitwise shift left by 8..
  loPort = array_pasv[5] & 255; // bitwise add 1 (AND operation)..
  hiPort = hiPort | loPort; //  bitwise OR..

  if(ftpdclient.connect(host, hiPort)) {
    msg = "Connected to data port..";
    uploadMessage(msg);
  }
  else {
    msg = "Connection failed!!";
    uploadMessage(msg);
    ftpclient.stop();
    ftx.close();
  }

  ftpclient.print("STOR ");
  ftpclient.println(fileName);
  FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
  
  if(FTPretcode >= 400) {
    ftpdclient.stop();
    
    return FTPretcode;
  }
  
  byte clientBuf[64];
  int clientCount = 0;
  
  while(ftx.available()) {
    clientBuf[clientCount] = ftx.read();
    clientCount++;
    
    if(clientCount > 63) {
      ftpdclient.write((const uint8_t *)clientBuf, 64);
      clientCount = 0;
    }
  }
  
  if(clientCount > 0) {
    ftpdclient.write((const uint8_t *)clientBuf, clientCount);
  }
  
  ftpdclient.stop();
  FTPretcode = eRcv(ftpclient, outBuf, Bufsize);
  
  if(FTPretcode >= 400) {
    return FTPretcode;
  } 

  ftpclient.println("QUIT");
  ftpclient.stop();
  msg = "Disconnected from the FTP server..";
  uploadMessage(msg);
  ftx.close();

  return FTPretcode;
}

short eRcv(WiFiClient aclient, char outBuf[], int size) {
  byte thisByte;
  char index;
  String respStr = "";
  
  while(!aclient.available()) {
    delay(1);
  }
  
  index = 0;
  
  while(aclient.available()) {  
    thisByte = aclient.read();
    
    if(index < (size - 2)) {
      outBuf[index] = thisByte;
      index++;
    }
  }
  
  outBuf[index] = 0; // putting a null because strtok requires a null-delimited string..

  for(index = 0; index < 3; index++) {
    respStr += (char)outBuf[index];
  }
  
  return respStr.toInt();
}
