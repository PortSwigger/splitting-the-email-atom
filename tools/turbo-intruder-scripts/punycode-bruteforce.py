import urllib
import re
import random
from random import choice
def queueRequests(target, wordlists):
    engine = RequestEngine(endpoint=target.endpoint,
                           concurrentConnections=5,
                           requestsPerConnection=100,
                           pipeline=False
                           )
    chrs = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.lower()

    #for i in range(0,10000):     
       #engine.queue(target.req, [i])
       #engine.queue(target.req, [urllib.quote_plus(chr(i).rstrip()),urllib.quote_plus(chr(i).rstrip())])
       #engine.queue(target.req, "0117"+(randomChoice(chrs,4)))
    #for i in range(0,10000):
    for i in chrs:      
        for j in chrs: 
            for k in chrs:               
                engine.queue(target.req, [i,j,k])

def randomChoice(chrs, amount):
    output = ""
    for i in range(0, amount):
        output += random.choice(chrs)
    return output

def handleResponse(req, interesting):
    #if re.search("Punycode conversion:x@.*@", req.response) and not re.search("xn--", req.response):
    if re.search("\\w@svg", req.response) and not re.search("xn--", req.response):
    #if re.search("Punycode conversion:", req.response) and not re.search("xn--", req.response):
        table.add(req)
