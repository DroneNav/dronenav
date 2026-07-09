# ACL

The ACL module, short for Access Control Lists, is an API for other modules to
create lists of users and give them access to nodes.

 * For a full description of the module, visit the project page:
   [project page](https://www.drupal.org/project/acl)

 * To submit bug reports and feature suggestions, or to track changes:
   [issue queue](https://www.drupal.org/project/issues/acl)


## Table of contents

- Requirements
- Installation
- Configuration
- Troubleshooting
- Maintainers
- Supporting organizations
- Acknowledgments


## Requirements

This module requires no modules outside of Drupal core 10+.


## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).


## Configuration

1. Navigate to Administration > Extend and enable the module.
1. It has no UI of its own and will not do anything by itself; install this
   module only if some other module tells you to.


## Troubleshooting

- Even though ACL does not do anything by its own, Core recognizes it as a node
  access module, and it requires you to rebuild permissions upon installation.

- The client module is fully responsible for the correct use of ACL. It is very
  unlikely that ACL should cause errors.

- Unfortunately, the tests have not been ported from Drupal 7, which means the
  present version cannot go out of beta.


## Maintainers

- Hans Salvisberg [salvis]
(https://www.drupal.org/u/salvis)
- Mikhail Khasaya - [dillix]
(https://www.drupal.org/u/dillix)
- Earl Miles [merlinofchaos]
(https://www.drupal.org/u/merlinofchaos)


## Supporting organizations

- Salvisberg Software & Consulting -
  https://www.drupal.org/salvisberg-software-consulting


## Acknowledgments

- Originally written for Drupal 5 and maintained by merlinofchaos.
- Ported to Drupal 6 and 7 and maintained by salvis.
- Ported to Drupal 8 by id.tarzanych (Serge Skripchuk),
  maintained by salvis and xeM8VfDh.
