phpadd
======

**phpadd** is a Abandoned Docblock Detector for PHP.

It will scan your applications for missing or invalid docblocks. You can also configure it saying to skip docblocks in private 
and/or protected methods.

Excluding
-----------
With the exclude parameter you can ignore certain directories or files in your code. Usefull when you have 3rd party libraries or
unittest which you want to ignore. Any globbing pattern will match and it's possible to add multiple excludes on the commandline:

phpadd --publish-html phpadd.html --exclude library/zend* --exclude tests/* /my/source/path


Reporting
------------
There are 3 different reporting types: HTML, XML and tab-delimited. It is possible to add multiple publishers on the commandline.
If you specify a '-' instead of a filename, the output will be send to stdout.
