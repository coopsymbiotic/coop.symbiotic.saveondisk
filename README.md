Save on Disk
============

This extension adds the possibility to save CiviCRM reports directly on
the disk.

The files are saved in the "user files" directory, which should be:  
`civicrm/custom/reports/instance-XX.csv`, where `XX` is the report instance.

To download the latest version of this module:  
https://github.com/coopsymbiotic/coop.symbiotic.saveondisk

Warnings
========

* This extension does not run the buildACLClause() function, meaning that you may have deleted contacts show up in some reports. If you are using ACLs in general, this can also cause important issues.

Requirements
============

- CiviCRM >= 4.7

Installation
============

Install as any other regular CiviCRM extension:

1- Download this extension and unpack it in your 'extensions' directory.
   You may need to create it if it does not already exist, and configure
   the correct path in CiviCRM -> Administer -> System -> Directories.

2- Enable the extension from CiviCRM -> Administer -> System -> Extensions.

Support
=======

Please post bug reports in the issue tracker of this project on github:  
https://github.com/coopsymbiotic/coop.symbiotic.saveondisk/issues

Commercial support via Coop SymbioTIC:  
<https://www.symbiotic.coop/en>

License
=======

(C) 2017 Mathieu Lutfy <mathieu@symbiotic.coop>

Distributed under the terms of the GNU Affero General public license (AGPL).
See LICENSE.txt for details.
