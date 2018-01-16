<?php


// ----------------------------------------------------------------
// --- Multi-line log file with pre-cooked IPs and URLs ----
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