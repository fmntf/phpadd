phpadd
======

**phpadd** is Abandoned Docblock Detector for PHP.

It can scan your applications for missing or invalid docblocks. You can also configure it saying to skip 
docblocks in private and/or protected methods.

Reporting
------------
There are several different reporters: HTML, XML, tab-delimited and JSON. It is possible to ask phpadd to 
process its output with multiple publishers:
<code>phpadd --publish-html out.html --publish-xml out.xml myapp/</code>

If you just want to see the scan stats, you can append <code>-stats</code> to your publisher:
<code>phpadd --publish-xml-stats stats.xml myapp/</code>

If you specify a dash instead of a filename, the output will be send to stdout.
<code>phpadd --publish-html - myapp/ | grep ...</code>
