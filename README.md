Aubrey
===========
Aubrey is a data store for PHP applications that is based on the entity-attribute-value with classes and relationships (EAV/CR) model. Aubrey enables rapid development of applications that

* have sparse and heterogenous data,
* have large numbers of classes and class instances, or
* have dynamic ontologies.

This framework also includes utilities for many useful operations that are not in the PHP standard library.
 
To get started checkout http://jtnelson.github.com/Koncourse/

Installation
------------
* Download the Aubrey source to your project directory
* Run bin/setup.php to configure your project's  preferences and to seed the database
* Read the full Aubrey documentation at http://jtnelson.github.com/Koncourse/ for more information on getting started
 
 
Author
------
Jeff Nelson
 
System Requirements
-------------------
* A Linux or Linux-like OS
* PHP 5.3.6 or greater with 
** Memcache
** PDO
** cURL
* MySQL 5.5.9 or greater
* Yahoo PlaceFinder API APP ID (only required for use with the KGeoDataUtils)
 
License
-------
Aubrey is released under the GNU Lesser General Public License. For more information see COPYING and COPYING.LESSER which are
included with this package or go to http://www.gnu.org/licenses/lgpl-3.0.txt