<?php


// ----------------------------------------------------------------
// ---  only declare $fl_config['log'] and $fl_config['pre']  once!  ----
// ----------------------------------------------------------------




// ----------------------------------------------------------------
// --- Apache log file ----
// ----------------------------------------------------------------

$fl_config['log'] = array(
    FAUX_IPv4,
    "str:: ",
    "str::-",
    "str:: ",
    "str::-",
    "str:: ",
    "str::[",
    FAUX_DATE1,
    "str::]",
    "str:: ",
    "str::\"",
    array('GET','POST'),
    "str:: ",
    "fle::urls.txt",
    "str:: ",
    "str::HTTP/1.1",
    "str::\"",
    "str:: ",
    array('200','304','404'),
    "str:: ",
    "int::101-90001",
    "str:: ",
    "str::\"",
    "str::-",
    "str::\"",
    "str:: ",
    "str::\"",
    "fle::useragents.txt",
    "str::\"",
    "fle::cookies.txt",
);



// ----------------------------------------------------------------
// --- Multi-line log file with pre-cooked IPs and URLs ----
// ----------------------------------------------------------------

$fl_config['pre'] = array(
    "fle::serverIPs.txt",
    "fle::urls.txt",
    "int::0-1",
    FAUX_IPv4,
    array('1','2','5', '6', '12'),
);
$fl_config['log'] = array(
    "str::Q", // Q/R, flag for query or response
    "str:: ",
    "pre::3", // source IP of host making the query
    "str:: ",
    "pre::0", // nameserver IP, used to determine customer
    "str:: ",
    "pre::2", // Proto; 0=UDP, 1=TCP
    "str:: ",
    "str::0", // opCode; 0=Query, 4=Notify, 5=Update, etc.
    "str:: ",
    "pre::4", // qType; 1=A, 2=NS, 5=CNAME, 6=SOA, 12=PTR, etc
    "str:: ",
    "pre::1", // query string
    "str:: ",
    "int::50-69", // packet size in bytes (used by roots)
    FAUX_NL,
    "str::R", // Q/R, flag for query or response
    "str:: ",
    "pre::3", // source IP of host making the query
    "str:: ",
    "pre::0", // nameserver IP, used to determine customer
    "str:: ",
    "pre::2", // Proto; 0=UDP, 1=TCP
    "str:: ",
    "str::0", // opCode; 0=Query, 4=Notify, 5=Update, etc.
    "str:: ",
    "pre::4", // qType; 1=A, 2=NS, 5=CNAME, 6=SOA, 12=PTR, etc
    "str:: ",
    "pre::1", // query string
    "str:: ",
    "int::400-700", // packet size in bytes (used by roots)
    "str:: ",
    array('0','2','3'), // Response code if field 1 was an R; 0=NOERROR, 3=NXDOMAIN, 2=SERVFAIL, etc.
);