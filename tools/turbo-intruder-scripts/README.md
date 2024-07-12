# Turbo Intruder scripts

Turbo Intruder is another free Burp extension written by James Kettle. I've created a Turbo Intruder script to help exploit a mailer. This script is used when you've identified that the server supports Encoded Word but you want to know if the mailer will allow you to split the email by using nulls or other characters. For ease of use we've also built a Burp Intruder word list. I've also included a Punycode bruteforce fuzzer that I used to help finding malformed Punycode.

# Install Turbo Intruder

You can install Turbo Intruder from the BApp store:
https://portswigger.net/bappstore/9abaa233088242e8be252cd4ff534988

# Using Turbo Intruder

1. Identify a parameter you want to use in Burp
2. Add %s in the request where you want to fuzz the email address
3. Right click on the request and go to Extensions->Turbo Intruder->Send to turbo intruder
4. Copy and paste the encoded-word.py file into Turbo Intruder
5. Customise the script to your needs. When testing the variables validServer and invalidServer should both point to your collaborator server until you've identified an exploit.
6. You should get collaborator interactions within Turbo Intruder if successful.
7. Once you've identified that the mailer is vulnerable to email splitting you can then change invalidServer to an invalid host.
