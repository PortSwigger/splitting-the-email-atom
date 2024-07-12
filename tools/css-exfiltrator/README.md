# CSS Exfiltrator

You can use the CSS exfiltrator to exfiltrate data via CSS. You can customise the script to your needs.

VICTIM - This constant defines the target application host

PORT - Defines the port the exfiltrator server runs on

PROTOCOL - Defines the protocol

HOSTNAME - Is the host name of the attacker's exfiltration server

PREFIX - Is used to find the token. In Joomla's case it tries to find the logout link which contains the token.

# Exploit

You can customise the /exploit route to change how the CSRF form is generated. This route will generate a CSRF attack by reading a file on the filesystem and replacing the token, user id and victim URL obtained from Joomla. This could be customised to your needs.

# Running

To run the exfiltrator server you first need to install the required dependancies. Then run:
node css-exfiltrator-server.js
