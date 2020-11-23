int LOPos = 2;
int LONeg = 3;

void setup() {
  Serial.begin(9600);
  pinMode(LOPos, INPUT);
  pinMode(LONeg, INPUT);
}

void loop() {
  if((digitalRead(LOPos) == HIGH) || (digitalRead(LONeg) == HIGH)) {
    Serial.println('!');
  }
  else {
    Serial.println(analogRead(A0));
  }

  delay(1);
}
