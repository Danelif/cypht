<?php
return array(

    'A1 CAPABILITY' =>
        "* CAPABILITY IMAP4rev1 LITERAL+ SASL-IR LOGIN-REFERRALS ID ENABLE IDLE AUTH=PLAIN AUTH=CRAM-MD5\r\n",

    'A3 CAPABILITY' =>
        "* CAPABILITY IMAP4rev1 LITERAL+ SASL-IR LOGIN-REFERRALS ID ENABLE IDLE SORT SORT=DISPLAY THREAD=".
        "REFERENCES THREAD=REFS THREAD=ORDEREDSUBJECT MULTIAPPEND URL-PARTIAL CATENATE UNSELECT CHILDREN NAMESPACE UIDPLUS ".
        "LIST-EXTENDED I18NLEVEL=1 CONDSTORE QRESYNC ESEARCH ESORT SEARCHRES WITHIN CONTEXT=SEARCH LIST-STATUS BINARY MOVE\r\n".
        "A3 OK Capability completed (0.001 + 0.000 secs).\r\n",

    'A2 LOGIN "testuser" "testpass"' =>
        "* BANNER\r\n".
        "* BANNER2\r\n".
        "A2 OK [CAPABILITY IMAP4rev1 LITERAL+ SASL-IR LOGIN-REFERRALS ID ENABLE IDLE SORT ".
        "SORT=DISPLAY THREAD=REFERENCES THREAD=REFS THREAD=ORDEREDSUBJECT MULTIAPPEND URL-PARTIAL CATENATE UNSELECT CHILDREN ".
        "NAMESPACE UIDPLUS LIST-EXTENDED I18NLEVEL=1 CONDSTORE QRESYNC ESEARCH ESORT SEARCHRES WITHIN CONTEXT=SEARCH LIST-STATUS ".
        "BINARY MOVE] Logged in\r\n",

    'A4 ENABLE QRESYNC' =>
        "* ENABLED QRESYNC\r\nA4 OK Enabled (0.001 + 0.000 secs).\r\n",

    'A2 AUTHENTICATE CRAM-MD5' =>
        "+ PDBFOTRCMUMwMkY5NDFFEFU2QkM5MjVFMUITFCMjZAbG9naW5wcm94eTZiLLmFsaWNlLml0Pg==\r\n",

    'dGVzdHVzZXIgMGYxMzE5YmIxMzMxOWViOWU4ZDdkM2JiZDJiZDJlOTQ=' =>
        "A2 OK authentication successful\r\n",

    'A2 AUTHENTICATE XOAUTH2 dXNlcj10ZXN0dXNlcgFhdXRoPUJlYXJlciB0ZXN0cGFzcwEB' =>
        "+ V1WTI5dENnPT0BAQ==\r\n",

    'A5 LIST (SPECIAL-USE) "" "*"' =>
        "* LIST (\NoInferiors \UnMarked \Sent) \"/\" Sent\r\n".
        "A5 OK List completed (0.003 + 0.000 + 0.002 secs).\r\n",

    'A6 LIST (SPECIAL-USE) "" "*"' =>
        "* LIST (\NoInferiors \UnMarked \Sent) \"/\" Sent\r\n".
        "A6 OK List completed (0.003 + 0.000 + 0.002 secs).\r\n",

    'A6 LIST "" "*" RETURN (CHILDREN STATUS (MESSAGES UNSEEN UIDVALIDITY UIDNEXT RECENT))' => 
        "* LIST (\NoInferiors \UnMarked) \"/\" Sent\r\n".
        "* STATUS Sent (MESSAGES 0 RECENT 0 UIDNEXT 1 UIDVALIDITY 1474301542 UNSEEN 0)\r\n".
        "* LIST (\HasNoChildren) \"/\" INBOX\r\n".
        "* STATUS INBOX (MESSAGES 93 RECENT 0 UIDNEXT 1736 UIDVALIDITY 1422554786 UNSEEN 0)\r\n".
        "A6 OK List completed (0.005 + 0.000 + 0.004 secs).\r\n",

    'A6 LSUB "" "*" RETURN (CHILDREN STATUS (MESSAGES UNSEEN UIDVALIDITY UIDNEXT RECENT))' =>
        "* LSUB (\NoInferiors \UnMarked \Sent) \"/\" Sent\r\n".
        "* STATUS Sent (MESSAGES 0 RECENT 0 UIDNEXT 1 UIDVALIDITY 1474301542 UNSEEN 0)\r\n".
        "* A7 OK Lsub completed (0.005 + 0.000 + 0.004 secs).\r\n",

    'A5 NAMESPACE' =>
        "* NAMESPACE ((\"\" \"/\")) NIL NIL\r\nA5 OK Namespace completed (0.001 + 0.000 secs).\r\n",

    'A5 STATUS "INBOX" (UNSEEN UIDVALIDITY UIDNEXT MESSAGES RECENT)' =>
        "* STATUS INBOX (MESSAGES 93 RECENT 0 UIDNEXT 1736 UIDVALIDITY 1422554786 UNSEEN 0)\r\n".
        "A5 OK Status completed (0.001 + 0.000 secs).\r\n",

    'A6 SELECT "INBOX"' =>
        "* FLAGS (\Answered \Flagged \Deleted \Seen \Draft)\r\n".
        "* OK [PERMANENTFLAGS (\Answered \Flagged \Deleted \Seen \Draft \*)] Flags permitted.\r\n".
        "* 93 EXISTS\r\n".
        "* 0 RECENT\r\n".
        "* OK [UIDVALIDITY 1422554786] UIDs valid\r\n".
        "* OK [UIDNEXT 1736] Predicted next UID\r\n".
        "* OK [HIGHESTMODSEQ 91323] Highest\r\n".
        "A6 OK [READ-WRITE] Select completed (0.001 + 0.000 secs).\r\n",

    'A2 STARTTLS' =>
        "A2 OK Begin TLS negotiation now\r\n",

    'A5 NOOP' =>
        "* 23 EXISTS\r\n".
        "A5 OK NOOP Completed\r\n",

    'A5 UID FETCH 1731,1732 (FLAGS INTERNALDATE RFC822.SIZE BODY.PEEK[HEADER.FIELDS (SUBJECT X-AUTO-BCC FROM DATE CONTENT-TYPE X-PRIORITY TO LIST-ARCHIVE REFERENCES MESSAGE-ID)])' =>
        "* 92 FETCH (UID 1731 FLAGS (\Seen) INTERNALDATE \"02-May-2017 16:32:24 -0500\" RFC822.SIZE 1940 BODY[HEADER.FIELDS (SUBJECT X-AUTO-BCC FROM DATE CONTENT-TYPE X-PRIORITY TO LIST-ARCHIVE REFERENCES MESSAGE-ID)] {240}\r\n".
        "Subject: =?utf-8?q?apt-listchanges=3A_news_for_shop?=\r\n".
        "To: root@shop.jackass.com\r\n".
        "Content-Type: text/plain; charset=\"utf-8\"\r\n".
        "Message-Id: <E1d5fPE-0005Vm-8L@shop>\r\n".
        "From: root <root@shop.jackass.com>\r\n".
        "Date: Tue, 02 May 2017 16:32:24 -0500\r\n".
        "\r\n".
        ")\r\n".
        "* 93 FETCH (UID 1732 FLAGS (\Seen) INTERNALDATE \"11-May-2017 14:28:40 -0500\" RFC822.SIZE 1089 BODY[HEADER.FIELDS (SUBJECT X-AUTO-BCC FROM DATE CONTENT-TYPE X-PRIORITY TO LIST-ARCHIVE REFERENCES MESSAGE-ID)] {240}\r\n".
        "Subject: =?utf-8?q?apt-listchanges=3A_news_for_shop?=\r\n".
        "To: root@shop.jackass.com\r\n".
        "Content-Type: text/plain; charset=\"utf-8\"\r\n".
        "Message-Id: <E1d8tlQ-00065l-4t@shop>\r\n".
        "From: root <root@shop.jackass.com>\r\n".
        "Date: Thu, 11 May 2017 14:28:40 -0500\r\n".
        "\r\n".
        ")\r\n".
        "A5 OK Fetch completed (0.001 + 0.000 secs).\r\n",

    'A5 UID FETCH 1731 BODYSTRUCTURE' =>
        "* 92 FETCH (UID 1731 BODYSTRUCTURE (\"text\" \"plain\" (\"charset\" \"utf-8\") NIL NIL \"7bit\" 1317 32 NIL NIL NIL NIL))\r\n".
        "A5 OK Fetch completed (0.001 + 0.000 secs).\r\n",

    'A5 UID FETCH 1731 BODY[]' =>
        "* 92 FETCH (UID 1731 BODY[] {706}\r\n".
        "Return-path: <root@shop.jackass.com>\r\n".
        "Envelope-to: root@shop.jackass.com\r\n".
        "Delivery-date: Tue, 02 May 2017 16:32:24 -0500\r\n".
        "Received: from root by shop with local (Exim 4.89)\r\n".
        "        (envelope-from <root@shop.jackass.com>)\r\n".
        "        id 1d5fPE-0005Vm-8L\r\n".
        "        for root@shop.jackass.com; Tue, 02 May 2017 16:32:24 -0500\r\n".
        "Auto-Submitted: auto-generated\r\n".
        "Subject: =?utf-8?q?apt-listchanges=3A_news_for_shop?=\r\n".
        "To: root@shop.jackass.com\r\n".
        "MIME-Version: 1.0\r\n".
        "Content-Type: text/plain; charset=\"utf-8\"\r\n".
        "Content-Transfer-Encoding: 7bit\r\n".
        "Message-Id: <E1d5fPE-0005Vm-8L@shop>\r\n".
        "From: root <root@shop.jackass.com>\r\n".
        "Date: Tue, 02 May 2017 16:32:24 -0500\r\n".
        "\r\n".
        "Test message\r\n".
        "\r\n".
        ")\r\n".
        "A5 OK Fetch completed (0.001 + 0.000 secs).\r\n",

);