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

Filtering
------------
It is possible to prevent PHPADD to look for abandoned DocBlocks in files and classes.

The switch <code>--exclude-paths <path></code> ignore files matching <code>&lt;path&gt;</code>.
The switches <code>--exclude-classes &lt;regexp&gt;</code> and <code>--exclude-methods &lt;regexp&gt;</code> will ignore 
respectively classes or methods whose names match <code>&lt;regexp&gt;</code>.

Exclude a directory:

<code>phpadd  --exclude-paths library/Zend/ --publish-xml stats.xml myapp/</code>

Exclude all directories named <code>controllers</code> in all <code>application</code> subfolders:

<code>phpadd  --exclude-paths application/*/controllers/ --publish-xml stats.xml application/</code>

Exclude all constructors:

<code>phpadd  --exclude-methods ^__construct$ --publish-xml stats.xml myapp/</code>

