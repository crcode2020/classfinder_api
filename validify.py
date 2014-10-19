#!/usr/bin/env python

import sys
import re

def filter(s):
    # fix non breaking spaces
    s=re.sub(r'&nbsp([^;])', '&nbsp;\g<1>', s)
    # fix /td /a inversions and missing closing font
    s=re.sub(r'</td></A>', '</A></font></td>', s)
    # fix double /tr's
    s=re.sub(r'</tr>\n</tr>', '</tr>\n<tr>', s)
    # fix callspan and double closed string
    s=re.sub(r'<td colspan="v', '<td v', s)
    s=re.sub(r'"">', '">', s)
    # fix font tag issue
    s=re.sub(r'face="Verdana</font>">Credits</b></td>',
             r'face="Verdana">Credits</font></b></td>', s)
    # insert missing tr
    s=re.sub(r'<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>',
             r'<tr>\n<td></td><td></td><td></td><td></td><td></td>', s)
    # remove extra inserted table cruft
    s=re.sub(r'</tr>\n<tr>\n<td></td>\n</tr>\n<tr>\n', '', s)
    s=re.sub(r'<tr align="center">\n</tr>\n', '', s)
    # remove form entries
    s=re.sub(r'</form>', '', s)
    s=re.sub(r'<FORM.*', '', s)
    s=re.sub(r' <form.*', '', s)
    # remove inputs
    s=re.sub(r'<input[^>]*name="sel_crn"[^>]*value="(\d+)"[^>]*>', '\g<1>', s)
    s=re.sub(r'<input[^>]*>', '', s)
    # fix missing tr
    s=re.sub(r'/tr>\n<td valign=top', '/tr>\n<tr>\n<td valign=top', s)
    s=re.sub(r'<td colspan="2"', '<tr>\n<td colspan="2"', s)
    # fix double tr's
    s=re.sub(r'<tr>\n<tr>', '<tr>', s)
    s=re.sub(r'<tr>\n<tr>', '<tr>', s)
    # fix empty tr's
    s=re.sub(r'<tr>\n</tr>', '<tr>\n<td>&nbsp;</td>\n</tr>', s)
    # encode &
    s=re.sub(r' & ', ' &amp; ', s)
    # fix font
    s=re.sub(r'    </FONT>', '&nbsp;</FONT>', s)
    s=re.sub(r'Verdana"></font>', 'Verdana">&nbsp;</font>', s)
    #remove map and center
    s=re.sub(r'<map.*', '', s)
    s=re.sub(r'<center.*', '', s)
    #fix script
    s=re.sub(r'<SCRIPT', '<SCRIPT type="javascript"', s)
    #fix table
    s=re.sub(r'<table', '<table summary=""', s)
    s=re.sub(r'<p>\n\n <pre>\n <P> *\n\n<hr />\n</pre>\n', '', s)
    s=re.sub(r'COLOR=#006600', 'COLOR="#006600"', s)
    return s

if __name__ == "__main__":
    print filter(sys.stdin.read())
